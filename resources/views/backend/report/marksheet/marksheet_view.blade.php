@extends('admin.admin_master')
@section('admin')

 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">
		  <div class="row">

<div class="col-12">
<div class="box bb-3 border-warning">
				  <div class="box-header">
					<h4 class="box-title">Manage <strong>MarkSheet Generate</strong></h4>
				  </div>

				  <div class="box-body">
				
			<div class="row">

<div class="col-md-3">
 		 <div class="form-group">
		<h5>Year <span class="text-danger"> *</span></h5>
		<div class="controls">
	 <select name="year_id" id="year_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Year</option>
			 @foreach($years as $year)
  <option value="{{ $year->id }}" {{ optional(getCurrentSession())->id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
 			</div> <!-- End Col md 3 --> 

<div class="col-md-3">
 		 <div class="form-group">
		<h5>Section <span class="text-danger"></span></h5>
		<div class="controls">
	 <select name="section_id" id="section_id" class="form-control">
			<option value="" selected="" disabled="">Select Section</option>
            @foreach($sections as $section)
                <option value="{{ $section->id }}">{{ $section->name }}</option>
            @endforeach
		</select>
	  </div>		 
	  </div>
 			</div> <!-- End Col md 3 --> 

<div class="col-md-3">
 		 <div class="form-group">
		<h5>Term <span class="text-danger"> *</span></h5>
		<div class="controls">
 <select name="term" id="term"  required="" class="form-control">
			<option value="" selected="" disabled="">Select Term</option>
			<option value="1st Term">1st Term</option>
			<option value="2nd Term">2nd Term</option>
			<option value="3rd Term">3rd Term</option>
		</select>
	  </div>		 
	  </div>
 			</div> <!-- End Col md 3 --> 

 		<div class="col-md-3">
 		 <div class="form-group">
		<h5>Class <span class="text-danger"> *</span></h5>
		<div class="controls">
	 <select name="class_id" id="class_id"  required="" class="form-control">
			<option value="" selected="" disabled="">Select Class</option>
			 @foreach($classes as $class)
			<option value="{{ $class->id }}">{{ $class->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
 			</div> <!-- End Col md 3 --> 

 <div class="col-md-3" style="padding-top: 25px;">
    <a id="search" class="btn btn-primary" name="search"> Find Students</a>
 </div> <!-- End Col md 3 --> 			

			</div><!--  end row --> 


<!--  ////////////////// Marksheet Student List Start //////////////////////// -->

 <div class="row d-none" id="marksheet-generate">
 	<div class="col-md-12">
 		<div class="box">
 			<div class="box-body">
 				<table class="table table-bordered table-striped" style="width: 100%">
 					<thead>
 						<tr>
 							<th>ID No</th>
 							<th>Student Name </th>
 							<th>Gender</th>
 							<th>Action</th>
 						</tr>
 					</thead>
 					<tbody id="marksheet-generate-tr">
 						
 					</tbody>
 					
 				</table>
 				
 			</div>
 		</div>
 	</div>
 </div>


<script type="text/javascript">
  $(document).on('change', '#section_id', function () {
    const sectionId = $(this).val();
    $('#class_id').prop('disabled', true).html('<option value="">Loading...</option>');

    $.get("{{ route('academic.marks.classes') }}", { section_id: sectionId }, function (classes) {
        let html = '<option value="" selected disabled>Select Class</option>';
        $.each(classes, function (_, studentClass) {
            html += `<option value="${studentClass.id}">${studentClass.name}</option>`;
        });
        $('#class_id').html(html).prop('disabled', false);
    });
  });

  $(document).on('click','#search',function(){
    var year_id = $('#year_id').val();
    var class_id = $('#class_id').val();
    var section_id = $('#section_id').val();
    var term = $('#term').val();
    
     if (!year_id || !class_id || !term) {
        alert("Please select Year, Class and Term");
        return false;
    }

     $.ajax({
      url: "{{ route('student.marks.getstudents') }}",
      type: "GET",
      data: {'year_id':year_id,'class_id':class_id, 'section_id':section_id},
      success: function (data) {
        $('#marksheet-generate').removeClass('d-none');
        var html = '';
        $.each( data, function(key, v){
          html += '<tr>'+
          '<td>'+v.student.id_no+'</td>'+
          '<td>'+v.student.name+'</td>'+
          '<td>'+(v.student.gender || 'N/A')+'</td>'+
          '<td>'+
          	'<a class="btn btn-sm btn-info" href="{{ url("reports/marksheet/generate/get") }}?year_id='+year_id+'&class_id='+class_id+'&section_id='+(section_id || '')+'&term='+term+'&id_no='+v.student.id_no+'" target="_blank"> <i class="fa fa-eye"></i> View Result</a>'+
          '</td>'+
          '</tr>';
        });
        $('#marksheet-generate-tr').html(html);
      }
    });
  });
</script>

			       
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>

@endsection
