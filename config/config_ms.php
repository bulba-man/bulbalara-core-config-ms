<?php

return [
    'database' => [
        'connection' => '',
        'core_config_table' => 'core_config_admin',
    ],
    'urls' => [
        'settings_uri' => 'settings',
        'settings_config_uri' => 'config',
    ],
    'pages' => [
        'settings' => \Bulbalara\CoreConfigMs\Moonshine\Pages\ConfigPage::class,
    ],
    'add_to_menu' => true,
    'cache' => [
        'enabled' => true,
        'key' => 'bl_config_cache',
        'ttl' => 60,
        'forever' => true,
    ],
    'classes' => [
        'model_base' => \Bulbalara\CoreConfig\Models\Config::class,
        'model' => \Bulbalara\CoreConfigMs\ConfigModel::class,
        'loader' => \Bulbalara\CoreConfigMs\Services\LoadConfig::class,
        'handlers' => [
            'before_merge' => [
                \Bulbalara\CoreConfigMs\Handlers\Before::class,
            ],
            'after_merge' => [
                \Bulbalara\CoreConfigMs\Handlers\After::class,
            ],
        ],
    ],
];
