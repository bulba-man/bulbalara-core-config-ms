<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Moonshine\Resources\Config\Pages;

use Bulbalara\CoreConfigMs\ConfigModel;
use Bulbalara\CoreConfigMs\Moonshine\Resources\Config\ConfigResource;
use Bulbalara\CoreConfigMs\Support\MethodsTransformer;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ConfigResource>
 */
class ConfigFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                $this->coreField(Text::make('Path', 'path')->required(), 'path'),
                $this->coreField(Textarea::make('Value', 'value')->nullable(), 'value'),
                $this->coreField(Textarea::make('Default', 'default')->nullable(), 'default'),
                $this->coreField(
                    Select::make('Cast', 'cast')
                        ->nullable()
                        ->options([
                            'string' => 'string',
                            'int' => 'int',
                            'integer' => 'integer',
                            'float' => 'float',
                            'decimal' => 'decimal',
                            'bool' => 'bool',
                            'boolean' => 'boolean',
                            'json' => 'json',
                            'array' => 'array',
                        ])
                        ->default('string'),
                    'cast'
                ),

                Text::make('Backend type', 'backend_type')
                    ->hint('Например: text, textarea, email, select, image, json, file или FQCN поля MoonShine')
                    ->nullable(),
                Textarea::make('Source (JSON)', 'source')->nullable(),
                Switcher::make('Resettable', 'resettable')->default(true),
                Text::make('Rules', 'rules')->nullable(),
                Text::make('Depends of', 'depends_of')->nullable(),
                Text::make('Depends value', 'depends_val')->nullable(),
                Text::make('Label', 'label')->nullable(),
                Textarea::make('Description', 'description')->nullable(),
                Number::make('Order', 'order')->default(0),
                $this->methodsField(),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        $configTable = config('bl.config.db.table', 'core_config');
        $ignoreId = $item->getOriginal()?->coreConfig?->getKey();

        return [
            'path' => ['required', 'string', 'max:255', Rule::unique($configTable, 'path')->ignore($ignoreId)],
            'cast' => ['nullable', 'string', 'max:50'],
            'backend_type' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function coreField(FieldContract $field, string $key): FieldContract
    {
        return $field->changeFill(static function (mixed $data, FieldContract $field) use ($key): mixed {
            if ($data instanceof ConfigModel) {
                return data_get($data->coreConfig, $key);
            }

            if (is_array($data)) {
                return data_get($data, $field->getColumn());
            }

            return null;
        });
    }

    private function methodsField(): FieldContract
    {
        return Json::make('Methods', 'methods')
            ->fields([
                Text::make('Method', 'key')->required(),
                Json::make('Arguments', 'arguments')
                    ->keyValue('Argument', 'Value'),
            ])
            ->removable()
            ->nullable()
            ->changeFill(static function (mixed $data): array {
                if ($data instanceof ConfigModel) {
                    return MethodsTransformer::denormalize($data->methods ?? null);
                }

                return [];
            });
    }
}
