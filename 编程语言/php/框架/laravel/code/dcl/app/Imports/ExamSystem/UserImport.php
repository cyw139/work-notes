<?php

namespace App\Imports\ExamSystem;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserImport implements WithHeadingRow, WithValidation, WithMultipleSheets
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
                '用户基本信息' => new UserFirstSheetImport($this->userId)
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
