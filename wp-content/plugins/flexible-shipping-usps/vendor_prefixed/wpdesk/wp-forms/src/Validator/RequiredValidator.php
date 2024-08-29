<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Validator;

use FlexibleShippingUspsVendor\WPDesk\Forms\Validator;
class RequiredValidator implements \FlexibleShippingUspsVendor\WPDesk\Forms\Validator
{
    public function is_valid($value) : bool
    {
        return $value !== null;
    }
    public function get_messages() : array
    {
        return [];
    }
}
