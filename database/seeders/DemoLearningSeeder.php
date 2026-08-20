<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoLearningSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@pedslovo.local')->first();
        $student = User::where('email', 'student@pedslovo.local')->first();
        $section = Section::where('type', 'specialty')->orderBy('id')->first();

        if (!$teacher || !$student || !$section) {
            return;
        }

        $course = Course::updateOrCreate(
            ['slug' => 'demo-music-course'],
            [
                'section_id' => $section->id,
                'instructor_id' => $teacher->id,
                'title' => 'Демонстрационный учебный курс',
                'description' => 'Тестовый курс для проверки структуры обучения, групп, прогресса и SCORM.',
                'study_year' => 1,
                'pass_score' => 60,
                'certificate_enabled' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Lesson::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Вводный урок'],
            [
                'description' => 'Первый демонстрационный урок.',
                'lesson_type' => 'text',
                'sort_order' => 1,
                'is_required' => true,
                'is_active' => true,
            ]
        );

        $group = Group::updateOrCreate(
            ['code' => 'DEMO-01'],
            ['name' => 'Демонстрационная группа', 'curator_id' => $teacher->id, 'is_active' => true]
        );

        $group->users()->syncWithoutDetaching([$student->id]);
        $group->courses()->syncWithoutDetaching([$course->id]);

        Enrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $student->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );
    }
}
