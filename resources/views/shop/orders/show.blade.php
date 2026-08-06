<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} #{{ $order->id }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Back to list</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl p-10">
                
                <!-- Status & Info Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 pb-8 border-b border-gray-100">
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Status</p>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-6 py-2 rounded-full text-sm font-black uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-gray-100' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Ordered On</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                <!-- Buyer Info -->
                @if(auth()->user()->hasRole('Admin', 'Accountant'))
                <div class="mb-12 p-6 bg-gray-50 rounded-2xl">
                    <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-4">Buyer Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-bold text-gray-900">{{ $order->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role</p>
                            <p class="font-bold text-gray-900 capitalize">{{ $order->role_type }}</p>
                        </div>
                        @if($order->student)
                        <div>
                            <p class="text-sm text-gray-500">Student</p>
                            <p class="font-bold text-gray-900">{{ $order->student->name }} ({{ $order->student->id_no ?? $order->student->id }})</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="mb-12 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                    <h4 class="text-xs uppercase tracking-widest text-blue-500 mb-4">Payment Verification</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Payment Method</p>
                            <p class="font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $order->payment_method ?? $order->payment_provider ?? 'bank_transfer')) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            <p class="font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $order->payment_status ?? 'pending')) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Reference</p>
                            <p class="font-bold text-gray-900">{{ $order->payment_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Transfer Reference</p>
                            <p class="font-bold text-gray-900">{{ $order->transfer_reference ?? 'Not submitted' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Receipt Submitted</p>
                            <p class="font-bold text-gray-900">{{ optional($order->receipt_submitted_at)->format('F d, Y \a\t h:i A') ?? 'Not submitted' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Verified By</p>
                            <p class="font-bold text-gray-900">{{ optional($order->verifier)->name ?? 'Not verified yet' }}</p>
                        </div>
                    </div>

                    @if($order->transfer_receipt)
                        <div class="mt-6">
                            <a href="{{ asset('storage/' . $order->transfer_receipt) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-blue-500">
                                View Transfer Receipt
                            </a>
                        </div>
                    @endif

                    @if($order->verification_note)
                        <div class="mt-6 p-4 bg-white rounded-xl">
                            <p class="text-sm text-gray-500">Verification Note</p>
                            <p class="font-semibold text-gray-900">{{ $order->verification_note }}</p>
                        </div>
                    @endif
                </div>

                <!-- Items Table -->
                <div class="mb-12">
                    <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-6">Order Items</h4>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-6 p-4 rounded-2xl border border-gray-50">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                    @if($item->product->image)
                                        <img src="{{ url('storage/'.$item->product->image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-gray-900">{{ $item->product->name }}</h5>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ₦{{ number_format($item->price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-gray-900">₦{{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totals -->
                <div class="flex justify-end pt-8 border-t border-gray-100">
                    <div class="w-full md:w-64">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="text-lg font-black text-gray-900">Total</span>
                            <span class="text-2xl font-black text-blue-600">₦{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                @if(auth()->user()->hasRole('Admin', 'Accountant') && $order->status !== 'completed' && $order->status !== 'rejected')
                <div class="mt-16 p-8 bg-gray-900 rounded-3xl">
                    <h4 class="text-white text-lg font-bold mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Update Order Status
                    </h4>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <textarea name="verification_note" rows="3" class="w-full rounded-xl border-gray-700 bg-gray-800 text-white" placeholder="Optional verification note for this order">{{ old('verification_note', $order->verification_note) }}</textarea>
                        <div class="flex flex-wrap gap-4">
                        @if($order->status === 'pending')
                            <button name="status" value="approved" type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition duration-300 transform active:scale-95 shadow-lg shadow-blue-600/20">
                                Verify Payment & Approve
                            </button>
                            <button name="status" value="rejected" type="submit" class="bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-xl font-bold transition duration-300 transform active:scale-95 shadow-lg shadow-red-600/20">
                                Reject Payment
                            </button>
                        @elseif($order->status === 'approved')
                            <button name="status" value="completed" type="submit" class="bg-green-600 hover:bg-green-500 text-white px-6 py-3 rounded-xl font-bold transition duration-300 transform active:scale-95 shadow-lg shadow-green-600/20">
                                Mark as Completed
                            </button>
                        @endif
                        </div>
                    </form>
                </div>
                @endif

                <div class="mt-8 text-center">
                    <a href="{{ route('orders.invoice', $order->id) }}" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:underline">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Invoice PDF
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
