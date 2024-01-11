<?php


namespace App\Imports\ExamSystem;
use App\Models\ExamSystem\Category;
use Illuminate\Support\Collection;
use \Maatwebsite\Excel\Concerns\ToCollection;

class CategoryFirstSheetImport implements ToCollection
{
    protected $userId;
    protected $nameIndexMap = [];
    protected $tree = [];
    protected $importRows = [];
    protected $type = Category::WORK_TYPE;
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
        $nodes = [];
        $tree = [];
        foreach ($rows as $index =>  $row) {
            if ($index === 0) {
                $this->getNameIndexMap($row);
            } else if ($index === 1) {
                continue;
            } else {
//                $tree = array_merge_recursive($tree, $this->treeFormatter($row));
                $nodes = $this->dataFormatter($row);
                $result = $this->import($nodes);
                $this->importRows[] = $result;
            }
        }
//        dd($tree);
//        Category::fixTree();
    }

    public function getImportRows() {
        return $this->importRows;
    }

    private function collectResult(&$result, $node, $isExists) {
        $rs['id'] = $node['id'];
        $rs['name'] = $node['name'];
        $rs['exists'] = $isExists;
        $result[] = $rs;
    }
    private function import(&$nodes) {
        $nodeResult = [];
        foreach ($nodes as $index => &$node) {
            // 序号
            if ($index === 0) {
                $nodeResult[] = ['name' => $node['name']];
                continue;
            }
            if (isset($node['id']) && $node['id'] > 0) {
                $this->collectResult($nodeResult, $node, 1);
                continue;
            }
            $pid = $index === 1 ? null : $nodes[$index - 1]['id'];
            $parentNode = $pid ? Category::find($pid) : null;
            $data = [
                'type' => $this->type,
                'name' => $node['name'],
                'admin_id' => $this->userId
            ];
            $result = Category::create($data, $parentNode);
            $node = $result->toArray();
            $this->collectResult($nodeResult, $node, 0);
        }
        $lastId = $nodes[count($nodes) - 1]['id'] ?? 0;
        $parentNode = $lastId > 0 ? Category::find($lastId) : null;

        // 添加考试类型：初训、复审、复审换证
        $subNodes = $this->addExamTypes($parentNode);
        $nodeResult[count($nodes)] = $subNodes;

        return $nodeResult;
    }

    /**
     * 添加报考类别：初训、复审、复审换证
     */
    private function addExamTypes($parentNode) {
        $names = ['初训', '复审', '复审换证'];
        $nodeResult = [];
        foreach ($names as $name) {
            $node = Category::withTrashed()->firstWhere([
                'type' => $this->type,
                'name' => $name,
                'pid' => $parentNode->id
            ]);
            if ($node) {
                $this->collectResult($nodeResult, $node->toArray(), 1);
                continue;
            }
            $data = [
                'type' => $this->type,
                'name' => $name,
                'admin_id' => $this->userId
            ];
            $result = Category::create($data, $parentNode);
            $node = $result->toArray();
            $this->collectResult($nodeResult, $node,0);
        }
        return $nodeResult;
    }

    private function getNameIndexMap($firstRow) {
        foreach($firstRow as $index => $item) {
            if (!$item) {
                continue;
            }
            $this->nameIndexMap[$item] = $index;
        }
    }

    private function dataFormatter($row) {
        $nodeNames[] = $row[$this->nameIndexMap['序号']];
        $nodeNames[] = $row[$this->nameIndexMap['人员类别']];
        $nodeNames[] = $row[$this->nameIndexMap['行业类别 - 1 / 作业类别']];
        $nodeNames[] = $row[$this->nameIndexMap['行业类别 - 2 / 操作项目']];

        $parentNode = null;
        $nodes = [];
        foreach ($nodeNames as $index => $name) {
            // 序号
            if ($index === 0) {
                $nodes[$index] = ['name' => $name];
                continue;
            }
            if (!$name) {
                continue;
            }
            $nodes[$index] = $this->formatter($name, $parentNode);
            $parentNode = $nodes[$index];
        }
        return $nodes;
    }

    private function treeFormatter($row) {
        $nodeNames[] = $row[$this->nameIndexMap['人员类别']];
        $nodeNames[] = $row[$this->nameIndexMap['行业类别 - 1 / 作业类别']];
        $nodeNames[] = $row[$this->nameIndexMap['行业类别 - 2 / 操作项目']];

        $parentNode = null;
        $total = count($nodeNames);
        foreach ($nodeNames as $level => $name) {
            if (!$name) {
                continue;
            }
            $index = $total - $level - 1;
            $nodes[$index] = $this->formatter($name, $parentNode);
            $parentNode = $nodes[$index];
        }
        for ($i = 0; $i < $total - 1; $i ++) {
            if (!isset($nodes[$i])) {
                continue;
            }
            $child = $nodes[$i];
            $parent = &$nodes[$i + 1];
            $parent['children'][] = $child;
        }
        return $nodes[$total-1];
    }

    private function formatter($name, $parentNode) {

        if (!$parentNode) { //  根节点
            $node = Category::firstWhere([
                'type' => $this->type,
                'name' => $name,
                'pid' => null
            ]);
            if (!$node) {
                $node = [ 'name' => $name ];
            } else {
                $node = $node->toArray();
            }
        } else {
            if (isset($parentNode['id']) && $parentNode['id'] > 0) {
                $node = Category::firstWhere([
                    'type' => $this->type,
                    'name' => $name,
                    'pid' => $parentNode['id']
                ]);
                if (!$node) {
                    $node = [ 'name' => $name ];
                } else {
                    $node = $node->toArray();
                }
            } else {
                $node = [ 'name' => $name ];
            }
        }

        return $node;
    }
}
