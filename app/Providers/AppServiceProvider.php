<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        if ((bool) config('security.force_https', false)) {
            URL::forceScheme('https');
        }

        DB::listen(function (QueryExecuted $query): void {
            $threshold = (int) config('security.slow_query_threshold_ms', 500);
            if ($query->time < $threshold) {
                return;
            }

            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'time_ms' => $query->time,
                'connection' => $query->connectionName,
            ]);
        });

        Queue::failing(function (JobFailed $event): void {
            Log::error('Queue job failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });

        view()->share([
            'site_title' => Setting::get('site_title', 'OIDB Panel'),
            'site_logo' => Setting::get('site_logo'),
            'site_favicon' => Setting::get('site_favicon'),
        ]);

        view()->composer('dashboard.header', function ($view): void {
            $user = Auth::user();
            if (! $user) {
                return;
            }

            $user->loadMissing([
                'department:id,name',
                'roles.permissions',
            ]);

            $unreadNotificationsQuery = $user->unreadNotifications()->latest();
            $unreadNotifications = (clone $unreadNotificationsQuery)
                ->limit(8)
                ->get();

            $view->with('currentUser', $user);
            $view->with('unreadNotifications', $unreadNotifications);
            $view->with('unreadNotificationsCount', $unreadNotificationsQuery->count());
        });
    }
}
