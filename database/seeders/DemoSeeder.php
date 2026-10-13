<?php

namespace Database\Seeders;

use App\Enums\LessonType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Review;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::firstOrCreate(
            ['email' => 'admin@kursus.test'],
            [
                'name' => 'Aditya Pratama',
                'password' => 'password',
                'email_verified_at' => now(),
                'headline' => 'Administrator Platform',
                'is_active' => true,
            ],
        );
        $admin->assignRole(Role::findOrCreate(UserRole::Admin->value, 'web'));

        $instructors = collect([
            ['name' => 'Rani Wijaya', 'email' => 'rani@kursus.test', 'headline' => 'Full-stack Developer'],
            ['name' => 'Bagus Setiawan', 'email' => 'bagus@kursus.test', 'headline' => 'Product Designer'],
            ['name' => 'Sinta Maharani', 'email' => 'sinta@kursus.test', 'headline' => 'Digital Marketer'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@kursus.test', 'headline' => 'Data Analyst'],
        ])->map(function (array $data): User {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'headline' => $data['headline'],
                    'bio' => 'Pengajar praktik dengan pengalaman industri lebih dari 8 tahun.',
                    'is_active' => true,
                ],
            );
            $user->assignRole(Role::findOrCreate(UserRole::Instructor->value, 'web'));

            return $user;
        });

        $students = collect([
            ['name' => 'Dewi Lestari', 'email' => 'dewi@kursus.test'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@kursus.test'],
            ['name' => 'Melati Putri', 'email' => 'melati@kursus.test'],
        ])->map(function (array $data): User {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ],
            );
            $user->assignRole(Role::findOrCreate(UserRole::Student->value, 'web'));

            return $user;
        });

        // Kategori kursus.
        $categories = collect([
            ['name' => 'Pemrograman', 'icon' => 'code'],
            ['name' => 'Desain', 'icon' => 'palette'],
            ['name' => 'Pemasaran Digital', 'icon' => 'megaphone'],
            ['name' => 'Data & Analitik', 'icon' => 'bar-chart'],
            ['name' => 'Fotografi', 'icon' => 'camera'],
            ['name' => 'Pengembangan Diri', 'icon' => 'sparkles'],
        ])->mapWithKeys(function (array $data): array {
            $category = Category::query()->firstOrCreate(
                ['name' => $data['name']],
                ['icon' => $data['icon'], 'is_active' => true, 'position' => 0],
            );

            return [$data['name'] => $category];
        });

        $blueprints = [
            [
                'title' => 'Belajar Laravel 12 dari Nol',
                'category' => 'Pemrograman',
                'instructor' => 0,
                'level' => 'beginner',
                'price' => 249000,
                'sale_price' => 179000,
                'lessons' => 6,
            ],
            [
                'title' => 'Dasar Desain UI dengan Figma',
                'category' => 'Desain',
                'instructor' => 1,
                'level' => 'beginner',
                'price' => 199000,
                'sale_price' => null,
                'lessons' => 5,
            ],
            [
                'title' => 'Digital Marketing untuk UMKM',
                'category' => 'Pemasaran Digital',
                'instructor' => 2,
                'level' => 'intermediate',
                'price' => 149000,
                'sale_price' => 99000,
                'lessons' => 4,
            ],
            [
                'title' => 'Analisis Data dengan Spreadsheet',
                'category' => 'Data & Analitik',
                'instructor' => 3,
                'level' => 'beginner',
                'price' => 0,
                'sale_price' => null,
                'lessons' => 4,
            ],
            [
                'title' => 'Fotografi Produk dengan Smartphone',
                'category' => 'Fotografi',
                'instructor' => 1,
                'level' => 'intermediate',
                'price' => 129000,
                'sale_price' => null,
                'lessons' => 3,
            ],
            [
                'title' => 'Public Speaking untuk Profesional',
                'category' => 'Pengembangan Diri',
                'instructor' => 2,
                'level' => 'advanced',
                'price' => 179000,
                'sale_price' => null,
                'lessons' => 3,
            ],
        ];

        $courses = collect($blueprints)->map(function (array $blueprint) use ($categories, $instructors): Course {
            $course = Course::factory()->published()->create([
                'instructor_id' => $instructors[$blueprint['instructor']]->getKey(),
                'category_id' => $categories[$blueprint['category']]->getKey(),
                'title' => $blueprint['title'],
                'level' => $blueprint['level'],
                'price' => $blueprint['price'],
                'sale_price' => $blueprint['sale_price'],
            ]);

            $this->buildCurriculum($course, (int) $blueprint['lessons']);
            $course->refreshCounters();

            return $course;
        });

        // Kuis akhir untuk dua kursus pertama.
        $courses->take(2)->each(function (Course $course): void {
            $this->buildFinalQuiz($course);
        });

        // Pembelian, enrollment, progres, ulasan, dan sertifikat.
        $courses->each(function (Course $course) use ($students): void {
            foreach ($students->random(2) as $index => $student) {
                if ($student->hasEnrolled($course)) {
                    continue;
                }

                $order = Order::factory()->paid()->create([
                    'user_id' => $student->getKey(),
                    'subtotal' => $course->getEffectivePriceAttribute(),
                    'total' => $course->getEffectivePriceAttribute(),
                ]);

                OrderItem::create([
                    'order_id' => $order->getKey(),
                    'course_id' => $course->getKey(),
                    'instructor_id' => $course->instructor_id,
                    'course_title' => $course->title,
                    'price' => $course->getEffectivePriceAttribute(),
                    'discount' => 0,
                    'instructor_earning' => (int) ($course->getEffectivePriceAttribute() * 0.7),
                ]);

                $enrollment = Enrollment::create([
                    'user_id' => $student->getKey(),
                    'course_id' => $course->getKey(),
                    'order_id' => $order->getKey(),
                    'source' => 'purchase',
                    'progress_percent' => 0,
                    'enrolled_at' => now()->subDays(14 - $index),
                ]);

                // Tandai sebagian materi selesai.
                $lessons = $course->lessons()->take(3)->get();
                foreach ($lessons as $lesson) {
                    LessonProgress::create([
                        'user_id' => $student->getKey(),
                        'lesson_id' => $lesson->getKey(),
                        'course_id' => $course->getKey(),
                        'last_position' => $lesson->duration,
                        'completed_at' => now()->subDays(10),
                    ]);
                }

                $enrollment->updateProgress();

                Review::factory()->create([
                    'user_id' => $student->getKey(),
                    'course_id' => $course->getKey(),
                    'rating' => fake()->numberBetween(4, 5),
                ]);
            }

            $course->recalculateRating();
        });

        Coupon::factory()->fixed(50000)->create([
            'code' => 'MULAI50',
            'description' => 'Diskon Rp50.000 untuk pembelian pertama',
        ]);

        Coupon::factory()->create([
            'code' => 'HEMAT20',
            'description' => 'Diskon 20% maksimal Rp100.000',
        ]);

        // Permintaan pencairan dana dari instruktur.
        $instructors->each(function (User $instructor): void {
            if ($instructor->courses()->count() === 0) {
                return;
            }

            Payout::factory()->create([
                'instructor_id' => $instructor->getKey(),
                'amount' => fake()->randomElement([750000, 1500000]),
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function buildCurriculum(Course $course, int $lessonCount): void
    {
        $sectionTitles = ['Pengenalan', 'Konsep Inti', 'Praktik', 'Studi Kasus'];

        for ($i = 0; $i < 3; $i++) {
            $section = Section::create([
                'course_id' => $course->getKey(),
                'title' => $sectionTitles[$i],
                'position' => $i + 1,
            ]);

            $perSection = (int) ceil($lessonCount / 3);

            for ($j = 0; $j < $perSection; $j++) {
                $type = $j % 3 === 0 ? LessonType::Video : ($j % 3 === 1 ? LessonType::Text : LessonType::Attachment);

                Lesson::create([
                    'section_id' => $section->getKey(),
                    'title' => $section->title.' — Materi '.($j + 1),
                    'type' => $type,
                    'content' => $type === LessonType::Text ? fake()->paragraphs(3, true) : null,
                    'video_url' => $type === LessonType::Video ? 'https://video.example.test/'.fake()->uuid() : null,
                    'video_provider' => $type === LessonType::Video ? 'self' : null,
                    'duration' => fake()->numberBetween(300, 1500),
                    'position' => $j + 1,
                    'is_preview' => $i === 0 && $j === 0,
                    'is_published' => true,
                ]);
            }
        }
    }

    private function buildFinalQuiz(Course $course): void
    {
        $quiz = Quiz::create([
            'course_id' => $course->getKey(),
            'title' => 'Kuis Akhir: '.$course->title,
            'description' => 'Kerjakan kuis untuk menguji pemahaman Anda.',
            'passing_grade' => 70,
            'max_attempts' => 3,
            'is_final' => true,
            'is_published' => true,
            'position' => 1,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $question = Question::create([
                'quiz_id' => $quiz->getKey(),
                'text' => 'Pertanyaan '.($i + 1).': '.rtrim(fake()->sentence(), '.').'?',
                'explanation' => 'Penjelasan jawaban '.($i + 1).'.',
                'points' => 1,
                'position' => $i + 1,
            ]);

            $correctIndex = $i % 4;

            for ($o = 0; $o < 4; $o++) {
                Option::create([
                    'question_id' => $question->getKey(),
                    'text' => 'Pilihan '.chr(65 + $o),
                    'is_correct' => $o === $correctIndex,
                    'position' => $o + 1,
                ]);
            }
        }
    }
}
