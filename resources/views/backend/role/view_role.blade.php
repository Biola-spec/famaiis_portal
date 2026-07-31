@extends('admin.admin_master')
@section('admin')

 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
		<!-- Main content -->
		<section class="content">
		  <div class="row">
			<div class="col-12">
			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Role List</h3>
	<a href="{{ route('role.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Role</a>			  
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>
				<th>Name</th>
				<th>Description</th>
				<th width="25%">Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $role )
			<tr>
				<td>{{ $key+1 }}</td>
				<td>{{ $role->name }}</td>
				<td>{{ $role->description }}</td>
				<td>
<a href="{{ route('role.edit',$role->id) }}" class="btn btn-info">Edit</a>
<a href="{{ route('role.delete',$role->id) }}" class="btn btn-danger" id="delete">Delete</a>
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
