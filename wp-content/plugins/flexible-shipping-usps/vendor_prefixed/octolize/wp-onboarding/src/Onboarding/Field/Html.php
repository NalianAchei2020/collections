<?php

namespace FlexibleShippingUspsVendor\Octolize\Onboarding\Field;

use FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField;
/**
 * Html field.
 */
class Html extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    protected $meta = ['priority' => self::DEFAULT_PRIORITY, 'default_value' => '', 'label' => '', 'description' => '', 'description_tip' => '', 'data' => [], 'type' => 'html'];
    public function get_template_name() : string
    {
        return 'html';
    }
}
