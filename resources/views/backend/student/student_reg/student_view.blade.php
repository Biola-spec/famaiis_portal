@extends('admin.admin_master')
@section('admin')


 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
		 

		<!-- Main content -->
		<section class="content">
		  <div class="row">
		
<div class="col-12">
<div class="box bb-3 border-warning">
				  <div class="box-header">
					<h4 class="box-title">Student <strong>Search</strong></h4>
				  </div>

				  <div class="box-body">
				
		<form method="GET" action="{{ route('student.registration.view') }}">
			
			<div class="row">



<div class="col-md-3">
    <div class="form-group">
		<h5>Year <span class="text-danger"> </span></h5>
		<div class="controls">
	        <select name="year_id" class="form-control">
			    <option value="">All Years</option>
			    @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ (@$year_id == $year->id)? "selected":"" }} >{{ $year->name }}</option>
		 	    @endforeach
		    </select>
	    </div>		 
	</div>
</div> <!-- End Col md 3 --> 

<div class="col-md-3">
    <div class="form-group">
		<h5>Class <span class="text-danger"> </span></h5>
		<div class="controls">
	        <select name="class_id" class="form-control">
			    <option value="">All Classes</option>
			    @foreach($classes as $class)
			        <option value="{{ $class->id }}" {{ (@$class_id == $class->id)? "selected":"" }}>{{ $class->name }}</option>
		 	    @endforeach
		    </select>
	    </div>		 
	</div>
</div> <!-- End Col md 3 --> 

<div class="col-md-4">
    <div class="form-group">
		<h5>Search <span class="text-danger"> </span></h5>
		<div class="controls">
	        <input type="text" name="search_query" id="student-live-search" class="form-control" placeholder="Search by student name" value="{{ @$search_query }}" autocomplete="off">
	    </div>		 
	</div>
</div> <!-- End Col md 4 --> 

<div class="col-md-2" style="padding-top: 25px;">
    <input type="submit" class="btn btn-rounded btn-dark mb-5" name="search" value="Search">
</div> <!-- End Col md 2 --> 

 

				
			</div><!--  end row --> 

		</form>
 
					 
				  </div>
				</div>
	</div> <!-- // end first col 12 -->


			 

			<div class="col-12">

			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Student List</h3>
				  
				  <div style="float: right;">
					@if(Auth::user()->hasPermission('create_student'))
					<a href="{{ route('student.registration.add') }}" class="btn btn-rounded btn-success mb-5"> Add Student </a>
					@endif
					
					@if(isset($year_id) && isset($class_id))
						<a href="{{ route('student.registration.export', ['year_id' => $year_id, 'class_id' => $class_id]) }}" class="btn btn-rounded btn-info mb-5"> Export CSV </a>
					@endif

					@if(Auth::user()->hasPermission('create_student'))
					<button type="button" class="btn btn-rounded btn-warning mb-5" data-toggle="modal" data-target="#modal-import">
					  Import CSV
					</button>
					@endif
				  </div>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">

	 <table class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Name</th>
				<th>ID No</th>
				<th>Roll</th>
				<th>Year</th>
				<th>Class</th>
				<th>Image</th>
				@if(Auth::user()->role == "Admin")
				<th>Code</th>
				 @endif
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody id="student-table-body">
			@include('backend.student.student_reg.partials.student_rows', ['allData' => $allData])
						</tbody>
						<tfoot>
							 
						</tfoot>
					  </table>

					<div id="student-result-count" class="text-muted mb-2">
						@if($allData->total() > 0)
							Showing {{ $allData->firstItem() }} to {{ $allData->lastItem() }} of {{ $allData->total() }} students
						@else
							No students found
						@endif
					</div>

					<div class="mt-3" id="student-pagination">
						{{ $allData->links('pagination::bootstrap-4') }}
					</div>

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

@push('scripts')
<script type="text/javascript">
$(function () {
	var liveSearchTimer = null;
	var liveSearchRequest = null;
	var endpoint = "{{ route('student.registration.live-search') }}";

	function collectStudentFilters(extraUrl) {
		if (extraUrl) {
			return extraUrl.split('?')[1] || '';
		}

		return $.param({
			year_id: $('select[name="year_id"]').val(),
			class_id: $('select[name="class_id"]').val(),
			search_query: $('#student-live-search').val()
		});
	}

	function loadStudents(extraUrl) {
		var query = collectStudentFilters(extraUrl);

		if (liveSearchRequest) {
			liveSearchRequest.abort();
		}

		$('#student-result-count').text('Searching...');

		liveSearchRequest = $.ajax({
			url: endpoint + (query ? '?' + query : ''),
			method: 'GET',
			dataType: 'json',
			success: function (response) {
				$('#student-table-body').html(response.rows);
				$('#student-pagination').html(response.pagination);
				$('#student-result-count').text(response.showing);
			},
			error: function (xhr) {
				if (xhr.statusText !== 'abort') {
					$('#student-result-count').text('Unable to search students. Please try again.');
				}
			}
		});
	}

	$('#student-live-search').on('keyup', function () {
		clearTimeout(liveSearchTimer);
		liveSearchTimer = setTimeout(loadStudents, 180);
	});

	$('select[name="year_id"], select[name="class_id"]').on('change', function () {
		loadStudents();
	});

	$(document).on('click', '#student-pagination a', function (event) {
		event.preventDefault();
		loadStudents($(this).attr('href'));
	});
});
</script>
@endpush

<!-- Modal -->
<div class="modal center-modal fade" id="modal-import" tabindex="-1">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Import Students CSV</h5>
		<button type="button" class="close" data-dismiss="modal">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <form method="POST" action="{{ route('student.registration.import') }}" enctype="multipart/form-data">
		@csrf
		<div class="modal-body">
			<div class="form-group">
				<h5>Year <span class="text-danger">*</span></h5>
				<div class="controls">
					<select name="year_id" required="" class="form-control">
						<option value="" selected="" disabled="">Select Year</option>
						@foreach($years as $year)
							<option value="{{ $year->id }}">{{ $year->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<h5>Class <span class="text-danger">*</span></h5>
				<div class="controls">
					<select name="class_id" required="" class="form-control">
						<option value="" selected="" disabled="">Select Class</option>
						@foreach($classes as $class)
							<option value="{{ $class->id }}">{{ $class->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<h5>Section <span class="text-danger">*</span></h5>
				<div class="controls">
					<select name="section_id" required="" class="form-control">
						<option value="" selected="" disabled="">Select Section</option>
						@foreach($sections as $section)
							<option value="{{ $section->id }}">{{ $section->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<h5>Group <span class="text-danger">*</span></h5>
				<div class="controls">
					<select name="group_id" required="" class="form-control">
						<option value="" selected="" disabled="">Select Group</option>
						@foreach($groups as $group)
							<option value="{{ $group->id }}">{{ $group->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<h5>Select CSV File <span class="text-danger">*</span></h5>
				<div class="controls">
					<input type="file" name="import_file" class="form-control" required="">
				</div>
				<small class="text-muted">Format: ID No, Name, First Name, Surname, Middle Name, Email, Mobile, Gender, Address (Include Header Row)</small>
			</div>
		</div>
		<div class="modal-footer modal-footer-uniform">
			<button type="button" class="btn btn-rounded btn-secondary" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-rounded btn-primary float-right">Start Import</button>
		</div>
	  </form>
	</div>
  </div>
</div>
