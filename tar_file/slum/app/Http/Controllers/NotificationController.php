<?php

namespace App\Http\Controllers;

use Mail;

use App\User;

use App\UsersCart;
use App\UsersCartInventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use Validator;

use App\Libraries\CurlFunctions;

use App\Http\Requests;

use DB;

use App\Libraries\Pusher\Pusher;


class NotificationController extends Controller {

    public function __construct()
    {
        //$this->middleware('guest');
    }

    /**
     * Homepage
     * URL: /
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

     public function pusher(Request $request){
       $notification = $request->all();

       /*$notification = array(
        'time_ms' => '1509454827365',
        'events' => array(
            '0' => array(
                'channel' => 'private-158',
                //'name' => 'channel_vacated'
                'name' => 'channel_occupied'
                )
            )
        );*/

       $event = (isset($notification['events']))?$notification['events']:'';;

       $options = array(
        'cluster' => config('custom.pusher.cluster'),
        'encrypted' => config('custom.pusher.encrypted')
        );

       $pusher = new Pusher(
        config('custom.pusher.key'),
        config('custom.pusher.secret'),
        config('custom.pusher.app_id'),
        $options
        );

       if(!empty($event) && count($event) > 0){
        foreach($event as $ev){
            $channel = $ev['channel'];
            $name = $ev['name'];

            if(strpos($channel, 'private') === 0){
                $channel_arr = explode('-', $channel);
                $user_id = (isset($channel_arr[1]))?$channel_arr[1]:0;
                $User = (is_numeric($user_id) && $user_id > 0)?getUsers($user_id):'';

                $status = '';

                if($name == 'channel_vacated'){
                    $status = 'Offline';
                }
                elseif($name == 'channel_occupied'){
                    $status = 'Online';
                }
                if(!empty($User) && count($User) > 0){
                    $data = ["status"=>$status, "user_id"=>$User->id];

                    if(isset($User->role_code)){
                        if($User->role_code == 'user'){                        
                            $pusher_resp = $pusher->trigger("private-".$User->agent_id, "online_status", $data);
                        }
                        elseif($User->role_code == 'agent'){
                            $UsersOfAgent = getUsersOfAgent($user_id);

                            if(count($UsersOfAgent) > 0){
                                foreach($UsersOfAgent as $UA){
                                    $pusher_resp = $pusher->trigger("private-".$UA->id, "online_status", $data);
                                }
                            }
                        }
                    }
                }
            }
            
        }
    }
       

       //prd();

       /*$email = 'vikas@ehostinguk.com';

       if(!empty($notification)){
        Mail::send('emails.pusher',
                [
                    'notification'  => $notification
                ],
                function($message) use ($email)
                {
                    $message->replyTo($email)
                        ->to($email)
                        ->subject('Pusher Notification');
                });
       }*/

       
     }




/* End of Controller */
}
