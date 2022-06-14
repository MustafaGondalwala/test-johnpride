<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{

    protected $table = 'products';

    protected $guarded = ['id'];

    protected $fillable = [

        'user_id',
        'name',
        'slug',
        'sku',
        'specifications',
        'description',
        'brand_id',
        'gender',
        'color_name',
        'color_id',
        'size_chart_id',
        'price',
        'sale_price',
        'discount',
        'stock',
        'gst',
        'weight',
        'video',
        'delivery_duration',
        'min_order_qty',
        'sort_order',
        'stamp',
        'featured',
        'trending',
        'popularity',
        'status',
        'is_approved',
        'total_sale_counter',
        'style_id',
        'manufacturer',
        'country_origin',
        'net_qty',
        'business_unit',
        'product_type',
        'standard_size',
        'hsn',
        'age_group',
        'brand_color',
        'base_color',
        'fashion_type',
        'prod_usage',
        'year',
        'season',
        'tags',
        'across_shoulder',
        'bust',
        'chest',
        'front_length',
        'to_fit_bust',
        'sleeve_length',
        'to_fit_waist',
        'waist',

    ];
    
    public function productCategories(){
        return $this->belongsToMany('App\Category', 'product_categories', 'product_id')->withPivot('p1_cat', 'p2_cat');
    }
    
    public function productP1Categories(){
        return $this->belongsToMany('App\Category', 'product_categories', 'product_id', 'p1_cat')->withPivot('p1_cat', 'p2_cat');
    }
    
    public function productP2Categories(){
        return $this->belongsToMany('App\Category', 'product_categories', 'product_id', 'p2_cat')->withPivot('p1_cat', 'p2_cat');
    }
    
    public function productInventory(){
        return $this->hasMany('App\ProductInventory', 'product_id');
    }

    public function productInventorySize(){
        return $this->belongsToMany('App\Size', 'product_inventory', 'product_id')->withPivot('stock');
    }

    public function productBrand(){
        return $this->belongsTo('App\Brand', 'brand_id');
    }
    
    public function productImages(){
        return $this->hasMany('App\ProductImage', 'product_id');
    }
    
    public function defaultImage(){
        return $this->hasOne('App\ProductImage', 'product_id')->where('is_default', 1);
    }

    public function reverseImage(){
        return $this->hasOne('App\ProductImage', 'product_id')->where('is_reverse', 1);
    }
    
    public function productAttributes(){
        return $this->hasMany('App\ProductAttribute', 'product_id');
    }
    
    public function productSizeChart(){
        return $this->belongsTo('App\SizeChart', 'size_chart_id');
    }

    
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    
    public function color(){
        return $this->belongsTo('App\ColorMaster', 'color_id');
    }

    
    public function orderedProducts(){
        return $this->hasMany('App\OrderItem', 'product_id');
    }




}