<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Appointment;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Observers\AuditLogObserver;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class,
    ];


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        if ($this->app->isProduction()) {
            \URL::forceScheme('https');
        }

        User::observe(AuditLogObserver::class);
        Appointment::observe(AuditLogObserver::class);
        MedicalRecord::observe(AuditLogObserver::class);

        Gate::define('view-logs', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
