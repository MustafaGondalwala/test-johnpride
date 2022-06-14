<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
class Country extends Model
{
    protected $table = 'countries';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'iso ',
        'name',
        'nicename '
    ];

}