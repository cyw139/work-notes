<?php


namespace App\Imports\ExamSystem;


use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Question;
use Illuminate\Support\Collection;
use \Maatwebsite\Excel\Concerns\ToCollection;

class QuestionFirstSheetImport implements ToCollection
{
    protected $userId;
    protected $nameIndexMap = [];
    protected $count = 0;
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        // TODO: Implement collection() method.
        foreach ($rows as $index =>  $row) {
            if ($index === 0) {
                $this->getNameIndexMap($row);
            } else {
                $category_id = $row[$this->nameIndexMap['工种类别ID']];
                if (!$category_id) {
                    continue;
                }
                $category = Category::find($category_id);
                if ($category) {
                    $result = Question::create([
                        'category_id' => $row[$this->nameIndexMap['工种类别ID']],
                        'type' => $row[$this->nameIndexMap['试题类型ID']],
                        'content' => $row[$this->nameIndexMap['试题内容']],
                        'answer_options' => $this->getAnswerOptions($row),
                        'answer_true_option' => $this->getAnswerTrueOption($row),
                        'analyze' => $row[$this->nameIndexMap['试题解析']],
                        'admin_id' => $this->userId,
                    ]);
                    $result && $this->count++;
                }
            }
        }
    }

    public function getImportCount(): int
    {
        return $this->count;
    }

    private function getNameIndexMap($firstRow) {
        foreach($firstRow as $index => $item) {
            if (!$item) {
                continue;
            }
            $this->nameIndexMap[$item] = $index;
        }
    }

    // 正确选项标识
    private function getAnswerTrueOption($row)
    {
        if ($row[$this->nameIndexMap['试题类型ID']] == 1) { // 单选
            return $row[$this->nameIndexMap['正确答案']] ? json_encode([$row[$this->nameIndexMap['正确答案']]]) : '';
        } else if ($row[$this->nameIndexMap['试题类型ID']] == 2) { // 多选
            return json_encode(explode('|', $row[$this->nameIndexMap['正确答案']]));
        } else if ($row[$this->nameIndexMap['试题类型ID']] == 3) { // 判断
            return $row[$this->nameIndexMap['正确答案']] ? json_encode([$row[$this->nameIndexMap['正确答案']]]) : '';
        } else {
            return '';
        }
    }

    // 已经过序列化的答题选项内容
    private function getAnswerOptions($row)
    {
        $rowKeys = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $options = [];
        $answer_options = [];
        foreach ($rowKeys as $rowKey) {
            $key = "试题选项（{$rowKey}）";
            if (isset($row[$this->nameIndexMap[$key]]) && $row[$this->nameIndexMap[$key]]) {
                $options[] = $row[$this->nameIndexMap[$key]];
            }
        }
        foreach ($options as $index => $option) {
            $key = $rowKeys[$index];
            $answer_options[$key] = $option;
        }

        return json_encode($answer_options);
    }
}
