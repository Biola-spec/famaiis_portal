<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher Assignment Report</title>
<style>
  body {
    font-family: sans-serif;
    font-size: 12px;
    color: #333333;
    line-height: 1.4;
    margin: 0;
    padding: 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
  }

  .header-table td {
    padding: 5px;
    vertical-align: middle;
  }

  .title-text {
    font-size: 18px;
    font-weight: bold;
    color: #1e3a8a;
    margin: 0;
    padding: 0;
  }

  .subtitle-text {
    font-size: 13px;
    font-weight: bold;
    color: #475569;
    margin-top: 4px;
    margin-bottom: 0;
  }

  .report-table th, .report-table td {
    border: 1px solid #cbd5e1;
    padding: 7px 10px;
    text-align: left;
    vertical-align: middle;
  }

  .report-table th {
    background-color: #1e40af;
    color: #ffffff;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
  }

  .report-table tr:nth-child(even) {
    background-color: #f8fafc;
  }

  .section-header {
    background-color: #f1f5f9;
    font-weight: bold;
    font-size: 13px;
    color: #0f172a;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
  }

  .summary-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    vertical-align: top;
    background-color: #ffffff;
  }

  .total-row {
    background-color: #eff6ff !important;
    font-weight: bold;
    color: #1e3a8a;
  }

  .footer-text {
    font-size: 10px;
    color: #64748b;
    text-align: right;
    margin-top: 25px;
  }
</style>
</head>
<body>

<!-- Header Table -->
<table class="header-table">
  <tr>
    <td width="25%">
      @if(!empty($setting->logo))
        <img src="{{ public_path('upload/' . $setting->logo) }}" width="140" style="max-height: 70px;">
      @else
        <div style="font-size: 16px; font-weight: bold; color: #1e40af;">{{ $setting->school_name ?? 'SCHOOL MANAGEMENT SYSTEM' }}</div>
      @endif
    </td>
    <td width="75%" style="text-align: right;">
      <div class="title-text">{{ $setting->school_name ?? 'SCHOOL MANAGEMENT SYSTEM' }}</div>
      @if(!empty($setting->school_email))
        <div style="font-size: 11px; color: #64748b;">Email: {{ $setting->school_email }}</div>
      @endif
      <div class="subtitle-text">TEACHER ACADEMIC ASSIGNMENT REPORT</div>
    </td>
  </tr>
</table>

<hr style="border: 0; border-top: 2px solid #1e40af; margin-bottom: 15px;">

<!-- Teacher Info Table -->
<table class="report-table">
  <thead>
    <tr>
      <th colspan="2">Teacher Profile Details</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td width="35%"><b>Teacher Name</b></td>
      <td width="65%">{{ $teacher->name }}</td>
    </tr>
    <tr>
      <td><b>Teacher ID / Code</b></td>
      <td>{{ $teacher->id_no ?? $teacher->code ?? $teacher->id }}</td>
    </tr>
    <tr>
      <td><b>Email Address</b></td>
      <td>{{ $teacher->email }}</td>
    </tr>
    <tr>
      <td><b>Phone / Mobile</b></td>
      <td>{{ $teacher->mobile ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td><b>Designation</b></td>
      <td>{{ $teacher->designation->name ?? 'Teacher' }}</td>
    </tr>
    <tr class="total-row">
      <td><b>Total Classes Taking</b></td>
      <td><b>{{ $total_classes }} Class(es)</b></td>
    </tr>
    <tr class="total-row">
      <td><b>Total Subjects Taking</b></td>
      <td><b>{{ $total_subjects }} Subject(s)</b></td>
    </tr>
  </tbody>
</table>

<br>

<!-- Detailed Assignments Table -->
<table class="report-table">
  <thead>
    <tr>
      <th width="10%">SL</th>
      <th width="30%">Class</th>
      <th width="30%">Section</th>
      <th width="30%">Subject Taking</th>
    </tr>
  </thead>
  <tbody>
    @forelse($assignments as $key => $row)
      <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $row['class_name'] }}</td>
        <td>{{ $row['section_name'] }}</td>
        <td><b>{{ $row['subject_name'] }}</b></td>
      </tr>
    @empty
      <tr>
        <td colspan="4" style="text-align: center; color: #94a3b8;">No class or subject assignments found for this teacher.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<br>

<!-- Summary & Totals Table -->
<table class="summary-table">
  <tr>
    <td colspan="2" class="section-header">Assignments Summary & List Breakdown</td>
  </tr>
  <tr>
    <td width="50%">
      <b style="color: #1e40af;">Classes Taking (Total: {{ $total_classes }}):</b>
      <div style="margin-top: 6px; line-height: 1.6;">
        @forelse($unique_classes as $cls)
          - {{ $cls }}<br>
        @empty
          <span style="color: #94a3b8;">None</span>
        @endforelse
      </div>
    </td>
    <td width="50%">
      <b style="color: #1e40af;">Subjects Taking (Total: {{ $total_subjects }}):</b>
      <div style="margin-top: 6px; line-height: 1.6;">
        @forelse($unique_subjects as $sub)
          - {{ $sub }}<br>
        @empty
          <span style="color: #94a3b8;">None</span>
        @endforelse
      </div>
    </td>
  </tr>
  <tr class="total-row">
    <td><b>TOTAL UNIQUE CLASSES: {{ $total_classes }}</b></td>
    <td><b>TOTAL UNIQUE SUBJECTS: {{ $total_subjects }}</b></td>
  </tr>
</table>

<div class="footer-text">
  Print / Generated Date: {{ date("d M Y, h:i A") }}
</div>

</body>
</html>
