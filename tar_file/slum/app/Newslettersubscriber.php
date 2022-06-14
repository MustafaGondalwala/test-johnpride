<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
class Newslettersubscriber extends Model
{
    protected $table = 'newsletter_subscriber';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [];


    


}