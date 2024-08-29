<?php

namespace FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker;

use FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk_Tracker;
/**
 * .
 */
class Tracker implements \FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var ViewPageTracker
     */
    private $view_page_tracker;
    /**
     * @param ViewPageTracker $view_page_tracker
     */
    public function __construct(\FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker $view_page_tracker)
    {
        $this->view_page_tracker = $view_page_tracker;
    }
    /**
     * Hooks.
     */
    public function hooks() : void
    {
        \add_action('wpdesk_tracker_started', [$this, 'register_tracker_provider']);
    }
    public function register_tracker_provider($tracker) : void
    {
        if ($tracker instanceof \FlexibleShippingUspsVendor\WPDesk_Tracker) {
            $tracker->add_data_provider(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider($this->view_page_tracker));
        }
    }
}
