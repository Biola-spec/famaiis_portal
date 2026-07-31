<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop.index');
    }

    public function show(Product $product)
    {
        if ($product->status !== 'active' || $product->stock_quantity <= 0) {
            return redirect()->route('shop.index')->with('error', 'Product not available.');
        }
        return view('shop.show', compact('product'));
    }

    public function cart()
    {
        return view('shop.cart');
    }
}
