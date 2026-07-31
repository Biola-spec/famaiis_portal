<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Cart;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cartUpdated')]
    public function updateCount()
    {
        if (auth()->check()) {
            $this->count = Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        return view('livewire.shop.cart-counter');
    }
}
