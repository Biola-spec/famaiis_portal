TASK: report_comment
STUDENT: {{ $student->name }}, {{ $className }}
SUBJECTS AND PRE-CALCULATED RESULTS:
{{ $marksJson }}
TERM: {{ $term }}
TEACHER'S ROUGH NOTES: "{{ $teacherNotes }}"
PREVIOUS COMMENT: "{{ $previousComment }}"
TONE: {{ $tone }}
LENGTH: 3 sentences
OUTPUT FORMAT: plain text only, no heading, do not repeat the student's name.

Write a report comment based only on the supplied facts.
