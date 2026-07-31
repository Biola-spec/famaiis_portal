@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">

        {{-- Hero Banner --}}
        <div class="shop-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 40px 30px; margin-bottom: 20px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -40px; right: -40px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -60px; left: 20%; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div class="row align-items-center" style="position: relative; z-index: 1;">
                <div class="col-md-7">
                    <span style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 1px; margin-bottom: 12px; text-transform: uppercase;">School Shop</span>
                    <h2 style="color: #fff; font-size: 28px; font-weight: 800; margin-bottom: 8px; line-height: 1.2;">Everything Your Child Needs</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 15px; margin-bottom: 20px;">Uniforms, books, stationery & more — delivered right to the school gate.</p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="#products-section" style="display: inline-flex; align-items: center; gap: 6px; background: #fff; color: #764ba2; padding: 10px 22px; border-radius: 25px; font-weight: 700; font-size: 14px; text-decoration: none; transition: transform 0.2s;">
                            <i class="ti-shopping-cart"></i> Shop Now
                        </a>
                        <a href="{{ route('shop.cart') }}" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); color: #fff; padding: 10px 22px; border-radius: 25px; font-weight: 600; font-size: 14px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); transition: transform 0.2s;">
                            <i class="ti-bag"></i> View Cart
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center d-none d-md-block">
                    <div style="font-size: 100px; opacity: 0.3;">🛒</div>
                </div>
            </div>
        </div>

        {{-- Feature Strips --}}
        <div class="row mb-4" style="gap: 0;">
            @foreach([
                ['ti-truck', 'Free Delivery', 'Pickup at school gate', '#e8f5e9', '#2e7d32'],
                ['ti-shield', 'Quality Assured', 'School-approved items', '#e3f2fd', '#1565c0'],
                ['ti-reload', 'Easy Returns', 'Hassle-free returns', '#fff3e0', '#e65100'],
                ['ti-headphone-alt', 'Support', 'Contact school office', '#f3e5f5', '#7b1fa2'],
            ] as [$icon, $title, $sub, $bg, $color])
            <div class="col-6 col-md-3 mb-2">
                <div style="background: {{ $bg }}; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; height: 100%;">
                    <div style="width: 42px; height: 42px; background: {{ $color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="{{ $icon }}" style="color: #fff; font-size: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 13px; color: {{ $color }};">{{ $title }}</div>
                        <div style="font-size: 11px; color: #888;">{{ $sub }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Main content -->
        <section class="content" id="products-section">
            @livewire('shop.product-list')
        </section>
        <!-- /.content -->
    </div>
</div>

@endsection
