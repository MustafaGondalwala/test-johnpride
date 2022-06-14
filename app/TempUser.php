<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class TempUser extends Authenticatable{

    protected $table = 'temp_users';

    protected $guarded = ['id'];

    protected $fillable = [
        'phone',
        'otp',
    ];



}