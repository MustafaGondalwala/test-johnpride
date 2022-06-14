<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model{

    protected $table = 'testimonials';

    protected $guarded = ['id'];    

    protected $fillable = [
    	'name',
    	'subject',
    	'description',
    	'date_on',
    	'featured',
    	'status'
    ];

    
}