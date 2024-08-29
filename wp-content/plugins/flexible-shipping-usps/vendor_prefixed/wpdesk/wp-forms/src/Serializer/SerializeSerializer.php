<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;
class SerializeSerializer implements \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        return \serialize($value);
    }
    public function unserialize(string $value)
    {
        return \unserialize($value);
    }
}
