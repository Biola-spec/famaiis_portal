@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Transaction History</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('wallet.view') }}"><i class="mdi mdi-home-outline"></i> Wallet</a></li>
                                <li class="breadcrumb-item active" aria-current="page">History</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">All Transactions</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Performed By</th>
                                            <th>Transaction ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $tx)
                                            <tr>
                                                <td>{{ $tx->created_at->format('M d, Y H:i:s') }}</td>
                                                <td>{{ $tx->description }}</td>
                                                <td>
                                                    <span class="text-{{ $tx->type == 'credit' ? 'success' : 'danger' }} font-weight-600">
                                                        {{ $tx->type == 'credit' ? '+' : '-' }} ₦ {{ number_format($tx->amount, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $tx->type == 'credit' ? 'success' : 'danger' }}-light">
                                                        {{ ucfirst($tx->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $tx->performer->name }}</td>
                                                <td><small class="text-mute">#WTX-{{ str_pad($tx->id, 6, '0', STR_PAD_LEFT) }}</small></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No transactions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-20">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
