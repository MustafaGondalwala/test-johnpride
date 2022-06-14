<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SizeChartsExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($sizeChartsArr, $headings){
        //$this->request = $request;
        $this->sizeChartsArr = $sizeChartsArr;
        $this->headings = $headings;
    }


    public function array(): array {

        $sizeChartsArr = $this->sizeChartsArr;
        return $sizeChartsArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}