@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Add Student </h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col">
                            <form method="post" action="{{ route('store.student.registration') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">	
                                        
                                        <div class="row"> <!-- 1st Row -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>First Name <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="first_name" class="form-control" required="" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Surname <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="surname" class="form-control" required="" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Middle Name</h5>
                                                    <div class="controls">
                                                        <input type="text" name="middle_name" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>ID No</h5>
                                                    <div class="controls">
                                                        <input type="text" name="id_no" class="form-control" placeholder="Enter ID Manually"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 1st Row -->

                                        <div class="row"> <!-- 2nd Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Mobile Number</h5>
                                                    <div class="controls">
                                                        <input type="text" name="mobile" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Address</h5>
                                                    <div class="controls">
                                                        <input type="text" name="address" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Gender <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="gender" id="gender" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 2nd Row -->

                                        <div class="row"> <!-- 3rd Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>NIN</h5>
                                                    <div class="controls">
                                                        <input type="text" name="nin" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Country <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="country" id="country_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Country</option>
                                                            <option value="Nigeria">Nigeria</option>
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>State <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="state" id="state_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select State</option>
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 3rd Row -->

                                        <div class="row"> <!-- 4th Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>LGA <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="lga" id="lga_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select LGA</option>
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Section <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="section_ids[]" id="section_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Section</option>
                                                            @foreach($sections as $section)
                                                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Date of Birth</h5>
                                                    <div class="controls">
                                                        <input type="date" name="dob" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 4th Row -->

                                        <div class="row"> <!-- 5th Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Discount</h5>
                                                    <div class="controls">
                                                        <input type="text" name="discount" class="form-control" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Academic Session <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="year_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Session</option>
                                                            @foreach($years as $year)
                                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Parent</h5>
                                                    <div class="controls">
                                                        <select name="parent_id" id="parent_id" class="form-control select2">
                                                            <option value="" selected="" disabled="">Select Parent</option>
                                                            @foreach($parents as $parent)
                                                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 5th Row -->

                                        <div class="row"> <!-- 6th Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Class <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="class_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Class</option>
                                                            @foreach($classes as $class)
                                                                <option value="{{ $class->id }}" data-section-id="{{ $class->section_id }}">{{ $class->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Group</h5>
                                                    <div class="controls">
                                                        <select name="group_id" class="form-control">
                                                            <option value="" selected="" disabled="">Select Group</option>
                                                            @foreach($groups as $group)
                                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 6th Row -->

                                        <div class="row"> <!-- New Login Credentials Row -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Email <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="email" name="email" class="form-control" required="" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Password <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="password" name="password" class="form-control" required="" > 
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End Login Credentials Row -->

                                        <div class="row"> <!-- 7th Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Profile Image</h5>
                                                    <div class="controls">
                                                        <input type="file" name="image" class="form-control" id="image" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <img id="showImage" src="{{ url('upload/no_image.jpg') }}" style="width: 100px; height: 100px; border: 1px solid #000000;"> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- End 7th Row -->

                                    </div>
                                </div>
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

<script type="text/javascript">
	$(document).ready(function(){
		$('#image').change(function(e){
			var reader = new FileReader();
			reader.onload = function(e){
				$('#showImage').attr('src',e.target.result);
			}
			reader.readAsDataURL(e.target.files['0']);
		});

        // Location Logic
        $('#country_id').on('change', function() {
            const country = $(this).val();
            const stateSelect = $('#state_id');
            const lgaSelect = $('#lga_id');
            
            stateSelect.html('<option value="" selected="" disabled="">Loading...</option>');
            lgaSelect.html('<option value="" selected="" disabled="">Select LGA</option>');

            fetch('{{ asset('locations.json') }}')
                .then(response => response.json())
                .then(data => {
                    const states = data[country];
                    stateSelect.html('<option value="" selected="" disabled="">Select State</option>');
                    if (states) {
                        Object.keys(states).forEach(state => {
                            stateSelect.append(`<option value="${state}">${state}</option>`);
                        });
                    }
                });
        });

        $('#state_id').on('change', function() {
            const country = $('#country_id').val();
            const state = $(this).val();
            const lgaSelect = $('#lga_id');
            
            lgaSelect.html('<option value="" selected="" disabled="">Loading...</option>');

            fetch('{{ asset('locations.json') }}')
                .then(response => response.json())
                .then(data => {
                    const lgas = data[country][state];
                    lgaSelect.html('<option value="" selected="" disabled="">Select LGA</option>');
                    if (lgas) {
                        lgas.forEach(lga => {
                            lgaSelect.append(`<option value="${lga}">${lga}</option>`);
                        });
                    }
                });
        });

        // Section to Class Filtering
        const classSelect = $('select[name="class_id"]');
        const originalClassOptions = classSelect.find('option').clone();
        const initialClassVal = classSelect.val();

        function filterClasses(sectionId) {
            classSelect.html(originalClassOptions.first().clone());

            if (sectionId) {
                originalClassOptions.each(function() {
                    const option = $(this);
                    if (option.data('section-id') == sectionId) {
                        classSelect.append(option.clone());
                    }
                });
            } else {
                originalClassOptions.each(function(index) {
                    if (index > 0) {
                        classSelect.append($(this).clone());
                    }
                });
            }
            
            if (initialClassVal) {
                classSelect.val(initialClassVal);
            }
        }

        $('#section_id').on('change', function() {
            filterClasses($(this).val());
        });

        if ($('#section_id').val()) {
            filterClasses($('#section_id').val());
        }
	});
</script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#parent_id').select2({
            placeholder: 'Search Parent...',
            allowClear: true
        });
    });
</script>

@endsection
