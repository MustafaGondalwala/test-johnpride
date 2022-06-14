<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Product;
use App\Category;
use App\Helpers\CustomHelper;

use DB;

class ProductImport implements ToCollection, WithHeadingRow {

    public function collection(Collection $rows){

    	//prd($rows);

        if(!empty($rows) && $rows->count() > 0){

            $productModel = new Product;

            $fieldArr = $productModel->getFillable();

            $total = $rows->count();

            $inserted = 0;
            $updated = 0;

            foreach ($rows as $row) {

                $otherImages=[];

                $id = (isset($row['id']))?$row['id']:'';
                $sku = (isset($row['sku']))?$row['sku']:'';
                $product_name = (isset($row['name']))?$row['name']:'';
                $sizes = (isset($row['sizes']))?$row['sizes']:'';

               /* if(!empty($sku))
                {
                    $productBySku = Product::where('sku',$sku)->first();
                    if(!empty($productBySku) && count($productBySku) > 0)
                    {
                         $id = $productBySku->id;
                    }
                    //echo $id;
                    //prd($productBySku->toArray());
                }*/
                //die;
                $sizesArr = (!empty($sizes))?(explode(',',$sizes)):[];

                if(!empty($product_name)){

                    $category_id = (isset($row['category_id']))?$row['category_id']:'';
                    $main_image = (isset($row['main_image']))?$row['main_image']:'';
                    $reverse_image = (isset($row['reverse_image']))?$row['reverse_image']:'';
                    $otherImages[] = (isset($row['other_image1']))?$row['other_image1']:'';
                    $otherImages[] = (isset($row['other_image2']))?$row['other_image2']:'';
                    $otherImages[] = (isset($row['other_image3']))?$row['other_image3']:'';
                    $product = new Product;

                    $isExist = false;

                    if( (is_numeric($id) && $id > 0) || !empty($sku) ){
                        $exist = '';

                        if(!empty($sku) ){
                            $exist = Product::where('sku',$sku)->first();
                        }
                        elseif(is_numeric($id) && $id > 0 ){
                            $exist = Product::find($id);
                        }
                        if(isset($exist->id) && count($exist) > 0){
                            $product = $exist;
                            $isExist = true;
                        }
                    }

                    $slug = '';

                    if($isExist){
                        $slug = CustomHelper::GetSlug('products', 'id', $id, $product_name);
                    }
                    else{
                        $slug = CustomHelper::GetSlug('products', 'id', '', $product_name);
                    }

                    foreach($fieldArr as $field){
                        if(isset($row[$field])){
                            $product->$field = $row[$field];
                        }
                    }

                    $product->slug = $slug;

                    $isSaved = $product->save();

                    $product_id = 0;

                    if($isSaved){
                        $product_id = $product->id;

                        // if(is_numeric($id) && $id > 0){
                        if($isExist){
                            $updated++;
                        }
                        else{
                            $inserted++;
                        }
                    }

                    if(is_numeric($product_id) && $product_id > 0 && is_numeric($category_id) && $category_id > 0){

                        if(!empty($sizesArr) && count($sizesArr) > 0){
                            $this->saveInventory($sizesArr, $product_id, $sku);
                        }

                        $category = Category::find($category_id);

                        if(!empty($category) && $category->count() > 0){

                            $subParentCategory = $category->parent;

                            $subParentCategoryId = (isset($subParentCategory->id))?$subParentCategory->id:0;
                            $parentCategory = (isset($subParentCategory->parent))?$subParentCategory->parent:'';
                            $parentCategorId = (isset($parentCategory->id))?$parentCategory->id:0;

                            $categoryAttributes = (isset($parentCategory->categoryAttributes))?$parentCategory->categoryAttributes:'';

                            $this->saveCategories($parentCategorId, $subParentCategoryId, $category_id, $product_id);

                            $this->saveAttributes($categoryAttributes, $row, $product_id);

                        }
                    }

                if(is_numeric($product_id) && $product_id > 0){
                    $this->saveImages($main_image ,$reverse_image, array_filter($otherImages), $product_id);
                }                    

                }
            }



            $scc_msg = '<div class="alert alert-success"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';

            $scc_msg .= '<strong>Products upload summary : </strong><br>';
            $scc_msg .= 'Total Records : '.$total.'<br>';
            $scc_msg .= 'New Inserted Record(s) : '.$inserted.'<br>';
            $scc_msg .= 'Updated Record(s) : '.$updated;

            $scc_msg .= '</div>';

            session()->flash('scc_msg', $scc_msg);
        }
    }


