<?php
namespace InnoShop\Install;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InstallServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->loadViewsFrom(inno_path('install/resources/views'), 'install');
    }

    /**
     * Boot front service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        if (has_install_lock()) {
            return;
        }

        $this->registerWebRoutes();
        $this->loadTranslations();
    }

    /**
     * Register admin front routes.
     *
     * @return void
     */
    protected function registerWebRoutes(): void
    {
        Route::name('install.')
            ->group(function () {
                $path = __DIR__.'/../routes/web.php';
                if (is_file($path)) {
                    $this->loadRoutesFrom($path);
                }
            });
    }

    /**
     * Register front language
     * @return void
     */
    protected function loadTranslations(): void
    {
        if (! is_dir(__DIR__.'/../lang')) {
            return;
        }

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'install');
    }
}
