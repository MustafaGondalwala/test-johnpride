<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CategoriesExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($categoriesArr, $headings){
        //$this->request = $request;
        $this->categoriesArr = $categoriesArr;
        $this->headings = $headings;
    }


    public function array(): array {

        $categoriesArr = $this->categoriesArr;
        return $categoriesArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}