TASK: lesson_plan
SUBJECT: {{ $subject }}
CLASS: {{ $classLevel }}
WEEK: {{ $weekNumber }}
SCHEME TOPIC: {{ $schemeTopic }}
SCHEME OBJECTIVES: {{ $schemeObjectives }}
DURATION: {{ $durationMinutes }} minutes
RESOURCES AVAILABLE: {{ $resources }}

OUTPUT FORMAT: valid JSON only, matching this schema:
{"topic":"string","objectives":["string"],"instructional_materials":["string"],"previous_knowledge":"string","introduction":"string","development_steps":[{"step":"string","teacher_activity":"string","student_activity":"string","duration_minutes":0}],"evaluation_questions":["string"],"assignment":"string"}

Base the plan strictly on the supplied topic and objectives.
