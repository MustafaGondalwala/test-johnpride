<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model{
    
    protected $table = 'product_attributes';

    //protected $guarded = ['id'];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'label',
        'value',
    ];
}