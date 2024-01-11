<?php


namespace App\Imports\ExamSystem;


use Illuminate\Support\Collection;
use \Maatwebsite\Excel\Concerns\ToCollection;

class BaseSheetImport implements ToCollection
{
    protected $userId;
    protected $nameIndexMap = [];
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
    }

    protected function getNameIndexMap($firstRow) {
        foreach($firstRow as $index => $item) {
            if (!$item) {
                continue;
            }
            $this->nameIndexMap[$item] = $index;
        }
    }

    protected function transformDate($value, $format = 'Y-m-d') {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
}
