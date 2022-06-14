<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SizesExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($sizesArr, $headings){
        //$this->request = $request;
        $this->sizesArr = $sizesArr;
        $this->headings = $headings;
    }


    public function array(): array {

        $sizesArr = $this->sizesArr;
        return $sizesArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}