<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $this->instructor = User::factory()->instructor()->create();
    $this->admin = User::factory()->admin()->create();
    $this->student = User::factory()->student()->create();

    $this->course = Course::factory()->published()->create([
        'instructor_id' => $this->instructor->getKey(),
    ]);
    $this->section = $this->course->sections()->create(['title' => 'Pengenalan', 'position' => 1]);
    $this->lesson = $this->section->lessons()->create([
        'title' => 'Materi 1',
        'type' => 'video',
        'duration' => 600,
        'position' => 1,
        'is_published' => true,
    ]);

    $this->quiz = Quiz::factory()->create([
        'course_id' => $this->course->getKey(),
        'is_final' => true,
    ]);

    $this->enrollment = Enrollment::create([
        'user_id' => $this->student->getKey(),
        'course_id' => $this->course->getKey(),
        'source' => 'purchase',
    ]);

    $this->order = Order::factory()->paid()->create(['user_id' => $this->student->getKey()]);
    $this->order->items()->create([
        'course_id' => $this->course->getKey(),
        'instructor_id' => $this->instructor->getKey(),
        'course_title' => $this->course->title,
        'price' => 100000,
        'discount' => 0,
        'instructor_earning' => 70000,
    ]);
});

it('menampilkan halaman autentikasi', function () {
    $this->get(route('login'))->assertOk();
    $this->get(route('register'))->assertOk();
    $this->get(route('password.request'))->assertOk();
});

it('menampilkan seluruh halaman siswa', function () {
    Wishlist::create(['user_id' => $this->student->getKey(), 'course_id' => $this->course->getKey()]);
    Certificate::factory()->create(['enrollment_id' => $this->enrollment->getKey()]);

    $routes = [
        route('dashboard'),
        route('student.courses.index'),
        route('student.learning.show', $this->course),
        route('student.learning.lesson', [$this->course, $this->lesson]),
        route('student.quizzes.index', $this->course),
        route('student.quizzes.show', [$this->course, $this->quiz]),
        route('student.wishlist.index'),
        route('student.orders.index'),
        route('student.orders.show', $this->order),
        route('student.certificates.index'),
        route('student.instructor-application.create'),
        route('checkout.create', ['courses' => [$this->course->getKey()]]),
        route('checkout.show', $this->order),
        route('checkout.success', $this->order),
    ];

    foreach ($routes as $url) {
        $response = $this->actingAs($this->student)->get($url);
        expect($response->status())->toBe(200, "Gagal memuat: {$url} ({$response->status()})");
    }
});

it('menampilkan seluruh halaman instruktur', function () {
    $routes = [
        route('instructor.dashboard'),
        route('instructor.courses.index'),
        route('instructor.courses.create'),
        route('instructor.courses.edit', $this->course),
        route('instructor.courses.curriculum', $this->course),
        route('instructor.quizzes.index'),
        route('instructor.quizzes.create'),
        route('instructor.quizzes.edit', [$this->course, $this->quiz]),
        route('instructor.students.index'),
        route('instructor.earnings.index'),
        route('instructor.payouts.index'),
    ];

    foreach ($routes as $url) {
        $response = $this->actingAs($this->instructor)->get($url);
        expect($response->status())->toBe(200, "Gagal memuat: {$url} ({$response->status()})");
    }
});

it('menampilkan seluruh halaman admin', function () {
    $routes = [
        route('admin.dashboard'),
        route('admin.users.index'),
        route('admin.instructor-applications.index'),
        route('admin.categories.index'),
        route('admin.courses.index'),
        route('admin.courses.show', $this->course),
        route('admin.orders.index'),
        route('admin.orders.show', $this->order),
        route('admin.coupons.index'),
        route('admin.reviews.index'),
        route('admin.reports.index'),
        route('admin.settings.index'),
    ];

    foreach ($routes as $url) {
        $response = $this->actingAs($this->admin)->get($url);
        expect($response->status())->toBe(200, "Gagal memuat: {$url} ({$response->status()})");
    }
});

it('mengarahkan admin dan instruktur ke dashboard yang tepat', function () {
    $this->actingAs($this->admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs($this->instructor)->get(route('dashboard'))->assertRedirect(route('instructor.dashboard'));
});

it('menempatkan popover menu di dalam tepi viewport', function () {
    // Popover Basecoat tetap ada di layout saat tertutup (`invisible`, bukan `display:none`),
    // jadi menu di tepi kanan wajib memakai align="end" agar tidak memicu scroll horizontal.
    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-align="end"', false)
        ->assertSee('min-w-0', false);
});

it('tidak menghasilkan form bersarang pada toolbar filter', function () {
    $pages = [
        [route('admin.users.index'), 'admin'],
        [route('admin.courses.index'), 'admin'],
        [route('admin.orders.index'), 'admin'],
        [route('admin.reviews.index'), 'admin'],
        [route('admin.instructor-applications.index'), 'admin'],
        [route('admin.reports.index'), 'admin'],
        [route('instructor.students.index'), 'instructor'],
        [route('instructor.earnings.index'), 'instructor'],
    ];

    foreach ($pages as [$url, $role]) {
        $user = $role === 'admin' ? $this->admin : $this->instructor;
        $html = $this->actingAs($user)->get($url)->assertOk()->getContent();

        // <form> di dalam <form> adalah HTML tidak valid yang membuat kontrol
        // terdorong keluar dari baris flex dan melebar memenuhi kontainer.
        expect(preg_match('/<form\b(?:(?!<\/form>).)*<form\b/s', $html))
            ->toBe(0, "Terdapat form bersarang pada {$url}");
    }
});

it('memakai toolbar filter bersama dengan ukuran kontrol konsisten', function () {
    $html = $this->actingAs($this->admin)->get(route('admin.users.index'))->assertOk()->getContent();

    expect($html)->toContain('data-filter-bar')
        // search-input berupa <div> (bukan <form>) dengan lebar tetap.
        ->and($html)->toContain('data-search-input')
        ->and($html)->toContain('sm:w-72')
        ->and($html)->not->toContain('sm:max-w-xs');
});

it('menyediakan id dan breakpoint sidebar agar toggle dan sinkronisasi berfungsi', function () {
    $html = $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->getContent();

    // id dipakai tombol toggle mobile; data-breakpoint menyelaraskan CSS (md:768px)
    // dengan sinkronisasi state di resources/js/modules/sidebar.js.
    expect($html)->toContain('id="sidebar"')
        ->and($html)->toContain('data-breakpoint="768"');
});

it('membatasi scroll tabel pada container tabel, bukan halaman', function () {
    // Verifikasi pada CSS yang benar-benar dikirim ke browser.
    $css = collect(glob(public_path('build/assets/*.css')))
        ->map(fn (string $file) => (string) file_get_contents($file))
        ->implode("\n");

    // Tabel dengan banyak kolom menggulir di dalam .table-container-nya sendiri.
    expect($css)->toContain('overflow-x:auto')
        // Item grid/flex tidak melebar karena konten lebar (tabel/chart).
        ->and($css)->toContain('min-width:0')
        // Tidak ada hack menyembunyikan overflow di html/body.
        ->and($css)->not->toContain('body{overflow-x:hidden');
});
