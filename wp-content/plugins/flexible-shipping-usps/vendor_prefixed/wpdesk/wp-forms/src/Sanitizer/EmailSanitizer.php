<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements \FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer
{
    public function sanitize($value) : string
    {
        return \sanitize_email($value);
    }
}
