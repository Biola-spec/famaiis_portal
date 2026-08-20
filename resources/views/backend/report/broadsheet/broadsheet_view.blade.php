@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-primary">
                        <div class="box-header">
                            <h4 class="box-title">Advanced <strong>Broadsheet Module</strong></h4>
                        </div>

                        <div class="box-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#full_broadsheet" role="tab">Full Broadsheet</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#by_subject" role="tab">By Subject</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#term_comparison" role="tab">Term Comparison</a> </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- FULL BROADSHEET TAB -->
                                <div class="tab-pane active" id="full_broadsheet" role="tabpanel">
                                    <div class="p-15">
                                        <form method="GET" action="{{ route('report.student.result.get') }}" target="_blank">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <h5>Year</h5>
                                                        <select name="year_id" required class="form-control">
                                                            <option value="" selected disabled>Select Year</option>
                                                            @foreach($years as $year)
                                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <h5>Class</h5>
                                                        <select name="class_id" required class="form-control">
                                                            <option value="" selected disabled>Select Class</option>
                                                            @foreach($classes as $class)
                                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <h5>Section</h5>
                                                        <select name="section_id" class="form-control">
                                                            <option value="">All Sections</option>
                                                            @foreach($sections as $section)
                                                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <h5>Term</h5>
                                                        <select name="term" required class="form-control">
                                                            <option value="" selected disabled>Select Term</option>
                                                            @foreach($terms as $term)
                                                                <option value="{{ $term }}">{{ $term }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" style="padding-top: 25px;">
                                                    <button type="button" id="search_full" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> View PDF</button>
                                                    <button type="button" class="btn btn-success btn-export-csv" data-type="full"><i class="fa fa-file-excel-o"></i> CSV</button>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered table-striped sticky-header">
                                                <thead class="bg-info text-white">
                                                    <tr id="full_header">
                                                        <th>Student Name</th>
                                                        <th>ID Number</th>
                                                        <!-- Subject headers will appear here -->
                                                    </tr>
                                                </thead>
                                                <tbody id="full_results_body">
                                                    <tr>
                                                        <td colspan="2" class="text-center">Search to view preview</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- BY SUBJECT TAB -->
                                <div class="tab-pane" id="by_subject" role="tabpanel">
                                    <div class="p-15">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <h5>Year</h5>
                                                    <select id="sub_year_id" class="form-control">
                                                        <option value="">Select Year</option>
                                                        @foreach($years as $year)
                                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <h5>Class</h5>
                                                    <select id="sub_class_id" class="form-control">
                                                        <option value="">Select Class</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <h5>Subject</h5>
                                                    <select id="sub_subject_id" class="form-control">
                                                        <option value="">Select Subject</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <h5>Section</h5>
                                                    <select id="sub_section_id" class="form-control">
                                                        <option value="">All Sections</option>
                                                        @foreach($sections as $section)
                                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <h5>Term</h5>
                                                    <select id="sub_term" class="form-control">
                                                        <option value="">Select Term</option>
                                                        @foreach($terms as $term)
                                                            <option value="{{ $term }}">{{ $term }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="padding-top: 25px;">
                                                <button id="search_subject" class="btn btn-primary"><i class="fa fa-search"></i> View</button>
                                                <button type="button" class="btn btn-success btn-export-csv" data-type="subject"><i class="fa fa-file-excel-o"></i> CSV</button>
                                            </div>
                                        </div>

                                        <div id="subject_stats_area" class="row mt-4" style="display:none;">
                                            <div class="col-md-4">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Subject Average</span>
                                                        <span class="info-box-number" id="sub_avg">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Highest Score</span>
                                                        <span class="info-box-number" id="sub_high">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-danger">
                                                    <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Lowest Score</span>
                                                        <span class="info-box-number" id="sub_low">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered table-striped sticky-header">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th>Pos</th>
                                                        <th>Student Name</th>
                                                        <th>ID Number</th>
                                                        <th>CA Score</th>
                                                        <th>Exam Score</th>
                                                        <th>Total</th>
                                                        <th>Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="subject_results_body">
                                                    <tr>
                                                        <td colspan="7" class="text-center">Select criteria to view results</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- TERM COMPARISON TAB -->
                                <div class="tab-pane" id="term_comparison" role="tabpanel">
                                    <div class="p-15">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Year</h5>
                                                    <select id="comp_year_id" class="form-control">
                                                        @foreach($years as $year)
                                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Class</h5>
                                                    <select id="comp_class_id" class="form-control">
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h5>Select Terms</h5>
                                                    <div class="d-flex flex-wrap" style="gap: 20px; padding-top: 10px;">
                                                        @foreach($terms as $term)
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="term_{{ $loop->index }}" name="comp_terms[]" value="{{ $term }}">
                                                                <label class="custom-control-label" for="term_{{ $loop->index }}">{{ $term }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3" style="padding-top: 25px;">
                                                <button id="compare_performance" class="btn btn-primary"><i class="fa fa-line-chart"></i> Compare</button>
                                                <button type="button" class="btn btn-success btn-export-csv" data-type="comparison"><i class="fa fa-file-excel-o"></i> CSV</button>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div id="chart_container" style="min-height: 350px;">
                                                    <div id="performance_chart"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="bg-dark text-white">
                                                            <tr id="comp_header">
                                                                <th>Subject</th>
                                                                <!-- Terms will be added here -->
                                                            </tr>
                                                        </thead>
                                                        <tbody id="comp_body">
                                                            <tr><td class="text-center">Search to compare</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
.sticky-header th {
    position: sticky;
    top: 0;
    z-index: 10;
}
.trend-up { color: #10b981; font-weight: bold; }
.trend-down { color: #ef4444; font-weight: bold; }
.info-box {
    display: flex;
    min-height: 80px;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 10px;
    color: white;
}
.info-box-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    font-size: 30px;
}
.info-box-content {
    flex: 1;
    padding-left: 15px;
}
.info-box-text { text-transform: uppercase; font-size: 12px; }
.info-box-number { display: block; font-weight: bold; font-size: 24px; }
</style>

<script>
$(document).ready(function() {
    // Full Broadsheet Preview
    $('#search_full').on('click', function() {
        var form = $(this).closest('form');
        var criteria = {
            year_id: form.find('select[name="year_id"]').val(),
            class_id: form.find('select[name="class_id"]').val(),
            section_id: form.find('select[name="section_id"]').val(),
            term: form.find('select[name="term"]').val()
        };

        if(!criteria.year_id || !criteria.class_id) {
            toastr.error('Please select Year and Class');
            return;
        }

        $.ajax({
            url: "{{ route('broadsheet.full.get') }}",
            type: "GET",
            data: criteria,
            success: function(response) {
                // Update Header
                var headerHtml = '<th>Student Name</th><th>ID Number</th>';
                $.each(response.subjects, function(k, sub) {
                    headerHtml += '<th>' + sub.name + '</th>';
                });
                $('#full_header').html(headerHtml);

                // Update Body
                var bodyHtml = '';
                $.each(response.students, function(k, st) {
                    bodyHtml += '<tr><td>' + st.name + '</td><td>' + st.id_no + '</td>';
                    $.each(response.subjects, function(ks, sub) {
                        bodyHtml += '<td>' + (st.subjects[sub.id] || '-') + '</td>';
                    });
                    bodyHtml += '</tr>';
                });
                $('#full_results_body').html(bodyHtml);
            },
            error: function() {
                $('#full_results_body').html('<tr><td colspan="2" class="text-center text-danger">No records found for these criteria.</td></tr>');
            }
        });
    });

    // Dynamic Subject Loading
    $('#sub_class_id').on('change', function() {
        var class_id = $(this).val();
        if(class_id) {
            $.ajax({
                url: "{{ route('marks.getsubject') }}",
                type: "GET",
                data: {class_id: class_id},
                success: function(data) {
                    var html = '<option value="">Select Subject</option>';
                    $.each(data, function(key, v) {
                        html += '<option value="'+v.id+'">'+v.school_subject.name+'</option>';
                    });
                    $('#sub_subject_id').html(html);
                }
            });
        }
    });

    // Subject Search
    $('#search_subject').on('click', function() {
        var criteria = {
            year_id: $('#sub_year_id').val(),
            class_id: $('#sub_class_id').val(),
            section_id: $('#sub_section_id').val(),
            subject_id: $('#sub_subject_id').val(),
            term: $('#sub_term').val()
        };

        if(!criteria.subject_id || !criteria.term) {
            toastr.error('Please select all criteria');
            return;
        }

        $.ajax({
            url: "{{ route('broadsheet.subject.get') }}",
            type: "GET",
            data: criteria,
            success: function(response) {
                $('#subject_stats_area').show();
                $('#sub_avg').text(parseFloat(response.stats.average).toFixed(2));
                $('#sub_high').text(response.stats.highest);
                $('#sub_low').text(response.stats.lowest);

                var html = '';
                $.each(response.marks, function(key, val) {
                    html += '<tr>' +
                        '<td>' + val.position + '</td>' +
                        '<td>' + val.student.name + '</td>' +
                        '<td>' + val.id_no + '</td>' +
                        '<td>' + (val.ca_score || 0) + '</td>' +
                        '<td>' + (val.exam_score || 0) + '</td>' +
                        '<td>' + val.total_score + '</td>' +
                        '<td><span class="badge badge-pill badge-primary">' + (val.grade || 'N/A') + '</span></td>' +
                    '</tr>';
                });
                $('#subject_results_body').html(html);
            },
            error: function() {
                $('#subject_stats_area').hide();
                $('#subject_results_body').html('<tr><td colspan="7" class="text-center text-danger">No records found</td></tr>');
            }
        });
    });

    // Performance Comparison
    var performanceChart;
    $('#compare_performance').on('click', function() {
        var terms = [];
        $('input[name="comp_terms[]"]:checked').each(function() {
            terms.push($(this).val());
        });

        if(terms.length < 2) {
            toastr.warning('Select at least 2 terms');
            return;
        }

        $.ajax({
            url: "{{ route('broadsheet.compare.get') }}",
            type: "GET",
            data: {
                year_id: $('#comp_year_id').val(),
                class_id: $('#comp_class_id').val(),
                terms: terms
            },
            success: function(response) {
                // Update Table Header
                var headerHtml = '<th>Subject</th>';
                $.each(response.terms, function(k, t) {
                    headerHtml += '<th>' + t + '</th>';
                });
                $('#comp_header').html(headerHtml);

                // Update Table Body
                var bodyHtml = '';
                $.each(response.comparison, function(k, item) {
                    bodyHtml += '<tr><td>' + item.subject + '</td>';
                    var prevVal = null;
                    $.each(response.terms, function(kt, t) {
                        var val = item.terms[t] || 0;
                        var trend = '';
                        if(prevVal !== null) {
                            if(val > prevVal) trend = ' <span class="trend-up">↑</span>';
                            else if(val < prevVal) trend = ' <span class="trend-down">↓</span>';
                        }
                        bodyHtml += '<td>' + val + trend + '</td>';
                        prevVal = val;
                    });
                    bodyHtml += '</tr>';
                });
                $('#comp_body').html(bodyHtml);

                // Update Chart
                var seriesData = [];
                $.each(response.terms, function(k, t) {
                    seriesData.push(response.overall[t]);
                });

                if(performanceChart) performanceChart.destroy();

                var options = {
                    series: [{
                        name: 'Overall Average',
                        data: seriesData
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: { enabled: false },
                        toolbar: { show: false }
                    },
                    colors: ['#727cf5'],
                    dataLabels: { enabled: true },
                    stroke: { curve: 'smooth' },
                    title: { text: 'Class Performance Trend', align: 'left' },
                    grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
                    xaxis: { categories: response.terms }
                };

                performanceChart = new ApexCharts(document.querySelector("#performance_chart"), options);
                performanceChart.render();
            }
        });
    });

    // CSV Export
    $('.btn-export-csv').on('click', function() {
        var type = $(this).data('type');
        var params = {};
        
        if(type == 'subject') {
            params = {
                type: 'subject',
                year_id: $('#sub_year_id').val(),
                class_id: $('#sub_class_id').val(),
                subject_id: $('#sub_subject_id').val(),
                term: $('#sub_term').val()
            };
            if(!params.subject_id) { toastr.error('Select criteria first'); return; }
        } else if(type == 'comparison') {
            var terms = [];
            $('input[name="comp_terms[]"]:checked').each(function() {
                terms.push($(this).val());
            });
            params = {
                type: 'comparison',
                year_id: $('#comp_year_id').val(),
                class_id: $('#comp_class_id').val(),
                terms: terms.join(',')
            };
            if(terms.length < 2) { toastr.error('Select at least 2 terms'); return; }
        } else {
            // Full broadsheet from the first tab
            var form = $(this).closest('form');
            params = {
                type: 'full',
                year_id: form.find('select[name="year_id"]').val(),
                class_id: form.find('select[name="class_id"]').val(),
                term: form.find('select[name="term"]').val()
            };
            if(!params.year_id) { toastr.error('Select criteria first'); return; }
        }

        var url = "{{ route('broadsheet.export.csv') }}?" + $.param(params);
        window.location.href = url;
    });
});
</script>

@endsection
