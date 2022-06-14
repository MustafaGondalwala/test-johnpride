<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PincodeExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($pincodeArr, $headings){
        //$this->request = $request;
        $this->pincodeArr = $pincodeArr;
        //$this->productQuery = $productQuery;
        $this->headings = $headings;
    }
    
    /*public function query(){
    	$productQuery = $this->productQuery;
        return $productQuery;
    }*/

    public function array(): array {

        $pincodeArr = $this->pincodeArr;
        return $pincodeArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}