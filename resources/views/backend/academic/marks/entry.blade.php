@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<style>
    .marks-entry-table th,
    .marks-entry-table td {
        vertical-align: middle;
    }

    .marks-entry-table input[type="number"] {
        min-width: 82px;
    }

    @media (max-width: 767.98px) {
        #marks-panel {
            margin-left: 0;
            margin-right: 0;
        }

        #marks-panel .col-md-12 {
            padding-left: 0;
            padding-right: 0;
        }

        .marks-entry-table {
            border: 0;
            margin-bottom: 1rem;
        }

        .marks-entry-table thead {
            display: none;
        }

        .marks-entry-table,
        .marks-entry-table tbody,
        .marks-entry-table tr,
        .marks-entry-table td {
            display: block;
            width: 100%;
        }

        .marks-entry-table tbody tr {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            margin-bottom: 14px;
            overflow: hidden;
        }

        .marks-entry-table tbody td {
            align-items: center;
            border: 0 !important;
            border-bottom: 1px solid #eef2f7 !important;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            min-height: 48px;
            padding: 10px 12px;
            text-align: right;
        }

        .marks-entry-table tbody td:last-child {
            border-bottom: 0 !important;
        }

        .marks-entry-table tbody td::before {
            color: #334155;
            content: attr(data-label);
            flex: 0 0 43%;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.25;
            text-align: left;
        }

        .marks-entry-table tbody td[data-label="Student"] {
            align-items: flex-start;
            font-weight: 700;
        }

        .marks-entry-table .form-control-sm {
            margin-left: auto;
            max-width: 136px;
            text-align: right;
            width: 48%;
        }

        #marks-form button[type="submit"] {
            display: block;
            width: 100%;
        }
    }
