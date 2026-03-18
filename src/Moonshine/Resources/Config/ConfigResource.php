<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Moonshine\Resources\Config;

use Bulbalara\CoreConfigMs\ConfigModel;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\Pages\ConfigFormPage;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\Pages\ConfigIndexPage;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Attributes\SaveHandler;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\Field;

/**
 * @extends ModelResource<ConfigModel, ConfigIndexPage, ConfigFormPage, PageContract>
 */
#[SaveHandler(ConfigSaveHandler::class)]
class ConfigResource extends ModelResource
{
    protected string $model = ConfigModel::class;

    protected array $with = ['coreConfig'];

    public function getTitle(): string
    {
        return __('bl_config::ui.page_titles.management_page');
    }
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ConfigIndexPage::class,
            ConfigFormPage::class,
        ];
    }

    /**
     * @param  DataWrapperContract<ConfigModel>  $item
     */
    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $fields ??= $this->getFormFields()->onlyFields(withApplyWrappers: true);
        $fields->fill($item->toArray(), $item);

        $handler = $this->getCore()->getContainer(ConfigSaveHandler::class);

        $initial = clone $item;
        $data = Field::silentApply(function () use ($item, $fields): array {
            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($item->getOriginal()));
            $fields->each(fn (FieldContract $field): mixed => $field->apply($this->fieldApply($field), $item->getOriginal()));
            $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($item->getOriginal()));

            return $item->toArray();
        });

        $result = $handler($initial->getOriginal(), $data);
        $this->isRecentlyCreated = $initial->getOriginal()->wasRecentlyCreated;
        $this->setItem($result);

        return $this->getCastedData();
    }

    public function getRedirectAfterSave(): ?string
    {
        if (! $this->isRecentlyCreated()) {
            return null;
        }

        return $this->getIndexPageUrl();
    }
}
