<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;
use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer\JsonSerializer;
class TimepickerField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'time';
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer
    {
        return new \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer\JsonSerializer();
    }
    public function get_template_name() : string
    {
        return 'timepicker';
    }
}
