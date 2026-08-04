<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Report Card</title>
<style>
    @page { size: A4 portrait; margin: {{ !empty($forPdf) ? '6mm' : '20px' }}; }
    body {
        font-family: Arial, sans-serif;
        color: #000;
        margin: 0;
        padding: 0;
        background: #fff;
        line-height: 1.2;
        font-size: {{ !empty($forPdf) ? '9px' : '11px' }};
    }
    .report-card {
        width: 100%;
        max-width: {{ !empty($forPdf) ? 'none' : '900px' }};
        margin: 0 auto;
        border: {{ !empty($forPdf) ? '1px' : '2px' }} solid #002366;
        padding: {{ !empty($forPdf) ? '5px' : '10px' }};
        box-sizing: border-box;
        position: relative;
        page-break-inside: avoid;
    }
    .watermark {
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -30%);
        opacity: 0.05;
        width: {{ !empty($forPdf) ? '220px' : '300px' }};
        z-index: 0;
        pointer-events: none;
    }
    .content-wrapper {
        position: relative;
        z-index: 1;
    }
    .header {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        color: #002366;
        margin-bottom: {{ !empty($forPdf) ? '2px' : '5px' }};
    }
    .header td {
        border: none;
        vertical-align: middle;
        padding: 0;
    }
    .header .image-cell {
        width: {{ !empty($forPdf) ? '58px' : '80px' }};
    }
    .header .logo {
        width: {{ !empty($forPdf) ? '50px' : '70px' }};
        height: {{ !empty($forPdf) ? '50px' : '70px' }};
        object-fit: contain;
        image-orientation: from-image;
        transform: rotate(0deg);
    }
    .header .title-area {
        padding: 0 10px;
    }
    .header h1 {
        margin: 0;
        font-size: {{ !empty($forPdf) ? '20px' : '28px' }};
        font-weight: 800;
        letter-spacing: 0.6px;
        line-height: 1.05;
        color: #002366;
    }
    .header p {
        margin: {{ !empty($forPdf) ? '1px 0' : '2px 0' }};
        font-size: {{ !empty($forPdf) ? '9px' : '13px' }};
        font-weight: bold;
    }
    .header h2 {
        margin: {{ !empty($forPdf) ? '1px 0' : '2px 0' }};
        font-size: {{ !empty($forPdf) ? '11px' : '16px' }};
        font-weight: bold;
    }
    .header h3 {
        margin: 0;
        font-size: {{ !empty($forPdf) ? '10px' : '14px' }};
        color: #000;
        background: none;
        padding: 0;
    }
    .section-title {
        background: #002366;
        color: white;
        padding: {{ !empty($forPdf) ? '2px 5px' : '4px 8px' }};
        font-size: {{ !empty($forPdf) ? '8.5px' : '12px' }};
        font-weight: bold;
        margin: {{ !empty($forPdf) ? '4px 0 0 0' : '8px 0 0 0' }};
        text-transform: uppercase;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: {{ !empty($forPdf) ? '8px' : '11px' }};
        margin-bottom: 0;
    }
    table th, table td {
        border: 1px solid #777;
        padding: {{ !empty($forPdf) ? '1.5px' : '3px' }};
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
        font-size: {{ !empty($forPdf) ? '8px' : '11px' }};
    }
    .particulars-table td {
        border: none;
        text-align: left;
        padding: {{ !empty($forPdf) ? '1.5px 4px' : '3px 8px' }};
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
        gap: {{ !empty($forPdf) ? '6px' : '15px' }};
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
        padding: {{ !empty($forPdf) ? '1px 2px' : '2px 4px' }};
    }
    
    .remark-box {
        border: 1px solid #777;
        border-top: none;
        padding: {{ !empty($forPdf) ? '2px' : '5px' }};
        font-size: {{ !empty($forPdf) ? '8px' : '11px' }};
        min-height: {{ !empty($forPdf) ? '28px' : '52px' }};
        position: relative;
    }
    .remark-signoff {
        margin-top: {{ !empty($forPdf) ? '5px' : '12px' }};
        padding-top: {{ !empty($forPdf) ? '2px' : '4px' }};
        border-top: 1px solid #777;
        font-size: {{ !empty($forPdf) ? '7.5px' : '10px' }};
        line-height: 1.25;
    }
    .remark-signoff strong {
        color: #333;
    }
    .remarks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: {{ !empty($forPdf) ? '4px' : '8px' }};
    }
    .remarks-table td {
        border: none;
        vertical-align: top;
        width: 50%;
        padding: 0;
    }
    .remarks-table .left-remark {
        padding-right: {{ !empty($forPdf) ? '4px' : '8px' }};
    }
    .remarks-table .right-remark {
        padding-left: {{ !empty($forPdf) ? '4px' : '8px' }};
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

    $normalizePdfImage = function ($absolutePath) {
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg'], true)
            || !function_exists('exif_read_data')
            || !function_exists('imagecreatefromjpeg')
            || !function_exists('imagerotate')
        ) {
            return str_replace('\\', '/', $absolutePath);
        }

        $exif = @exif_read_data($absolutePath);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if (!in_array($orientation, [3, 6, 8], true)) {
            return str_replace('\\', '/', $absolutePath);
        }

        $image = @imagecreatefromjpeg($absolutePath);
        if (!$image) {
            return str_replace('\\', '/', $absolutePath);
        }

        if ($orientation === 3) {
            $image = imagerotate($image, 180, 0);
        } elseif ($orientation === 6) {
            $image = imagerotate($image, -90, 0);
        } elseif ($orientation === 8) {
            $image = imagerotate($image, 90, 0);
        }

        ob_start();
        imagejpeg($image, null, 90);
        $contents = ob_get_clean();
        imagedestroy($image);

        return 'data:image/jpeg;base64,'.base64_encode($contents);
    };

    $pdfAsset = function ($path, $fallback) use ($forPdf, $normalizePdfImage) {
        $relativePath = $path ?: $fallback;
        $absolutePath = public_path($relativePath);

        if (!file_exists($absolutePath)) {
            $absolutePath = public_path($fallback);
            $relativePath = $fallback;
        }

        return !empty($forPdf) ? $normalizePdfImage($absolutePath) : url($relativePath);
    };

    $schoolLogo = $pdfAsset(optional($setting)->logo, 'upload/logo/no_image.jpg');
    $studentImage = $pdfAsset(!empty($studentInfo->image) ? 'upload/student_images/'.$studentInfo->image : null, 'upload/no_image.jpg');
