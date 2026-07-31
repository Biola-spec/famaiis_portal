<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
class AddToCart extends Component
{
    public $productId;
    public $quantity = 1;
    public $productPrice;
    public $totalPrice;

    public function mount($productId)
    {
        $this->productId = $productId;
        $product = Product::findOrFail($productId);
        $this->productPrice = $product->price;
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->totalPrice = $this->productPrice * (int)$this->quantity;
    }

    public function increment()
    {
        $this->quantity = max(1, (int) $this->quantity) + 1;
        $this->calculateTotal();
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->calculateTotal();
        }
    }

    public function updatedQuantity()
    {
        $this->quantity = max(1, (int) $this->quantity);
        $this->calculateTotal();
    }

    public function addToCart()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($this->productId);
        
        if ($product->stock_quantity < $this->quantity) {
            $this->dispatch('notify', type: 'error', message: 'Insufficient stock!');
            return;
        }

        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('product_id', $this->productId)
                        ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $cartItem->quantity + $this->quantity]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $this->productId,
                'quantity' => $this->quantity,
            ]);
        }

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', type: 'success', message: 'Added to cart!');
    }

    public function buyNow()
    {
        $this->addToCart();
        return redirect()->route('shop.cart');
    }

    public function render()
    {
        $this->totalPrice = $this->productPrice * (int)$this->quantity;
        return view('livewire.shop.add-to-cart');
    }
}
