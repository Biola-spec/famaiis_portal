<?php

namespace App\Services;

use App\Models\Event;
use App\Models\SchoolTimetable;
use App\Models\User;
use Carbon\Carbon;

class SchoolScheduleService
{
    public function dashboardData(?User $user = null): array
    {
        $timetableQuery = SchoolTimetable::with(['section', 'studentClass', 'subject', 'teacher'])
            ->where('is_active', true);
        $teacherTimetable = collect();

        if ($user && !$this->isAdmin($user)) {
            $sectionIds = $this->sectionIdsFor($user);
            $timetableQuery->where(function ($query) use ($sectionIds) {
                $query->whereNull('section_id');
                if ($sectionIds->isNotEmpty()) {
                    $query->orWhereIn('section_id', $sectionIds);
                }
            });

            if ($this->isTeacher($user)) {
                $teacherTimetable = SchoolTimetable::with(['section', 'studentClass', 'subject'])
                    ->where('is_active', true)
                    ->where('teacher_id', $user->id)
                    ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                    ->orderBy('start_time')->get();
            }
        }

        return [
            'upcoming_events' => Event::with('section')
                ->where('event_date', '>=', Carbon::today())
                ->orderBy('event_date')->orderBy('event_time')
                ->limit(8)->get(),
            'calendar_events' => Event::with('section')
                ->whereBetween('event_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->orderBy('event_date')->orderBy('event_time')->get(),
            'timetable_entries' => $timetableQuery
                ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->orderBy('start_time')->get(),
            'teacher_timetable_entries' => $teacherTimetable,
        ];
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === 'Admin' || $user->hasRole('Admin') || $user->hasRole('Super Admin');
    }

    private function isTeacher(User $user): bool
    {
        return $user->role === 'Teacher' || $user->role === 'Staff' || $user->hasRole('Teacher') || $user->hasRole('Staff');
    }

    private function sectionIdsFor(User $user)
    {
        $ids = collect();

        if ($this->isTeacher($user)) {
            $ids = $ids->merge($user->teacherSections()->wherePivot('is_active', true)->pluck('school_sections.id'))
                ->merge($user->teacherAssignments()->whereNotNull('section_id')->pluck('section_id'));
        } elseif ($user->hasRole('Student') || strtolower((string) $user->usertype) === 'student') {
            $ids = $user->activeSections()->pluck('school_sections.id');
            if ($user->section_id) {
                $ids->push($user->section_id);
            }
        } elseif ($user->hasRole('Parent') || strtolower((string) $user->usertype) === 'parent') {
            $ids = $user->children()->with('activeSections')->get()
                ->flatMap(fn ($child) => $child->activeSections->pluck('id'));
        }

        return $ids->filter()->unique()->values();
    }
}
