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
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
</head>
<body>


<table id="customers">
  <tr>
   <td><h2>
  <img src="{{ public_path($setting->logo) }}" width="100" height="100">
    </h2></td>
    <td><h2>{{ $setting->school_name }}</h2>
<p>{{ $setting->address }}</p>
<p>Phone : {{ $setting->phone_one }}</p>
<p>Email : {{ $setting->school_email }}</p>

    </td> 
  </tr>
  
   
</table>



<table id="customers">
  <tr>
    <th width="10%">Sl</th>
    <th width="45%">Student Details</th>
    <th width="45%">Student Data</th>
  </tr>
  <tr>
    <td>1</td>
    <td><b>Full Name</b></td>
    <td>{{ $details['student']['name'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>2</td>
    <td><b>First Name</b></td>
    <td>{{ $details['student']['first_name'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>3</td>
    <td><b>Surname</b></td>
    <td>{{ $details['student']['surname'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>4</td>
    <td><b>Middle Name</b></td>
    <td>{{ $details['student']['middle_name'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>5</td>
    <td><b>Student ID No</b></td>
    <td>{{ $details['student']['id_no'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>6</td>
    <td><b>Student Email</b></td>
    <td>{{ $details['student']['email'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>7</td>
    <td><b>Parent Name</b></td>
    <td>
        @if(!empty($details['student']) && $details['student']->parents->isNotEmpty())
            {{ $details['student']->parents->first()->name }}
        @else
            No Parent Linked
        @endif
    </td>
  </tr>
  <tr>
    <td>8</td>
    <td><b>Mobile Number </b></td>
    <td>{{ $details['student']['mobile'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>9</td>
    <td><b>Address</b></td>
    <td>{{ $details['student']['address'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>10</td>
    <td><b>Gender</b></td>
    <td>{{ $details['student']['gender'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>11</td>
    <td><b>Date of Birth</b></td>
    <td>{{ (!empty($details['student']['dob'])) ? date('d-m-Y', strtotime($details['student']['dob'])) : 'N/A' }}</td>
  </tr>
  <tr>
    <td>12</td>
    <td><b>NIN</b></td>
    <td>{{ $details['student']['nin'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>13</td>
    <td><b>Country</b></td>
    <td>{{ $details['student']['country'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>14</td>
    <td><b>State</b></td>
    <td>{{ $details['student']['state'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>15</td>
    <td><b>LGA</b></td>
    <td>{{ $details['student']['lga'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>16</td>
    <td><b>Academic Session</b></td>
    <td>{{ $details['student_year']['name'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>17</td>
    <td><b>Class</b></td>
    <td>{{ $details['student_class']['name'] ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>18</td>
    <td><b>Section</b></td>
    <td>{{ (!empty($details['student']) && $details['student']->section) ? $details['student']->section->name : 'N/A' }}</td>
  </tr>
  <tr>
    <td>19</td>
    <td><b>Group</b></td>
    <td>{{ optional($details['group'])->name ?? 'N/A' }}</td>
  </tr>
  <tr>
    <td>20</td>
    <td><b>Discount </b></td>
    <td>{{ $details['discount']['discount'] ?? '0' }} %</td>
  </tr>
   
</table>
<br> <br>
  <i style="font-size: 10px; float: right;">Print Data : {{ date("d M Y") }}</i>

</body>
</html>
