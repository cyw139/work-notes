<?php

namespace App\Imports\ExamSystem;

use App\Models\ExamSystem\Category;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CategoryImport implements WithHeadingRow, WithValidation, WithMultipleSheets
{
    protected $userId;
    protected $type; // 类别的种类：1工种类别、2企业类别；
    protected $importSheets;

    public function __construct(int $userId, int $type)
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    public function sheets(): array
    {
        // TODO: Implement sheets() method.
        $typeData = Category::TYPES[$this->type];
        if (!$typeData) {
            return [];
        }
        // 已实现的可导入
        $this->importSheets = [
            Category::WORK_TYPE => new CategoryFirstSheetImport($this->userId),
            Category::ENTERPRISE_TYPE => new CategorySecondSheetImport($this->userId),
        ];
        return [
            $typeData['name'] => $this->importSheets[$this->type]
        ];
    }

    public function getImportRows() {
        return $this->importSheets[$this->type]->getImportRows() ?? [];
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

}
