<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;
class TextFieldSanitizer implements \FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer
{
    /** @return string|string[] */
    public function sanitize($value)
    {
        if (\is_array($value)) {
            return \array_map('sanitize_text_field', $value);
        }
        return \sanitize_text_field($value);
    }
}
