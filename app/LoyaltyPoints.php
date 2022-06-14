<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoints extends Model{

    protected $table = 'loyalty_points_to_customer';
    protected $guarded = ['id'];
    protected $fillable = [];
    public $timestamps = false;
    
}