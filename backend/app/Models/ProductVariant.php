<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
    'product_id','shopify_id','sku','price','compare_at_price','position','option1','option2','option3','taxable','requires_shipping','inventory_quantity'
    ];
    public function product(){ return $this->belongsTo(Product::class); }
}
