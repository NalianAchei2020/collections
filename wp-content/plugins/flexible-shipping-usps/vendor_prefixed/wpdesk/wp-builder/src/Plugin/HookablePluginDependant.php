<?php

namespace FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin;

interface HookablePluginDependant extends \FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * Set Plugin.
     *
     * @param AbstractPlugin $plugin Plugin.
     *
     * @return null
     */
    public function set_plugin(\FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin $plugin);
    /**
     * Get plugin.
     *
     * @return AbstractPlugin.
     */
    public function get_plugin();
}
