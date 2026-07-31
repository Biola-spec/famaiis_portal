@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Fee Structure</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('fee.structures.update', $structure->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Section <span class="text-danger">*</span></label>
                                            <select name="section_id" class="form-control" required>
                                                <option value="" disabled>Select Section</option>
                                                @foreach($sections as $s)
                                                <option value="{{ $s->id }}" {{ $structure->section_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Year <span class="text-danger">*</span></label>
                                            <select name="year_id" class="form-control" required>
                                                <option value="" disabled>Select Year</option>
                                                @foreach($years as $y)
                                                <option value="{{ $y->id }}" {{ $structure->year_id == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
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
                                                <option value="{{ $c->id }}" {{ $structure->class_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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
                                                <option value="{{ $t->id }}" {{ $structure->term_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5 class="mb-3">Fee Items</h5>
                                
                                <div id="fee_items_container">
                                    @foreach($structure->feeItems as $index => $item)
                                    <div class="row fee-item-row mb-2 {{ $index > 0 ? 'mt-2' : '' }}">
                                        <div class="col-md-6">
                                            <select name="fee_type_ids[]" class="form-control" required>
                                                <option value="" disabled>Select Fee Type</option>
                                                @foreach($feeTypes as $ft)
                                                <option value="{{ $ft->id }}" {{ $item->fee_type_id == $ft->id ? 'selected' : '' }}>{{ $ft->name }} ({{ $ft->category }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" name="amounts[]" class="form-control" value="{{ $item->amount }}" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-1">
                                            @if($index == 0)
                                            <button type="button" class="btn btn-success add-more"><i class="fa fa-plus"></i></button>
                                            @else
                                            <button type="button" class="btn btn-danger remove-item"><i class="fa fa-minus"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-rounded btn-primary">Update Structure</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
