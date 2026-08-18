<?php

namespace App\Exports;

use App\Models\AssignStudent;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function __construct(protected ?int $yearId = null, protected ?int $classId = null) {}

    public function query()
    {
        return AssignStudent::query()
            ->with('student')
            ->whereHas('student')
            ->when($this->yearId, fn ($query) => $query->where('year_id', $this->yearId))
            ->when($this->classId, fn ($query) => $query->where('class_id', $this->classId))
            ->orderBy('class_id')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['admission_no', 'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender', 'email', 'guardian_phone', 'address'];
    }

    public function map($assignment): array
    {
        $student = $assignment->student;
        if (!$student) {
            return array_fill(0, 9, null);
        }

        return [$student->id_no, $student->first_name, $student->surname, $student->middle_name, $student->dob, $student->gender, $student->email, $student->mobile, $student->address];
    }
}
