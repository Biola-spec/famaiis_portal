<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Cart;
use App\Models\PaymentSetting;
use Livewire\Attributes\On;

class CartList extends Component
{
    public $cartItems = [];
    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    #[On('cartUpdated')]
    public function loadCart()
    {
        $this->cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();
        
        $this->total = $this->cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    }

    public function increment($itemId)
    {
        $item = Cart::findOrFail($itemId);
        if ($item->product->stock_quantity > $item->quantity) {
            $item->increment('quantity');
            $this->loadCart();
            $this->dispatch('cartUpdated');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Insufficient stock!');
        }
    }

    public function decrement($itemId)
    {
        $item = Cart::findOrFail($itemId);
        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function removeItem($itemId)
    {
        Cart::findOrFail($itemId)->delete();
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.shop.cart-list', [
            'paymentSetting' => PaymentSetting::firstOrCreate(
                ['id' => 1],
                [
                    'provider' => 'paystack',
                    'payment_url' => 'https://api.paystack.co',
                    'bank_transfer_enabled' => true,
                    'is_active' => true,
                ]
            ),
        ]);
    }
}
