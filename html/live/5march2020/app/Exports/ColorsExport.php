<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ColorsExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($colorsArr, $headings){
        //$this->request = $request;
        $this->colorsArr = $colorsArr;
        $this->headings = $headings;
    }


    public function array(): array {

        $colorsArr = $this->colorsArr;
        return $colorsArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}