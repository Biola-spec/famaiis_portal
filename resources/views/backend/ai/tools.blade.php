@extends('admin.admin_master')

@section('admin')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header"><h4 class="box-title">Lesson Plan Generator</h4></div>
                    <div class="box-body">
                        <form id="lesson-plan-form">
                            @csrf
                            <input class="form-control mb-2" name="subject" placeholder="Subject" required>
                            <input class="form-control mb-2" name="class_level" placeholder="Class" required>
                            <input class="form-control mb-2" name="week_number" placeholder="Week number">
                            <input class="form-control mb-2" name="scheme_topic" placeholder="Scheme topic" required>
                            <textarea class="form-control mb-2" name="scheme_objectives" placeholder="Scheme objectives" required></textarea>
                            <input class="form-control mb-2" type="number" name="duration_minutes" placeholder="Duration in minutes" value="40" min="1" required>
                            <input class="form-control mb-2" name="resources" placeholder="Available resources">
                            <button class="btn btn-primary" type="submit">Generate Lesson Plan</button>
                        </form>
                        <pre id="lesson-plan-result" class="mt-3 d-none" style="white-space:pre-wrap;"></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header"><h4 class="box-title">Comment Assistant</h4></div>
                    <div class="box-body">
                        <form id="expand-comment-form">
                            @csrf
                            <textarea class="form-control mb-2" name="raw_text" placeholder="Teacher's rough comment" required></textarea>
                            <input class="form-control mb-2" name="subject" placeholder="Subject or context">
                            <input class="form-control mb-2" name="tone" placeholder="Tone, for example encouraging but honest">
                            <button class="btn btn-info" type="submit">Polish Comment</button>
                        </form>
                        <pre id="expand-comment-result" class="mt-3 d-none" style="white-space:pre-wrap;"></pre>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header"><h4 class="box-title">Student Insight</h4></div>
                    <div class="box-body">
                        <form id="student-insight-form">
                            @csrf
                            <select class="form-control mb-2" name="student_id" required>
                                <option value="">Select student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->id_no }})</option>
                                @endforeach
                            </select>
                            <input class="form-control mb-2" name="class_name" placeholder="Class" required>
                            <textarea class="form-control mb-2" name="term_results" placeholder="Pre-calculated term results" required></textarea>
                            <textarea class="form-control mb-2" name="attendance_data" placeholder="Attendance data"></textarea>
                            <textarea class="form-control mb-2" name="conduct_log" placeholder="Factual conduct log"></textarea>
                            <button class="btn btn-success" type="submit">Generate Student Insight</button>
                        </form>
                        <pre id="student-insight-result" class="mt-3 d-none" style="white-space:pre-wrap;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(function () {
    function submitAiForm(selector, resultSelector, url, transform) {
        $(selector).on('submit', function (event) {
            event.preventDefault();
            const form = $(this);
            const result = $(resultSelector);
            const button = form.find('button[type="submit"]');
            button.prop('disabled', true).text('Generating...');
            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    result.removeClass('d-none').text(transform(response));
                },
                error: function (xhr) {
                    result.removeClass('d-none').text(xhr.responseJSON?.message || 'Generation failed. Check the Gemini configuration.');
                },
                complete: function () { button.prop('disabled', false); }
            });
        });
    }
    submitAiForm('#lesson-plan-form', '#lesson-plan-result', '{{ route('ai.lesson-plan') }}', response => JSON.stringify(response, null, 2));
    submitAiForm('#expand-comment-form', '#expand-comment-result', '{{ route('ai.expand-comment') }}', response => response.text || '');
    submitAiForm('#student-insight-form', '#student-insight-result', '{{ route('ai.student-insight') }}', response => response.text || '');
});
</script>
@endsection
