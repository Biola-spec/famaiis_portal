@extends('admin.admin_master')
@section('admin')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Base Select2 Styling */
    .select2-container--default .select2-selection--single {
        height: 45px;
        line-height: 45px;
        border: 1px solid #dcdfe6;
        border-radius: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 45px;
        padding-left: 15px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
    }

    /* Dark Mode Support */
    body.dark-skin .select2-container--default .select2-selection--single {
        background-color: #1a233a !important;
        border-color: #404b66 !important;
        color: #ffffff !important;
    }
    body.dark-skin .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff !important;
    }
    body.dark-skin .select2-dropdown {
        background-color: #1a233a !important;
        color: #ffffff !important;
        border-color: #404b66 !important;
    }
    body.dark-skin .select2-search__field {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border-color: #404b66 !important;
    }
    body.dark-skin .select2-results__option--highlighted[aria-selected] {
        background-color: #512da8 !important;
    }
    body.dark-skin .select2-results__option[aria-selected=true] {
        background-color: #262f45 !important;
    }
</style>


<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Pay School Fees</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('wallet.view') }}"><i class="mdi mdi-home-outline"></i> Wallet</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Pay Fees</li>
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
                            <h4 class="box-title">Select Student and Fee</h4>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('wallet.pay_fees.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Select Student <span class="text-danger">*</span></label>
                                            <select name="student_id" id="student_id" class="form-control select2" required style="width: 100%;">
                                                <option value="" selected disabled>Select a student</option>
                                                @forelse($children as $child)
                                                    <option value="{{ $child->id }}">{{ $child->name }} ({{ $child->id_no ?? 'No ID' }})</option>
                                                @empty
                                                    <option value="" disabled>No students found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Your Wallet Balance</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₦</span>
                                                </div>
                                                <input type="text" class="form-control" value="{{ number_format($wallet->balance, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20" id="fee_selection_container" style="display: none;">
                                    <div class="col-12">
                                        <div class="box box-outline-primary">
                                            <div class="box-header with-border">
                                                <h5 class="box-title">Available Fees for Selected Student</h5>
                                            </div>
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Fee Title</th>
                                                                <th>Total Amount</th>
                                                                <th>Paid</th>
                                                                <th>Balance</th>
                                                                <th>Amount to Pay</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="fee_list_body">
                                                            <!-- Loaded via AJAX -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="no_fees_alert" class="alert alert-warning mt-20" style="display: none;">
                                    <i class="fa fa-exclamation-triangle mr-10"></i> No pending fees found for this student.
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        if ($('.select2').length > 0) {
            $('.select2').select2({
                placeholder: "Select a student",
                allowClear: true,
                width: '100%'
            });
        }

        $('#student_id').on('change', function() {
            var student_id = $(this).val();
            if (student_id) {
                $('#fee_selection_container').hide();
                $('#no_fees_alert').hide();
                
                $.ajax({
                    url: "{{ url('/wallet/get-student-fees') }}/" + student_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var html = '';
                        if (data.length > 0) {
                            var pendingCount = 0;
                            $.each(data, function(key, value) {
                                if (value.balance > 0) {
                                    pendingCount++;
                                    html += '<tr>';
                                    html += '<td>' + (pendingCount) + '</td>';
                                    html += '<td>' + value.title + ' (' + value.term + ' - ' + value.session + ')</td>';
                                    html += '<td>₦ ' + parseFloat(value.amount).toLocaleString() + '</td>';
                                    html += '<td>₦ ' + parseFloat(value.paid_amount).toLocaleString() + '</td>';
                                    html += '<td class="text-danger"><strong>₦ ' + parseFloat(value.balance).toLocaleString() + '</strong></td>';
                                    html += '<td><input type="number" name="pay_amount" id="pay_amount_'+value.id+'" class="form-control form-control-sm" value="'+value.balance+'" max="'+value.balance+'" min="1"></td>';
                                    html += '<td><button type="button" class="btn btn-sm btn-success pay-btn" data-fee-id="'+value.id+'">Pay Now</button></td>';
                                    html += '</tr>';
                                }
                            });

                            if (pendingCount > 0) {
                                $('#fee_list_body').html(html);
                                $('#fee_selection_container').fadeIn();
                            } else {
                                $('#no_fees_alert').fadeIn();
                            }
                        } else {
                            $('#no_fees_alert').fadeIn();
                        }
                    }
                });
            }
        });

        $(document).on('click', '.pay-btn', function() {
            var fee_id = $(this).data('fee-id');
            var student_id = $('#student_id').val();
            var amount = $('#pay_amount_' + fee_id).val();
            var walletBalance = {{ $wallet->balance }};

            if (amount > walletBalance) {
                Swal.fire('Error', 'Insufficient wallet balance!', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirm Payment',
                text: "Pay ₦ " + amount + " from your wallet?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, pay now!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a temporary form to submit
                    var form = $('<form action="{{ route("wallet.pay_fees.store") }}" method="POST">' +
                        '@csrf' +
                        '<input type="hidden" name="student_id" value="' + student_id + '">' +
                        '<input type="hidden" name="fee_id" value="' + fee_id + '">' +
                        '<input type="hidden" name="amount" value="' + amount + '">' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
