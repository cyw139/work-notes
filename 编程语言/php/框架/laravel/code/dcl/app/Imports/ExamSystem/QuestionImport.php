<?php

namespace App\Imports\ExamSystem;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuestionImport implements WithHeadingRow, WithValidation, WithMultipleSheets
{
    protected $userId;
    protected $importSheets;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function sheets(): array
    {
        // TODO: Implement sheets() method.
        $this->importSheets = [
            '试题导入模板' => new QuestionFirstSheetImport($this->userId)
        ];
        return $this->importSheets;
    }

    public function getImportCount() {
        return $this->importSheets['试题导入模板']->getImportCount() ?? 0;
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
