<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Report Card</title>
<style>
    @page { margin: 20px; }
    body {
        font-family: Arial, sans-serif;
        color: #000;
        margin: 0;
        padding: 0;
        background: #fff;
        line-height: 1.2;
    }
    .report-card {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        border: 2px solid #002366;
        padding: 10px;
        box-sizing: border-box;
        position: relative;
    }
    .watermark {
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -30%);
        opacity: 0.05;
        width: 300px;
        z-index: 0;
        pointer-events: none;
    }
    .content-wrapper {
        position: relative;
        z-index: 1;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: center;
        color: #002366;
        margin-bottom: 5px;
    }
    .header .logo {
        width: 70px;
        height: auto;
    }
    .header .title-area {
        flex-grow: 1;
        padding: 0 10px;
    }
    .header h1 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
    }
    .header p {
        margin: 2px 0;
        font-size: 13px;
        font-weight: bold;
    }
    .header h2 {
        margin: 2px 0;
        font-size: 16px;
        font-weight: bold;
    }
    .header h3 {
        margin: 0;
        font-size: 14px;
        color: #000;
        background: none;
        padding: 0;
    }
    .section-title {
        background: #002366;
        color: white;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: bold;
        margin: 8px 0 0 0;
        text-transform: uppercase;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-bottom: 0;
    }
    table th, table td {
        border: 1px solid #777;
        padding: 3px;
        text-align: center;
    }
    table th {
        background: #f4f4f4;
        color: #002366;
        font-weight: bold;
    }
    /* Particulars */
    .particulars-table {
        border: 1px solid #777;
        border-top: none;
        width: 100%;
        font-size: 11px;
    }
    .particulars-table td {
        border: none;
        text-align: left;
        padding: 3px 8px;
    }
    .particulars-table td strong {
        font-weight: normal;
        color: #333;
    }
    /* Academic Performance */
    .academic-table {
        border-top: none;
    }
    .academic-table th {
        background: #f0f0f0;
    }
    .academic-table td.subject-name {
        text-align: left;
    }
    
    .grid-2 {
        display: flex;
        gap: 15px;
        margin-top: 0;
    }
    .grid-2 > div {
        flex: 1;
    }
    .grid-table {
        border-top: none;
    }
    .grid-table th {
        background: #fff;
        color: #002366;
    }
    .grid-table td {
        border: none;
        border-bottom: 1px solid #ccc;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        padding: 2px 4px;
    }
    
    .remark-box {
        border: 1px solid #777;
        border-top: none;
        padding: 5px;
        font-size: 11px;
        min-height: 20px;
    }
    
    .signatures {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 15px;
        text-align: center;
        font-size: 11px;
        font-weight: bold;
    }
    .signatures .sign-box {
        width: 200px;
    }
    .signatures .sign-line {
        border-bottom: 1px solid #000;
        margin-bottom: 3px;
        height: 25px;
    }
    .seal-box {
        text-align: center;
        color: #002366;
        font-weight: bold;
    }
    .seal-box img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid #002366;
        margin-bottom: 3px;
    }

    @media print {
        .print-button { display: none; }
        .report-card { border: 4px solid #002366; }
    }
</style>
</head>
<body>

@php
    $studentInfo = optional($allMarks[0])->student;
    $sectionInfo = $sectionInfo ?? optional($studentInfo)->section;
    $classInfo = optional($allMarks[0])->student_class;
    $yearInfo = optional($allMarks[0])->year;

    $schoolLogo = !empty($setting->logo) ? url($setting->logo) : url('upload/logo/no_image.jpg');
    $studentImage = !empty($studentInfo->image) ? url('upload/student_images/'.$studentInfo->image) : url('upload/no_image.jpg');
@endphp

<div style="text-align: right; margin: 20px auto; max-width: 950px;" class="print-button">
    <button onclick="window.print()" style="padding: 10px 20px; background: #002366; color: #fff; border: none; cursor: pointer; font-size: 16px; font-weight: bold;">Print Report Card</button>
</div>

<div class="report-card">
    <img src="{{ $schoolLogo }}" class="watermark" alt="Watermark">
    
    <div class="content-wrapper">
        <div class="header">
            <div>
                <img src="{{ $schoolLogo }}" class="logo" alt="School Logo">
            </div>
            <div class="title-area">
                <h1>{{ strtoupper(optional($setting)->school_name ?? 'FEDERAL GOVERNMENT COLLEGE, ABUJA') }}</h1>
                <p>{{ strtoupper(optional($setting)->school_address ?? 'P.M.B. 123, GARKI, ABUJA') }}</p> 
                @if(optional($setting)->school_mobile_one)
                    <p>TEL: {{ $setting->school_mobile_one }} {{ $setting->school_mobile_two ? '/ '.$setting->school_mobile_two : '' }}</p>
                @endif
                <h2>{{ $sectionInfo ? strtoupper($sectionInfo->name) : 'SECONDARY' }} REPORT CARD</h2>
                <h3>{{ optional($yearInfo)->name }} ACADEMIC SESSION</h3>
            </div>
            <div>
                <img src="{{ $studentImage }}" class="logo" alt="Student Passport" style="border: 2px solid #002366; object-fit: cover;">
            </div>
        </div>

        <!-- A. STUDENT'S PARTICULARS -->
        <div class="section-title">A. STUDENT'S PARTICULARS</div>
        <table class="particulars-table">
            <tr>
                <td style="width: 20%;"><strong>NAME OF STUDENT:</strong></td>
                <td style="width: 30%;">{{ optional($studentInfo)->name }}</td>
                <td style="width: 20%;"><strong>DATE OF BIRTH:</strong></td>
                <td style="width: 30%;">{{ optional($studentInfo)->dob ? date('jS F, Y', strtotime($studentInfo->dob)) : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>ADMISSION NUMBER:</strong></td>
                <td>{{ optional($studentInfo)->id_no }}</td>
                <td><strong>GENDER:</strong></td>
                <td>{{ optional($studentInfo)->gender }}</td>
            </tr>
            <tr>
                <td><strong>CLASS:</strong></td>
                <td>{{ optional($classInfo)->name }}</td>
                <td><strong>TERM:</strong></td>
                <td>{{ $term }}</td>
            </tr>
            <tr>
                <td><strong>SECTION:</strong></td>
                <td>{{ optional($sectionInfo)->name ?? 'N/A' }}</td>
                <td><strong>SESSION:</strong></td>
                <td>{{ optional($yearInfo)->name }}</td>
            </tr>
        </table>

        <!-- B. ACADEMIC PERFORMANCE -->
        <div class="section-title">B. ACADEMIC PERFORMANCE</div>
        <table class="academic-table">
            <thead>
                <tr>
                    <th style="width: 5%;">S/N</th>
                    <th style="width: 30%; text-align: left;">SUBJECT</th>
                    @if($markingConfig && is_array($markingConfig->ca_labels))
                        @php $labels = $markingConfig->ca_labels; $weights = $markingConfig->ca_weights; @endphp
                        @foreach($labels as $k => $label)
                            <th>{{ $label }} ({{ $weights[$k] ?? '0' }}%)</th>
                        @endforeach
                    @else
                        <th style="width: 12%;">CA (30%)</th>
                    @endif
                    <th style="width: 12%;">{{ $markingConfig->exam_label ?? 'EXAM' }} ({{ $markingConfig->exam_weight ?? '70' }}%)</th>
                    <th style="width: 12%;">TOTAL (%)</th>
                    <th style="width: 10%;">GRADE</th>
                    <th style="width: 14%;">REMARK</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalScore = 0;
                    $subjectCount = count($allMarks);
                    $numPassed = 0;
                    $numFailed = 0;
                @endphp

                @foreach($allMarks as $key => $mark)
                    @php
                        $ca_score = $mark->ca_score ?? 0;
                        $exam_score = $mark->exam_score ?? 0;
                        $total = $mark->marks ?? 0;
                        $totalScore += $total;

                        $grade_marks = App\Models\MarksGrade::where([['start_marks','<=', (int)$total],['end_marks', '>=',(int)$total ]])->first();
                        $grade_name = optional($grade_marks)->grade_name ?? 'N/A';
                        $remark = optional($grade_marks)->remarks ?? 'N/A';

                        if($total >= 40) {
                            $numPassed++;
                        } else {
                            $numFailed++;
                        }

                        $breakdown = $mark->ca_breakdown ?? [];
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="subject-name">{{ optional($mark->subject)->name ?? optional(optional($mark->assign_subject)->school_subject)->name }}</td>
                        
                        @if($markingConfig && is_array($markingConfig->ca_labels))
                            @foreach($markingConfig->ca_labels as $k => $label)
                                <td>{{ $breakdown[$k] ?? '0' }}</td>
                            @endforeach
                        @else
                            <td>{{ $ca_score }}</td>
                        @endif

                        <td>{{ $exam_score }}</td>
                        <td>{{ $total }}</td>
                        <td>{{ $grade_name }}</td>
                        <td>{{ $remark }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid-2">
            <!-- C. GRADING KEY -->
            <div>
                <div class="section-title">C. GRADING KEY</div>
                <table class="grid-table" style="border: 1px solid #777; border-top: none;">
                    <thead>
                        <tr>
                            <th style="background:#fff; color:#002366;">GRADE</th>
                            <th style="background:#fff; color:#002366;">SCORE (%)</th>
                            <th style="background:#fff; color:#002366; text-align:left;">REMARK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allGrades as $grade)
                        <tr>
                            <td><strong>{{ $grade->grade_name }}</strong></td>
                            <td>{{ $grade->start_marks }} - {{ $grade->end_marks }}</td>
                            <td style="text-align:left;">{{ $grade->remarks }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- D. SUMMARY OF PERFORMANCE -->
            @php
                $avg = $subjectCount > 0 ? number_format($totalScore / $subjectCount, 2) : 0;
                $overallGrade = App\Models\MarksGrade::where([['start_marks','<=', (int)$avg],['end_marks', '>=',(int)$avg ]])->first();
                $avgGradeName = optional($overallGrade)->grade_name ?? 'N/A';

                // Calculate Position
                $studentIdForPosition = optional($studentInfo)->id;
                $positionQuery = App\Models\StudentMarks::where('year_id', $year_id)
                    ->where('class_id', $class_id)
                    ->where('term', $term);
                
                if ($section_id) {
                    $positionQuery->where('section_id', $section_id);
                }

                $allStudentsMarks = $positionQuery->selectRaw('student_id, SUM(marks) as total')
                    ->groupBy('student_id')
                    ->orderByDesc('total')
                    ->get();

                $position = 0;
                foreach ($allStudentsMarks as $index => $sm) {
                    if ($sm->student_id == $studentIdForPosition) {
                        $position = $index + 1;
                        break;
                    }
                }

                if (!function_exists('appendSuffix')) {
                    function appendSuffix($number) {
                        if ($number % 100 >= 11 && $number % 100 <= 13) {
                            return $number . 'th';
                        }
                        switch ($number % 10) {
                            case 1: return $number . 'st';
                            case 2: return $number . 'nd';
                            case 3: return $number . 'rd';
                            default: return $number . 'th';
                        }
                    }
                }
            @endphp
            <div>
                <div class="section-title">D. SUMMARY OF PERFORMANCE</div>
                <table class="grid-table" style="border: 1px solid #777; border-top: none;">
                    <tbody>
                        <tr>
                            <td style="text-align:left;">NUMBER OF SUBJECTS:</td>
                            <td style="text-align:left;">{{ $subjectCount }}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">NUMBER PASSED:</td>
                            <td style="text-align:left;">{{ $numPassed }}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">NUMBER FAILED:</td>
                            <td style="text-align:left;">{{ $numFailed }}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">AGGREGATE SCORE (%):</td>
                            <td style="text-align:left;">{{ $avg }}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">AVERAGE GRADE:</td>
                            <td style="text-align:left;">{{ $avgGradeName }}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">POSITION IN CLASS:</td>
                            <td style="text-align:left;">{{ $position > 0 ? appendSuffix($position) : 'N/A' }} out of {{ count($allStudentsMarks) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid-2">
            <!-- E. CO-CURRICULAR ACTIVITIES -->
            <div>
                <div class="section-title">E. CO-CURRICULAR ACTIVITIES</div>
                <div style="display: flex; gap: 0; border: 1px solid #777; border-top: none;">
                    @php
                        $savedAreas = $assessment->cognitive_areas ?? [];
                        $chunks = array_chunk($assessmentAreas, 5);
                    @endphp
                    
                    @foreach($chunks as $chunk)
                    <table class="grid-table" style="flex: 1; border: none; margin: 0;">
                        <tbody>
                            @foreach($chunk as $area)
                            <tr>
                                <td style="text-align:left; text-transform: capitalize; border-top: none; border-left: none;">{{ str_replace('_', ' ', $area) }}</td>
                                <td style="text-align:center; font-weight:bold; border-top: none; width: 40px;">{{ $savedAreas[$area] ?? '---' }}</td>
                            </tr>
                            @endforeach
                            {{-- Fill empty rows to maintain alignment if chunks are uneven --}}
                            @for($i = count($chunk); $i < 5; $i++)
                            <tr>
                                <td style="border: none;">&nbsp;</td>
                                <td style="border: none;">&nbsp;</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                    @endforeach
                </div>
            </div>

            <!-- F. ATTENDANCE -->
            <div>
                <div class="section-title">F. ATTENDANCE</div>
                <table class="grid-table" style="border: 1px solid #777; border-top: none;">
                    <tbody>
                        <tr>
                            <td style="text-align:left;">NUMBER OF DAYS SCHOOL OPENED:</td>
                            <td style="text-align:center;">---</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">NUMBER OF DAYS PRESENT:</td>
                            <td style="text-align:center;">---</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">NUMBER OF DAYS ABSENT:</td>
                            <td style="text-align:center;">---</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;">ATTENDANCE (%):</td>
                            <td style="text-align:center;">---</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- G. CLASS TEACHER'S REMARK -->
        <div class="section-title">G. CLASS TEACHER'S REMARK</div>
        <div class="remark-box">
            @if(isset($assessment) && $assessment->teacher_comment)
                {{ $assessment->teacher_comment }}
            @endif
        </div>

        <!-- H. {{ $sectionInfo && $sectionInfo->head_title ? strtoupper($sectionInfo->head_title) : 'PRINCIPAL' }}'S REMARK -->
        <div class="section-title">H. {{ $sectionInfo && $sectionInfo->head_title ? strtoupper($sectionInfo->head_title) : 'PRINCIPAL' }}'S REMARK</div>
        <div class="remark-box">
            @if(isset($assessment) && $assessment->head_teacher_comment)
                {{ $assessment->head_teacher_comment }}
            @endif
        </div>

        <!-- SIGNATURES -->
        <div class="signatures">
            <div class="sign-box">
                <div class="sign-line"></div>
                CLASS TEACHER
                <br>
                <span style="font-weight:bold; color: #555;">{{ optional($classTeacher->teacher)->name ?? '---' }}</span>
                <br>
                <span style="font-weight:normal; font-size: 11px;">Date: {{ date('jS F, Y') }}</span>
            </div>
            
            <div class="seal-box">
                <img src="{{ $schoolLogo }}" alt="School Seal">
                <br>
                SCHOOL SEAL
            </div>

            <div class="sign-box">
                <div class="sign-line"></div>
                @if($sectionInfo && $sectionInfo->head_title)
                    {{ strtoupper($sectionInfo->head_title) }}
                    <br>
                    <span style="font-weight:bold; color: #555;">{{ optional($sectionInfo->headTeacher)->name }}</span>
                @else
                    PRINCIPAL
                @endif
                <br>
                <span style="font-weight:normal; font-size: 11px;">Date: {{ date('jS F, Y') }}</span>
            </div>
        </div>

    </div>
</div>

</body>
</html>
