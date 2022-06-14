<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($productsArr, $headings){
        //$this->request = $request;
        $this->productsArr = $productsArr;
        //$this->productQuery = $productQuery;
        $this->headings = $headings;
    }
/*
    public function query(){
    	$productQuery = $this->productQuery;
        return $productQuery;
    }*/

    public function array(): array {

        $productsArr = $this->productsArr;
        return $productsArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}