@endphp

@empty($forPdf)
    <div style="text-align: right; margin: 20px auto; max-width: 950px;" class="print-button">
        <button onclick="window.print()" style="padding: 10px 20px; background: #002366; color: #fff; border: none; cursor: pointer; font-size: 16px; font-weight: bold;">Print Report Card</button>
    </div>
@endempty

<div class="report-card">
    <img src="{{ $schoolLogo }}" class="watermark" alt="Watermark">
    
    <div class="content-wrapper">
        <table class="header">
            <tr>
                <td class="image-cell">
                    <img src="{{ $studentImage }}" class="logo" alt="Student Passport" style="border: 2px solid #002366; object-fit: cover;">
                </td>
                <td class="title-area">
                    <h1>{{ strtoupper(optional($setting)->school_name ?? 'FEDERAL GOVERNMENT COLLEGE, ABUJA') }}</h1>
                    <p>{{ strtoupper(optional($setting)->school_address ?? 'P.M.B. 123, GARKI, ABUJA') }}</p>
                    @if(optional($setting)->school_mobile_one)
                        <p>TEL: {{ $setting->school_mobile_one }} {{ $setting->school_mobile_two ? '/ '.$setting->school_mobile_two : '' }}</p>
                    @endif
                    <h2>{{ $sectionInfo ? strtoupper($sectionInfo->name) : 'SECONDARY' }} REPORT CARD</h2>
                    <h3>{{ optional($yearInfo)->name }} ACADEMIC SESSION</h3>
                </td>
                <td class="image-cell">
                    <img src="{{ $schoolLogo }}" class="logo" alt="School Logo">
                </td>
            </tr>
        </table>

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

        <table class="remarks-table">
            <tr>
                <td class="left-remark">
                    <div class="section-title">CLASS TEACHER'S REMARK</div>
                    <div class="remark-box">
                        @if(isset($assessment) && $assessment->teacher_comment)
                            {{ $assessment->teacher_comment }}
                        @endif
                        <div class="remark-signoff">
                            <strong>Class Teacher:</strong> {{ optional($classTeacher->teacher)->name ?? '---' }}<br>
                            <strong>Date:</strong> {{ date('jS F, Y') }}
                        </div>
                    </div>
                </td>
                <td class="right-remark">
                    <div class="section-title">{{ $sectionInfo && $sectionInfo->head_title ? strtoupper($sectionInfo->head_title) : 'PRINCIPAL' }}'S REMARK</div>
                    <div class="remark-box">
                        @if(isset($assessment) && $assessment->head_teacher_comment)
                            {{ $assessment->head_teacher_comment }}
                        @endif
                        <div class="remark-signoff">
                            <strong>{{ $sectionInfo && $sectionInfo->head_title ? $sectionInfo->head_title : 'Principal' }}:</strong> {{ optional($sectionInfo->headTeacher)->name ?? '---' }}<br>
                            <strong>Date:</strong> {{ date('jS F, Y') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>
