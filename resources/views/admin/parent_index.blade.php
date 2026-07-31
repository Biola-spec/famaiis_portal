@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xl-3 col-6">
                    <div class="box overflow-hidden pull-up stat-card-success">
                        <div class="box-body">                          
                            <div class="stat-card-wrapper">
                                <div>
                                    <p class="stat-card-title">{{ __('ui.my_children') }}</p>
                                    <h3 class="stat-card-number">{{ count($children) }} <small class="text-success" style="font-size: 12px; font-weight: 600;">{{ __('ui.linked') }}</small></h3>
                                </div>
                                <div class="stat-icon-box">
                                    <i class="font-size-24 mdi mdi-account-multiple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">{{ __('ui.list_of_my_children') }}</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>{{ __('ui.student_id') }}</th>
                                            <th>{{ __('ui.name') }}</th>
                                            <th>{{ __('ui.class') }}</th>
                                            <th>{{ __('ui.gender') }}</th>
                                            <th width="15%">{{ __('ui.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($children as $key => $child)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ optional($child['student'])->id_no }}</td>
                                                <td>{{ optional($child['student'])->name }}</td>
                                                <td>{{ optional($child['assignment']->student_class)->name ?? __('ui.no_data') }}</td>
                                                <td>{{ optional($child['student'])->gender }}</td>
                                                <td>
                                                    @if($child['assignment'])
                                                        <a href="{{ route('report.marksheet.get', [
                                                            'year_id' => $child['assignment']->year_id,
                                                            'class_id' => $child['assignment']->class_id,
                                                            'exam_type_id' => 1,
                                                            'id_no' => $child['student']->id_no
                                                        ]) }}" class="btn btn-primary btn-sm" target="_blank">{{ __('ui.view_result') }}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">{{ __('ui.no_children_linked') }}</td>
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
