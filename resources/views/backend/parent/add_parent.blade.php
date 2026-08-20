@extends('admin.admin_master')
@section('admin')
<!-- Select2 CSS -->
<link href="{{ asset('assets/vendor_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<style>
    /* Theme-aware Select2 custom styles */
    .select2-container--default .select2-selection--single {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 8px !important;
        height: 40px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: var(--text-main) !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-color) !important;
        outline: 0 !important;
    }
    .select2-container--default .select2-selection--multiple {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 8px !important;
        min-height: 40px;
        padding: 4px 8px;
    }
    
    /* Tags / Selected items */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-color) !important;
        border: 1px solid var(--primary-color) !important;
        color: #ffffff !important;
        border-radius: 4px !important;
        padding: 2px 8px !important;
        margin-top: 4px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.8) !important;
        margin-right: 5px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffffff !important;
    }
    
    /* Search input inside container */
    .select2-container--default .select2-search--inline .select2-search__field {
        color: var(--text-main) !important;
        height: 28px !important;
        margin-top: 4px !important;
    }
    
    /* Dropdown container */
    .select2-dropdown {
        background-color: var(--card-bg) !important;
        color: var(--text-main) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        overflow: hidden;
    }
    
    /* Search field inside dropdown */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: var(--bg-body) !important;
        color: var(--text-main) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
    }
    
    /* Results Options styling */
    .select2-container--default .select2-results__option {
        padding: 8px 12px !important;
        color: var(--text-main) !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: var(--primary-light) !important;
        color: var(--primary-color) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary-color) !important;
        color: #ffffff !important;
    }
    
    /* Optgroup styling */
    .select2-container--default .select2-results__group {
        font-weight: 700 !important;
        color: var(--primary-color) !important;
        background-color: var(--bg-body) !important;
        padding: 6px 12px !important;
        border-bottom: 1px solid var(--card-border) !important;
        border-top: 1px solid var(--card-border) !important;
    }
</style>

 <div class="content-wrapper">
	  <div class="container-full">
		<section class="content">
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Add Parent</h4>
			</div>
			<div class="box-body">
			  <div class="row">
				<div class="col">
	 <form method="post" action="{{ route('parent.store') }}" enctype="multipart/form-data">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<h5>Parent Name <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="text" name="name" class="form-control" required="">
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<h5>Parent Email <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="email" name="email" class="form-control" required="">
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<h5>Password <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="password" name="password" class="form-control" required="">
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="form-group">
            <h5>Select Child (Student) <span class="text-secondary">(Type name/ID to search)</span></h5>
            <div class="controls">
                <select id="student_select" class="form-control select2" style="width: 100%;">
                    <option value="" disabled selected>Search and select student...</option>
                    @foreach($groupedStudents as $className => $studentGroup)
                        <optgroup label="{{ $className }}">
                            @foreach($studentGroup as $student)
                                <option value="{{ $student->id }}" data-name="{{ $student->name }}" data-id-no="{{ $student->id_no }}" data-class="{{ $student->class_name }}">
                                    {{ $student->name }} ({{ $student->id_no }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-2" style="padding-top: 25px;">
        <button type="button" id="add_child_btn" class="btn btn-success btn-block" style="height: 40px; font-weight: 700;">
            <i class="fa fa-plus-circle"></i> Add
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5>Assigned Children</h5>
        <div id="children_list_container">
            <table class="table table-bordered table-striped" id="selected_children_table" style="display: none;">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>ID Number</th>
                        <th>Class</th>
                        <th style="width: 15%;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="selected_children_tbody">
                    <!-- Dynamic Rows Go Here -->
                </tbody>
            </table>
            <div id="no_children_alert" class="alert alert-secondary text-center py-3" style="background: var(--bg-body); border: 1px dashed var(--card-border); color: var(--text-muted); border-radius: 8px;">
                No children assigned yet. Use the dropdown and "+ Add" button above to link children.
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <h5>Profile Image</h5>
            <div class="controls">
                <input type="file" name="image" class="form-control" id="image">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <div class="controls">
                <img id="showImage" src="{{ url('upload/no_image.jpg') }}" style="width: 100px; height: 100px; border: 1px solid #000000;">
            </div>
        </div>
    </div>
</div>

<br>
						<div class="text-xs-right">
	 <input type="submit" class="btn btn-rounded btn-info mb-5" value="Submit">
						</div>
					</form>
				</div>
			  </div>
			</div>
		  </div>
		</section>
	  </div>
  </div>

@push('scripts')
<script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.min.js') }}"></script>

<script type="text/javascript">
	$(document).ready(function(){
		// Initialize Select2
		$('.select2').select2({
			placeholder: 'Search and select student...',
			allowClear: true
		});

		// Add Child Button click
		$('#add_child_btn').click(function(){
			var studentSelect = $('#student_select');
			var selectedOption = studentSelect.find('option:selected');
			var id = studentSelect.val();
			
			if(!id) {
				return;
			}
			
			var name = selectedOption.data('name');
			var idNo = selectedOption.data('id-no');
			var className = selectedOption.data('class');
			
			// Check if already added
			if ($('#child-row-' + id).length > 0) {
				return;
			}
			
			// Add row to table
			var rowHtml = `
				<tr id="child-row-${id}">
					<td>
						${name}
						<input type="hidden" name="student_id[]" value="${id}">
					</td>
					<td>${idNo}</td>
					<td>${className}</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger btn-sm remove-child-btn" data-id="${id}" style="padding: 2px 8px !important; font-size: 11px !important;">
							<i class="fa fa-trash"></i> Remove
						</button>
					</td>
				</tr>
			`;
			
			$('#selected_children_tbody').append(rowHtml);
			$('#selected_children_table').show();
			$('#no_children_alert').hide();
			
			// Reset Select2 selection
			studentSelect.val(null).trigger('change');
		});

		// Remove Child click
		$(document).on('click', '.remove-child-btn', function(){
			var id = $(this).data('id');
			$('#child-row-' + id).remove();
			
			if ($('#selected_children_tbody tr').length === 0) {
				$('#selected_children_table').hide();
				$('#no_children_alert').show();
			}
		});

		$('#image').change(function(e){
			var reader = new FileReader();
			reader.onload = function(e){
				$('#showImage').attr('src',e.target.result);
			}
			reader.readAsDataURL(e.target.files[0]);
		});
	});
</script>
@endpush
@endsection
