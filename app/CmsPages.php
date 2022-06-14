<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmsPages extends Model
{
    protected $table = 'cms_pages';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'slug',
        'title',
        'heading',
        'content',
        'old_content',
        'default_content',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'status',
    ];
}