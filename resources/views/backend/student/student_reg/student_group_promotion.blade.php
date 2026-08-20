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
					<h4 class="box-title">Student <strong>Group Promotion</strong></h4>
				  </div>

				  <div class="box-body">
				
		<form method="post" action="{{ route('student.promotion.group.store') }}">
			@csrf
			<div class="row">

<div class="col-md-3">
    <div class="form-group">
		<h5>Current Session <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="current_year_id" id="current_year_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Session</option>
			 @foreach($years as $year)
                <option value="{{ $year->id }}" >{{ $year->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
</div> <!-- End Col md 3 --> 

<div class="col-md-3">
    <div class="form-group">
		<h5>Current Class <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="current_class_id" id="current_class_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Class</option>
			 @foreach($classes as $class)
			    <option value="{{ $class->id }}">{{ $class->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
</div> <!-- End Col md 3 --> 

<div class="col-md-2" style="padding-top: 25px;">
    <button type="button" id="search" class="btn btn-primary">Search Students</button>
</div> <!-- End Col md 2 --> 		

<div class="col-md-2">
    <div class="form-group">
		<h5>Target Session <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="target_year_id" id="target_year_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Session</option>
			 @foreach($years as $year)
                <option value="{{ $year->id }}" >{{ $year->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
</div> <!-- End Col md 2 --> 

<div class="col-md-2">
    <div class="form-group">
		<h5>Target Class <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="target_class_id" id="target_class_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Class</option>
			 @foreach($classes as $class)
			    <option value="{{ $class->id }}">{{ $class->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
</div> <!-- End Col md 2 --> 

			</div><!--  end row --> 


 <!--  ////////////////// Student list table /////////////  -->


 <div class="row d-none" id="student-list-div">
 	<div class="col-md-12">
        <hr>
        <div class="mb-3">
            <input type="checkbox" id="select_all" class="filled-in chk-col-primary">
            <label for="select_all"><strong>Select All Students</strong></label>
        </div>
 		<table class="table table-bordered table-striped" style="width: 100%">
 			<thead>
 				<tr>
 					<th width="5%">Select</th>
 					<th>ID No</th>
 					<th>Student Name </th>
 					<th>Current Roll</th>
 					<th>Gender</th>
 				 </tr> 				
 			</thead>
 			<tbody id="student-list-body">
 				
 			</tbody>
 		</table>

        <div class="text-xs-right">
            <input type="submit" class="btn btn-rounded btn-info mb-5" value="Promote Selected Students">
        </div>
 		
 	</div>
 	
 </div>


		</form> 

			       
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
      $('#search').on('click', function() {
          var year_id = $('#current_year_id').val();
          var class_id = $('#current_class_id').val();

          if(!year_id || !class_id){
              alert('Please select current session and class');
              return;
          }

          $.ajax({
              url: "{{ route('student.promotion.group.getstudents') }}",
              type: "GET",
              data: {'year_id': year_id, 'class_id': class_id},
              success: function (data) {
                  $('#student-list-div').removeClass('d-none');
                  var html = '';
                  if (data && data.length > 0) {
                      $.each(data, function(key, v) {
                          if (v.student) {
                              html +=
                              '<tr>'+
                              '<td><input type="checkbox" name="student_ids[]" id="student_'+v.student_id+'" value="'+v.student_id+'" class="student_checkbox filled-in chk-col-primary"><label for="student_'+v.student_id+'"></label></td>'+
                              '<td>'+(v.student.id_no || '')+'</td>'+
                              '<td>'+(v.student.name || '')+'</td>'+
                              '<td>'+(v.roll || 'N/A')+'</td>'+
                              '<td>'+(v.student.gender || '')+'</td>'+
                              '</tr>';
                          }
                      });
                  } else {
                      html = '<tr><td colspan="5" class="text-center text-danger">No students found for the selected session and class.</td></tr>';
                  }
                  $('#student-list-body').html(html);
              },
              error: function(err) {
                  console.error('Error fetching students:', err);
                  alert('Failed to load students. Please try again.');
              }
          });
      });

      // Select all functionality
      $(document).on('change', '#select_all', function() {
          $('.student_checkbox').prop('checked', $(this).prop('checked'));
      });
  });
</script>
@endpush

@endsection
