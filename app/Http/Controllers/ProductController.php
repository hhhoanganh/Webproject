<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Models\Response\ProductResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    protected static $size = 10;

    public function getAllProducts(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'sortBy' => 'nullable|string|in:name,price',
            'sortDirection' => 'nullable|in:asc,desc',
        ]);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', 'id'); // Default to sorting by 'id'
        $sortDirection = $request->input('sortDirection', 'asc');
        $products = Product::with('images')->orderBy($sortBy, $sortDirection)->paginate(self::$size,['*'], 'page', $page);
        $meta = ['current_page' => $products->currentPage(),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
                'total' => $products->total(),];

        return $this->sendSuccess(
            $products->items(),
            $meta
        );
    }

    public function getProduct(Request $request)
    {
        $product = Product::where("id",$request['id'])->get();
        return $this->sendSuccess($product);
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'thumbnail' => 'required',
            'file' => 'required|array|min:1',
            'file.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Create the product
        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
        ]);

        $images = $request->file('file');
        $thumbnail = $request->file('thumbnail');
        $uploadedImages = [];
        $uploadedDir = 'data/' . (string)$product->id;
        $product->thumbnail = $this->saveFile($uploadedDir,$product->id,$thumbnail);
        $product->save();
//        foreach ($images as $image) {
//            $path = $image->store('uploads', $product->id,'public');
//            $uploadedImages[] = $path;
//        }

        return response()->json(['images' => $product->thumbnail]);
    }
}
