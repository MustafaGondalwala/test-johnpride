<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Orderstatus extends Model
{
    protected $table = 'order_status';

    protected $guarded = ['status_id'];

    public $timestamps = true;

    protected $fillable = [];

    public $primaryKey= 'status_id';
}