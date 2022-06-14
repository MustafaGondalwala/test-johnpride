<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($customersArr, $headings){
        //$this->request = $request;
        $this->customersArr = $customersArr;
        //$this->productQuery = $productQuery;
        $this->headings = $headings;
    }
/*
    public function query(){
    	$productQuery = $this->productQuery;
        return $productQuery;
    }*/

    public function array(): array {

        $customersArr = $this->customersArr;
        return $customersArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}