<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\ProductInventory;
use App\Helpers\CustomHelper;

use DB;

class InventoryImport implements ToCollection, WithHeadingRow {

    public function collection(Collection $rows){

    	//prd($rows->toArray());

    	if(!empty($rows) && $rows->count() > 0){

    		//$fieldArr = $productModel->getFillable();

    		//pr($fieldArr);

    		$total = $rows->count();

    		$inserted = 0;
    		$updated = 0;

    		foreach ($rows as $row) {
    			//prd($row->toArray());

    			// $product_id = (isset($row['product_id']))?trim($row['product_id']):'';
       //          $product_name = (isset($row['product_name']))?trim($row['product_name']):'';
                $sku = (isset($row['sku']))?trim($row['sku']):'';
                $size_name = (isset($row['size']))?trim($row['size']):'';
                $stock = (isset($row['stock']))?trim($row['stock']):0;

                $product = '';

                if(!empty($sku) && !empty($size_name)){

                    $size = DB::table('sizes')->where('name', $size_name)->first();

                    $size_id = (isset($size->id))?$size->id:0;

                    // $productInventory = ProductInventory::where('sku', $sku)->first();

                    // if(is_numeric($product_id) && $product_id > 0){
                    //     $product = DB::table('products')->select(['id','name'])->where('id', $product_id)->first();
                    // }
                    // elseif(!empty($product_name)){
                    //     $product = DB::table('products')->select(['id','name'])->where('name', $product_name)->first();
                    // }
                    // else
                    // {
                        // $product = DB::table('products')->select(['id','name'])->where('sku', $sku)->first();

                   // }

                    $product = DB::table('products')->select(['id','name'])->where('sku', $sku)->first();

                    $product_id = (isset($product->id))?$product->id:0;

                    if(!empty($sku) && $size_id > 0){

                        $invWhere = [];
                        $invWhere['sku'] = $sku.'_'.$size_name;
                        //$invWhere['product_id'] = $product_id;
                        $invWhere['size_id'] = $size_id;
                        //prd($invWhere);

                        $productInventory = ProductInventory::where($invWhere)->first();

                        if(isset($productInventory->id)){
                            $productInventory->stock = $stock;

                             
                             if(!empty($size_name))
                             {
                                $productInventory->sku = $sku.'_'.$size_name;
                             }
                             else
                             {
                                $productInventory->sku = $sku;
                             }



                            $isSaved = $productInventory->save();

                            if($isSaved){
                                $updated++;
                            }
                        }else{
                            $dbData = [];
                            $dbData = $invWhere;
                            $dbData['product_id'] = $product_id;
                            
                            $dbData['sku'] = $sku;
                            if(!empty($size_name))
                            {
                                 $dbData['sku'] = $sku.'_'.$size_name;
                            }     


                            $dbData['size_name'] = $size_name;
                            $dbData['stock'] = $stock;

                            $isInserted = ProductInventory::insert($dbData);

                            if($isInserted){
                                $inserted++;
                            }
                        }
                    }

                }

    		}

            $scc_msg = '<div class="alert alert-success"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';

            $scc_msg .= '<strong>Product Inventory(s) import summary : </strong><br>';
            $scc_msg .= 'Total Records : '.$total.'<br>';
            $scc_msg .= 'New Inserted Record(s) : '.$inserted.'<br>';
            $scc_msg .= 'Updated Record(s) : '.$updated;

            $scc_msg .= '</div>';

            session()->flash('scc_msg', $scc_msg);

                //prd($product->toArray());
    	}
    }

    /* end of class */
}