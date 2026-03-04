<?php

namespace Bulbalara\CoreConfigMs;

use Bulbalara\CoreConfigMs\Moonshine\Pages\ConfigPage;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;
use Illuminate\Support\Facades\Schema;
use Throwable;

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

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bl.config');

        $this->registerMoonshine($core, $menu);

        $this->loadConfigs();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config_ms.php', 'bl.config');

        $this->app->singleton('bl.config.config_ms', function () {
            return new \Bulbalara\CoreConfigMs\Facades\Implement\CoreConfigMs;
        });

        $this->commands($this->commands);
    }

    protected function registerMoonshine(CoreContract $core, MenuManagerContract $menu): void
    {
        $pages = config('bl.config.pages', []);

        $core
            ->resources([
                ConfigResource::class,
            ])
            ->pages(array_values($pages));

        if (config('bl.config.add_to_menu')) {
            $settingsPage = $pages['settings'] ?? ConfigPage::class;

            if (is_string($settingsPage)) {
                $menu->add([
                    MenuItem::make($settingsPage, __('bl.config::core_config.menu_config_label')),
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
                'config_ms.php'
            ),
        ], 'config_ms.config');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/config_ms'),
        ], 'config_ms.lang');
    }

    protected function loadConfigs(): void
    {
        if (config('bl.config.cache.enabled')) {
            $configs = $this->getConfigsCache();
        } else {
            $configs = $this->getConfigs();
        }

        foreach ($configs as $config) {
            $value = (! is_null($config->value)) ? $config->value : $config->default;
            config([$config->path => $value]);
        }

        $mailer = config('mail.transport.mailer');
        config(['mail.default' => $mailer]);
        if (config('mail.transport.'.$mailer)) {
            config(['mail.mailers.'.$mailer => array_merge(
                config('mail.mailers.'.$mailer), config('mail.transport.'.$mailer)
            )]);
        }

        config(['mail.from' => config('mail.addresses.from')]);
    }

    protected function getConfigs(): Collection|array
    {
        $connection = config('bl.config.database.connection') ?: config('database.default');
        $table = config('bl.config.db.table', 'core_config');

        try {
            if (! Schema::connection($connection)->hasTable($table)) {
                return [];
            }
        } catch (Throwable) {
            return [];
        }

        return \Bulbalara\CoreConfig\Models\Config::all();
    }

    protected function getConfigsCache(): Collection|array
    {
        if (config('bl.config.cache.forever')) {
            return Cache::rememberForever(config('bl.config.cache.key', 'bl_config_cache'), function () {
                return $this->getConfigs();
            });
        } else {
            return Cache::remember(config('bl.config.cache.key', 'bl_config_cache'), config('bl.config.cache.ttl', 60), function () {
                return $this->getConfigs();
            });
        }
    }
}
