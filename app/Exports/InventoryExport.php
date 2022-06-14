<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromArray, WithHeadings {

    use Exportable;

    public function __construct($inventoryArr, $headings){
        //$this->request = $request;
        $this->inventoryArr = $inventoryArr;
        //$this->productQuery = $productQuery;
        $this->headings = $headings;
    }
    
    /*public function query(){
    	$productQuery = $this->productQuery;
        return $productQuery;
    }*/

    public function array(): array {

        $inventoryArr = $this->inventoryArr;
        return $inventoryArr;
    }

    public function headings(): array {
        $headings = $this->headings;

        return $headings;
    }
}