<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BrandsExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($brandsArr, $headings){
        //$this->request = $request;
        $this->brandsArr = $brandsArr;
        $this->headings = $headings;
    }


    public function array(): array {

        $brandsArr = $this->brandsArr;
        return $brandsArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}