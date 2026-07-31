@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
	

<section class="content">

		 <!-- Basic Forms -->
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Add Assign Subject</h4>
			  
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

	 <form method="post" action="{{ route('store.assign.subject') }}">
	 	@csrf
					  <div class="row">
						<div class="col-12">
						<div class="add_item">
							
						 

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                <h5>Section Name <span class="text-danger"></span></h5>
                <div class="controls">
                <select name="section_id" id="section_id" class="form-control">
                    <option value="" selected="">All Sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach	 
                    </select>
                </div>
                </div> <!-- // end form group -->
            </div>
        </div>



        <div class="row">

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Teacher <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="teacher_id[]" required="" class="form-control teacher-select">
		<option value="" selected="" disabled="">Select Teacher</option>
		@foreach($teachers as $teacher)
		<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Class <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="class_id[]" required="" class="form-control class-select">
		<option value="" selected="" disabled="">Select Class</option>
		@foreach($classes as $class)
		<option value="{{ $class->id }}">{{ $class->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Student Subject <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="subject_id[]" required="" class="form-control">
		<option value="" selected="" disabled="">Select Subject</option>
		@foreach($subjects as $subject)
		<option value="{{ $subject->id }}">{{ $subject->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Full Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="full_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

<div class="col-md-4">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-12" style="padding-top: 5px;">
 <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span>    		
     	</div><!-- End col-md-12 -->
     	
     </div> <!-- end Row -->

 </div>	<!-- // End add_item -->
							 
		 	<div class="text-xs-right">
  <input type="submit" class="btn btn-rounded btn-info mb-5" value="Submit">
						</div>
					</form>

				</div>
				<!-- /.col -->
			  </div>
			  <!-- /.row -->
			</div>
			<!-- /.box-body -->
		  </div>
		  <!-- /.box -->

		</section>


 
 
	  
	  </div>
  </div>


  <div style="visibility: hidden;">
  	<div class="whole_extra_item_add" id="whole_extra_item_add">
  		<div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">
  			<div class="form-row">

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Teacher <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="teacher_id[]" required="" class="form-control teacher-select">
		<option value="" selected="" disabled="">Select Teacher</option>
		@foreach($teachers as $teacher)
		<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Class <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="class_id[]" required="" class="form-control class-select">
		<option value="" selected="" disabled="">Select Class</option>
		@foreach($classes as $class)
		<option value="{{ $class->id }}">{{ $class->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

	 <div class="col-md-4">

   <div class="form-group">
	<h5>Student Subject <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="subject_id[]" required="" class="form-control">
		<option value="" selected="" disabled="">Select Subject</option>
		@foreach($subjects as $subject)
		<option value="{{ $subject->id }}">{{ $subject->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Full Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="full_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

<div class="col-md-4">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-12" style="padding-top: 5px;">
 <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span>
  <span class="btn btn-danger removeeventmore"><i class="fa fa-minus-circle"></i> </span>    		
     	</div><!-- End col-md-12 -->
     	


  				
  			</div>  			
  		</div>  		
  	</div>  	
  </div>


 <script type="text/javascript">
 	$(document).ready(function(){
 		var counter = 0;
        var classOptions = null;
        var teacherOptions = null;
 		$(document).on("click",".addeventmore",function(){
 			var whole_extra_item_add = $('#whole_extra_item_add').html();
 			$(this).closest(".add_item").append(whole_extra_item_add);
            if (classOptions !== null) {
                $(this).closest(".add_item").find('.class-select').last().html(classOptions);
            }
            if (teacherOptions !== null) {
                $(this).closest(".add_item").find('.teacher-select').last().html(teacherOptions);
            }
 			counter++;
 		});
 		$(document).on("click",'.removeeventmore',function(event){
 			$(this).closest(".delete_whole_extra_item_add").remove();
 			counter -= 1
 		});

        $(document).on('change', '#section_id', function() {
            var section_id = $(this).val();
            $('.class-select').prop('disabled', true).html('<option value="">Loading...</option>');
            $('.teacher-select').prop('disabled', true).html('<option value="">Loading...</option>');
            
            $.ajax({
                url: "{{ route('academic.marks.classes') }}",
                type: "GET",
                data: { section_id: section_id },
                success: function(data) {
                    var html = '<option value="" selected="" disabled="">Select Class</option>';
                    $.each(data, function(key, v) {
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    classOptions = html;
                    $('.class-select').html(html).prop('disabled', false);
                }
            });

            $.ajax({
                url: "{{ route('assign.subject.teachers') }}",
                type: "GET",
                data: { section_id: section_id },
                success: function(data) {
                    var html = '<option value="" selected="" disabled="">Select Teacher</option>';
                    $.each(data, function(key, v) {
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    teacherOptions = html;
                    $('.teacher-select').html(html).prop('disabled', false);
                }
            });
        });

 	});
 </script>


@endsection
