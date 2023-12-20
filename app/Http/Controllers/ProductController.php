<?php

namespace App\Http\Controllers;

use App\Models\Product\Images;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    //
    protected static int $size = 10;

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
        $products = Product::orderBy($sortBy, $sortDirection)->paginate(self::$size,['*'], 'page', $page);
////        $products->getCollection()->map(function ($product) {
////            $product->thumbnail = 'public/storage/data/' . $product->id . '/' . $product->thumbnail;
//            return $product;
//        });
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

    public function getProduct(String $id)
    {
        $product = Product::where("id",$id)->with('reviews')->get();
//        $product->thumbnail = 'public/storage/data/'.$product->id.'/'.$product->thumnail;
        return $this->sendSuccess($product);
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Create the product
        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'thumbnail' => ''
        ]);
//        dd($images);
        $thumbnailName = $request->file('thumbnail')->getClientOriginalName();
        $uploadedDir = '/data/' . $product->id;
        $thumbnailPath = $this->saveFile($uploadedDir, $thumbnailName, $request->file('thumbnail'));
        if ($thumbnailPath) {
            $product->thumbnail = $thumbnailName;
            $product->save();
        }
        return $this->sendSuccess($product,null,"Add product success");
    }

    public static function saveFile($uploadDir, $fileName, UploadedFile $uploadedFile)
    {
        // Ensure the directory exists or create it
        Storage::makeDirectory($uploadDir);

        // Store the file in the specified directory
        $path = $uploadedFile->storeAs($uploadDir, $fileName);

        return $path;
    }
    function searchProduct(Request $request)
    {
        $products = [];
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', 'id'); // Default to sorting by 'id'
        $sortDirection = $request->input('sortDirection', 'asc');
        if($request->has('q')){
            $search = $request->q;
            $products =Product::select("id", "name")
                ->where('name', 'LIKE', "%$search%")
                ->get()->orderBy($sortBy, $sortDirection)->paginate(self::$size,['*'], 'page', $page);;
        }
        return $this->sendSuccess($products);
    }


}
