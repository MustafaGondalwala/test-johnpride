<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\City;
use App\Helpers\CustomHelper;

use DB;

class CityImport implements ToCollection, WithHeadingRow {

    public function collection(Collection $rows){

    	//prd($rows);

    	if(!empty($rows) && $rows->count() > 0){

    		//$fieldArr = $productModel->getFillable();

    		//pr($fieldArr);

    		$total = $rows->count();

    		$inserted = 0;
    		$updated = 0;

    		foreach ($rows as $row) {
    			//prd($row->toArray());

    			$state_name = (isset($row['state']))?$row['state']:'';
                $city_name = (isset($row['city']))?$row['city']:'';

                $state_name = strtolower($state_name);
                //$city_name = strtolower($city_name);

                $state_id = 0;
                $city_id = 0;

                if(!empty($state_name)){
                    $state = DB::table('states')->select('id')->whereRaw("LOWER(`name`)='$state_name'")->first();

                    $state_id = (isset($state->id))?$state->id:0;
                }

                /*if(!empty($city_name)){
                    $city = DB::table('cities')->select('id')->whereRaw("LOWER(`name`)='$city_name'")->first();

                    $city_id = (isset($city->id))?$city->id:0;
                }*/

                if(!empty($state_name) && !empty($city_name) ){

                    $dbData = [];
                    $dbData['name'] = $city_name;
                    $dbData['state_id'] = $state_id;
                    $dbData['state'] = strtolower($state_name);

                    /*$where = [];
                    $where['state_id'] = $state_id;
                    $where['city_id'] = $city_id;
                    $where['pin'] = $pincode;

                    $existData = City::where($where)->select(['id', 'state_id', 'city_id', 'pin', 'status', 'created_at', 'updated_at'])->first();*/

                    /*if(!empty($existData)){
                        $existData->pin = $pincode;
                        $existData->cod_amount = $cod_amount;
                        $existData->zone = $zone;
                        $existData->field1 = $field1;
                        $existData->field2 = $field2;
                        $existData->field3 = $field3;
                        $existData->cod_available = $cod_available;
                        $existData->status = $status;

                        $existData->save();

                        $updated++;
                    }*/
                    //else{
                        City::insert($dbData);

                        $inserted++;
                    //}


                }
    		}

            $scc_msg = '<div class="alert alert-success"><a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';

            $scc_msg .= '<strong>Pincode(s) import summary : </strong><br>';
            $scc_msg .= 'Total Records : '.$total.'<br>';
            $scc_msg .= 'New Inserted Record(s) : '.$inserted.'<br>';
            $scc_msg .= 'Updated Record(s) : '.$updated;

            $scc_msg .= '</div>';

            session()->flash('scc_msg', $scc_msg);
    	}
    }

    /* end of class */
}