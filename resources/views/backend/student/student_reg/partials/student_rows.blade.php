@forelse($allData as $key => $value)
<tr>
    <td>{{ $allData->firstItem() + $key }}</td>
    <td>{{ $value['student']['name'] ?? 'N/A' }}</td>
    <td>{{ $value['student']['id_no'] ?? 'N/A' }}</td>
    <td>{{ $value->roll }}</td>
    <td>{{ $value['student_year']['name'] ?? 'N/A' }}</td>
    <td>{{ $value['student_class']['name'] ?? 'N/A' }}</td>
    <td>
        <img src="{{ (!empty($value['student']) && !empty($value['student']['image'])) ? url('upload/student_images/'.$value['student']['image']) : url('upload/no_image.jpg') }}" style="width: 60px; height: 60px;">
    </td>
    @if(Auth::user()->role == "Admin")
    <td>{{ $value['student']['code'] ?? 'N/A' }}</td>
    @endif
    <td>
        @if(Auth::user()->hasPermission('edit_student'))
        <a title="Edit" href="{{ route('student.registration.edit', $value->student_id) }}" class="btn btn-info"><i class="fa fa-edit"></i></a>
        @endif

        <a title="Promotion" href="{{ route('student.registration.promotion', $value->student_id) }}" class="btn btn-primary"><i class="fa fa-check"></i></a>

        <a target="_blank" title="Details" href="{{ route('student.registration.details', $value->student_id) }}" class="btn btn-danger"><i class="fa fa-eye"></i></a>

        @if(Auth::user()->hasPermission('delete_student'))
        <a title="Delete" href="{{ route('student.registration.delete', $value->id) }}" class="btn btn-warning" id="delete"><i class="fa fa-trash"></i></a>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="{{ Auth::user()->role == 'Admin' ? 9 : 8 }}" class="text-center">No students found.</td>
</tr>
@endforelse
