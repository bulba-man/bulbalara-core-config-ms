<?php

namespace Bulbalara\CoreConfigMs;

use Bulbalara\CoreConfigMs\Moonshine\Pages\ConfigPage;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
use Bulbalara\CoreConfigMs\Services\LoadConfigInterface;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;

class ConfigMsServiceProvider extends ServiceProvider
{
    protected array $commands = [
        Console\InstallCommand::class,
    ];

    public function boot(CoreContract $core, MenuManagerContract $menu): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishes();
        }

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'bl_config');

        $this->registerMoonshine($core, $menu);

        $this->loadConfigs();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config_ms.php', 'bl_config');

        $this->app->singleton('bl.config.config_ms', function () {
            return new \Bulbalara\CoreConfigMs\Facades\Implement\CoreConfigMs;
        });

        $this->commands($this->commands);
    }

    protected function registerMoonshine(CoreContract $core, MenuManagerContract $menu): void
    {
        $pages = config('bl_config.pages', []);

        $core
            ->resources([
                ConfigResource::class,
            ])
            ->pages(array_values($pages));

        if (config('bl_config.add_to_menu')) {
            $settingsPage = $pages['settings'] ?? ConfigPage::class;

            if (is_string($settingsPage)) {
                $menu->add([
                    MenuItem::make($settingsPage, __('bl_config::ui.menu.config_page')),
                ]);
            }
        }
    }

    protected function registerPublishes(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'config_ms.migrations');

        $this->publishes([
            __DIR__.'/../config/config_ms.php' => config_path(
                'bl_config.php'
            ),
        ], 'config_ms.config');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/bl_config'),
        ], 'config_ms.lang');
    }

    protected function loadConfigs(): void
    {
        $loader = config('bl_config.classes.loader');

        if (class_exists($loader) && is_subclass_of($loader, LoadConfigInterface::class)) {
            app($loader)->load();
        }
    }
}
