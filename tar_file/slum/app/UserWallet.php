<?php
namespace App;
use Illuminate\Database\Eloquent\Model;


class UserWallet extends Model{

    protected $table = 'users_wallet';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'credit_amount',
        'debit_amount',
        'balance',
        'description',
        'created',
        'updated',
    ];

    public $timestamps = false;




    /* end of model */
}