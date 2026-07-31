@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Edit Class Marking Setting</h4>
                            <a href="{{ route('academic.config.index') }}" class="btn btn-secondary float-right">Back</a>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('academic.config.marking.update', $setting->id) }}">
                                @csrf
                                <div class="form-group">
                                    <label>Class</label>
                                    <select name="class_id" class="form-control" required>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $setting->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Subject (optional)</label>
                                    <select name="subject_id" class="form-control">
                                        <option value="">General for all subjects</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ $setting->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>CA Count</label>
                                    <input name="ca_count" id="ca_count" class="form-control" type="number" min="1" value="{{ $setting->ca_count }}" required>
                                </div>
                                <div id="ca-labels-container">
                                    <!-- Dynamic CA Labels will be injected here -->
                                </div>
                                <div class="form-group">
                                    <label>Session (optional)</label>
                                    <select name="session_id" class="form-control">
                                        <option value="">All sessions</option>
                                        @foreach(\App\Models\StudentYear::query()->orderByDesc('id')->get() as $session)
                                            <option value="{{ $session->id }}" {{ $setting->session_id == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Term (optional)</label>
                                    <select name="term" class="form-control">
                                        <option value="">All terms</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term }}" {{ $setting->term == $term ? 'selected' : '' }}>{{ $term }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Exam Label (optional)</label>
                                    <input name="exam_label" class="form-control" type="text" placeholder="e.g. Final Theory" value="{{ $setting->exam_label ?? 'Exam' }}">
                                </div>
                                <div class="form-group">
                                    <label>Exam Weight</label>
                                    <input name="exam_weight" class="form-control" type="number" step="0.01" value="{{ $setting->exam_weight }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Total Score</label>
                                    <input name="total_score" class="form-control" type="number" step="0.01" value="{{ $setting->total_score }}" required>
                                </div>
                                <div class="form-group">
                                    <label><input name="project_enabled" value="1" type="checkbox" {{ $setting->project_enabled ? 'checked' : '' }}> Project Enabled</label>
                                </div>
                                <button class="btn btn-info" type="submit">Update Setting</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const prefilledLabels = {!! json_encode($setting->ca_labels ?? []) !!};
        const prefilledWeights = {!! json_encode($setting->ca_weights ?? []) !!};

        function updateCALabels() {
            const countInput = document.getElementById('ca_count');
            if(!countInput) return;
            const count = parseInt(countInput.value) || 0;
            let html = '';
            const ordinals = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'];
            for (let i = 0; i < count; i++) {
                const defaultLabel = (ordinals[i] || (i+1)) + ' CA';
                const labelValue = prefilledLabels[i] !== undefined ? prefilledLabels[i] : defaultLabel;
                const weightValue = prefilledWeights[i] !== undefined ? prefilledWeights[i] : '';

                html += `
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Name for ${ordinals[i] || (i+1)} CA</label>
                            <input name="ca_labels[${i}]" class="form-control" type="text" placeholder="e.g. ${ordinals[i] || (i+1)} Test" value="${labelValue}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Max Score (Weight)</label>
                            <input name="ca_weights[${i}]" class="form-control" type="number" step="0.01" min="0" placeholder="e.g. 15" value="${weightValue}" required>
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
