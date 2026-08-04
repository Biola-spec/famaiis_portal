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
				  <h3 class="box-title">Assign Subject List</h3>
	<a href="{{ route('assign.subject.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Assign Subject</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Class Name</th> 
                <th>Section</th>
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $assign )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $assign['student_class']['name'] ?? 'N/A' }}</td>
                <td> {{ $assign->section->name ?? 'All Sections' }}</td>				 
				<td>
<a href="{{ route('assign.subject.edit', ['class_id' => $assign->class_id, 'section_id' => $assign->section_id] ) }}" class="btn btn-info">Edit</a>
<a href="{{ route('assign.subject.details', ['class_id' => $assign->class_id, 'section_id' => $assign->section_id] ) }}" class="btn btn-primary" >Details</a>

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

			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Teacher Subject & Class Overview</h3>
				</div>
				<div class="box-body">
					<div class="table-responsive">
					  <table id="teacherAssignmentOverview" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>
				<th>Teacher</th>
				<th>Subject</th>
				<th>Class</th>
				<th>Section</th>
				<th>Full Mark</th>
				<th>Pass Mark</th>
				<th>Subjective Mark</th>
				<th width="18%">Action</th>
			</tr>
		</thead>
		<tbody>
			@forelse($allAssignments as $key => $assignment)
			<tr>
				<td>{{ $key+1 }}</td>
				<td>
					@if($assignment->assignedTeachers->isNotEmpty())
						{{ $assignment->assignedTeachers->pluck('name')->join(', ') }}
					@else
						{{ $assignment->teacher->name ?? 'Not Assigned' }}
					@endif
				</td>
				<td>{{ $assignment->school_subject->name ?? 'N/A' }}</td>
				<td>{{ $assignment->student_class->name ?? 'N/A' }}</td>
				<td>{{ $assignment->section->name ?? 'All Sections' }}</td>
				<td>{{ $assignment->full_mark }}</td>
				<td>{{ $assignment->pass_mark }}</td>
				<td>{{ $assignment->subjective_mark }}</td>
				<td>
					<a href="{{ route('assign.subject.edit', ['class_id' => $assignment->class_id, 'section_id' => $assignment->section_id]) }}" class="btn btn-sm btn-info">Edit</a>
					@if($assignment->teacher_id)
					<a href="{{ route('assign.subject.teacher.pdf', $assignment->teacher_id) }}" target="_blank" class="btn btn-sm btn-warning" title="Download Teacher PDF"><i class="fa fa-file-pdf-o"></i> PDF</a>
					@endif
					<a href="{{ route('assign.subject.delete', $assignment->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="8" class="text-center">No subject assignments found.</td>
			</tr>
			@endforelse
		</tbody>
					  </table>
					</div>
				</div>
			  </div>

			       
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>





@endsection
