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
				  <h3 class="box-title">Assign Class Teacher List</h3>
	<a href="{{ route('assign.class.teacher.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Assign Class Teacher</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Class Name</th> 
                <th>Section Name</th>
                <th>Session/Year</th>
                <th>Teacher Name</th>
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $assign )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $assign['studentClass']['name'] ?? 'N/A' }}</td>
                <td> {{ $assign['section']['name'] ?? 'All Sections' }}</td>
                <td> {{ $assign['studentYear']['name'] ?? 'Global/All' }}</td>
                <td> {{ $assign['teacher']['name'] ?? 'N/A' }}</td>
				<td>
<a href="{{ route('assign.class.teacher.edit',$assign->id) }}" class="btn btn-info">Edit</a>
<a href="{{ route('assign.class.teacher.delete',$assign->id) }}" class="btn btn-danger" id="delete">Delete</a>

				</td>
				 
			</tr>
			@endforeach
							 
						</tbody>
						<tfoot>
							 
						</tfoot>
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
