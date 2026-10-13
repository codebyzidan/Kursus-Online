@include('student.learning._content', [
    'course' => $course,
    'lesson' => $current,
    'enrollment' => $enrollment,
    'completedLessonIds' => $completedLessonIds,
])
