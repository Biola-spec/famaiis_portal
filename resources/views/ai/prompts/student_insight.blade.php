TASK: student_insight
STUDENT: {{ $student->name }}, {{ $className }}
PRE-CALCULATED TERM RESULTS:
{{ $termResultsJson }}
ATTENDANCE DATA: {{ $attendanceData }}
CONDUCT LOG: {{ $conductLog }}

OUTPUT FORMAT: plain text, 3-5 sentences, teacher/counselor audience.
Narrate patterns only. Do not speculate about causes. End with one concrete suggestion if a concerning pattern is visible.
