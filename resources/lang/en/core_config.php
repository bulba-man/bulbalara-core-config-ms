<?php

return [
    'menu_main_label' => 'Settings',
    'menu_config_label' => 'Configuration',

    'role_main_label' => 'Settings',
    'role_config_label' => 'Settings: Configuration',

    'config' => [
        'general' => [
            'tab_label' => 'General',
            'general' => [
                'group_label' => 'General',
                'site_name' => 'Site name',
            ],
        ],
        'web' => [
            'tab_label' => 'WEB',
            'assets' => [
                'group_label' => 'Assets',
                'favicon' => 'Site favicon',
                'logo' => 'Site logo',
                'html_lang' => 'Attribute "lang" for &lt;html&gt; tag',
                'custom_scripts' => 'Javascript',
                'custom_styles' => 'CSS Styles',
            ],
        ],
        'design' => [
            'tab_label' => 'Design',
            'head' => [
                'group_label' => 'HTML Head',
                'favicon' => 'Site favicon',
                'html_lang' => '&lt;html&gt; lang',
                'html_lang-help' => 'Attribute "lang" for &lt;html&gt; tag',
                'custom_scripts' => 'Javascript',
                'custom_styles' => 'CSS Styles',
            ],
            'header' => [
                'group_label' => 'Header',
                'logo' => 'Site logo',
                'logo_width' => 'Logo Attribute Width',
                'logo_height' => 'Logo Attribute Height',
                'logo_alt' => 'Logo Image Alt',
            ],
            'footer' => [
                'group_label' => 'Footer',
                'copyright' => 'Copyright',
            ],
        ],
        'seo' => [
            'tab_label' => 'SEO',
            'defaults' => [
                'group_label' => 'Defaults',
                'default_title' => 'Default Page Title',
                'title_prefix' => 'Page Title Prefix',
                'title_suffix' => 'Page Title Suffix',
                'default_description' => 'Default Meta Description',
                'default_keywords' => 'Default Meta Keywords',

            ],
        ],
        'contacts' => [
            'tab_label' => 'Contacts',
            'general' => [
                'group_label' => 'General',
                'phone' => 'Phone',
                'email' => 'Email',
                'address' => 'Address',
                'schedule' => 'Store Hours of Operation',
                'messengers' => 'Messengers',
                'messengers_icon' => 'Icon',
                'messengers_name' => 'Name',
                'messengers_value' => 'Value',
                'socials' => 'Socials',
                'socials_icon' => 'Icon',
                'socials_url' => 'URL',

            ],
        ],
        'mail' => [
            'tab_label' => 'Mail',
            'transport' => [
                'group_label' => 'Transport',
                'mailer' => 'Driver',
                'smtp' => [
                    'host' => 'Host',
                    'port' => 'Port',
                    'port-help' => 'Usually: 25, 465 (for SSL) and 587 (for TLS)',
                    'encryption' => 'Encryption',
                    'username' => 'Username',
                    'password' => 'Password',
                ],
                'log' => [
                    'channel' => 'Channel',
                ],
            ],
            'addresses' => [
                'group_label' => 'Addresses',
                'receivers' => 'Receivers',
                'sender_email' => 'Sender Email',
                'sender_name' => 'Sender Name',
            ],
        ],
    ],
];
