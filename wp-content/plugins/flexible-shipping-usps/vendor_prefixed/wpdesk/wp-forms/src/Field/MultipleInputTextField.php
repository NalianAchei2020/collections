<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

class MultipleInputTextField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\InputTextField
{
    public function get_template_name() : string
    {
        return 'input-text-multiple';
    }
}
