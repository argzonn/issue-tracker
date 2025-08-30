<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Support\ViewHelpers;

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
        Blade::directive('statusBadge', function ($expr) {
        return "<?php echo '<span class=\"'.\\App\\Support\\ViewHelpers::statusClass($expr).'\">'.e($expr).'</span>'; ?>";
    });

    Blade::directive('priorityBadge', function ($expr) {
        return "<?php echo '<span class=\"'.\\App\\Support\\ViewHelpers::priorityClass($expr).'\">'.e($expr).'</span>'; ?>";
    });
    }
}
