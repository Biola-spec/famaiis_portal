<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $inStockOnly = false;
    public $sortBy = 'newest';
    public $categories = [];
    public $totalProducts = 0;

    public function mount()
    {
        $this->categories = Product::distinct()->pluck('category')->filter()->toArray();
        $this->totalProducts = Product::active()->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingInStockOnly()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($productId);
        
        if ($product->stock_quantity <= 0) {
            $this->dispatch('notify', type: 'error', message: 'Out of stock!');
            return;
        }

        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', type: 'success', message: 'Added to cart!');
    }

    public function render()
    {
        $products = Product::active()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->when($this->inStockOnly, fn($q) => $q->where('stock_quantity', '>', 0))
            ->when($this->sortBy === 'price_low', fn($q) => $q->orderBy('price', 'asc'))
            ->when($this->sortBy === 'price_high', fn($q) => $q->orderBy('price', 'desc'))
            ->when($this->sortBy === 'name', fn($q) => $q->orderBy('name', 'asc'))
            ->when(!in_array($this->sortBy, ['price_low', 'price_high', 'name']), fn($q) => $q->latest())
            ->paginate(12);

        return view('livewire.shop.product-list', [
            'products' => $products
        ]);
    }
}
