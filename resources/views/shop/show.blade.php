@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="background: transparent; padding: 8px 0; font-size: 13px;">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" style="color: #764ba2; text-decoration: none;"><i class="ti-home mr-1"></i>Shop</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category]) }}" style="color: #764ba2; text-decoration: none;">{{ $product->category }}</a></li>
                @endif
                <li class="breadcrumb-item active" style="color: #999;">{{ Str::limit($product->name, 40) }}</li>
            </ol>
        </nav>

        <div class="row" style="row-gap: 20px;">
            {{-- Product Image --}}
            <div class="col-md-5 col-12">
                <div style="background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #eee; position: sticky; top: 20px;">
                    <div style="position: relative;">
                        @if($product->created_at && $product->created_at->diffInDays(now()) <= 14)
                            <span style="position: absolute; top: 12px; left: 12px; background: #ff6b35; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 4px; z-index: 2; text-transform: uppercase;">New</span>
                        @endif
                        <img src="{{ (!empty($product->image)) ? url('storage/'.$product->image) : url('upload/no_image.jpg') }}" alt="{{ $product->name }}" style="width: 100%; height: auto; max-height: 450px; object-fit: contain; display: block; padding: 10px;">
                    </div>
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-md-7 col-12">
                <div style="background: #fff; border-radius: 16px; border: 1px solid #eee; padding: 28px;">
                    {{-- Category --}}
                    @if($product->category)
                        <span style="display: inline-block; background: #f3e5f5; color: #764ba2; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 15px; margin-bottom: 10px;">{{ $product->category }}</span>
                    @endif

                    {{-- Title --}}
                    <h2 style="font-size: 24px; font-weight: 800; color: #222; margin-bottom: 8px; line-height: 1.3;">{{ $product->name }}</h2>

                    {{-- Price --}}
                    <div style="background: #fafafa; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px;">
                        <div style="font-size: 32px; font-weight: 800; color: #764ba2;">₦{{ number_format($product->price, 2) }}</div>
                        <div style="font-size: 12px; color: #999; margin-top: 2px;">Inclusive of all fees</div>
                    </div>

                    {{-- Stock Status --}}
                    <div style="margin-bottom: 20px;">
                        @if($product->stock_quantity > 10)
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <span style="width: 10px; height: 10px; background: #4caf50; border-radius: 50%; display: inline-block;"></span>
                                <span style="color: #2e7d32; font-weight: 700; font-size: 14px;">In Stock</span>
                                <span style="color: #888; font-size: 13px;">({{ $product->stock_quantity }} available)</span>
                            </div>
                            <div style="height: 6px; background: #e8f5e9; border-radius: 3px; overflow: hidden; max-width: 200px;">
                                <div style="height: 100%; width: {{ min(100, ($product->stock_quantity / 50) * 100) }}%; background: #4caf50; border-radius: 3px;"></div>
                            </div>
                        @elseif($product->stock_quantity > 0)
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <span style="width: 10px; height: 10px; background: #ff9800; border-radius: 50%; display: inline-block;"></span>
                                <span style="color: #e65100; font-weight: 700; font-size: 14px;">Low Stock</span>
                                <span style="color: #e65100; font-size: 13px;">Only {{ $product->stock_quantity }} left — order soon!</span>
                            </div>
                            <div style="height: 6px; background: #fff3e0; border-radius: 3px; overflow: hidden; max-width: 200px;">
                                <div style="height: 100%; width: {{ ($product->stock_quantity / 50) * 100 }}%; background: #ff9800; border-radius: 3px;"></div>
                            </div>
                        @else
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 10px; height: 10px; background: #f44336; border-radius: 50%; display: inline-block;"></span>
                                <span style="color: #c62828; font-weight: 700; font-size: 14px;">Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($product->description)
                        <div style="margin-bottom: 24px;">
                            <h5 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 8px;"><i class="ti-info-alt mr-1"></i> Description</h5>
                            <p style="font-size: 14px; color: #666; line-height: 1.7; margin: 0;">{{ $product->description }}</p>
                        </div>
                    @endif

                    {{-- Highlights --}}
                    <div style="border-top: 1px solid #f0f0f0; padding-top: 16px; margin-bottom: 20px;">
                        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px; background: #f8f8f8; padding: 8px 14px; border-radius: 8px;">
                                <i class="ti-truck" style="color: #764ba2;"></i>
                                <span style="font-size: 12px; font-weight: 600; color: #555;">School Gate Pickup</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; background: #f8f8f8; padding: 8px 14px; border-radius: 8px;">
                                <i class="ti-shield" style="color: #764ba2;"></i>
                                <span style="font-size: 12px; font-weight: 600; color: #555;">Quality Assured</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; background: #f8f8f8; padding: 8px 14px; border-radius: 8px;">
                                <i class="ti-reload" style="color: #764ba2;"></i>
                                <span style="font-size: 12px; font-weight: 600; color: #555;">Easy Returns</span>
                            </div>
                        </div>
                    </div>

                    {{-- Add to Cart --}}
                    @if($product->stock_quantity > 0)
                        <div style="border-top: 1px solid #f0f0f0; padding-top: 20px;">
                            @livewire('shop.add-to-cart', ['productId' => $product->id])
                        </div>
                    @else
                        <div style="border-top: 1px solid #f0f0f0; padding-top: 20px;">
                            <button class="btn btn-lg btn-block" disabled style="background: #eee; color: #999; border-radius: 12px; font-weight: 700; font-size: 16px; padding: 14px;">
                                <i class="ti-na mr-2"></i> Currently Unavailable
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Back to Shop --}}
                <div class="mt-3">
                    <a href="{{ route('shop.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #764ba2; font-weight: 600; font-size: 14px; text-decoration: none; padding: 8px 0;">
                        <i class="ti-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
