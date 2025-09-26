<?php
namespace App\GraphQL\Queries;

use App\Models\Product;

class ProductQuery
{
    public function list($_, array $args)
    {
        $q = Product::with(['variants','images'])->where('is_archived', false);
        if (!empty($args['search'])) { $q->where('title','like','%'.$args['search'].'%'); }
        return $q->get();
    }


    public function show($_, array $args)
    {
        return Product::with(['variants','images'])->findOrFail($args['id']);
    }
}