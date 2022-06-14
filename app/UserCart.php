<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model{

    protected $table = 'user_cart';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}