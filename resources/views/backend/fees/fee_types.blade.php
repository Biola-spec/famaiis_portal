@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
            <div class="row">

                <div class="col-8">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fee Types List</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Items Used</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($feeTypes as $key => $type)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $type->name }}</td>
                                            <td><span class="badge badge-info">{{ ucfirst($type->category) }}</span></td>
                                            <td>{{ $type->fee_items_count }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal{{$type->id}}">Edit</button>
                                                @if($type->fee_items_count == 0)
                                                <a href="{{ route('fee.types.delete', $type->id) }}" class="btn btn-danger btn-sm" id="delete">Delete</a>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal center-modal fade" id="editModal{{$type->id}}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Fee Type</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="post" action="{{ route('fee.types.update', $type->id) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Name</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $type->name }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select name="category" class="form-control" required>
                                                                    <option value="mandatory" {{ $type->category == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                                                                    <option value="optional" {{ $type->category == 'optional' ? 'selected' : '' }}>Optional</option>
                                                                    <option value="one-time" {{ $type->category == 'one-time' ? 'selected' : '' }}>One-Time</option>
                                                                    <option value="recurring" {{ $type->category == 'recurring' ? 'selected' : '' }}>Recurring</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer modal-footer-uniform">
                                                            <button type="button" class="btn btn-rounded btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-rounded btn-primary float-right">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Fee Type Form -->
                <div class="col-4">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add Fee Type</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('fee.types.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Fee Name <span class="text-danger">*</span></label>
                                    <div class="controls">
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <div class="controls">
                                        <select name="category" class="form-control" required>
                                            <option value="" selected="" disabled="">Select Category</option>
                                            <option value="mandatory">Mandatory</option>
                                            <option value="optional">Optional</option>
                                            <option value="one-time">One-Time</option>
                                            <option value="recurring">Recurring</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-xs-right">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="Submit">
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
