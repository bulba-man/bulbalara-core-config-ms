<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Moonshine\Pages;

use Bulbalara\CoreConfigMs\ConfigModel;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;

#[Icon('cog-6-tooth')]
class ConfigPage extends Page
{
    private ?Collection $configs = null;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return __('bl_config::ui.page_titles.config_page');
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make($this->buildTabs()),
            ]),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            Flex::make([
                ActionButton::make(__('bl_config::ui.manage_button'), app(ConfigResource::class)->getIndexPageUrl())
                    ->primary()
                    ->icon('rectangle-stack'),
            ])->itemsAlign('center')->justifyAlign('end')->customAttributes([
                'style' => 'margin-bottom: 12px;',
            ]),
            $this->getForm(),
        ];
    }

    public function getForm(): FormBuilder
    {
        return FormBuilder::make()
            ->name('config-form')
            ->class('config-form')
            ->fields($this->fields())
            ->fill($this->getValues())
            ->asyncMethod('save', __('moonshine::ui.saved'), page: $this)
            ->submit(__('moonshine::ui.save'), [
                'class' => 'btn-primary btn-lg',
            ]);
    }

    #[AsyncMethod]
    public function save(): RedirectResponse
    {
        $form = $this->getForm();
        $fields = $form->getFields()->onlyFields();

        try {
            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($field->getData()->getOriginal()));
            $fields->withoutOutside()
                ->each(function (FieldContract $field) {
                    $res = $field->apply(function (mixed $item) use ($field): mixed {
                        if (! $field->hasRequestValue() && ! $field->getDefaultIfExists()) {
                            return $item;
                        }

                        $value = $field->getRequestValue() !== false ? $field->getRequestValue() : null;
                        data_set($item, $field->getColumn(), $value);

                        return $item;
                    }, []);

                    $value = data_get($res, $field->getColumn());
                    if ($value === null) {
                        $value = data_get($res, str_replace('.', '->', $field->getColumn()));
                    }

                    $model = $field->getData()->getOriginal();
                    $model->coreConfig->value = $value;

                    return $field;
                });
            $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($field->getData()->getOriginal()));

            DB::transaction(function () use ($fields): void {
                $fields->each(static fn (FieldContract $field): mixed => $field->getData()->getOriginal()->coreConfig->save());
            });
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage(), previous: $queryException);
        }

        Cache::forget(config('bl_config.cache.key', 'bl_config_cache'));

        return back();
    }

    /**
     * @return list<Tab>
     */
    private function buildTabs(): array
    {
        $tabs = [];
        $grouped = $this->groupConfigs();

        foreach ($grouped as $section => $groups) {
            $groupFields = [];

            foreach ($groups as $group => $configs) {
                $fields = $this->buildGroupFields($configs);

                if ($fields === []) {
                    continue;
                }

                $groupFields[] = Fieldset::make(
                    $this->getGroupLabel($section, $group),
                    $fields
                );
            }

            if ($groupFields === []) {
                continue;
            }

            $tabs[] = Tab::make(
                $this->getTabLabel($section),
                $groupFields
            );
        }

        return $tabs;
    }

    /**
     * @param  Collection<int, ConfigModel>  $configs
     * @return list<FieldContract>
     */
    private function buildGroupFields(Collection $configs): array
    {
        $fields = [];

        foreach ($configs as $config) {
            $field = $this->makeField($config);

            if (! $field instanceof FieldContract) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * @return Collection<int, ConfigModel>
     */
    private function getConfigs(): Collection
    {
        if (! $this->configs instanceof Collection) {
            $this->configs = ConfigModel::query()
                ->with('coreConfig')
                ->orderBy('order')
                ->orderBy('config_id')
                ->get();
        }

        return $this->configs;
    }

    /**
     * @return array<string, array<string, Collection<int, ConfigModel>>>
     */
    private function groupConfigs(): array
    {
        $grouped = [];

        foreach ($this->getConfigs() as $config) {
            $path = $config->coreConfig->path;
            $parts = explode('.', $path);

            $section = $parts[0] ?? 'general';
            $group = $parts[1] ?? 'general';

            if (! isset($grouped[$section][$group])) {
                $grouped[$section][$group] = new Collection;
            }

            $grouped[$section][$group]->push($config);
        }

        return $grouped;
    }

    /**
     * @return array<string, mixed>
     */
    private function getValues(): array
    {
        $values = [];

        foreach ($this->getConfigs() as $config) {
            $path = $config->coreConfig->path;
            $value = $config->coreConfig->value ?? $config->coreConfig->default;

            data_set($values, $path, $value);
        }

        return $values;
    }

    private function getTabLabel(string $section): string
    {
        $key = "bl_config::config.$section.tab_label";
        $label = __($key);

        return $label !== $key ? $label : ucfirst($section);
    }

    private function getGroupLabel(string $section, string $group): string
    {
        $key = "bl_config::config.$section.$group.group_label";
        $label = __($key);

        return $label !== $key ? $label : ucfirst($group);
    }

    private function makeField(ConfigModel $config): ?FieldContract
    {
        $path = $config->coreConfig->path;
        $label = $config->label ? __($config->label) : $path;

        $field = $this->makeFieldByType($config, $label, $path);

        if (! $field instanceof FieldContract) {
            return null;
        }

        $field->changeFill(
            function (mixed $data, FieldContract $field): mixed {
                if ($data instanceof ConfigModel) {
                    return $data->coreConfig->value;
                }

                if (is_array($data)) {
                    return data_get($data, $field->getColumn());
                }

                return null;
            }
        );
        $field->fillData(new ModelDataWrapper($config));

        if ($config->description) {
            $field->hint(__($config->description));
        }

        if ($config->coreConfig->default !== null && $field instanceof HasDefaultValueContract) {
            $field->default($config->coreConfig->default);
        }

        if ($config->depends_of && $config->depends_val !== null) {
            $field->showWhen($config->depends_of, $config->depends_val);
        }

        if ($config->rules) {
            if (str_contains($config->rules, 'nullable')) {
                $field->nullable();
            }

            if (str_contains($config->rules, 'required')) {
                $field->required();
            }
        }

        return $this->applyFieldMethods($field, $config->methods);
    }

    private function makeFieldByType(ConfigModel $config, string $label, string $path): ?FieldContract
    {
        $backendType = $config->backend_type;
        $rules = $config->rules ?? '';
        $cast = $config->coreConfig->cast ?? null;

        if (is_string($backendType) && class_exists($backendType) && is_subclass_of($backendType, FieldContract::class)) {
            /** @var class-string<FieldContract> $backendType */
            return $backendType::make($label, $path);
        }

        if (in_array($cast, ['bool', 'boolean'], true) && in_array($backendType, ['text', 'switcher', 'checkbox', 'boolean'], true)) {
            return Switcher::make($label, $path);
        }

        if (in_array($cast, ['int', 'integer', 'float', 'decimal'], true) && in_array($backendType, ['text', 'number'], true)) {
            return Number::make($label, $path);
        }

        if ($backendType === 'text' && str_contains($rules, 'email')) {
            return Email::make($label, $path);
        }

        if ($backendType === 'text' && str_contains($rules, 'url')) {
            return Url::make($label, $path);
        }

        if ($backendType === 'text' && $cast === 'json') {
            return Json::make($label, $path)->object()->removable();
        }

        return match ($backendType) {
            'text' => Text::make($label, $path)->unescape(),
            'textarea' => Textarea::make($label, $path)->unescape(),
            'email' => Email::make($label, $path),
            'url' => Url::make($label, $path),
            'password' => Password::make($label, $path),
            'number' => Number::make($label, $path),
            'switcher', 'checkbox', 'boolean' => Switcher::make($label, $path),
            'date' => Date::make($label, $path),
            'time' => Text::make($label, $path)->setAttribute('type', 'time'),
            'color' => Color::make($label, $path),
            'range' => Range::make($label, $path),
            'select' => Select::make($label, $path)->options($this->decodeOptions($config->source)),
            'list' => Json::make($label, $path)
                ->onlyValue('', $this->makeListValueField($config))
                ->removable(),
            'json' => Json::make($label, $path)->object()->removable(),
            'file' => File::make($label, $path)->removable(),
            'image' => Image::make($label, $path)
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable(),
            default => Text::make($label, $path)->unescape(),
        };
    }

    private function makeListValueField(ConfigModel $config): FieldContract
    {
        $rules = $config->rules ?? '';

        if (str_contains($rules, 'email')) {
            return Email::make('', 'value');
        }

        if (str_contains($rules, 'url')) {
            return Url::make('', 'value');
        }

        if (str_contains($rules, 'numeric')) {
            return Number::make('', 'value');
        }

        return Text::make('', 'value');
    }

    /**
     * @param  array<string, mixed>|null  $methods
     */
    private function applyFieldMethods(FieldContract $field, ?array $methods): FieldContract
    {
        if ($methods === null || $methods === []) {
            return $field;
        }

        foreach ($methods as $method => $arguments) {
            if (! is_string($method) || ! is_callable([$field, $method])) {
                continue;
            }

            if (is_array($arguments)) {
                $result = $field->{$method}(...$arguments);
            } elseif ($arguments === null) {
                $result = $field->{$method}();
            } else {
                $result = $field->{$method}($arguments);
            }

            if ($result instanceof FieldContract) {
                $field = $result;
            }
        }

        return $field;
    }

    /**
     * @return array<string, string>
     */
    private function decodeOptions(?string $options): array
    {
        if ($options === null || $options === '') {
            return [];
        }

        $decoded = json_decode($options, true);

        return is_array($decoded) ? $decoded : [];
    }
}
