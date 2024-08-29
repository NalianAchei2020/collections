<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Serializer;
class JsonSerializer implements \FlexibleShippingUspsVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        return (string) \json_encode($value);
    }
    public function unserialize(string $value)
    {
        return \json_decode($value, \true);
    }
}
