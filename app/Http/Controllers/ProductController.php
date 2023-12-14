<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    protected $productModel;

    public function __construct(){
        $this->productModel = new Product;
    }
    public function getAllProducts()
    {
        $productList = $this->productModel->get();
        if ($productList->isNotEmpty())
            return $productList;
        else
            return null;
    }

    public function getProductById($id)
    {
        $product = $this->productModel->where('id',$id)->get();
        if ($product->isNotEmpty())
            return $product;
        else
            return null;
    }
}
