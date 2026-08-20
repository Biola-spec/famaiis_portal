@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Edit Student </h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col">
                            <form method="post" action="{{ route('update.student.registration', $editData->student_id) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $editData->id }}">
                                <div class="row">
                                    <div class="col-12">	
                                        
                                        <div class="row"> <!-- 1st Row -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>First Name <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="first_name" class="form-control" required="" value="{{ $editData['student']['first_name'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Surname <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="surname" class="form-control" required="" value="{{ $editData['student']['surname'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Middle Name</h5>
                                                    <div class="controls">
                                                        <input type="text" name="middle_name" class="form-control" value="{{ $editData['student']['middle_name'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>ID No</h5>
                                                    <div class="controls">
                                                        <input type="text" name="id_no" class="form-control" value="{{ $editData['student']['id_no'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 1st Row -->

                                        <div class="row"> <!-- 2nd Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Mobile Number</h5>
                                                    <div class="controls">
                                                        <input type="text" name="mobile" class="form-control" value="{{ $editData['student']['mobile'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Address</h5>
                                                    <div class="controls">
                                                        <input type="text" name="address" class="form-control" value="{{ $editData['student']['address'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Gender <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="gender" id="gender" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Gender</option>
                                                            <option value="Male" {{ $editData['student']['gender'] == 'Male' ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ $editData['student']['gender'] == 'Female' ? 'selected' : '' }}>Female</option>
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
                                                        <input type="text" name="nin" class="form-control" value="{{ $editData['student']['nin'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Country <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="country" id="country_id" required="" class="form-control">
                                                            <option value="" selected="" disabled="">Select Country</option>
                                                            <option value="Nigeria" {{ $editData['student']['country'] == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>State <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <select name="state" id="state_id" required="" class="form-control">
                                                            <option value="{{ $editData['student']['state'] }}" selected>{{ $editData['student']['state'] }}</option>
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
                                                            <option value="{{ $editData['student']['lga'] }}" selected>{{ $editData['student']['lga'] }}</option>
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
                                                                <option value="{{ $section->id }}" {{ $editData['student']->sections->contains($section->id) ? 'selected' : '' }}>{{ $section->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Date of Birth</h5>
                                                    <div class="controls">
                                                        <input type="date" name="dob" class="form-control" value="{{ $editData['student']['dob'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                        </div> <!-- End 4th Row -->

                                        <div class="row"> <!-- 5th Row -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h5>Discount</h5>
                                                    <div class="controls">
                                                        <input type="text" name="discount" class="form-control" value="{{ $editData['discount']['discount'] }}"> 
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
                                                                <option value="{{ $year->id }}" {{ $editData->year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
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
                                                            @php
                                                                $currentParent = $editData->student->parents->first();
                                                            @endphp
                                                            @foreach($parents as $parent)
                                                                <option value="{{ $parent->id }}" {{ ($currentParent && $currentParent->id == $parent->id) ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->email }})</option>
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
                                                                <option value="{{ $class->id }}" data-section-id="{{ $class->section_id }}" {{ $editData->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Group</h5>
                                                    <div class="controls">
                                                        <select name="group_id" class="form-control">
                                                            <option value="" selected="" disabled="">Select Group</option>
                                                            @foreach($groups as $group)
                                                                <option value="{{ $group->id }}" {{ $editData->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
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
                                                        <input type="email" name="email" class="form-control" required="" value="{{ $editData['student']['email'] }}"> 
                                                    </div>		 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Password <span class="text-secondary">(Leave blank to keep current)</span></h5>
                                                    <div class="controls">
                                                        <input type="password" name="password" class="form-control" > 
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
                                                        <img id="showImage" src="{{ (!empty($editData['student']['image']))? url('upload/student_images/'.$editData['student']['image']):url('upload/no_image.jpg') }}" style="width: 100px; height: 100px; border: 1px solid #000000;"> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- End 7th Row -->

                                    </div>
                                </div>
                                <div class="text-xs-right">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update">
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
        const savedState = "{{ $editData['student']['state'] }}";
        const savedLga = "{{ $editData['student']['lga'] }}";

        $('#country_id').on('change', function(e, isInitial) {
            const country = $(this).val();
            const stateSelect = $('#state_id');
            const lgaSelect = $('#lga_id');
            
            if(!isInitial) {
                stateSelect.html('<option value="" selected="" disabled="">Loading...</option>');
                lgaSelect.html('<option value="" selected="" disabled="">Select LGA</option>');
            }

            fetch('{{ asset('locations.json') }}')
                .then(response => response.json())
                .then(data => {
                    const states = data[country];
                    stateSelect.html('<option value="" selected="" disabled="">Select State</option>');
                    if (states) {
                        Object.keys(states).forEach(state => {
                            stateSelect.append(`<option value="${state}" ${state == savedState ? 'selected' : ''}>${state}</option>`);
                        });
                        if(isInitial && savedState) {
                            stateSelect.trigger('change', [true]);
                        }
                    }
                });
        });

        $('#state_id').on('change', function(e, isInitial) {
            const country = $('#country_id').val();
            const state = $(this).val();
            const lgaSelect = $('#lga_id');
            
            if(!isInitial) {
                lgaSelect.html('<option value="" selected="" disabled="">Loading...</option>');
            }

            fetch('{{ asset('locations.json') }}')
                .then(response => response.json())
                .then(data => {
                    const lgas = data[country][state];
                    lgaSelect.html('<option value="" selected="" disabled="">Select LGA</option>');
                    if (lgas) {
                        lgas.forEach(lga => {
                            lgaSelect.append(`<option value="${lga}" ${lga == savedLga ? 'selected' : ''}>${lga}</option>`);
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

        // Trigger initial load
        if($('#country_id').val()) {
            $('#country_id').trigger('change', [true]);
        }
	});
</script>

<!-- Select2 CSS & JS -->
<link href="{{ asset('assets/vendor_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#parent_id').select2({
            placeholder: 'Search Parent...',
            allowClear: true
        });
    });
</script>

@endsection
