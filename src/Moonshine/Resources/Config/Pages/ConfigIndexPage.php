<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Moonshine\Resources\Config\Pages;

use Bulbalara\CoreConfigMs\ConfigModel;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ConfigResource>
 */
class ConfigIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('ID', 'config_id'),
            Text::make(__('bl_config::form.path'), 'path', formatted: static fn (ConfigModel $item): ?string => $item->coreConfig?->path),
            Text::make(__('bl_config::form.value'), 'value', formatted: static fn (ConfigModel $item): mixed => self::formatValue($item)),
            Text::make(__('bl_config::form.label'), 'label', formatted: static fn (ConfigModel $item): ?string => self::formatLabel($item)),
            Text::make(__('bl_config::form.backend'), 'backend_type'),
        ];
    }

    static public function formatValue($item): string
    {
        $value = $item->coreConfig?->value;

        if ($value === null) {
            return '';
        }

        if ($item->backend_type === 'select') {
            if (is_array($item->source)) {
                return $item->source[$value] ?? '';
            }
            return $value;
        }

        if ($item->backend_type === 'list') {
            if (is_array($value)) {
                return implode(', ', $value);
            }
            return '';
        }

        if (is_array($value)) {
            return '';
        }

        return $value ?: '';
    }

    static public function formatLabel($item): string
    {
        $label = __($item->label);

        return $label ?: '';
    }
}
