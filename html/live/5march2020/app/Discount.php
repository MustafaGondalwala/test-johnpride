<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model{

    protected $table = 'discounts';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'type',
        'min_len',
        'max_len',
        'value',
        'page',
        'image',
        'link',
        'sort_order',
        'status'
    ];

    
}