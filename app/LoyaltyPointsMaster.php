<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPointsMaster extends Model{

    protected $table = 'loyalty_points_master';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $fillable = [];

    
}