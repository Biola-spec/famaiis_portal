<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array { return []; }

    public function headings(): array
    {
        return ['admission_no', 'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender', 'email', 'guardian_phone', 'address'];
    }
}
