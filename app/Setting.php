<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model{

    protected $table = 'website_settings';

    protected $guarded = ['id'];

    protected $fillable = [];

}