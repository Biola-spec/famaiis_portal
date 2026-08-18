<?php

namespace App\Imports;

use App\Models\AssignStudent;
use App\Models\DiscountStudent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading, ShouldQueue
{
    use SkipsErrors, SkipsFailures;

    public function __construct(
        protected int $yearId,
        protected int $classId,
        protected int $sectionId,
        protected ?int $groupId = null,
    ) {}

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $code = (string) random_int(1000, 9999);
            $firstName = trim($row['first_name']);
            $lastName = trim($row['last_name']);
            $middleName = trim($row['middle_name'] ?? '');

            $user = new User();
            $user->id_no = trim($row['admission_no']);
            $user->name = trim($firstName . ' ' . $lastName . ' ' . $middleName);
            $user->first_name = $firstName;
            $user->surname = $lastName;
            $user->middle_name = $middleName ?: null;
            $user->dob = $row['date_of_birth'] ?? null;
            $user->gender = $row['gender'] ?? null;
            $user->email = $row['email'] ?? null;
            $user->mobile = $row['guardian_phone'] ?? null;
            $user->address = $row['address'] ?? null;
            $user->password = bcrypt($code);
            $user->usertype = 'student';
            $user->code = $code;
            $user->status = 1;
            $user->section_id = $this->sectionId;
            $user->save();

            $assignment = AssignStudent::create([
                'student_id' => $user->id,
                'year_id' => $this->yearId,
                'class_id' => $this->classId,
                'group_id' => $this->groupId,
            ]);

            DB::table('student_section')->insert([
                'student_id' => $user->id,
                'section_id' => $this->sectionId,
                'class_id' => $this->classId,
                'year_id' => $this->yearId,
                'is_active' => true,
                'enrollment_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DiscountStudent::create([
                'assign_student_id' => $assignment->id,
                'fee_category_id' => 1,
                'discount' => 0,
            ]);

            return $user;
        });
    }

    public function rules(): array
    {
        return [
            'admission_no' => ['required', 'distinct', 'unique:users,id_no'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'email' => ['nullable', 'email'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function batchSize(): int { return 200; }
    public function chunkSize(): int { return 200; }
}
