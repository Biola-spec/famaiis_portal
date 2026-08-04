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
<div class="col-12">
<div class="box dashboard-chart-panel">
    <div class="box-header">
        <h4 class="box-title align-items-start flex-column">
            Student Analytics
            <small class="subtitle">Performance, enrollment, attendance and fees</small>
        </h4>
    </div>
    <div class="box-body">
        <div class="dashboard-insight-strip">
            <div class="dashboard-insight dashboard-insight-purple">
                <span>Enrollment</span>
                <strong>{{ $total_student }}</strong>
            </div>
            <div class="dashboard-insight dashboard-insight-cyan">
                <span>Classes</span>
                <strong>{{ $total_classes }}</strong>
            </div>
            <div class="dashboard-insight dashboard-insight-amber">
                <span>Attendance</span>
                <strong>{{ $attendance_present_today }}/{{ $attendance_records_today }}</strong>
            </div>
            <div class="dashboard-insight dashboard-insight-green">
                <span>Fees</span>
                <strong>{{ $fees_collected }}</strong>
            </div>
        </div>

        <div class="dashboard-chart-grid">
            <div class="dashboard-chart-card">
                <h5>Enrollment by Class</h5>
                <div class="dashboard-chart-wrap">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
            <div class="dashboard-chart-card">
                <h5>Performance by Class</h5>
                <div class="dashboard-chart-wrap">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            <div class="dashboard-chart-card">
                <h5>Attendance Today ({{ $attendance_today }}%)</h5>
                <div class="dashboard-chart-wrap">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
            <div class="dashboard-chart-card">
                <h5>Fee Status</h5>
                <div class="dashboard-chart-wrap">
                    <canvas id="feeStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="col-xl-8 col-12">
<div class="box">
	<div class="box-header">
		<h4 class="box-title align-items-start flex-column">
			{{ __('ui.new_student_arrivals') }}
			<small class="subtitle">{{ __('ui.latest_registrations') }}</small>
		</h4>
	</div>
	<div class="box-body">
		<div class="table-responsive dashboard-compact-table">
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
						<td class="pl-0">
							<div class="d-flex align-items-center">
								<div class="flex-shrink-0 mr-10">
                                    <img src="{{ (!empty($student->image)) ? url('upload/student_images/'.$student->image) : url('upload/no_image.jpg') }}" 
                                         alt="" class="rounded-circle dashboard-student-avatar">
								</div>

								<div>
									<a href="#" class="font-weight-600 hover-primary mb-1 font-size-14">{{ $student->name }}</a>
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

@push('scripts')
@if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
<script src="{{ asset('assets/vendor_components/chart.js-master/Chart.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        Chart.defaults.global.defaultFontColor = '#9ca3af';
        Chart.defaults.global.defaultFontFamily = "'Plus Jakarta Sans', Arial, sans-serif";

        const palette = ['#8B5CF6', '#F59E0B', '#10B981', '#EF4444', '#06B6D4', '#EC4899', '#14B8A6', '#6366F1', '#F97316', '#84CC16'];
        const gridColor = 'rgba(156, 163, 175, 0.16)';

        const enrollmentLabels = @json($class_distribution->keys());
        const enrollmentValues = @json($class_distribution->values());
        const performanceLabels = @json($performance_distribution->keys());
        const performanceValues = @json($performance_distribution->values());
        const attendanceLabels = @json($attendance_distribution->keys());
        const attendanceValues = @json($attendance_distribution->values());
        const feeLabels = @json($fee_status_distribution->keys());
        const feeValues = @json($fee_status_distribution->values());

        function fallback(labels, values, label) {
            return {
                labels: labels.length ? labels : [label],
                values: values.length ? values : [1]
            };
        }

        function chartOptions(showScales) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: '#9ca3af',
                        fontSize: 11,
                        boxWidth: 12,
                        padding: 12
                    }
                },
                scales: showScales ? {
                    xAxes: [{
                        gridLines: { color: gridColor, zeroLineColor: gridColor },
                        ticks: { fontColor: '#9ca3af' }
                    }],
                    yAxes: [{
                        gridLines: { color: gridColor, zeroLineColor: gridColor },
                        ticks: { fontColor: '#9ca3af', beginAtZero: true }
                    }]
                } : undefined
            };
        }

        const enrollment = fallback(enrollmentLabels, enrollmentValues, 'No class data');
        new Chart(document.getElementById('enrollmentChart'), {
            type: 'doughnut',
            data: {
                labels: enrollment.labels,
                datasets: [{
                    data: enrollment.values,
                    backgroundColor: palette,
                    borderColor: 'rgba(17, 24, 39, 0.88)',
                    borderWidth: 2
                }]
            },
            options: chartOptions(false)
        });

        const performance = fallback(performanceLabels, performanceValues, 'No performance data');
        new Chart(document.getElementById('performanceChart'), {
            type: 'bar',
            data: {
                labels: performance.labels,
                datasets: [{
                    label: 'Average Score',
                    data: performance.values,
                    backgroundColor: ['#7C3AED', '#2563EB', '#0891B2', '#059669', '#D97706', '#DC2626', '#DB2777'],
                    borderWidth: 0
                }]
            },
            options: chartOptions(true)
        });

        const attendance = fallback(attendanceLabels, attendanceValues, 'No attendance data');
        new Chart(document.getElementById('attendanceChart'), {
            type: 'pie',
            data: {
                labels: attendance.labels,
                datasets: [{
                    data: attendance.values,
                    backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#06B6D4'],
                    borderColor: 'rgba(17, 24, 39, 0.88)',
                    borderWidth: 2
                }]
            },
            options: chartOptions(false)
        });

        const fees = fallback(feeLabels, feeValues, 'No fee data');
        new Chart(document.getElementById('feeStatusChart'), {
            type: 'polarArea',
            data: {
                labels: fees.labels,
                datasets: [{
                    data: fees.values,
                    backgroundColor: ['rgba(16, 185, 129, 0.82)', 'rgba(245, 158, 11, 0.82)', 'rgba(239, 68, 68, 0.82)'],
                    borderColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 1
                }]
            },
            options: chartOptions(false)
        });
    });
</script>
@endif
@endpush
