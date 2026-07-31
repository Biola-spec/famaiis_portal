@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">

        {{-- Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding: 16px 0; border-bottom: 1px solid #eee;">
            <h3 style="font-weight: 800; color: #222; font-size: 22px; margin: 0;">
                <i class="ti-bag mr-2" style="color: #764ba2;"></i>My Cart
            </h3>
            <a href="{{ route('shop.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #764ba2; font-weight: 600; font-size: 14px; text-decoration: none;">
                <i class="ti-arrow-left"></i> Back to Shop
            </a>
        </div>

        <!-- Main content -->
        <section class="content">
            @livewire('shop.cart-list')
        </section>
    </div>
</div>

@endsection
