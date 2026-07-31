@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Payment & Bank Transfer Settings</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col">
                            <form method="post" action="{{ route('update.payment.setting') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Provider <span class="text-danger">*</span></h5>
                                            <select name="provider" class="form-control" required>
                                                <option value="paystack" {{ $setting->provider === 'paystack' ? 'selected' : '' }}>Paystack</option>
                                                <option value="flutterwave" {{ $setting->provider === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Gateway API URL <span class="text-danger">*</span></h5>
                                            <input type="url" name="payment_url" class="form-control" value="{{ old('payment_url', $setting->payment_url) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Public Key</h5>
                                            <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $setting->public_key) }}" placeholder="pk_test_...">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Secret Key</h5>
                                            <input type="password" name="secret_key" class="form-control" value="{{ old('secret_key', $setting->secret_key) }}" placeholder="sk_test_...">
                                            <small class="text-muted">Stored securely in the database.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="controls mt-2">
                                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ $setting->is_active ? 'checked' : '' }}>
                                                <label for="is_active">Enable online payment gateway</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4 class="mb-3">School Bank Transfer Account</h4>
                                <p class="text-muted">These details will be shown to buyers during shop checkout. They will upload a transfer receipt for admin verification.</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Bank Name</h5>
                                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="e.g. Access Bank">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Account Number</h5>
                                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $setting->account_number) }}" placeholder="e.g. 0123456789">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Account Name</h5>
                                            <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $setting->account_name) }}" placeholder="e.g. FAMA Islamic International School">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="controls mt-2">
                                                <input type="checkbox" id="bank_transfer_enabled" name="bank_transfer_enabled" value="1" {{ $setting->bank_transfer_enabled ? 'checked' : '' }}>
                                                <label for="bank_transfer_enabled">Enable bank transfer checkout for school shop</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Transfer Instructions</h5>
                                            <textarea name="transfer_instructions" class="form-control" rows="4" placeholder="Tell buyers what to include in the transfer narration and how verification works.">{{ old('transfer_instructions', $setting->transfer_instructions) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs-right">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update Payment Settings">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection
