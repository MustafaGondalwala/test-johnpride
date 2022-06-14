<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Admin - Settings
     * URL: /admin/settings
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function index()
    {
        $settings = Setting::orderBy('created_at', 'desc')->get();

        return view('admin.settings.index', compact('settings'));
    }*/

    public function index(Request $request){
        
        $settingRow = '';

        $method = $request->method();
        //prd($method);
        $setting_id = $request->setting_id;

        $admin_id = auth('admin')->user()->id;

        //prd($admin_id);

        if(is_numeric($setting_id) && $setting_id > 0)
        {
            $settingRow = Setting::find($setting_id);
        }

        if($method == 'POST' || $method == 'post')
        {
            //prd($request->all());            

                $rules = array(
                    'title' => 'required',
                    'name' => 'required|unique:website_settings,name,'.$setting_id,
                    'value' => 'required'
                    );

                $validator = $this->validate($request, $rules);

                $title = $request->input('title');
                $name = $request->input('name');
                $value = $request->input('value');

                $settings_data['display_name'] = $title;
                $settings_data['name'] = $name;
                $settings_data['value'] = $value;
                $settings_data['old_value'] = $value;

                $save_date = date('Y-m-d H:i:s');

                if(is_numeric($setting_id) && $setting_id > 0)
                {
                    $settings_data['display_name'] = $title;
                    $settings_data['name'] = $name;
                    $settings_data['value'] = $value;
                    $settings_data['old_value'] = $settingRow->value;
                    $settings_data['updated_at'] = $save_date;

                    $saved_data = Setting::where('id', $setting_id)->update($settings_data);

                    $success_msg = 'Setting has been updated';

                    $activity_description = 'Update Setting';
                    $module_name = 'Update Setting';
                }
                else
                {
                    $saved_data = Setting::create($settings_data);

                    $setting_id = (isset($saved_data->id))?$saved_data->id:'';

                    $success_msg = 'Setting has been added successfully';

                    $activity_description = 'Add Setting';
                    $module_name = 'Add Setting';
                }

                if(!empty($saved_data))
                {

                    session()->flash('alert-success', $success_msg);
                    
                    return redirect('admin/settings');
                };
        }



        $data = [];

        $settings = Setting::where('status',1)->orderBy('id', 'asc')->get();        

        $data['settings'] = $settings;
        $data['settingRow'] = $settingRow;

        return view('admin.settings.index', $data);
    }

    /**
     * Admin - Update Setting
     * URL: /admin/settings/{setting} (PUT)
     *
     * @param Request $request
     * @param $setting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $setting)
    {
        $data = $request->all();

        $setting->state = isset($data['state']);

        $result = $setting->save();

        if ($result) {
            return redirect(route('admin.settings.index'))->with('alert-success', 'The setting has been updated successfully.');
        } else {
            return back()->with('alert-danger', 'The setting cannot be updated, please try again or contact the administrator.');
        }
    }
}