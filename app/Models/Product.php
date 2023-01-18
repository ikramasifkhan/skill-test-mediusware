<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function variants(){
        return $this->belongsToMany(Variant::class, 'product_variants');
    }

    public function product_variant_prices(){
        return $this->hasMany(ProductVariantPrice::class, 'product_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

}
