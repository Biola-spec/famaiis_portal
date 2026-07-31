@extends('admin.admin_master')
@section('admin')

 <div class="content-wrapper">
	  <div class="container-full">
		<section class="content">
		  <div class="row">
			<div class="col-12">
			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Parent List</h3>
	<a href="{{ route('parent.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Parent</a>			  
				</div>
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>
				<th>Name</th>
				<th>Email</th>
				<th>Code</th>
				<th>Children</th>
				<th width="25%">Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $parent )
			<tr>
				<td>{{ $key+1 }}</td>
				<td>{{ $parent->name }}</td>
				<td>{{ $parent->email }}</td>
				<td>{{ $parent->code }}</td>
				<td>
                    @foreach($parent->children as $child)
                        <span class="badge badge-info">{{ $child->name }} ({{ $child->id_no }})</span>
                    @endforeach
                </td>
				<td>
<a href="{{ route('parent.edit',$parent->id) }}" class="btn btn-info">Edit</a>
<a href="{{ route('parent.delete',$parent->id) }}" class="btn btn-danger" id="delete">Delete</a>
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

@endsection
