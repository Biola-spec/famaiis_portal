@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Wallet Management (Admin)</h3>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">All User Wallets</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User Name</th>
                                            <th>ID No</th>
                                            <th>Role</th>
                                            <th>Current Balance</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wallets as $wallet)
                                            <tr>
                                                <td><strong>{{ $wallet->user->name }}</strong></td>
                                                <td>{{ $wallet->user->id_no ?? 'N/A' }}</td>
                                                <td><span class="badge badge-primary-light">{{ ucfirst($wallet->role) }}</span></td>
                                                <td>₦ {{ number_format($wallet->balance, 2) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $wallet->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($wallet->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#adjustModal{{ $wallet->id }}">
                                                        <i class="fa fa-pencil-square-o"></i> Adjust
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Adjust Balance Modal -->
                                            <div class="modal fade" id="adjustModal{{ $wallet->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Adjust Wallet: {{ $wallet->user->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="nav-tabs-custom">
                                                                <ul class="nav nav-tabs">
                                                                    <li><a href="#credit{{ $wallet->id }}" class="active" data-toggle="tab">Credit (+)</a></li>
                                                                    <li><a href="#debit{{ $wallet->id }}" data-toggle="tab">Debit (-)</a></li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane active" id="credit{{ $wallet->id }}">
                                                                        <form action="{{ route('wallet.admin.credit') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="user_id" value="{{ $wallet->user_id }}">
                                                                            <div class="form-group mt-15">
                                                                                <label>Amount to Add (₦)</label>
                                                                                <input type="number" name="amount" class="form-control" step="0.01" required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Reason / Description</label>
                                                                                <input type="text" name="description" class="form-control" placeholder="e.g. Scholarship Refund" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary btn-block">Credit Wallet</button>
                                                                        </form>
                                                                    </div>
                                                                    <div class="tab-pane" id="debit{{ $wallet->id }}">
                                                                        <form action="{{ route('wallet.admin.debit') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="user_id" value="{{ $wallet->user_id }}">
                                                                            <div class="form-group mt-15">
                                                                                <label>Amount to Subtract (₦)</label>
                                                                                <input type="number" name="amount" class="form-control" step="0.01" max="{{ $wallet->balance }}" required>
                                                                                <small class="text-mute">Max: ₦ {{ number_format($wallet->balance, 2) }}</small>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Reason / Description</label>
                                                                                <input type="text" name="description" class="form-control" placeholder="e.g. Correction" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-danger btn-block">Debit Wallet</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-20">
                                {{ $wallets->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
