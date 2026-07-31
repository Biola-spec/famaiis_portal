@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Financial Wallet</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Wallet</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- Wallet Card -->
                <div class="col-xl-4 col-12">
                    <div class="box overflow-hidden pull-up">
                        <div class="box-body pr-0 pl-lg-50 pl-15 py-0">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-8">
                                    <div class="py-10">
                                        <h2 class="text-white">₦ {{ number_format($wallet->balance, 2) }}</h2>
                                        <p class="text-white-50 mb-0">Current Wallet Balance</p>
                                        <div class="mt-20">
                                            <span class="badge badge-pill badge-primary-light px-15 py-5">{{ ucfirst($wallet->role) }} Account</span>
                                            @if($wallet->status == 'active')
                                                <span class="badge badge-pill badge-success-light px-15 py-5">Active</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4 d-none d-lg-block">
                                    <div class="p-10">
                                        <i class="fa fa-google-wallet text-white-50" style="font-size: 80px; opacity: 0.2;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-xl-8 col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Wallet Actions</h4>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @if(Auth::user()->hasRole('Parent', 'Admin'))
                                    <div class="col-md-4 col-12 mb-10">
                                        <button type="button" class="btn btn-primary btn-block btn-lg" data-toggle="modal" data-target="#fundWalletModal">
                                            <i class="fa fa-plus-circle mr-5"></i> Fund Wallet
                                        </button>
                                    </div>
                                    <div class="col-md-4 col-12 mb-10">
                                        <a href="{{ route('wallet.pay_fees') }}" class="btn btn-success btn-block btn-lg">
                                            <i class="fa fa-money mr-5"></i> Pay School Fees
                                        </a>
                                    </div>
                                @endif
                                
                                @if(Auth::user()->hasRole('Admin'))
                                    <div class="col-md-4 col-12 mb-10">
                                        <a href="{{ route('wallet.admin.manage') }}" class="btn btn-info btn-block btn-lg">
                                            <i class="fa fa-users mr-5"></i> Manage All Wallets
                                        </a>
                                    </div>
                                @endif

                                @if(Auth::user()->hasRole('Student'))
                                    <div class="col-12 text-center py-20">
                                        <div class="alert alert-info-light">
                                            <i class="fa fa-info-circle mr-10"></i> <strong>View Only Mode:</strong> You can see your balance and history, but spending is managed by your parent or admin.
                                        </div>
                                    </div>
                                @endif

                                @if(Auth::user()->hasRole('Teacher'))
                                    <div class="col-md-4 col-12 mb-10">
                                        <button class="btn btn-warning btn-block btn-lg" disabled>
                                            <i class="fa fa-history mr-5"></i> Allowance Tracking (Coming Soon)
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border d-flex justify-content-between align-items-center">
                            <h4 class="box-title">Recent Transactions</h4>
                            <a href="{{ route('wallet.history') }}" class="btn btn-sm btn-outline btn-primary">View All</a>
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
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $tx)
                                            <tr>
                                                <td>{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                                <td>{{ $tx->description }}</td>
                                                <td>
                                                    <span class="text-{{ $tx->type == 'credit' ? 'success' : 'danger' }}">
                                                        {{ $tx->type == 'credit' ? '+' : '-' }} ₦ {{ number_format($tx->amount, 2) }}
                                                    </span>
                                                </td>
                                                <td><span class="badge badge-{{ $tx->type == 'credit' ? 'success' : 'danger' }}-light">{{ ucfirst($tx->type) }}</span></td>
                                                <td>{{ $tx->performer->name }}</td>
                                                <td><span class="badge badge-success">Completed</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No transactions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-10">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->hasRole('Parent'))
            <!-- Children Activity Ledger -->
            <div class="row">
                <div class="col-12">
                    <div class="box border-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title text-primary">Children's Payment Ledger</h4>
                            <p class="mb-0 text-mute">Track payments made on behalf of your children</p>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="bg-primary-light">
                                            <th>Student Name</th>
                                            <th>Payment Date</th>
                                            <th>Description</th>
                                            <th>Amount Paid</th>
                                            <th>Paid By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($childrenTransactions as $ctx)
                                            <tr>
                                                <td><strong>{{ $ctx->user->name }}</strong></td>
                                                <td>{{ $ctx->created_at->format('M d, Y') }}</td>
                                                <td>{{ $ctx->description }}</td>
                                                <td class="font-weight-600">₦ {{ number_format($ctx->amount, 2) }}</td>
                                                <td><span class="badge badge-info-light">{{ $ctx->performer->name }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No activity found for your children.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-10">
                                {{ $childrenTransactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </section>
        <!-- /.content -->
    </div>
</div>

<!-- Fund Wallet Modal -->
<div class="modal fade" id="fundWalletModal" tabindex="-1" role="dialog" aria-labelledby="fundWalletModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fundWalletModalLabel">Fund Your Wallet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('wallet.fund.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount to Fund (₦)</label>
                        <input type="number" name="amount" class="form-control" placeholder="Enter amount" min="100" required>
                        <small class="form-text text-muted">Minimum funding amount is ₦ 100.00</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle mr-5"></i> In a live environment, you will be redirected to the payment gateway (Paystack/Flutterwave). For this version, funding is simulated.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Funding</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .box.overflow-hidden.pull-up {
        background: linear-gradient(135deg, #1a237e 0%, #512da8 100%);
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.3);
        border: none;
        transition: all 0.3s ease;
    }
    .box.overflow-hidden.pull-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(26, 35, 126, 0.4);
    }
    .badge-primary-light {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .badge-success-light {
        background-color: rgba(76, 175, 80, 0.2);
        color: #81c784;
    }
</style>
@endsection
