@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title">Student Assessment & Comments Entry</h4>
                        </div>
                        <div class="box-body">
                            <form id="loadStudentsForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Session <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="year_id" id="year_id" required class="form-control">
                                                    <option value="" selected disabled>Select Session</option>
                                                    @foreach($years as $year)
                                                        <option value="{{ $year->id }}" {{ optional($currentSession)->id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Section <span class="text-danger"></span></h5>
                                            <div class="controls">
                                                <select name="section_id" id="section_id" class="form-control">
                                                    <option value="" selected disabled>Select Section</option>
                                                    @foreach($sections as $section)
                                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Term <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="term" id="term" required class="form-control">
                                                    <option value="" selected disabled>Select Term</option>
                                                    <option value="1st Term">1st Term</option>
                                                    <option value="2nd Term">2nd Term</option>
                                                    <option value="3rd Term">3rd Term</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Class <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="class_id" id="class_id" required class="form-control">
                                                    <option value="" selected disabled>Select Class</option>
                                                    @foreach($classes as $class)
                                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="padding-top: 25px;">
                                        <button type="submit" class="btn btn-primary" id="btnSearch">Search Students</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List Container -->
            <div class="row d-none" id="studentsContainer">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Enter Assessments</h4>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('academic.assessment.store') }}" id="assessmentForm">
                                @csrf
                                 <input type="hidden" name="class_id" id="form_class_id">
                                <input type="hidden" name="section_id" id="form_section_id">
                                <input type="hidden" name="year_id" id="form_year_id">
                                <input type="hidden" name="term" id="form_term">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID No</th>
                                                <th>Student Name</th>
                                                <th>Cognitive Areas (1-5)</th>
                                                <th>Class Teacher Comment</th>
                                                <th>AI Assistant</th>
                                                <th id="headTeacherHeader" class="d-none">Section Head Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsBody">
                                            <!-- Dynamic Rows -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-xs-right mt-4">
                                    @if(auth()->user()->hasRole('Admin'))
                                        <button type="button" class="btn btn-outline-primary mr-2" id="btnBulkAiComments" disabled>
                                            Generate All Report Comments
                                        </button>
                                    @endif
                                    <button type="submit" class="btn btn-rounded btn-info" id="btnSubmit">Save Assessments</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    $(document).ready(function() {
        const cognitiveAreas = @json($assessmentAreas);

        $(document).on('change', '#section_id', function () {
            const sectionId = $(this).val();
            $('#class_id').prop('disabled', true).html('<option value="">Loading...</option>');

            $.get("{{ route('academic.marks.classes') }}", { section_id: sectionId }, function (classes) {
                let html = '<option value="" selected disabled>Select Class</option>';
                $.each(classes, function (_, studentClass) {
                    html += `<option value="${studentClass.id}">${studentClass.name}</option>`;
                });
                $('#class_id').html(html).prop('disabled', false);
            });
        });

        $('#loadStudentsForm').on('submit', function(e) {
            e.preventDefault();
            let class_id = $('#class_id').val();
            let section_id = $('#section_id').val();
            let year_id = $('#year_id').val();
            let term = $('#term').val();

            if (!class_id || !year_id || !term) {
                alert('Please select all fields.');
                return;
            }

            let btn = $('#btnSearch');
            btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: "{{ route('academic.assessment.load') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    section_id: section_id,
                    year_id: year_id,
                    term: term,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#studentsBody').empty();
                    
                    if (response.students.length === 0) {
                        $('#studentsContainer').addClass('d-none');
                        alert('No students found for this selection.');
                        btn.prop('disabled', false).text('Search Students');
                        return;
                    }

                    if (response.is_section_head) {
                        $('#headTeacherHeader').removeClass('d-none');
                    } else {
                        $('#headTeacherHeader').addClass('d-none');
                    }

                    $('#form_class_id').val(class_id);
                    $('#form_section_id').val(section_id);
                    $('#form_year_id').val(year_id);
                    $('#form_term').val(term);

                    let html = '';
                    response.students.forEach(function(student, index) {
                        let existingComment = student.existing && student.existing.teacher_comment ? student.existing.teacher_comment : '';
                        let existingCognitive = student.existing && student.existing.cognitive_areas ? student.existing.cognitive_areas : {};
                        let assessmentId = student.existing && student.existing.id ? student.existing.id : '';
                        let aiDraft = student.existing && student.existing.ai_comment_draft ? student.existing.ai_comment_draft : '';
                        let aiFlag = student.existing && student.existing.ai_flag ? student.existing.ai_flag : '';
                        
                        let cognitiveHtml = '';
                        cognitiveAreas.forEach(function(area) {
                            let label = area.replace(/_/g, ' ');
                            let score = existingCognitive[area] ? existingCognitive[area] : '';
                            cognitiveHtml += `
                                <div class="form-group mb-1 d-flex align-items-center">
                                    <label class="mr-2 mb-0" style="width: 150px; font-size:12px;">${label}</label>
                                    <input type="number" name="assessments[${index}][cognitive_areas][${area}]" class="form-control form-control-sm" min="1" max="5" value="${score}" style="width: 60px;">
                                </div>
                            `;
                        });

                        html += `
                            <tr>
                                <td>
                                    ${student.id_no}
                                    <input type="hidden" name="assessments[${index}][student_id]" value="${student.student_id}">
                                </td>
                                <td>${student.name}</td>
                                <td>
                                    ${cognitiveHtml}
                                </td>
                                <td>
                                    <textarea name="assessments[${index}][teacher_comment]" class="form-control" rows="6" placeholder="Enter remark...">${existingComment}</textarea>
                                </td>
                                <td style="min-width: 190px;">
                                    ${assessmentId ? `<button type="button" class="btn btn-sm btn-outline-primary ai-comment-button mb-2" data-assessment-id="${assessmentId}">Generate comment</button>` : '<small class="text-muted">Save assessment first</small>'}
                                    <textarea class="form-control form-control-sm ai-comment-result" rows="5" readonly placeholder="AI draft appears here">${aiDraft}</textarea>
                                    <small class="ai-comment-status text-muted">${aiFlag ? `Review flag: ${aiFlag}` : ''}</small>
                                </td>
                                <td class="${response.is_section_head ? '' : 'd-none'}">
                                    <textarea name="assessments[${index}][head_teacher_comment]" class="form-control" rows="6" placeholder="Enter section head remark...">${student.existing && student.existing.head_teacher_comment ? student.existing.head_teacher_comment : ''}</textarea>
                                </td>
                            </tr>
                        `;
                    });

                    $('#studentsBody').html(html);
                    $('#studentsContainer').removeClass('d-none');
                    $('#btnBulkAiComments').prop('disabled', $('.ai-comment-button').length === 0);
                    btn.prop('disabled', false).text('Search Students');
                },
                error: function(xhr) {
                    alert('Error loading students: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    btn.prop('disabled', false).text('Search Students');
                }
            });
            });

        $(document).on('click', '.ai-comment-button', function () {
            const button = $(this);
            const cell = button.closest('td');
            button.prop('disabled', true).text('Generating...');
            $.post("{{ url('/ai/assessment') }}/" + button.data('assessment-id') + '/comment', {
                _token: "{{ csrf_token() }}"
            }).done(function (response) {
                cell.find('.ai-comment-result').val(response.comment || 'Queued. Reload after processing to view the draft.');
                cell.find('.ai-comment-status').text(response.flag ? 'Review flag: ' + response.flag : (response.status || 'Generated'));
            }).fail(function (xhr) {
                cell.find('.ai-comment-status').text(xhr.responseJSON?.message || 'Generation failed.');
            }).always(function () {
                button.prop('disabled', false).text('Generate comment');
            });
        });

        $('#btnBulkAiComments').on('click', function () {
            const button = $(this);
            const ids = $('.ai-comment-button').map(function () { return $(this).data('assessment-id'); }).get();
            if (!ids.length) return;
            button.prop('disabled', true).text('Queueing...');
            $.post("{{ route('ai.assessment.comments.bulk') }}", {
                _token: "{{ csrf_token() }}",
                assessment_ids: ids
            }).done(function (response) {
                alert(response.message || 'Report comments queued.');
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Bulk generation failed.');
            }).always(function () {
                button.prop('disabled', false).text('Generate All Report Comments');
            });
        });
    });
</script>
@endsection
