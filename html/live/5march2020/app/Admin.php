<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class Admin extends Authenticatable{

    use Notifiable, EntrustUserTrait;

    protected $guard = 'admin';

    //protected $table = 'admins';

    protected $guarded = ['id'];    

    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'password',
        'phone',
        'address'
    ];
}