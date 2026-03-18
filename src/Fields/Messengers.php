<?php

namespace Bulbalara\CoreConfigMs\Fields;

use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;

class Messengers extends Json
{
    public static function make(...$arguments): static
    {
        $parent = parent::make(...$arguments);
        $parent->removable()->fields([
            Image::make(__('bl_config::config.contacts.general.messengers_icon'), 'icon')
                ->nullable()
                ->dir('images/icons/messengers')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable()
                ->keepOriginalFileName(),
            Text::make(__('bl_config::config.contacts.general.messengers_name'), 'name'),
            Text::make(__('bl_config::config.contacts.general.messengers_value'), 'value'),
        ]);

        return $parent;
    }
}
