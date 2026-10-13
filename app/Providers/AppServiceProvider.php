<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Payment\MidtransGateway;
use App\Services\Payment\PaymentGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Gateway pembayaran default: Midtrans. Ganti binding untuk Xendit dsb.
        $this->app->bind(PaymentGateway::class, MidtransGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin otomatis lolos semua pemeriksaan otorisasi (super admin).
        Gate::before(function (User $user) {
            return $user->isAdmin() ? true : null;
        });

        // Rate limiter tambahan untuk endpoint sensitif (webhook, verifikasi sertifikat).
        RateLimiter::for('certificate-verify', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Paginasi bergaya Basecoat.
        Paginator::defaultView('vendor.pagination.basecoat');
        Paginator::defaultSimpleView('vendor.pagination.basecoat');
    }
}
