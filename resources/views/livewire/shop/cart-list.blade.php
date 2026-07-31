<div>
    <style>
        .cart-item { background: #fff; border-radius: 12px; border: 1px solid #eee; margin-bottom: 12px; overflow: hidden; transition: box-shadow 0.2s; }
        .cart-item:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .cart-item-inner { display: flex; align-items: center; gap: 16px; padding: 16px; flex-wrap: wrap; }
        .cart-img { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0; border: 1px solid #f0f0f0; }
        .cart-img-placeholder { width: 80px; height: 80px; border-radius: 10px; background: #f8f8f8; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #ddd; font-size: 28px; }
        .cart-info { flex: 1; min-width: 120px; }
        .cart-info h5 { font-size: 15px; font-weight: 700; color: #222; margin-bottom: 3px; }
        .cart-info small { color: #999; font-size: 12px; }
        .cart-price { font-size: 18px; font-weight: 800; color: #764ba2; min-width: 100px; text-align: right; }
        .cart-qty { display: flex; align-items: center; gap: 0; }
        .cart-qty button { width: 34px; height: 34px; border: 2px solid #e8e8e8; background: #fff; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: #555; }
        .cart-qty button:first-child { border-radius: 8px 0 0 8px; }
        .cart-qty button:last-child { border-radius: 0 8px 8px 0; }
        .cart-qty button:hover { background: #764ba2; color: #fff; border-color: #764ba2; }
        .cart-qty input { width: 50px; height: 34px; border: 2px solid #e8e8e8; border-left: 0; border-right: 0; text-align: center; font-weight: 700; font-size: 14px; color: #333; }
        .cart-remove { width: 36px; height: 36px; border-radius: 50%; border: none; background: #fef2f2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: all 0.2s; flex-shrink: 0; }
        .cart-remove:hover { background: #ef4444; color: #fff; }

        .checkout-box { background: #fff; border-radius: 16px; border: 1px solid #eee; overflow: hidden; position: sticky; top: 20px; }
        .checkout-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 20px; }
        .checkout-body { padding: 20px; }
        .checkout-total { font-size: 36px; font-weight: 800; color: #764ba2; }
        .bank-info { background: #f8f8f8; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        .bank-info p { margin-bottom: 4px; font-size: 13px; color: #555; }
        .bank-info strong { color: #333; }

        .empty-cart-wrap { text-align: center; padding: 80px 20px; }
    </style>

    @if($cartItems->count() > 0)
        <div class="row" style="row-gap: 16px;">
            {{-- Cart Items --}}
            <div class="col-lg-7">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding: 0 4px;">
                    <h4 style="font-weight: 800; color: #222; font-size: 20px; margin: 0;">
                        <i class="ti-shopping-cart mr-1" style="color: #764ba2;"></i>
                        Shopping Cart
                    </h4>
                    <span style="font-size: 13px; color: #999;">{{ $cartItems->count() }} item{{ $cartItems->count() > 1 ? 's' : '' }}</span>
                </div>

                @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="cart-item-inner">
                            {{-- Image --}}
                            <a href="{{ route('shop.show', $item->product->id) }}">
                                @if($item->product->image)
                                    <img src="{{ url('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}" class="cart-img">
                                @else
                                    <div class="cart-img-placeholder"><i class="ti-image"></i></div>
                                @endif
                            </a>

                            {{-- Info --}}
                            <div class="cart-info">
                                <h5><a href="{{ route('shop.show', $item->product->id) }}" style="color: inherit; text-decoration: none;">{{ $item->product->name }}</a></h5>
                                <small>{{ $item->product->category ?? 'Uncategorized' }}</small>
                                <div style="font-size: 14px; font-weight: 700; color: #555; margin-top: 4px;">
                                    ₦{{ number_format($item->product->price, 2) }} each
                                </div>
                            </div>

                            {{-- Quantity --}}
                            <div class="cart-qty">
                                <button wire:click="decrement({{ $item->id }})" type="button">−</button>
                                <input type="text" value="{{ $item->quantity }}" readonly>
                                <button wire:click="increment({{ $item->id }})" type="button">+</button>
                            </div>

                            {{-- Total --}}
                            <div class="cart-price">
                                ₦{{ number_format($item->product->price * $item->quantity, 2) }}
                            </div>

                            {{-- Remove --}}
                            <button wire:click="removeItem({{ $item->id }})" class="cart-remove" title="Remove">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach

                <div class="mt-2">
                    <a href="{{ route('shop.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #764ba2; font-weight: 600; font-size: 14px; text-decoration: none; padding: 8px 0;">
                        <i class="ti-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            {{-- Checkout Panel --}}
            <div class="col-lg-5">
                <div class="checkout-box">
                    <div class="checkout-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="font-weight: 800; margin: 0; font-size: 18px;">Order Summary</h4>
                            <span style="background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 15px; font-size: 11px;">{{ $cartItems->count() }} items</span>
                        </div>
                        <div class="checkout-total mt-2" style="color: #fff;">₦{{ number_format($total, 2) }}</div>
                    </div>
                    <div class="checkout-body">
                        @if($paymentSetting && $paymentSetting->bank_transfer_enabled && $paymentSetting->bank_name && $paymentSetting->account_number && $paymentSetting->account_name)
                            <div class="bank-info">
                                <h6 style="font-weight: 700; font-size: 14px; color: #333; margin-bottom: 10px;">
                                    <i class="ti-credit-card mr-1" style="color: #764ba2;"></i> Transfer to School Account
                                </h6>
                                <p><strong>Bank:</strong> {{ $paymentSetting->bank_name }}</p>
                                <p><strong>Account No:</strong> {{ $paymentSetting->account_number }}</p>
                                <p><strong>Account Name:</strong> {{ $paymentSetting->account_name }}</p>
                                @if($paymentSetting->transfer_instructions)
                                    <p class="mt-2" style="font-size: 12px; color: #999;">{{ $paymentSetting->transfer_instructions }}</p>
                                @else
                                    <p class="mt-2" style="font-size: 12px; color: #999;">After transfer, upload your receipt below. Admin will verify before approval.</p>
                                @endif
                            </div>

                            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div style="margin-bottom: 12px;">
                                    <label style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 4px; display: block;">Transfer Reference <span style="color:#ef4444">*</span></label>
                                    <input type="text" name="transfer_reference" class="form-control" style="border-radius: 10px; border: 2px solid #e8e8e8; padding: 10px 14px;" value="{{ old('transfer_reference') }}" placeholder="Bank reference or narration" required>
                                </div>
                                <div style="margin-bottom: 16px;">
                                    <label style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 4px; display: block;">Upload Receipt <span style="color:#ef4444">*</span></label>
                                    <input type="file" name="transfer_receipt" class="form-control" style="border-radius: 10px; border: 2px solid #e8e8e8; padding: 8px 14px;" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <small style="font-size: 11px; color: #999;">JPG, PNG, or PDF. Max 4MB.</small>
                                </div>
                                <button type="submit" style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: opacity 0.2s;">
                                    <i class="ti-check mr-1"></i> Submit Transfer for Verification
                                </button>
                            </form>
                        @else
                            <div style="background: #fff8e1; border-radius: 12px; padding: 20px; text-align: center;">
                                <i class="ti-alert" style="font-size: 32px; color: #f9a825; margin-bottom: 10px; display: block;"></i>
                                <h6 style="font-weight: 700; color: #333; margin-bottom: 6px;">Bank Transfer Not Configured</h6>
                                <p style="font-size: 13px; color: #888; margin-bottom: 12px;">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Accountant'))
                                        Set up the school bank details so buyers can submit transfer receipts.
                                    @else
                                        Please contact the school office to complete your purchase.
                                    @endif
                                </p>
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Accountant'))
                                    <a href="{{ route('payment.setting') }}" style="display: inline-block; background: #764ba2; color: #fff; padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none;">
                                        Set Up Transfer Account
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart --}}
        <div class="empty-cart-wrap">
            <div style="font-size: 100px; opacity: 0.15; margin-bottom: 16px;">🛒</div>
            <h3 style="font-weight: 800; color: #333; margin-bottom: 6px;">Your Cart is Empty</h3>
            <p style="color: #999; font-size: 14px; margin-bottom: 20px;">Looks like you haven't added anything yet. Start shopping to fill it up!</p>
            <a href="{{ route('shop.index') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 12px 28px; border-radius: 25px; font-weight: 700; font-size: 14px; text-decoration: none;">
                <i class="ti-shopping-cart"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
