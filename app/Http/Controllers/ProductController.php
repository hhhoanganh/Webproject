
<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Models\Response\ProductResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    protected static $size = 10;

    public function getAllProduct(Request $request)
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
}