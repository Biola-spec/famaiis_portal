@extends('admin.admin_master')
@section('admin')
<style>
    .teacher-picker {
        border: 1px solid #d9e2ef;
        border-radius: 6px;
        padding: 8px;
        background: #fff;
    }

    .teacher-picker .input-group {
        margin-bottom: 6px;
    }

    .teacher-picker-select {
        font-size: 13px;
        height: 36px;
    }

    .teacher-add {
        width: 32px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .selected-teachers {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-height: 26px;
    }

    .teacher-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #cfe0f5;
        border-radius: 4px;
        background: #f4f8fd;
        color: #24415f;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
    }

    .remove-teacher {
        border: 0;
        background: transparent;
        color: #6b7f95;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        padding: 0;
    }
</style>

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
	 <select name="teacher_id[0][]" multiple class="teacher-select d-none">
		<option value="" selected="" disabled="">Select Teacher</option>
		@foreach($teachers as $teacher)
		<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
		@endforeach	 
		</select>
        <div class="teacher-picker">
            <div class="input-group">
                <select class="form-control teacher-picker-select">
                    <option value="" selected disabled>Select Teacher</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-info teacher-add" title="Add teacher"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="selected-teachers"></div>
        </div>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Class <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="class_id[0]" required="" class="form-control class-select">
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
	 <select name="subject_id[0]" required="" class="form-control">
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
	 <input type="text" name="full_mark[0]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

<div class="col-md-4">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[0]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[0]" class="form-control" > 
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
	 <select name="teacher_id[__INDEX__][]" multiple class="teacher-select d-none">
		<option value="" selected="" disabled="">Select Teacher</option>
		@foreach($teachers as $teacher)
		<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
		@endforeach	 
		</select>
        <div class="teacher-picker">
            <div class="input-group">
                <select class="form-control teacher-picker-select">
                    <option value="" selected disabled>Select Teacher</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-info teacher-add" title="Add teacher"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="selected-teachers"></div>
        </div>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-2 -->

     	<div class="col-md-4">

   <div class="form-group">
	<h5>Class <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="class_id[__INDEX__]" required="" class="form-control class-select">
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
	 <select name="subject_id[__INDEX__]" required="" class="form-control">
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
	 <input type="text" name="full_mark[__INDEX__]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

<div class="col-md-4">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[__INDEX__]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-4 -->

     	<div class="col-md-4">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[__INDEX__]" class="form-control" > 
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

        function refreshTeacherPicker($picker) {
            var selectedIds = $picker.find('.teacher-select option:selected').map(function() {
                return String($(this).val());
            }).get().filter(function(value) {
                return value !== '';
            });

            $picker.find('.teacher-picker-select option').each(function() {
                var value = String($(this).val());
                $(this).prop('disabled', value !== '' && selectedIds.indexOf(value) !== -1);
            });

            $picker.find('.teacher-picker-select').val('');
        }

        function addTeacher($picker, teacherId, teacherName) {
            if (!teacherId) {
                return;
            }

            var $hiddenSelect = $picker.find('.teacher-select');
            if ($hiddenSelect.find('option[value="' + teacherId + '"]:selected').length) {
                refreshTeacherPicker($picker);
                return;
            }

            $hiddenSelect.find('option[value="' + teacherId + '"]').prop('selected', true);
            $picker.find('.selected-teachers').append(
                '<span class="teacher-chip" data-teacher-id="' + teacherId + '">' +
                    teacherName +
                    '<button type="button" class="remove-teacher" title="Remove teacher">&times;</button>' +
                '</span>'
            );
            refreshTeacherPicker($picker);
        }

        $(document).on('click', '.teacher-add', function() {
            var $picker = $(this).closest('.teacher-picker').parent();
            var $select = $picker.find('.teacher-picker-select');
            addTeacher($picker, $select.val(), $select.find('option:selected').text());
        });

        $(document).on('change', '.teacher-picker-select', function() {
            var $picker = $(this).closest('.teacher-picker').parent();
            addTeacher($picker, $(this).val(), $(this).find('option:selected').text());
        });

        $(document).on('click', '.remove-teacher', function() {
            var $chip = $(this).closest('.teacher-chip');
            var teacherId = $chip.data('teacher-id');
            var $picker = $chip.closest('.teacher-picker').parent();
            $picker.find('.teacher-select option[value="' + teacherId + '"]').prop('selected', false);
            $chip.remove();
            refreshTeacherPicker($picker);
        });

        $('form').on('submit', function(event) {
            var valid = true;
            $(this).find('.teacher-select').each(function() {
                var selectedCount = $(this).find('option:selected').filter(function() {
                    return $(this).val() !== '';
                }).length;

                if (selectedCount === 0) {
                    valid = false;
                    $(this).closest('.controls').find('.teacher-picker').css('border-color', '#e66767');
                } else {
                    $(this).closest('.controls').find('.teacher-picker').css('border-color', '#d9e2ef');
                }
            });

            if (!valid) {
                event.preventDefault();
                alert('Please add at least one teacher for each subject row.');
            }
        });

 		$(document).on("click",".addeventmore",function(){
            counter++;
 			var whole_extra_item_add = $('#whole_extra_item_add').html().replace(/__INDEX__/g, counter);
 			$(this).closest(".add_item").append(whole_extra_item_add);
            if (classOptions !== null) {
                $(this).closest(".add_item").find('.class-select').last().html(classOptions);
            }
            if (teacherOptions !== null) {
                var $newTeacherBlock = $(this).closest(".add_item").find('.teacher-select').last().closest('.controls');
                $newTeacherBlock.find('.teacher-select, .teacher-picker-select').html(teacherOptions);
                refreshTeacherPicker($newTeacherBlock);
            }
 		});
 		$(document).on("click",'.removeeventmore',function(event){
 			$(this).closest(".delete_whole_extra_item_add").remove();
 		});

        $(document).on('change', '#section_id', function() {
            var section_id = $(this).val();
            $('.class-select').prop('disabled', true).html('<option value="">Loading...</option>');
            $('.teacher-select, .teacher-picker-select').prop('disabled', true).html('<option value="">Loading...</option>');
            
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
                    $('.teacher-select, .teacher-picker-select').html(html).prop('disabled', false);
                    $('.selected-teachers').empty();
                }
            });
        });

 	});
 </script>


@endsection
