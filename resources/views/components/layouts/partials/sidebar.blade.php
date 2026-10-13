@php
    $user = auth()->user();
    $roleLabel = \App\Enums\UserRole::tryFrom($user?->roles->first()?->name ?? '')?->label();

    $groups = [];

    $groups[] = [
        'label' => 'Jelajahi',
        'items' => [
            ['label' => 'Beranda', 'route' => 'home', 'icon' => 'house', 'active' => ['home']],
            ['label' => 'Katalog kursus', 'route' => 'courses.index', 'icon' => 'library', 'active' => ['courses.*']],
        ],
    ];

    $groups[] = [
        'label' => 'Belajar',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'active' => ['dashboard']],
            ['label' => 'Kursus saya', 'route' => 'student.courses.index', 'icon' => 'book-open', 'active' => ['student.courses.*', 'student.learning.*']],
            ['label' => 'Wishlist', 'route' => 'student.wishlist.index', 'icon' => 'heart', 'active' => ['student.wishlist.*']],
            ['label' => 'Pesanan', 'route' => 'student.orders.index', 'icon' => 'receipt', 'active' => ['student.orders.*']],
            ['label' => 'Sertifikat', 'route' => 'student.certificates.index', 'icon' => 'award', 'active' => ['student.certificates.*']],
        ],
    ];

    if ($user?->isInstructor() || $user?->isAdmin()) {
        $groups[] = [
            'label' => 'Instruktur',
            'items' => [
                ['label' => 'Dashboard instruktur', 'route' => 'instructor.dashboard', 'icon' => 'presentation', 'active' => ['instructor.dashboard']],
                ['label' => 'Kelola kursus', 'route' => 'instructor.courses.index', 'icon' => 'folder-kanban', 'active' => ['instructor.courses.*']],
                ['label' => 'Kuis', 'route' => 'instructor.quizzes.index', 'icon' => 'list-checks', 'active' => ['instructor.quizzes.*']],
                ['label' => 'Siswa', 'route' => 'instructor.students.index', 'icon' => 'users', 'active' => ['instructor.students.*']],
                ['label' => 'Pendapatan', 'route' => 'instructor.earnings.index', 'icon' => 'wallet', 'active' => ['instructor.earnings.*']],
                ['label' => 'Pencairan dana', 'route' => 'instructor.payouts.index', 'icon' => 'banknote', 'active' => ['instructor.payouts.*']],
            ],
        ];
    }

    if ($user?->isAdmin()) {
        $groups[] = [
            'label' => 'Admin',
            'items' => [
                ['label' => 'Dashboard admin', 'route' => 'admin.dashboard', 'icon' => 'shield-check', 'active' => ['admin.dashboard']],
                ['label' => 'Pengguna', 'route' => 'admin.users.index', 'icon' => 'users-round', 'active' => ['admin.users.*']],
                ['label' => 'Pengajuan instruktur', 'route' => 'admin.instructor-applications.index', 'icon' => 'user-plus', 'active' => ['admin.instructor-applications.*']],
                ['label' => 'Kategori', 'route' => 'admin.categories.index', 'icon' => 'folder-tree', 'active' => ['admin.categories.*']],
                ['label' => 'Review kursus', 'route' => 'admin.courses.index', 'icon' => 'clipboard-check', 'active' => ['admin.courses.*']],
                ['label' => 'Pesanan', 'route' => 'admin.orders.index', 'icon' => 'receipt-text', 'active' => ['admin.orders.*']],
                ['label' => 'Kupon', 'route' => 'admin.coupons.index', 'icon' => 'ticket-percent', 'active' => ['admin.coupons.*']],
                ['label' => 'Ulasan', 'route' => 'admin.reviews.index', 'icon' => 'star', 'active' => ['admin.reviews.*']],
                ['label' => 'Laporan', 'route' => 'admin.reports.index', 'icon' => 'chart-column', 'active' => ['admin.reports.*']],
                ['label' => 'Pengaturan', 'route' => 'admin.settings.index', 'icon' => 'settings', 'active' => ['admin.settings.*']],
            ],
        ];
    }

    $groups[] = [
        'label' => 'Akun',
        'items' => [
            ['label' => 'Profil', 'route' => 'profile.edit', 'icon' => 'user-round-cog', 'active' => ['profile.*']],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar" data-side="left" data-breakpoint="768">
    <nav aria-label="Navigasi aplikasi">
        <header>
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-2 py-1 font-semibold">
                <x-lucide-graduation-cap class="size-4" />
                <span class="truncate">{{ config('app.name') }}</span>
            </a>
        </header>

        <section class="scrollbar-sm">
            @foreach ($groups as $group)
                <div role="group" aria-labelledby="nav-group-{{ $loop->index }}">
                    <h3 id="nav-group-{{ $loop->index }}">{{ $group['label'] }}</h3>
                    <ul>
                        @foreach ($group['items'] as $item)
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    @if (request()->routeIs(...$item['active'])) aria-current="page" @endif
                                >
                                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-4" />
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>

        <footer>
            <div class="flex items-center gap-2 px-2 py-1">
                <x-ui.avatar :src="$user?->avatar_url" :name="$user?->name ?? 'Tamu'" size="sm" />
                <div class="grid min-w-0">
                    <span class="truncate text-sm font-medium">{{ $user?->name }}</span>
                    <span class="text-muted-foreground truncate text-xs">{{ $roleLabel ?? 'Pengguna' }}</span>
                </div>
            </div>
        </footer>
    </nav>
</aside>
