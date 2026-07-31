@extends('admin.admin_master')
@section('admin')

  <div class="content-wrapper">
	  <div class="container-full">

		<!-- Main content -->
		<section class="content">
			<div class="row">
				@include('admin.body.live_sessions_widget')
			</div>
			<div class="row">
				@if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
                <div class="col-xl-3 col-6">
					<div class="box overflow-hidden pull-up stat-card-primary">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.total_students') }}</p>
                                    <h3 class="stat-card-number">{{ $total_student }}</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-account-multiple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-6">
                    <div class="box overflow-hidden pull-up stat-card-warning">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.total_teachers') }}</p>
                                    <h3 class="stat-card-number">{{ $total_teacher }}</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-school"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-6">
                    <div class="box overflow-hidden pull-up stat-card-success">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.total_parents') }}</p>
                                    <h3 class="stat-card-number">{{ $total_parent }}</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-human-male-female"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(Auth::user()->role == 'Teacher' || Auth::user()->hasRole('Teacher'))
                <div class="col-xl-3 col-6">
					<div class="box overflow-hidden pull-up stat-card-info">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.my_classes') }}</p>
                                    <h3 class="stat-card-number">6</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-book-open-page-variant"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(Auth::user()->role == 'Parent' || Auth::user()->hasRole('Parent'))
                <div class="col-xl-3 col-6">
					<div class="box overflow-hidden pull-up stat-card-success">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.my_children') }}</p>
                                    <h3 class="stat-card-number">2</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-human-male-female"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-xl-3 col-6">
                    <div class="box overflow-hidden pull-up stat-card-danger">
                        <div class="box-body">							
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.attendance_today') }}</p>
                                    <h3 class="stat-card-number">{{ $attendance_today }}%</h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

@if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
<div class="col-xl-8 col-12">
<div class="box">
	<div class="box-header">
		<h4 class="box-title align-items-start flex-column">
			{{ __('ui.new_student_arrivals') }}
			<small class="subtitle">{{ __('ui.latest_registrations') }}</small>
		</h4>
	</div>
	<div class="box-body">
		<div class="table-responsive">
			<table class="table no-border">
				<thead>
					<tr class="text-uppercase bg-lightest">
						<th style="min-width: 250px"><span>{{ __('ui.student_name') }}</span></th>
						<th style="min-width: 100px"><span class="text-fade">{{ __('ui.student_id') }}</span></th>
						<th style="min-width: 150px"><span class="text-fade">{{ __('ui.email') }}</span></th>
						<th style="min-width: 130px"><span class="text-fade">{{ __('ui.joined_date') }}</span></th>
						<th style="min-width: 120px"></th>
					</tr>
				</thead>
				<tbody>
                    @foreach($recent_students as $student)
					<tr>										
						<td class="pl-0 py-8">
							<div class="d-flex align-items-center">
								<div class="flex-shrink-0 mr-20">
                                    <img src="{{ (!empty($student->image)) ? url('upload/student_images/'.$student->image) : url('upload/no_image.jpg') }}" 
                                         alt="" class="rounded h-50 w-50" style="object-fit: cover;">
								</div>

								<div>
									<a href="#" class="font-weight-600 hover-primary mb-1 font-size-16">{{ $student->name }}</a>
									<span class="text-fade d-block font-size-12">{{ $student->address }}</span>
								</div>
							</div>
						</td>
						<td>
							<span class="font-weight-600 d-block font-size-14">
								{{ $student->id_no }}
							</span>
						</td>
						<td>
							<span class="font-weight-600 d-block font-size-14">
								{{ $student->email }}
							</span>
						</td>
						<td>
							<span class="font-weight-600 d-block font-size-14">
								{{ date('d M Y', strtotime($student->created_at)) }}
							</span>
						</td>
						<td class="text-right">
							<a href="{{ route('student.registration.details', $student->id) }}" class="waves-effect waves-light btn btn-info btn-circle mx-5"><span class="mdi mdi-arrow-right"></span></a>
						</td>
					</tr>
                    @endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>  
</div>

@include('admin.body.events_widget')

@else
<div class="col-xl-8 col-12">
    <div class="box">
        <div class="box-body d-flex align-items-center">
            @php
                $user = Auth::user();
                $userType = strtolower($user->usertype);
                if ($userType == 'student') {
                    $folder = 'student_images';
                } elseif ($userType == 'employee') {
                    $folder = 'employee_images';
                } elseif ($userType == 'parent') {
                    $folder = 'parent_images';
                } else {
                    $folder = 'user_images';
                }
                $imagePath = (!empty($user->image)) ? url('upload/'.$folder.'/'.$user->image) : url('upload/no_image.jpg');
            @endphp
            <img src="{{ $imagePath }}" class="rounded-circle h-100 w-100 mr-20" style="max-width: 80px; object-fit: cover; border: 3px solid #512da8;" alt="User Image">
            <div>
                <h4 class="text-white mb-0">{{ __('ui.welcome_back') }}, {{ $user->name }}!</h4>
                <p class="text-fade mb-0">{{ __('ui.sidebar_nav_tip') }}</p>
            </div>
        </div>
    </div>
</div>

@include('admin.body.events_widget')

@endif

				</div>
			</div>
		</section>
		<!-- /.content -->
	  </div>
  </div>

  @endsection
