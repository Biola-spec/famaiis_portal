<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SiteSetting;
use View;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $basePath = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');
        $prefix = $basePath ? '/' . $basePath : '';

        // Ensure Livewire endpoints work when app is served from a subdirectory
        // like /sms/public instead of domain root.
        Livewire::setScriptRoute(function ($handle) use ($prefix) {
            return Route::get($prefix . '/livewire/livewire.js', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) use ($prefix) {
            return Route::post($prefix . '/livewire/update', $handle)->middleware('web');
        });

        if (!function_exists('getCurrentSession') || !function_exists('getCurrentTerm')) {
            require_once app_path('helpers.php');
        }

        View::composer('*', function ($view) {
            static $shared = null;

            if ($shared === null) {
                $activeSectionId = session('active_section_id');

                $shared = [
                    'setting' => SiteSetting::first(),
                    'currentAcademicSession' => \getCurrentSession(),
                    'currentAcademicTerm' => \getCurrentTerm(),
                    'activeSectionId' => $activeSectionId,
                    'upcoming_events' => \App\Models\Event::where(function ($q) use ($activeSectionId) {
                        $q->whereNull('section_id');
                        if ($activeSectionId) {
                            $q->orWhere('section_id', $activeSectionId);
                        }
                    })
                    ->where('event_date', '>=', now()->toDateString())
                    ->orderBy('event_date', 'asc')
                    ->take(5)
                    ->get(),
                ];
            }

            foreach ($shared as $key => $value) {
                $view->with($key, $value);
            }
        });

        View::composer(['admin.body.header', 'admin.body.sidebar'], function ($view) {
            $user = auth()->user();
            if ($user) {
                static $adminChrome = null;

                $user->loadMissing('roles.permissions');

                if ($adminChrome === null) {
                    if ($user->role == 'Admin' || $user->hasRole('Admin')) {
                        $headerSections = \App\Models\SchoolSection::query()->orderBy('name')->get();
                    } elseif ($user->role == 'Teacher' || $user->hasRole('Teacher')) {
                        $headerSections = $user->teacherSections;
                    } else {
                        $headerSections = collect();
                    }

                    $adminChrome = [
                        'unreadMessageCount' => \App\Models\Message::where('receiver_id', $user->id)
                            ->whereNull('seen_at')
                            ->count(),
                        'unreadNotifications' => $user->unreadNotifications()->latest()->limit(8)->get(),
                        'unreadNotificationCount' => $user->unreadNotifications()->count(),
                        'headerSections' => $headerSections,
                    ];
                }

                foreach ($adminChrome as $key => $value) {
                    $view->with($key, $value);
                }
            }
        });
    }
}
