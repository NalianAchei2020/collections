<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer\ProductSelectSerializer;
use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;
class ProductSelect extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\SelectField
{
    public function __construct()
    {
        $this->set_multiple();
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer
    {
        return new \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer\ProductSelectSerializer();
    }
    public function get_template_name() : string
    {
        return 'product-select';
    }
}
