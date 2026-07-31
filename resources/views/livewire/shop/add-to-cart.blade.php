<div wire:key="add-to-cart-{{ $productId }}">
    <style>
        .atc-wrap input::-webkit-outer-spin-button,
        .atc-wrap input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .atc-wrap input[type=number] { -moz-appearance: textfield; }
    </style>

    <div class="atc-wrap">
        {{-- Total Price --}}
        <div style="margin-bottom: 20px;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 4px;">Total Price</div>
            <div style="font-size: 28px; font-weight: 800; color: #764ba2;">
                ₦{{ number_format($totalPrice, 2) }}
                <span wire:loading style="display: inline-block; width: 18px; height: 18px; border: 2px solid #e0e0e0; border-top-color: #764ba2; border-radius: 50%; animation: spin 0.6s linear infinite; vertical-align: middle; margin-left: 8px;"></span>
            </div>
        </div>

        {{-- Quantity Selector --}}
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <span style="font-size: 14px; font-weight: 600; color: #555;">Quantity:</span>
            <div style="display: flex; align-items: center;">
                <button type="button" wire:click="decrement" style="width: 40px; height: 40px; border: 2px solid #e8e8e8; border-radius: 10px 0 0 10px; background: #fff; font-size: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.2s;">−</button>
                <input type="number" wire:model.live="quantity" style="width: 60px; height: 40px; border: 2px solid #e8e8e8; border-left: 0; border-right: 0; text-align: center; font-weight: 700; font-size: 16px; color: #333; background: #fff;" min="1" readonly>
                <button type="button" wire:click="increment" style="width: 40px; height: 40px; border: 2px solid #e8e8e8; border-radius: 0 10px 10px 0; background: #fff; font-size: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.2s;">+</button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button type="button" wire:click="addToCart" style="flex: 1; min-width: 160px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; padding: 14px 20px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: opacity 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="ti-shopping-cart"></i> Add to Cart
            </button>
            <button type="button" wire:click="buyNow" style="flex: 1; min-width: 160px; background: #fff; color: #764ba2; border: 2px solid #764ba2; padding: 14px 20px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="ti-bolt"></i> Buy Now
            </button>
        </div>
    </div>

    @push('styles')
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
    @endpush
</div>
