<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model{
    
    protected $table = 'category_attributes';

    //protected $guarded = ['id'];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'label',
        'sort_order',
    ];
}