@include('student.learning._content', [
    'course' => $course,
    'lesson' => $lesson,
    'enrollment' => $enrollment,
    'completedLessonIds' => $completedLessonIds,
])
