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
                <div class="col-xl-3 col-6">
                    <div class="box overflow-hidden pull-up stat-card-primary">
                        <div class="box-body">                          
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.my_students') }}</p>
                                    <h3 class="stat-card-number">{{ $teacher_total_students }} <small class="text-success" style="font-size: 12px; font-weight: 600;"><i class="fa fa-caret-up"></i> {{ __('ui.total') }}</small></h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-account-multiple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('admin.body.events_widget')
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">{{ __('ui.students_in_my_class') }}</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ui.student_id') }}</th>
                                            <th>{{ __('ui.name') }}</th>
                                            <th>{{ __('ui.class') }}</th>
                                            <th>{{ __('ui.gender') }}</th>
                                            <th>{{ __('ui.mobile') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teacher_students as $student)
                                            <tr>
                                                <td>{{ optional($student->student)->id_no }}</td>
                                                <td>{{ optional($student->student)->name }}</td>
                                                <td>{{ optional($student->student_class)->name }}</td>
                                                <td>{{ optional($student->student)->gender }}</td>
                                                <td>{{ optional($student->student)->mobile }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('ui.no_students_found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
</div>
@endsection
