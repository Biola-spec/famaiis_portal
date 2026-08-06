@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <h3 class="page-title">Request Leave</h3>
        </div>

        <section class="content">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('leave.requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Leave Type</label>
                            <input type="text" name="leave_type" value="{{ old('leave_type') }}" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" rows="5" class="form-control" required>{{ old('reason') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Supporting Document</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <small class="text-muted">PDF or image, max 5MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                        <a href="{{ route('leave.requests.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
