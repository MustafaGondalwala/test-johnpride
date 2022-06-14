<?php

namespace App\Http\Controllers\Admin;

use App\Pincode;
use App\City;
use App\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\Helpers\CustomHelper;

use App\Exports\PincodeExport;
use App\Imports\PincodeImport;

use Maatwebsite\Excel\Facades\Excel;

class PincodeController extends Controller{

    private $limit;
    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){
        
        $limit = $this->limit;

        $pincodeRow = [];

        $method = $request->method();

        $id = $request->id;

        if(is_numeric($id) && $id > 0){
            $pincodeRow = Pincode::find($id);

            if(!isset($pincodeRow->id)){
                return back();
            }
        }

        $export_xls = (isset($request->export_xls))?$request->export_xls:'';

        if($method == 'POST' || $method == 'post'){
            return $this->save($request, $id);
        }

        $data = [];
        $pincodes = Pincode::orderBy('state_id', 'asc')->paginate($limit);

        if(!empty($export_xls) && ($export_xls == 1 || $export_xls == '1') ){
            $pincodes_export = Pincode::orderBy('id', 'asc')->get();
            return $this->export($pincodes_export);
        }

        $state = State::orderBy('name', 'asc')->get();
        $city = City::orderBy('name', 'asc')->get();

        $data['pincodes'] = $pincodes;
        $data['pincodeRow'] = $pincodeRow;
        $data['city'] = $city;
        $data['state'] = $state;

        return view('admin.pincodes.index', $data);
    }



    private function export($pincodes){
//prd($pincodes);
        ini_set('max_execution_time', 180); //3 minutes

        $exportArr = [];

        if(!empty($pincodes) && $pincodes->count() > 0){
            foreach($pincodes as $pincode){

                $stateName = '';
                $cityName = '';

                if(!empty($pincode->pincodeState) && count($pincode->pincodeState) > 0){
                    $stateName = $pincode->pincodeState->name;
                }

                if(!empty($pincode->pincodeCity) && count($pincode->pincodeCity) > 0){
                    $cityName = $pincode->pincodeCity->name;
                }

                $pincodeArr = [];

                $pincodeArr['state'] = $stateName;
                $pincodeArr['city'] = $cityName;
                $pincodeArr['pincode'] = $pincode->pin;
                $pincodeArr['cod_amount'] = $pincode->cod_amount;
                $pincodeArr['zone'] = $pincode->zone;
                $pincodeArr['field1'] = $pincode->field1;
                $pincodeArr['field2'] = $pincode->field2;
                $pincodeArr['field3'] = $pincode->field3;
                $pincodeArr['cod_available'] = $pincode->cod_available;
                $pincodeArr['status'] = CustomHelper::getStatusStr($pincode->status);

                $exportArr[] = $pincodeArr;
            }
        }

        $fieldNames = array_keys($exportArr[0]);

        //prd($filedNames);

        $fileName = 'pincodes_'.date('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new PincodeExport($exportArr, $fieldNames), $fileName);

    }



    private function export30oct2019($pincodes){

        $fileName = 'pincodes_'.date('Y-m-d-H-i-s').'.xls';

        $viewData = [];
        $viewData['pincodes'] = $pincodes;

        //$viewHtml = view('admin.pincodes._export', $viewData)->render();

        //echo $viewHtml; die;

        header('Content-Type: application/vnd.ms-excel');
        //tell browser what's the file name
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        //no cache
        header('Cache-Control: max-age=0');        

        return view('admin.pincodes._export', $viewData);
    }

    private function save($request, $id){

        //prd($request->toArray());

        $rules = [];
        $rules['state_id'] = 'required';
        $rules['city_id'] = 'required';
        $rules['pin'] = 'required|numeric';
        $rules['status'] = 'required';

        $validator = $this->validate($request, $rules);

        $pincode = new Pincode;

        if(is_numeric($id) && $id > 0){
            $exist = Pincode::find($id);

            if(isset($exist->id) && $exist->id == $id){
                $pincode = $exist;
            }
            $success_msg = 'Pincode has been updated';
            $activity_description = 'Update Pincode';
            $module_name = 'Update Pincode';
        }

        $pincode->state_id = $request->state_id;
        $pincode->city_id = $request->city_id;
        $pincode->pin = $request->pin;
        $pincode->cod_amount = ($request->cod_amount)?$request->cod_amount:0;
        $pincode->zone = $request->zone;
        $pincode->field1 = $request->field1;
        $pincode->field2 = $request->field2;
        $pincode->field3 = $request->field3;
        $pincode->status = (isset($request->status))?$request->status:0;
        $pincode->cod_available = (isset($request->cod_available))?$request->cod_available:0;

        $isSaved = $pincode->save();

        if($isSaved){
            return redirect('admin/pincodes')->with('alert-success', 'Pincode has been saved successfully.');
        }
        else{
            return redirect('admin/pincodes')->with('alert-success', 'something went wrong, please try again.');
        }

    }

    public function delete(Request $request){
        $method = $request->method();
        
        $is_deleted = 0;

        if($method == 'POST'){
            $id = (isset($request->id))?$request->id:0;

            if(is_numeric($id) && $id > 0){
                $pincode = Pincode::find($id);
                if(!empty($pincode) && count($pincode) > 0){
                    $is_deleted = $pincode->delete();
                }
            }
        }

        if($is_deleted){
            return redirect(url('admin/pincodes'))->with('alert-success', 'The Pincode has been deleted successfully.');
        }
        else{
            return redirect(url('admin.pincodes'))->with('alert-danger', 'The Pincode cannot be deleted, please try again or contact the administrator.');
        }

    }


    public function import(Request $request){
        $data = [];

        ini_set('max_execution_time', 180); //3 minutes

        if($request->method() == 'POST' || $request->method() == 'post'){

            $extArr = ['csv', 'xls', 'xlsx'];

            //prd($request->toArray());

            $path = $request->file('upload')->getRealPath();
            $file = $request->file('upload');

            $file_ext = strtolower($file->getClientOriginalExtension());

            //prd($file->getClientOriginalExtension());

            $rules = [];
            $messages = [];

            //$rules['upload'] = 'required|mimes:csv';
            $rules['upload'] = 'required';

            //$this->validate($request, $rules, $messages);

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($file_ext, $extArr) {
                if ( !in_array($file_ext, $extArr) ) {
                    $validator->errors()->add('upload', 'please upload valid csv/xls/xlsx file.');
                }
            });

            if($validator->fails()){
                return back()->withErrors($validator->errors());
            }

            //prd($request->toArray());
            
            $file_name = $request->file_name;
            $column = $request->column;

            $result = '';

            $result = Excel::import(new PincodeImport, $file);

            if($result){
                return redirect('admin/pincodes/import');
            }
            else{
                return redirect('admin/pincodes/import')->with('err_msg', 'something went wrong, please try again.');
            }
            

        }
        return view('admin.pincodes.import', $data);
    }


    /* end of controller */
}