<?php

use App\Models\Course;

it('menampilkan beranda', function () {
    Course::factory()->published()->create();

    $this->get(route('home'))->assertOk()->assertSee(config('app.name'));
});

it('menampilkan katalog dengan filter', function () {
    Course::factory()->published()->create(['title' => 'Belajar Laravel 12 dari Nol']);

    $this->get(route('courses.index'))->assertOk()->assertSee('Katalog kursus');
    $this->get(route('courses.index', ['q' => 'Laravel']))->assertOk()->assertSee('Belajar Laravel 12');
});

it('mengembalikan partial katalog untuk permintaan htmx', function () {
    Course::factory()->published()->create();

    $this->get(route('courses.index'), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('catalog-results', false)
        ->assertDontSee('<html', false);
});

it('menampilkan detail kursus', function () {
    $course = Course::factory()->published()->create();

    $this->get(route('courses.show', $course))->assertOk()->assertSee($course->title);
});

it('menampilkan halaman verifikasi sertifikat', function () {
    $this->get(route('certificates.verify.form'))->assertOk()->assertSee('Verifikasi sertifikat');
});

it('memverifikasi kode sertifikat yang tidak ada', function () {
    $this->post(route('certificates.verify'), ['code' => 'TIDAKADA'])
        ->assertOk()
        ->assertSee('tidak ditemukan');
});
