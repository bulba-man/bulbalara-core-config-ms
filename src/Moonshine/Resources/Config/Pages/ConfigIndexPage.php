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
            Text::make('Path', 'path', formatted: static fn (ConfigModel $item): ?string => $item->coreConfig?->path),
            Text::make('Value', 'value', formatted: static fn (ConfigModel $item): mixed => $item->coreConfig?->value),
            Text::make('Label', 'label'),
            Text::make('Backend', 'backend_type'),
        ];
    }
}
