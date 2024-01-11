<?php

namespace App\Imports\ExamSystem;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RegisterImport implements WithHeadingRow, WithValidation, WithMultipleSheets
{
    protected $userId;
    protected $classId;

    public function __construct(int $userId, int $classId)
    {
        $this->userId = $userId;
        $this->classId = $classId;
    }

    public function sheets(): array
    {
        // TODO: Implement sheets() method.
        return [
            new RegisterFirstSheetImport($this->userId, $this->classId),
        ];
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
