<?php

namespace Bulbalara\CoreConfigMs\Services;

use Bulbalara\CoreConfigMs\Handlers\ConfigHandlerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LoadConfig implements LoadConfigInterface
{

    public function load(): void
    {
        if (config('bl_config.cache.enabled')) {
            $configs = $this->getConfigsCache();
        } else {
            $configs = $this->getConfigs();
        }

        $this->handleHandlers(
            config('bl_config.classes.handlers.before_merge', []),
            $configs
        );

        foreach ($configs as $config) {
            $value = (! is_null($config->value)) ? $config->value : $config->default;
            config([$config->path => $value]);
        }

        $this->handleHandlers(
            config('bl_config.classes.handlers.after_merge', []),
            $configs
        );
    }

    protected function getConfigs(): Collection|array
    {
        $connection = config('bl_config.database.connection') ?: config('database.default');
        $table = config('bl_config.db.table', 'core_config');

        try {
            if (! Schema::connection($connection)->hasTable($table)) {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }

        $configs = config('bl_config.classes.model_base')::all();

        $this->handleHandlers(
            config('bl_config.classes.handlers.after_get_configs', []),
            $configs
        );

        return $configs;
    }

    protected function getConfigsCache(): Collection|array
    {
        if (config('bl_config.cache.forever')) {
            return Cache::rememberForever(config('bl_config.cache.key', 'bl_config_cache'), function () {
                return $this->getConfigs();
            });
        } else {
            return Cache::remember(config('bl_config.cache.key', 'bl_config_cache'), config('bl_config.cache.ttl', 60), function () {
                return $this->getConfigs();
            });
        }
    }

    protected function handleHandlers(array $handlers, array|Collection &$configs): void
    {
        foreach ($handlers as $handler) {
            if (!$this->validateHandler($handler)) {
                continue;
            }

            $rows = app($handler)->handle($configs);
        }
    }

    protected function validateHandler($handler): bool
    {
        if (! is_string($handler) || $handler === '') {
           return false;
        }

        if (! class_exists($handler)) {
            return false;
        }

        if (! is_subclass_of($handler, ConfigHandlerInterface::class)) {
            return false;
        }

        return true;
    }
}
