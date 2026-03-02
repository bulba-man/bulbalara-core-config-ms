<?php

namespace Bulbalara\CoreConfigMs\Facades\Implement;

use Bulbalara\CoreConfigMs\ConfigModel;
use Illuminate\Support\Arr;

/**
 * Facade alias is ConfigOA
 */
class CoreConfigMs
{
    public static $configTabLabels = [
        'mail' => 'bl.config::core_config.config.mail.tab_label',
        'general' => 'bl.config::core_config.config.general.tab_label',
        'web' => 'bl.config::core_config.config.web.tab_label',
        'design' => 'bl.config::core_config.config.design.tab_label',
        'seo' => 'bl.config::core_config.config.seo.tab_label',
        'contacts' => 'bl.config::core_config.config.contacts.tab_label',
    ];

    public static $configTabGroupLabels = [
        'mail.transport' => 'bl.config::core_config.config.mail.transport.group_label',
        'mail.addresses' => 'bl.config::core_config.config.mail.addresses.group_label',
        'general.general' => 'bl.config::core_config.config.general.general.group_label',
        'web.assets' => 'bl.config::core_config.config.web.assets.group_label',
        'design.head' => 'bl.config::core_config.config.design.head.group_label',
        'design.header' => 'bl.config::core_config.config.design.header.group_label',
        'design.footer' => 'bl.config::core_config.config.design.footer.group_label',
        'seo.defaults' => 'bl.config::core_config.config.seo.defaults.group_label',
        'contacts.general' => 'bl.config::core_config.config.contacts.general.group_label',
    ];

    /**
     * Store config in database
     *
     * @return ConfigModel
     */
    public function addConfig(string $path, array $options = [])
    {
        $coreConfig = \CoreConfig::addConfig(
            $path,
            Arr::get($options, 'value', null),
            Arr::get($options, 'cast', null),
            Arr::get($options, 'default', null)
        );

        $backendType = Arr::get($options, 'backend_type', 'notset');
        if ($backendType == 'notset') {
            return null;
        }

        $config = new ConfigModel;

        $config->config_id = $coreConfig->getKey();
        $config->backend_type = Arr::get($options, 'backend_type');
        $config->source = Arr::get($options, 'source');
        $config->label = Arr::get($options, 'label');
        $config->description = Arr::get($options, 'description');
        $config->resettable = Arr::get($options, 'resettable');
        $config->order = Arr::get($options, 'order', 0);
        $config->depends_of = Arr::get($options, 'depends_of');
        $config->depends_val = Arr::get($options, 'depends_val');
        $config->rules = Arr::get($options, 'rules');
        $config->save();

        return $config;
    }

    /**
     * Add label for config tab
     * $tab - is string of tab id. like 'mail'
     *
     * @return void
     */
    public function addTabLabel(string $tab, string $label)
    {
        static::$configTabLabels[$tab] = $label;
    }

    /**
     * Return config tab label
     * $tab - is string of tab id. like 'mail'
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getTabLabel(string $tab)
    {
        return Arr::get(static::$configTabLabels, $tab);
    }

    /**
     * Return or set config tab label
     * see also addTabLabel and getTabLabel methods
     *
     * @return array|\ArrayAccess|mixed|void
     */
    public function tabLabel(string $tab, string $label = '')
    {
        if ($label) {
            $this->addTabLabel($tab, $label);

            return;
        }

        return $this->getTabLabel($tab);
    }

    /**
     * Add label for config group in the tab
     * $tabGroup - is string of tab and group id separated by dot. like 'mail.transport'
     *
     * @return void
     */
    public function addTabGroupLabel(string $tabGroup, string $label)
    {
        static::$configTabGroupLabels[$tabGroup] = $label;
    }

    /**
     * Return config group in the tab label
     * $tabGroup - is string of tab and group id separated by dot. like 'mail.transport'
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getTabGroupLabel(string $tabGroup)
    {
        return Arr::get(static::$configTabGroupLabels, $tabGroup);
    }

    /**
     * Return or set config group in the tab label
     * see also addTabGroupLabel and getTabGroupLabel methods
     *
     * @return array|\ArrayAccess|mixed|void
     */
    public function tabGroupLabel(string $tabGroup, string $label = '')
    {
        if ($label) {
            $this->addTabGroupLabel($tabGroup, $label);

            return;
        }

        return $this->getTabGroupLabel($tabGroup);
    }
}
