<?php

namespace Bulbalara\CoreConfigMs;

use Bulbalara\CoreConfigMs\Moonshine\Pages\ConfigPage;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
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
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            __DIR__.'/../config/config_ms.php' => config_path(
                'config_ms.php'
            ),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bl.config');

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

    protected function loadConfigs(): void
    {
        foreach (\Bulbalara\CoreConfig\Models\Config::all() as $config) {
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
}
