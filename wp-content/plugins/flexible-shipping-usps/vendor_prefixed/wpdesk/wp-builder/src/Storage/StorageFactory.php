<?php

namespace FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}
