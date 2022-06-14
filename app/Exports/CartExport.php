<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Maatwebsite\Excel\Events\BeforeSheet;

class CartExport implements FromArray, WithHeadings ,ShouldAutoSize, WithEvents, WithColumnFormatting {

    use Exportable;
    private $sheetHeading;

    public function __construct($careerArr, $headings, $sheetHeading){
        //$this->request = $request;
        $this->careerArr = $careerArr;
        //$this->productQuery = $productQuery;
        $this->headings = $headings;

        $this->sheetHeading = $sheetHeading;
    }
    
    public function array(): array {

        $careerArr = $this->careerArr;
        
        //prd($careerArr['parmArr']['title']);
        
        return $careerArr;
    }

    public function headings(): array {
        $headings = $this->headings;


        return $headings;
    }

    public function registerEvents(): array {
        return [
            beforeSheet::class    => function(beforeSheet $event1) {
            // All headers - set font size to 14
             /*$event1->sheet->appendRows(array(
                array(' ', $this->sheetHeading),
                array(' ', ' '),
                array(' ', ''),
            ), $event1);*/
             
             // All headers - set font size to 14
             //$cellRange = 'B1';
            
            $style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                )
            );

            /*$event1->sheet->getStyle('B1')->applyFromArray([
                'font' => [
                    'bold' => true
                ]
            ]);*/

            //$event1->sheet->getStyle("B1")->applyFromArray($style);

            //$event1->sheet->getDelegate()->getStyle('B1')->getFont()->setSize(12);

            //$event1->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);

            // Apply array of styles to B2:G8 cell range
             /*$styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => 'FF000000'],
                    ]
                ]
            ];
            $event1->sheet->getDelegate()->getStyle('B1')->applyFromArray($styleArray);
*/
            // Set A1:D4 range to wrap text in cells
            /*$event->sheet->getDelegate()->getStyle('A1:D4')
            ->getAlignment()->setWrapText(true);*/
         },

            
            //AfterSheet::class    => function(AfterSheet $event) {
            // All headers - set font size to 14
                //$cellRange = 'A4:H4'; 
                //$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);

            // Apply array of styles to B2:G8 cell range
                /*$styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ]
                    ]
                ];
                $event->sheet->getDelegate()->getStyle('A4:E4')->applyFromArray($styleArray);*/

                // Set first row to height 20
                //$event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(20);

                // Set A1:D4 range to wrap text in cells
                /*$event->sheet->getDelegate()->getStyle('A4:E4')
                ->getAlignment()->setWrapText(true);*/

                /*$event->sheet->getDelegate()->getStyle('E5:E20')
                ->getNumberFormat()
                ->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);*/
                
            //},
        ];
    }

   public function columnFormats(): array
    {
        return [
            'E5' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E6' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }


   /* public static function beforeSheet(BeforeSheet $event){
        $event->sheet->appendRows(array(
            array('test1', 'test2'),
            array('test3', 'test4'),
            //....
        ), $event);
    }*/

}