    private function saveInventory($sizesArr=array(), $product_id=0, $productSku=''){

        //prd($sizesArr);

        if(!empty($sizesArr) && count($sizesArr) > 0 && !empty($productSku)){
            foreach($sizesArr as $size){

                $size = trim($size);

                if(!empty($size)){

                    $Size = DB::table('sizes')->where(['name'=>$size])->first();

                    //prd($Size);

                    if(isset($Size->id) && $Size->id > 0){

                        $size_id = $Size->id;
                        $size_name = $Size->name;

                        $sku = $productSku.$size;

                        $inventoryData = [];
                        $inventoryData['sku'] = $sku;
                        $inventoryData['product_id'] = $product_id;
                        $inventoryData['size_id'] = $size_id;
                        $inventoryData['size_name'] = $size_name;

                        $exist = DB::table('product_inventory')->select('id','sku','product_id','size_id')->where(['product_id'=>$product_id, 'sku'=>$sku])->first();

                        if(isset($exist->id) && $exist->id > 0){
                            DB::table('product_inventory')->where(['id'=>$exist->id, 'sku'=>$sku])->update($inventoryData);
                        }
                        else{
                            DB::table('product_inventory')->insert($inventoryData);
                        }
                    }
                }

            }
        }
        
    }

    private function saveCategories($p1_cat, $p2_cat, $category_id, $product_id){

        if(is_numeric($product_id) && $product_id > 0){

            $category_data = [];

            if(!empty($p1_cat) && !empty($p2_cat) && !empty($category_id)){

                DB::table('product_categories')->where('product_id', $product_id)->delete();

                $category_data['product_id'] = $product_id;
                $category_data['p1_cat'] = $p1_cat;
                $category_data['p2_cat'] = $p2_cat;
                $category_data['category_id'] = $category_id;

                DB::table('product_categories')->insert($category_data);

            } 
        }
    }

    private function saveImages($mainImage, $reverseImage, $imgArr, $product_id){

        if(is_numeric($product_id) && $product_id > 0){
            $update_data=[];
            if(!empty($mainImage)){
                $image_data['product_id'] = $product_id;
                $image_data['image'] = $mainImage;
                $image_data['is_default'] = 1;
                $image_data['is_reverse'] = 0;
                $image_data['created_at'] = date('Y-m-d h:m:s');
                $image_data['updated_at'] = date('Y-m-d h:m:s');
                $update_data[]=$image_data;
                //DB::table('product_images')->insert($image_data);
            }

            if(!empty($reverseImage)){
                $rev_data['product_id'] = $product_id;
                $rev_data['image'] = $reverseImage;
                $rev_data['is_default'] = 0;
                $rev_data['is_reverse'] = 1;
                $rev_data['created_at'] = date('Y-m-d h:m:s');
                $rev_data['updated_at'] = date('Y-m-d h:m:s');
                //DB::table('product_images')->insert($rev_data);

                $update_data[]=$rev_data;
            }

            if(!empty($imgArr)){
                foreach ($imgArr as $key => $img) {
                    $img_data['product_id'] = $product_id;
                    $img_data['image'] = $img;
                    $img_data['is_default'] = 0;
                    $img_data['is_reverse'] = 0;
                    $img_data['created_at'] = date('Y-m-d h:m:s');
                    $img_data['updated_at'] = date('Y-m-d h:m:s');
                    //DB::table('product_images')->insert($img_data);

                    $update_data[]=$img_data;
                }
            }

            if(!empty($update_data) && count($update_data)){

                DB::table('product_images')->where('product_id', $product_id)->delete();
                DB::table('product_images')->insert($update_data);
            }

            
        }
    }    

    private function saveAttributes($categoryAttributes, $row, $product_id){

    	$is_inserted = '';

    	if(!empty($categoryAttributes)){

    		$attrData = [];

    		$attrCount = 1;

    		foreach($categoryAttributes as $ca){


                /*$attrIndex = $ca->label;

                if(isset($row[$attrIndex]) && !empty($row[$attrIndex])){
                  $attrData[] = array(
                      'product_id' => $product_id,
                      'label' => $attrIndex,
                      'value' => $row[$attrIndex],
                  );
                }*/


    			$attrIndex = 'attribute_'.$attrCount;

    			if(isset($row[$attrIndex]) && !empty($row[$attrIndex])){
    				$attrData[] = array(
    					'product_id' => $product_id,
    					'label' => $ca->label,
    					'value' => $row[$attrIndex],
    				);
    			}

    			$attrCount++;
    		}

    		//pr($attrData);

    		if(!empty($attrData) && count($attrData) > 0){

    			DB::table('product_attributes')->where('product_id', $product_id)->delete();

    			$is_inserted = DB::table('product_attributes')->insert($attrData);
    		}
    	}

    	return $is_inserted;

    }

    /* end of class */
}