</style>
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title">Structured Marks Entry</h4>
                            <p class="mb-0">
                                Session: <strong>{{ optional($currentSession)->name }}</strong>
                            </p>
                        </div>
                        <div class="box-body">
                            <form id="marks-form" method="POST" action="{{ route('academic.marks.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Session <span class="text-danger"> </span></h5>
                                            <div class="controls">
                                                <select name="year_id" id="year_id" required class="form-control">
                                                    <option value="" selected disabled>Select Session</option>
                                                    @foreach($years as $year)
                                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Section <span class="text-danger"> </span></h5>
                                            <div class="controls">
                                                <select name="section_id" id="section_id" required class="form-control">
                                                    <option value="" selected>All Sections</option>
                                                    @foreach($sections as $section)
                                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Class <span class="text-danger"> </span></h5>
                                            <div class="controls">
                                                <select name="class_id" id="class_id" required class="form-control" disabled>
                                                    <option value="" selected disabled>Select Class</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Term <span class="text-danger"> </span></h5>
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
                                            <h5>Subject <span class="text-danger"> </span></h5>
                                            <div class="controls">
                                                <select name="subject_id" id="subject_id" required class="form-control" disabled>
                                                    <option value="" selected disabled>Select Subject</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button id="load-context" type="button" class="btn btn-primary">Load Students</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="marks-panel" class="row mt-3 d-none">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-striped marks-entry-table">
                                            <thead>
                                                <tr id="marks-header-row">
                                                    <th>ID No</th>
                                                    <th>Student</th>
                                                    <th>Gender</th>
                                                    <th>Exam</th>
                                                    <th>Project</th>
                                                </tr>
                                            </thead>
                                            <tbody id="marks-body"></tbody>
                                        </table>
                                        <button type="submit" class="btn btn-success">Save Results</button>
                                    </div>
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
    let currentSetting = null;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function labelWithMax(label, maxWeight) {
        const safeLabel = escapeHtml(label);
        const safeMaxWeight = escapeHtml(maxWeight);
        return {
            text: safeMaxWeight ? `${safeLabel} (Max: ${safeMaxWeight})` : safeLabel,
            html: safeMaxWeight ? `${safeLabel} <br><small>(Max: ${safeMaxWeight})</small>` : safeLabel
        };
    }

    $(document).on('change', '#section_id', function () {
        const sectionId = $(this).val();
        $('#class_id').prop('disabled', true).html('<option value="">Loading...</option>');
        $('#subject_id').prop('disabled', true).html('<option value="">Select Subject</option>');

        $.get("{{ route('academic.marks.classes') }}", { section_id: sectionId }, function (classes) {
            let html = '<option value="" selected disabled>Select Class</option>';
            $.each(classes, function (_, studentClass) {
                html += `<option value="${studentClass.id}">${studentClass.name}</option>`;
            });
            $('#class_id').html(html).prop('disabled', false);
        });
    });

    $(document).on('change', '#class_id', function () {
        const classId = $(this).val();
        const sectionId = $('#section_id').val();
        $('#subject_id').prop('disabled', true).html('<option value="">Loading...</option>');

        $.get("{{ route('academic.marks.subjects') }}", { class_id: classId, section_id: sectionId }, function (subjects) {
            let html = '<option value="" selected disabled>Select Subject</option>';
            $.each(subjects, function (_, subject) {
                html += `<option value="${subject.id}">${subject.name}</option>`;
            });
            $('#subject_id').html(html).prop('disabled', false);
        });
    });

    $(document).on('click', '#load-context', function () {
        const classId = $('#class_id').val();
        const sectionId = $('#section_id').val();
        const subjectId = $('#subject_id').val();
        const term = $('#term').val();

        $.get("{{ route('academic.marks.context') }}", {
            class_id: classId,
            section_id: sectionId,
            subject_id: subjectId,
            term: term
        }, function (response) {
            currentSetting = response.setting;

            let header = '<th>ID No</th><th>Student</th><th>Gender</th>';
            const ordinals = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'];
            const customLabels = currentSetting.ca_labels || [];
            for (let i = 1; i <= parseInt(currentSetting.ca_count); i++) {
                const label = customLabels[i-1] || `${ordinals[i-1] || i} CA`;
                const maxWeight = currentSetting.ca_weights && currentSetting.ca_weights[i-1] ? currentSetting.ca_weights[i-1] : '';
                header += `<th>${labelWithMax(label, maxWeight).html}</th>`;
            }
            header += `<th>${labelWithMax(currentSetting.exam_label || 'Exam', currentSetting.exam_weight).html}</th>`;
            if (currentSetting.project_enabled) {
                header += '<th>Project</th>';
            }
            $('#marks-header-row').html(header);

            let body = '';
            $.each(response.students, function (index, student) {
                const existing = student.existing || {};
                body += `<tr>
                    <td data-label="ID No">${escapeHtml(student.id_no || '')}<input type="hidden" name="student_marks[${index}][student_id]" value="${escapeHtml(student.student_id)}"><input type="hidden" name="student_marks[${index}][id_no]" value="${escapeHtml(student.id_no || '')}"></td>
                    <td data-label="Student">${escapeHtml(student.name || '')}</td>
                    <td data-label="Gender">${escapeHtml(student.gender || '')}</td>`;

                const existingCa = existing.ca_breakdown ? existing.ca_breakdown : [];
                for (let i = 0; i < parseInt(currentSetting.ca_count); i++) {
                    const value = existingCa[i] !== undefined ? existingCa[i] : '';
                    const maxWeight = currentSetting.ca_weights && currentSetting.ca_weights[i] ? currentSetting.ca_weights[i] : '100';
                    const customLabel = customLabels[i] || `${ordinals[i] || i + 1} CA`;
                    const caLabel = labelWithMax(customLabel, maxWeight).text;
                    body += `<td data-label="${caLabel}"><input type="number" min="0" max="${escapeHtml(maxWeight)}" step="0.01" class="form-control form-control-sm" name="student_marks[${index}][ca][${i}]" value="${escapeHtml(value)}"></td>`;
                }

                const maxExamWeight = currentSetting.exam_weight || '100';
                const examLabel = labelWithMax(currentSetting.exam_label || 'Exam', maxExamWeight).text;
                body += `<td data-label="${examLabel}"><input type="number" min="0" max="${escapeHtml(maxExamWeight)}" step="0.01" class="form-control form-control-sm" name="student_marks[${index}][exam_score]" value="${escapeHtml(existing.exam_score || '')}"></td>`;
                if (currentSetting.project_enabled) {
                    body += `<td data-label="Project"><input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm" name="student_marks[${index}][project_score]" value="${escapeHtml(existing.project_score || '')}"></td>`;
                }
                body += '</tr>';
            });

            $('#marks-body').html(body);
            $('#marks-panel').removeClass('d-none');
        }).fail(function (xhr) {
            const errorData = xhr.responseJSON || {};
            alert(errorData.message || 'Unable to load class marking context.');
        });
    });
    $('#marks-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if(typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Results saved successfully.');
                } else {
                    alert(response.message || 'Results saved successfully.');
                }
                submitBtn.prop('disabled', false).text(originalText);
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalText);
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    $.each(errors, function(key, val) {
                        errorMsg += val[0] + '\n';
                    });
                    
                    if(typeof toastr !== 'undefined') {
                        toastr.error(errorMsg, 'Validation Error');
                    } else {
                        alert("Validation Error:\n" + errorMsg);
                    }
                } else {
                    const errorData = xhr.responseJSON || {};
                    const msg = errorData.message || 'An error occurred while saving.';
                    if(typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            }
        });
    });
</script>
@endsection
