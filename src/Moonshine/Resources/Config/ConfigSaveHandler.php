<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Moonshine\Resources\Config;

use Bulbalara\CoreConfig\Models\Config;
use Bulbalara\CoreConfigMs\ConfigModel;
use Bulbalara\CoreConfigMs\Support\MethodsTransformer;
use Illuminate\Database\QueryException;
use MoonShine\Core\Exceptions\ResourceException;

final class ConfigSaveHandler
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(ConfigModel $model, array $data): ConfigModel
    {
        try {
            $coreData = $this->extractCoreData($data);

            if (! $model->exists) {
                $core = new Config;
                $core->path = (string) ($this->getCoreValue($coreData, 'path', '') ?? '');
                $core->value = $this->getCoreValue($coreData, 'value', null);
                $core->default = $this->getCoreValue($coreData, 'default', null);
                $core->cast = (string) ($this->getCoreValue($coreData, 'cast', 'string') ?? 'string');
                $core->save();

                $model->config_id = $core->getKey();
            } else {
                $core = $model->coreConfig ?? new Config;
                $core->path = (string) ($this->getCoreValue($coreData, 'path', $core->path) ?? $core->path);
                $core->value = $this->getCoreValue($coreData, 'value', $core->value);
                $core->default = $this->getCoreValue($coreData, 'default', $core->default);
                $core->cast = (string) ($this->getCoreValue($coreData, 'cast', $core->cast ?? 'string') ?? 'string');
                $core->save();

                $model->config_id = $core->getKey();
            }

            unset($model->path, $model->value, $model->default, $model->cast);
            $model->backend_type = (string) ($data['backend_type'] ?? $model->backend_type ?? 'text');
            $model->source = $data['source'] ?? null;
            $model->resettable = $data['resettable'] ?? null;
            $model->rules = $data['rules'] ?? null;
            $model->depends_of = $data['depends_of'] ?? null;
            $model->depends_val = $data['depends_val'] ?? null;
            $model->label = $data['label'] ?? null;
            $model->description = $data['description'] ?? null;
            $model->order = $data['order'] ?? 0;
            $model->methods = MethodsTransformer::normalize(
                is_array($data['methods'] ?? null) ? $data['methods'] : null
            );

            $model->save();
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage(), previous: $queryException);
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function extractCoreData(array $data): array
    {
        if (isset($data['coreConfig']) && is_array($data['coreConfig'])) {
            return $data['coreConfig'];
        }

        if (isset($data['core_config']) && is_array($data['core_config'])) {
            return $data['core_config'];
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $coreData
     */
    private function getCoreValue(array $coreData, string $key, mixed $fallback): mixed
    {
        if (array_key_exists($key, $coreData)) {
            return $coreData[$key];
        }

        return $fallback;
    }
}
