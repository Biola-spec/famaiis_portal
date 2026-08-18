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
				<div class="col-12">
					<div class="box {{ ($unreadNotificationCount ?? 0) > 0 ? 'border-warning' : '' }}" id="dashboard-notifications">
						<div class="box-header with-border d-flex align-items-center justify-content-between">
							<h4 class="box-title mb-0">
								<i class="fa fa-bell-o text-warning mr-5"></i>
								{{ __('ui.notifications') }}
							</h4>
							<span class="badge badge-{{ ($unreadNotificationCount ?? 0) > 0 ? 'danger' : 'secondary' }}">
								{{ $unreadNotificationCount ?? 0 }} {{ ($unreadNotificationCount ?? 0) === 1 ? 'unread' : 'unread notifications' }}
							</span>
						</div>
						<div class="box-body">
							@if(($unreadNotifications ?? collect())->isNotEmpty())
								<div class="list-group list-group-flush">
									@foreach($unreadNotifications as $notification)
										@php
											$notificationData = is_array($notification->data ?? null) ? $notification->data : [];
											$notificationTitle = $notificationData['title'] ?? $notificationData['message'] ?? __('ui.notifications');
											$notificationTime = $notificationData['timestamp'] ?? $notification->created_at ?? now();
										@endphp
										<div class="list-group-item px-0 d-flex align-items-center justify-content-between">
											<span><i class="fa fa-bell text-warning mr-5"></i>{{ $notificationTitle }}</span>
											<small class="text-muted">{{ \Carbon\Carbon::parse($notificationTime)->diffForHumans() }}</small>
										</div>
									@endforeach
								</div>
							@else
								<p class="text-muted mb-0">{{ __('ui.no_notifications') }}</p>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				@if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
                <x-dashboard.stat-card :label="__('ui.total_students')" :value="$total_student" icon="mdi-account-multiple" variant="primary" />
                <x-dashboard.stat-card :label="__('ui.total_teachers')" :value="$total_teacher" icon="mdi-school" variant="warning" />
                <x-dashboard.stat-card :label="__('ui.total_parents')" :value="$total_parent" icon="mdi-human-male-female" variant="success" />
                <x-dashboard.stat-card :label="__('ui.attendance_today')" :value="$attendance_today . '%'" icon="mdi-calendar-check" variant="danger" />
                <x-dashboard.stat-card label="Total Staff" :value="$total_staff" icon="mdi-briefcase" variant="teal" />
                <x-dashboard.stat-card label="Total Classes" :value="$total_classes" icon="mdi-layers" variant="indigo" />
                <x-dashboard.stat-card label="Fees Collected" :value="$fees_collected" icon="mdi-wallet" variant="emerald" />
                <x-dashboard.stat-card label="Pending Admissions" :value="$pending_admissions" icon="mdi-account-plus" variant="amber" />
                <x-dashboard.stat-card label="Upcoming Events" :value="$upcoming_events_count" icon="mdi-calendar-clock" variant="pink" />
                <x-dashboard.stat-card label="Library Books Issued" :value="$library_books_issued" icon="mdi-book-open-page-variant" variant="cyan" />
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

                @if(!(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin')))
                <x-dashboard.stat-card :label="__('ui.attendance_today')" :value="$attendance_today . '%'" icon="mdi-calendar-check" variant="danger" />
                @endif

@if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
@include('admin.body.school_schedule_widget')

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

@include('admin.body.school_schedule_widget')

@endif

				</div>
			</div>
		</section>
		<!-- /.content -->
	  </div>
  </div>

  @endsection
