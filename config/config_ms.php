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
];
