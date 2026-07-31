@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Edit Product</h3>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title text-primary">Edit: {{ $product->name }}</h4>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Product Name <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Category <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <input type="text" name="category" class="form-control" value="{{ $product->category }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Price (₦) <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Stock Quantity <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <input type="number" name="stock_quantity" class="form-control" value="{{ $product->stock_quantity }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Status <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="status" required class="form-control">
                                                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Description</h5>
                                            <div class="controls">
                                                <textarea name="description" rows="3" class="form-control">{{ $product->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Product Image</h5>
                                            @if($product->image)
                                                <div class="mb-2">
                                                    <img src="{{ url('storage/'.$product->image) }}" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                            @endif
                                            <div class="controls">
                                                <input type="file" name="image" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs-right">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update Product">
                                    <a href="{{ route('products.index') }}" class="btn btn-rounded btn-secondary mb-5">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
</div>

@endsection
