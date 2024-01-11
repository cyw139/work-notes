<?php

namespace App\Imports\Kspx;

use App\Models\Kspx\ExamsQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExamsQuestionImport implements ToModel, WithHeadingRow, WithValidation, WithMultipleSheets
{
    protected $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function sheets(): array
    {
        // TODO: Implement sheets() method.
        return [
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $examsQuestion = new ExamsQuestion([
            'exams_question_type_id' => $row['试题类型ID'],
            'exams_subject_id' => $row['专业ID'],
            'exams_point_id' => $row['考点ID'],
            'exams_module_id' => $row['试题所属板块ID'],
            'level' => $row['试题难度'],
            'content' => $row['试题内容'],
            'answer_options' => $this->getAnswerOptions($row),
            'answer_true_option' => $this->getAnswerTrueOption($row),
            'analyze' => $row['试题解析'],
            'admin_id' => $this->userId,
        ]);
        return $examsQuestion;
    }

    public function rules(): array
    {
        return [

        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    // 正确选项标识
    private function getAnswerTrueOption($row)
    {
        if ($row['试题类型ID'] == 1) { // 单选
            return $row['正确答案'] ? serialize($row['正确答案']) : '';
        } else if ($row['试题类型ID'] == 2) { // 多选
            return serialize(explode('|', $row['正确答案']));
        } else if ($row['试题类型ID'] == 3) { // 判断
            return $row['正确答案'] ? serialize($row['正确答案']) : '';
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
            if (isset($row[$key]) && $row[$key]) {
                $options[] = $row[$key];
            }
        }
        foreach ($options as $index => $option) {
            $key = $rowKeys[$index];
            $answer_options[$key] = $option;
        }

        return serialize($answer_options);
    }
}
