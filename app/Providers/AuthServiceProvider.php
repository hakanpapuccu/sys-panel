<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Vacation' => 'App\Policies\VacationPolicy',
        'App\Models\Task' => 'App\Policies\TaskPolicy',
        'App\Models\Announcement' => 'App\Policies\AnnouncementPolicy',
        'App\Models\FileShare' => 'App\Policies\FileSharePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
