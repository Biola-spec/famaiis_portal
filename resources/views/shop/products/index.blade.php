@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Product Management</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item" aria-current="page">Shop</li>
                                <li class="breadcrumb-item active" aria-current="page">Products</li>
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
                            <h3 class="box-title">Product List</h3>
                            <a href="{{ route('products.create') }}" style="float: right;" class="btn btn-rounded btn-success mb-5">Add Product</a>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $key => $product)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>
                                                <img src="{{ (!empty($product->image)) ? url('storage/'.$product->image) : url('upload/no_image.jpg') }}" style="width: 50px; height: 50px;">
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category }}</td>
                                            <td>₦{{ number_format($product->price, 2) }}</td>
                                            <td>
                                                <span class="badge badge-pill {{ $product->stock_quantity > 0 ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill {{ $product->status == 'active' ? 'badge-primary' : 'badge-secondary' }}">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('products.destroy', $product->id) }}" class="btn btn-danger" id="delete" title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
</div>

@endsection
