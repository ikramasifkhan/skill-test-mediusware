<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'title', 'description'
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'product_variants');
    }

    public function product_variants(){
        return $this->hasMany(ProductVariant::class, 'variant_id', 'id');
    }

}
