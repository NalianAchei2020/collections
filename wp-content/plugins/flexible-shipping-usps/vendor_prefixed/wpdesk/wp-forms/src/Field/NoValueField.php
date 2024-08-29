<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

/**
 * Base class for Fields that can show itself on form but cannot process any value.
 *
 * @package WPDesk\Forms
 */
abstract class NoValueField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct()
    {
        $this->set_name('');
    }
}
