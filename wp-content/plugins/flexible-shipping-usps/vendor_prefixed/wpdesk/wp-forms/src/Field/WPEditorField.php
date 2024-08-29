<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

class WPEditorField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    public function get_template_name() : string
    {
        return 'wp-editor';
    }
}
