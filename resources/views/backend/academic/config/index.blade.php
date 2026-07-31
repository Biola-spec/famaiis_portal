@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Step 1: Configure Co-Curricular / Assessment Areas</h4>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('academic.config.assessment.areas.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Assessment Areas (Comma Separated)</label>
                                    <textarea name="assessment_areas" class="form-control" rows="3" placeholder="e.g. Punctuality, Attendance, Neatness, Politeness, Honesty, Relationship_with_peers">@php
                                        $setting = \App\Models\AcademicSetting::first();
                                        echo $setting && $setting->assessment_areas ? implode(', ', $setting->assessment_areas) : '';
                                    @endphp</textarea>
                                    <small class="text-muted">Enter the labels for co-curricular activities/affective domains, separated by commas. These will appear on all report cards.</small>
                                </div>
                                <button class="btn btn-info" type="submit">Update Assessment Areas</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border"><h4 class="box-title">Class Marking Setting</h4></div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('academic.config.marking.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Class</label>
                                    <select name="class_id" class="form-control" required>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Subject (optional)</label>
                                    <select name="subject_id" class="form-control">
                                        <option value="">General for all subjects</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>CA Count</label>
                                    <input name="ca_count" id="ca_count" class="form-control" type="number" min="1" value="2" required>
                                </div>
                                <div id="ca-labels-container">
                                    <!-- Dynamic CA Labels will be injected here -->
                                </div>
                                <div class="form-group">
                                    <label>Session (optional)</label>
                                    <select name="session_id" class="form-control">
                                        <option value="">All sessions</option>
                                        @foreach(\App\Models\StudentYear::query()->orderByDesc('id')->get() as $session)
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Term (optional)</label>
                                    <select name="term" class="form-control">
                                        <option value="">All terms</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term }}">{{ $term }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Total CA Weight is now calculated from individual CA weights automatically on the backend -->
                                <div class="form-group">
                                    <label>Exam Label (optional)</label>
                                    <input name="exam_label" class="form-control" type="text" placeholder="e.g. Final Theory" value="Exam">
                                </div>
                                <div class="form-group">
                                    <label>Exam Weight</label>
                                    <input name="exam_weight" class="form-control" type="number" step="0.01" value="60" required>
                                </div>
                                <div class="form-group">
                                    <label>Total Score</label>
                                    <input name="total_score" class="form-control" type="number" step="0.01" value="100" required>
                                </div>
                                <div class="form-group">
                                    <label><input name="project_enabled" value="1" type="checkbox"> Project Enabled</label>
                                </div>
                                <button class="btn btn-primary" type="submit">Save Setting</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Existing Marking Settings</h4>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Period / Term</th>
                                        <th>CAs</th>
                                        <th>Weights (CA/Exam/Total)</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($markingSettings as $setting)
                                        <tr>
                                            <td>{{ $setting->studentClass->name }}</td>
                                            <td>{{ $setting->subject->name ?? 'All Subjects' }}</td>
                                            <td>
                                                {{ $setting->session->name ?? 'All Sessions' }}<br>
                                                <span class="badge badge-primary">{{ $setting->term ?? 'All Terms' }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $setting->ca_count }} CAs</strong><br>
                                                <small class="text-muted">
                                                    @if($setting->ca_labels)
                                                        @foreach($setting->ca_labels as $index => $label)
                                                            {{ $label }} (Max: {{ $setting->ca_weights[$index] ?? '?' }})@if(!$loop->last), @endif
                                                        @endforeach
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                {{ $setting->ca_weight }} / {{ $setting->exam_weight }} / {{ $setting->total_score }}
                                            </td>
                                            <td>
                                                @if($setting->project_enabled)
                                                    <span class="badge badge-success">Enabled</span>
                                                @else
                                                    <span class="badge badge-secondary">Disabled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($setting->is_active)
                                                    <span class="badge badge-success">Enabled</span>
                                                @else
                                                    <span class="badge badge-danger">Disabled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('academic.config.marking.edit', $setting->id) }}" class="btn btn-info btn-sm" title="Edit Data"><i class="fa fa-pencil"></i></a>
                                                <a href="{{ route('academic.config.marking.toggle', $setting->id) }}" class="btn {{ $setting->is_active ? 'btn-secondary' : 'btn-success' }} btn-sm" title="Toggle Status"><i class="fa fa-power-off"></i></a>
                                                <a href="{{ route('academic.config.marking.delete', $setting->id) }}" class="btn btn-danger btn-sm" id="delete" title="Delete Data"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        function updateCALabels() {
            const countInput = document.getElementById('ca_count');
            if(!countInput) return;
            const count = parseInt(countInput.value) || 0;
            let html = '';
            const ordinals = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'];
            for (let i = 0; i < count; i++) {
                html += `
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Name for ${ordinals[i] || (i+1)} CA</label>
                            <input name="ca_labels[${i}]" class="form-control" type="text" placeholder="e.g. ${ordinals[i] || (i+1)} Test" value="${ordinals[i] || (i+1)} CA" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Max Score (Weight)</label>
                            <input name="ca_weights[${i}]" class="form-control" type="number" step="0.01" min="0" placeholder="e.g. 15" required>
                        </div>
                    </div>
                </div>`;
            }
            document.getElementById('ca-labels-container').innerHTML = html;
        }

        const countInput = document.getElementById('ca_count');
        if(countInput) {
            countInput.addEventListener('input', updateCALabels);
            countInput.addEventListener('change', updateCALabels);
            updateCALabels(); // Initial call
        }
    });
</script>
@endsection
