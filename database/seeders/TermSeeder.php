<?php

namespace Database\Seeders;

use App\Models\StudentYear;
use App\Models\Term;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    public function run(): void
    {
        $years = StudentYear::all();

        foreach ($years as $year) {
            $existingTerms = Term::where('session_id', $year->id)->count();
            
            if ($existingTerms == 0) {
                Term::create([
                    'name' => 'First Term',
                    'session_id' => $year->id,
                    'student_year_id' => $year->id,
                    'is_active' => false,
                ]);
                
                Term::create([
                    'name' => 'Second Term',
                    'session_id' => $year->id,
                    'student_year_id' => $year->id,
                    'is_active' => false,
                ]);
                
                Term::create([
                    'name' => 'Third Term',
                    'session_id' => $year->id,
                    'student_year_id' => $year->id,
                    'is_active' => false,
                ]);
            }
        }
    }
}
