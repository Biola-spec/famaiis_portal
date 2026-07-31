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
				  <h3 class="box-title">School Section List</h3>
	<a href="{{ route('school.section.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Section</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="5%">SL</th>  
								<th>Section Name</th> 
								<th>Code</th>
								<th>Head Teacher</th>
								<th>Head Title</th>
								<th width="25%">Action</th>
								 
							</tr>
						</thead>
						<tbody>
							@foreach($allData as $key => $section )
							<tr>
								<td>{{ $key+1 }}</td>
								<td> {{ $section->name }}</td>				
								<td> {{ $section->code }}</td>
								<td> {{ $section['headTeacher']['name'] ?? 'N/A' }}</td>
								<td> {{ $section->head_title }}</td>
								<td>
<a href="{{ route('school.section.edit',$section->id) }}" class="btn btn-info">Edit</a>
<a href="{{ route('school.section.delete',$section->id) }}" class="btn btn-danger" id="delete">Delete</a>

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
