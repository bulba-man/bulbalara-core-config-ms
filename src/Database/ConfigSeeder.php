<?php

namespace Bulbalara\CoreConfigMs\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ConfigSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        \Bulbalara\CoreConfig\Models\Config::truncate();
        \Bulbalara\CoreConfigMs\ConfigModel::truncate();
        Schema::enableForeignKeyConstraints();

        \ConfigMS::addConfig('general.general.site_name', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.general.general.site_name',
        ]);

        \ConfigMS::addConfig('mail.transport.mailer', [
            'value' => 'smtp',
            'cast' => 'string',
            'default' => 'log',
            'backend_type' => 'select',
            'source' => json_encode(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'log' => 'Log']),
            'label' => 'bl.config::core_config.config.mail.transport.mailer',
        ]);

        \ConfigMS::addConfig('mail.transport.smtp.host', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'smtp',
            'label' => 'bl.config::core_config.config.mail.transport.smtp.host',
        ]);

        \ConfigMS::addConfig('mail.transport.smtp.port', [
            'value' => '465',
            'cast' => 'string',
            'default' => '465',
            'backend_type' => 'text',
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'smtp',
            'label' => 'bl.config::core_config.config.mail.transport.smtp.port',
            'description' => 'bl.config::core_config.config.mail.transport.smtp.port-help',
        ]);
        \ConfigMS::addConfig('mail.transport.smtp.encryption', [
            'value' => 'tls',
            'cast' => 'string',
            'default' => 'tls',
            'backend_type' => 'select',
            'source' => json_encode(['ssl' => 'SSL', 'tls' => 'TLS']),
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'smtp',
            'label' => 'bl.config::core_config.config.mail.transport.smtp.encryption',
        ]);
        \ConfigMS::addConfig('mail.transport.smtp.username', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'smtp',
            'label' => 'bl.config::core_config.config.mail.transport.smtp.username',
        ]);
        \ConfigMS::addConfig('mail.transport.smtp.password', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'smtp',
            'label' => 'bl.config::core_config.config.mail.transport.smtp.password',
        ]);

        \ConfigMS::addConfig('mail.transport.log.channel', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'depends_of' => 'mail.transport.mailer',
            'depends_val' => 'log',
            'label' => 'bl.config::core_config.config.mail.transport.log.channel',
        ]);

        \ConfigMS::addConfig('mail.addresses.from.address', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'email',
            'label' => 'bl.config::core_config.config.mail.addresses.sender_email',
        ]);

        \ConfigMS::addConfig('mail.addresses.from.name', [
            'value' => '',
            'cast' => 'string',
            'default' => 'Sender',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.mail.addresses.sender_name',
        ]);

        \ConfigMS::addConfig('mail.addresses.receivers', [
            'value' => '',
            'cast' => 'json',
            'default' => '[]',
            'backend_type' => 'list',
            'label' => 'bl.config::core_config.config.mail.addresses.receivers',
            'rules' => 'nullable|email',
        ]);

        \ConfigMS::addConfig('design.head.favicon', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'image',
            'label' => 'bl.config::core_config.config.design.head.favicon',
        ]);
        \ConfigMS::addConfig('design.head.html_lang', [
            'value' => 'ru',
            'cast' => 'string',
            'default' => 'en',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.design.head.html_lang',
            'description' => 'bl.config::core_config.config.design.head.html_lang-help',
        ]);
        \ConfigMS::addConfig('design.head.custom_scripts', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'textarea',
            'label' => 'bl.config::core_config.config.design.head.custom_scripts',
        ]);
        \ConfigMS::addConfig('design.head.custom_styles', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'textarea',
            'label' => 'bl.config::core_config.config.design.head.custom_styles',
        ]);

        \ConfigMS::addConfig('design.header.logo', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'image',
            'label' => 'bl.config::core_config.config.design.header.logo',
        ]);
        \ConfigMS::addConfig('design.header.logo_width', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.design.header.logo_width',
            'description' => 'px',
        ]);
        \ConfigMS::addConfig('design.header.logo_height', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.design.header.logo_height',
            'description' => 'px',
        ]);
        \ConfigMS::addConfig('design.header.logo_alt', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.design.header.logo_alt',
        ]);
        \ConfigMS::addConfig('design.footer.copyright', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.design.footer.copyright',
        ]);

        \ConfigMS::addConfig('seo.defaults.default_title', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.seo.defaults.default_title',
        ]);
        \ConfigMS::addConfig('seo.defaults.title_prefix', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.seo.defaults.title_prefix',
        ]);
        \ConfigMS::addConfig('seo.defaults.title_suffix', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.seo.defaults.title_suffix',
        ]);
        \ConfigMS::addConfig('seo.defaults.default_description', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'textarea',
            'label' => 'bl.config::core_config.config.seo.defaults.default_description',
        ]);
        \ConfigMS::addConfig('seo.defaults.default_keywords', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'textarea',
            'label' => 'bl.config::core_config.config.seo.defaults.default_keywords',
        ]);

        \ConfigMS::addConfig('contacts.general.phone', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.contacts.general.phone',
        ]);
        \ConfigMS::addConfig('contacts.general.email', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.contacts.general.email',
        ]);
        \ConfigMS::addConfig('contacts.general.address', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.contacts.general.address',
        ]);
        \ConfigMS::addConfig('contacts.general.schedule', [
            'value' => '',
            'cast' => 'string',
            'backend_type' => 'text',
            'label' => 'bl.config::core_config.config.contacts.general.schedule',
        ]);
        \ConfigMS::addConfig('contacts.general.messengers', [
            'value' => '',
            'cast' => 'json',
            'backend_type' => \Bulbalara\CoreConfigMs\Fields\Messengers::class,
            'label' => 'bl.config::core_config.config.contacts.general.messengers',
        ]);
        \ConfigMS::addConfig('contacts.general.socials', [
            'value' => '',
            'cast' => 'json',
            'backend_type' => \Bulbalara\CoreConfigMs\Fields\Socials::class,
            'label' => 'bl.config::core_config.config.contacts.general.socials',
        ]);

    }
}
