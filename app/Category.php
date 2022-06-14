<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    
    protected $table = 'categories';

    protected $guarded = ['id'];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'banner_image',
        'mob_banner_image',
        'sort_order',
        'status',
        'featured',
        'meta_title',
        'meta_keyword',
        'meta_description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'menu' => 'boolean'
    ];

    /**
     * Products in a Category (Category - Product: One to Many)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryImages() {
        return $this->hasMany('App\CategoryImage', 'category_id');
    }

    public function defaultImage(){
        return $this->hasOne('App\CategoryImage', 'category_id')->where('is_default', 1);
    }

    public function categoryAttributes() {
        return $this->hasMany('App\CategoryAttribute', 'category_id');
    }

    public function categoryProducts(){
        return $this->belongsToMany('App\Product', 'product_categories', 'category_id')->withPivot('p1_cat', 'p2_cat');
    }

    public function p1CategoryProducts(){
        return $this->belongsToMany('App\Product', 'product_categories', 'p1_cat')->withPivot('p1_cat', 'p2_cat');
    }

    public function p2CategoryProducts(){
        return $this->belongsToMany('App\Product', 'product_categories', 'p2_cat')->withPivot('p1_cat', 'p2_cat');
    }

    public function products() {
        return $this->hasMany('App\Product');
    }

    /**
     * Sub-Categories of this Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(){
        return $this->hasMany('App\Category', 'parent_id');
    }

    /**
     * Parent Category of this Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function countProducts(){
        return $this->belongsToMany('App\Product', 'product_categories', 'product_id')->count();
    }


}