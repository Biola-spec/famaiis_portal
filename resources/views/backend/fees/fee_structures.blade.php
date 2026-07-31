@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <!-- Fee Structures List -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fee Structures List</h3>
                            <button class="btn btn-rounded btn-success float-right" data-toggle="modal" data-target="#addModal">Add Fee Structure</button>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Section</th>
                                            <th>Class</th>
                                            <th>Term</th>
                                            <th>Year</th>
                                            <th>Total Amount</th>
                                            <th>Items</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($structures as $struct)
                                        <tr>
                                            <td>{{ $struct->section->name ?? 'N/A' }}</td>
                                            <td>{{ $struct->studentClass->name ?? 'All Classes' }}</td>
                                            <td>{{ $struct->term->name ?? 'All Terms' }}</td>
                                            <td>{{ $struct->year->name ?? 'N/A' }}</td>
                                            <td>₦{{ number_format($struct->total_amount, 2) }}</td>
                                            <td>
                                                @foreach($struct->feeItems as $item)
                                                <span class="badge badge-primary">{{ $item->feeType->name }}: ₦{{ $item->amount }}</span><br>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('fee.structures.edit', $struct->id) }}" class="btn btn-info btn-sm">Edit</a>
                                                <a href="{{ route('fee.structures.delete', $struct->id) }}" class="btn btn-danger btn-sm" id="delete">Delete</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Add Modal -->
<div class="modal center-modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Fee Structure</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="{{ route('fee.structures.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Section <span class="text-danger">*</span></label>
                                <select name="section_id" class="form-control" required>
                                    <option value="" selected disabled>Select Section</option>
                                    @foreach($sections as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year <span class="text-danger">*</span></label>
                                <select name="year_id" class="form-control" required>
                                    <option value="" selected disabled>Select Year</option>
                                    @foreach($years as $y)
                                    <option value="{{ $y->id }}">{{ $y->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Class (Optional)</label>
                                <select name="class_id" class="form-control">
                                    <option value="">All Classes in Section</option>
                                    @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Term (Optional)</label>
                                <select name="term_id" class="form-control">
                                    <option value="">All Terms / Annual</option>
                                    @foreach($terms as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Fee Items</h5>
                    
                    <div id="fee_items_container">
                        <div class="row fee-item-row mb-2">
                            <div class="col-md-6">
                                <select name="fee_type_ids[]" class="form-control" required>
                                    <option value="" selected disabled>Select Fee Type</option>
                                    @foreach($feeTypes as $ft)
                                    <option value="{{ $ft->id }}">{{ $ft->name }} ({{ $ft->category }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="amounts[]" class="form-control" placeholder="Amount (₦)" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-success add-more"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-uniform">
                    <button type="button" class="btn btn-rounded btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-rounded btn-primary float-right">Save Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var feeTypesHtml = `
        <select name="fee_type_ids[]" class="form-control" required>
            <option value="" selected disabled>Select Fee Type</option>
            @foreach($feeTypes as $ft)
            <option value="{{ $ft->id }}">{{ $ft->name }} ({{ $ft->category }})</option>
            @endforeach
        </select>
    `;

    $(document).on('click', '.add-more', function(){
        var row = `
            <div class="row fee-item-row mb-2 mt-2">
                <div class="col-md-6">
                    ${feeTypesHtml}
                </div>
                <div class="col-md-5">
                    <input type="number" name="amounts[]" class="form-control" placeholder="Amount (₦)" min="0" step="0.01" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-item"><i class="fa fa-minus"></i></button>
                </div>
            </div>
        `;
        $('#fee_items_container').append(row);
    });

    $(document).on('click', '.remove-item', function(){
        $(this).closest('.fee-item-row').remove();
    });
});
</script>

@endsection
