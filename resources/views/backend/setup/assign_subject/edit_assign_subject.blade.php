@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        width: 38px;
        height: 36px;
        padding: 0;
        line-height: 34px;
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
			  <h4 class="box-title">Edit Assign Subject</h4>
			  
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

 <form method="post" action="{{ route('update.assign.subject',$editData[0]->class_id) }}">
	 	@csrf
					  <div class="row">
						<div class="col-12">
						<div class="add_item">
							
						 

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <h5>Section Name <span class="text-danger"></span></h5>
                <div class="controls">
                <select name="section_id" id="section_id" class="form-control">
                    <option value="" {{ ($editData['0']->section_id == null)? "selected":"" }}>All Sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ ($editData['0']->section_id == $section->id)? "selected":"" }} >{{ $section->name }}</option>
                    @endforeach	 
                    </select>
                </div>
                </div> <!-- // end form group -->
            </div>

            <div class="col-md-6">
                <div class="form-group">
                <h5>Class Name <span class="text-danger">*</span></h5>
                <div class="controls">
                <select name="class_id" id="class_id" required="" class="form-control">
                    <option value="" selected="" disabled="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ ($editData['0']->class_id == $class->id)? "selected":"" }} >{{ $class->name }}</option>
                    @endforeach	 
                    </select>
                </div>
                </div> <!-- // end form group -->
            </div>
        </div>


@foreach($editData as $rowIndex => $edit)
@php
    $assignedTeacherIds = $edit->assigned_teacher_ids ?? array_filter([$edit->teacher_id]);
@endphp
  <div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">
        <div class="row">
     	<div class="col-md-3">

   <div class="form-group">
	<h5>Student Subject <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="subject_id[{{ $rowIndex }}]" required="" class="form-control">
		<option value="" selected="" disabled="">Select Subject</option>
		@foreach($subjects as $subject)
		<option value="{{ $subject->id }}" {{ ($edit->subject_id == $subject->id)? "selected": ""  }}>{{ $subject->name }}</option>
		@endforeach	 
		</select>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-3 -->

     	<div class="col-md-3">

   <div class="form-group">
	<h5>Teacher <span class="text-danger">*</span></h5>
	<div class="controls">
	 <select name="teacher_id[{{ $rowIndex }}][]" multiple class="teacher-select d-none">
		<option value="" selected="" disabled="">Select Teacher</option>
		@foreach($teachers as $teacher)
		<option value="{{ $teacher->id }}" {{ in_array($teacher->id, $assignedTeacherIds) ? "selected": ""  }}>{{ $teacher->name }}</option>
		@endforeach	 
		</select>
        <div class="teacher-picker">
            <div class="input-group">
                <select class="form-control teacher-picker-select">
                    <option value="" selected disabled>Select Teacher</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ in_array($teacher->id, $assignedTeacherIds) ? "disabled": "" }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-info teacher-add" title="Add teacher"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="selected-teachers">
                @foreach($teachers->whereIn('id', $assignedTeacherIds) as $teacher)
                    <span class="teacher-chip" data-teacher-id="{{ $teacher->id }}">
                        {{ $teacher->name }}
                        <button type="button" class="remove-teacher" title="Remove teacher">&times;</button>
                    </span>
                @endforeach
            </div>
        </div>
	 </div>
          </div> <!-- // end form group -->
     	</div> <!-- End col-md-3 -->


     	<div class="col-md-2">     		
      <div class="form-group">
		<h5>Full Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="full_mark[{{ $rowIndex }}]" value="{{ $edit->full_mark }}" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->

<div class="col-md-2">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[{{ $rowIndex }}]" value="{{ $edit->pass_mark }}" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->

     	<div class="col-md-2">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[{{ $rowIndex }}]" value="{{ $edit->subjective_mark }}" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->


     	<div class="col-md-2" style="padding-top: 25px;">
 <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span> <span class="btn btn-danger removeeventmore"><i class="fa fa-minus-circle"></i> </span> 
     	</div><!-- End col-md-5 -->
     	
     </div> <!-- end Row -->
     </div><!-- // End Remove Delete  -->
 @endforeach




 </div>	<!-- // End add_item -->
							 
		 	<div class="text-xs-right">
  <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update">
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

	 <div class="col-md-3">

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
     	</div> <!-- End col-md-3 -->

     	<div class="col-md-3">

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
     	</div> <!-- End col-md-3 -->


     	<div class="col-md-2">     		
      <div class="form-group">
		<h5>Full Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="full_mark[__INDEX__]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->

<div class="col-md-2">     		
      <div class="form-group">
		<h5>Pass Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="pass_mark[__INDEX__]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->

     	<div class="col-md-2">     		
      <div class="form-group">
		<h5>Subjective Mark <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="subjective_mark[__INDEX__]" class="form-control" > 
	  </div>		 
	</div>
     	</div><!-- End col-md-5 -->

     	<div class="col-md-2" style="padding-top: 25px;">
 <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i> </span>
  <span class="btn btn-danger removeeventmore"><i class="fa fa-minus-circle"></i> </span>    		
     	</div><!-- End col-md-2 -->
     	


  				
  			</div>  			
  		</div>  		
  	</div>  	
  </div>


 <script type="text/javascript">
 	$(document).ready(function(){
 		var counter = {{ count($editData) }};

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
            $('.teacher-select').each(function() {
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
 		});
 		$(document).on("click",'.removeeventmore',function(event){
 			$(this).closest(".delete_whole_extra_item_add").remove();
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
