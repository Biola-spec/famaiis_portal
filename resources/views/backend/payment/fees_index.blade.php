@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Fee Setup</h4>
                            <a href="{{ route('fees.create') }}" class="btn btn-primary btn-sm" style="float:right;">Add Fee</a>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Term</th>
                                            <th>Session</th>
                                            <th>Title</th>
                                            <th>Amount (₦)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($fees as $fee)
                                            <tr>
                                                <td>{{ optional($fee->studentClass)->name }}</td>
                                                <td>{{ $fee->term }}</td>
                                                <td>{{ $fee->session }}</td>
                                                <td>{{ $fee->title }}</td>
                                                <td>{{ number_format((float)$fee->amount, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('fees.edit', $fee) }}" class="btn btn-info btn-sm">Edit</a>
                                                    <form action="{{ route('fees.delete', $fee) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this fee?')" type="submit">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6">No fee record found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $fees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
