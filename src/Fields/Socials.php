<?php

namespace Bulbalara\CoreConfigMs\Fields;

use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

class Socials extends Json
{
    public static function make(...$arguments): static
    {
        $parent = parent::make(...$arguments);
        $parent->removable()->fields([
            Image::make(__('bl_config::config.contacts.general.socials_icon'), 'icon')
                ->nullable()
                ->dir('images/icons/social')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable()
                ->keepOriginalFileName(),
            Text::make(__('bl_config::config.contacts.general.messengers_name'), 'name'),
            Url::make(__('bl_config::config.contacts.general.socials_url'), 'url'),
        ]);

        return $parent;
    }
}
