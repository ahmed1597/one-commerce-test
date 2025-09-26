<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
    'shopify_id','title','body_html','handle','vendor','product_type','updated_at_shopify','is_archived'
    ];
    protected $casts = [
    'updated_at_shopify' => 'datetime',
    'is_archived' => 'boolean',
    ];
    public function variants(){ return $this->hasMany(ProductVariant::class); }
    public function images(){ return $this->hasMany(ProductImage::class); }
}
