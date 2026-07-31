<!DOCTYPE html>
<html>
<head>
<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
  font-size: 11px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #1a237e;
  color: white;
}
.header {
    text-align: center;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="header">
    <h2>{{ $setting->school_name }}</h2>
    <h3>Academic Broadsheet - {{ $term }}</h3>
    <p>Year: {{ $year->name }} | Class: {{ $class->name }}</p>
</div>

<table id="customers">
  <thead>
    <tr>
      <th>Student Name</th>
      <th>ID Number</th>
      @foreach($subjects as $sub)
        <th>{{ $sub->name }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($students as $studentId => $studentMarks)
    <tr>
      <td>{{ $studentMarks->first()->student->name ?? 'N/A' }}</td>
      <td>{{ $studentMarks->first()->id_no }}</td>
      @foreach($subjects as $sub)
        @php
            $mark = $studentMarks->where('subject_id', $sub->id)->first();
        @endphp
        <td>{{ $mark ? $mark->total_score : '-' }}</td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>

<br>
<i style="font-size: 10px; float: right;">Generated on: {{ date("d M Y H:i") }}</i>

</body>
</html>
