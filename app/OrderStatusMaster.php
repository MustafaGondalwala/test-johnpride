<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatusMaster extends Model{

    protected $table = 'order_status_master';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $fillable = [];
}