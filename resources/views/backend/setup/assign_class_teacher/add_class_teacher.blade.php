@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

 <div class="content-wrapper">
	  <div class="container-full">

<section class="content">

		 <!-- Basic Forms -->
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Add Assign Class Teacher</h4>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

	 <form method="post" action="{{ route('assign.class.teacher.store') }}">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
                        <div class="row">
                            <div class="col-md-6">
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
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Student Class <span class="text-danger">*</span></h5>
                                    <div class="controls">
                                        <select name="class_id" id="class_id" required="" class="form-control">
                                            <option value="" selected="" disabled="">Select Class</option>
                                            @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('class_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <h5>Session/Year <span class="text-danger"></span></h5>
                            <div class="controls">
                                <select name="student_year_id" class="form-control">
                                    <option value="" selected="" disabled="">Select Session (Optional)</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                                @error('student_year_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="add_item">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <h5>Class Teacher <span class="text-danger">*</span></h5>
                                        <div class="controls">
                                            <select name="teacher_id[]" required="" class="form-control">
                                                <option value="" selected="" disabled="">Select Teacher</option>
                                                @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-top: 25px;">
                                    <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span>    		
                                </div>
                            </div>
                        </div>

							 
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
  			<div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <h5>Class Teacher <span class="text-danger">*</span></h5>
                        <div class="controls">
                            <select name="teacher_id[]" required="" class="form-control">
                                <option value="" selected="" disabled="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2" style="padding-top: 25px;">
                    <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span>
                    <span class="btn btn-danger removeeventmore"><i class="fa fa-minus-circle"></i> </span>    		
                </div>
  			</div>  			
  		</div>  		
  	</div>  	
  </div>

 <script type="text/javascript">
 	$(document).ready(function(){
 		var counter = 0;
 		$(document).on("click",".addeventmore",function(){
 			var whole_extra_item_add = $('#whole_extra_item_add').html();
 			$(this).closest(".add_item").append(whole_extra_item_add);
 			counter++;
 		});
 		$(document).on("click",'.removeeventmore',function(event){
 			$(this).closest(".delete_whole_extra_item_add").remove();
 			counter -= 1
 		});

        $(document).on('change', '#section_id', function() {
            var section_id = $(this).val();
            $('#class_id').prop('disabled', true).html('<option value="">Loading...</option>');
            
            $.ajax({
                url: "{{ route('academic.marks.classes') }}",
                type: "GET",
                data: { section_id: section_id },
                success: function(data) {
                    var html = '<option value="" selected="" disabled="">Select Class</option>';
                    $.each(data, function(key, v) {
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    $('#class_id').html(html).prop('disabled', false);
                }
            });
        });
 	});
 </script>

@endsection
