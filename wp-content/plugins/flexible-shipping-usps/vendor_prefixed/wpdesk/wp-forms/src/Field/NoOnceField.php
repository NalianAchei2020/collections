<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

use FlexibleShippingUspsVendor\WPDesk\Forms\Validator;
use FlexibleShippingUspsVendor\WPDesk\Forms\Validator\NonceValidator;
class NoOnceField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct(string $action_name)
    {
        $this->meta['action'] = $action_name;
    }
    public function get_validator() : \FlexibleShippingUspsVendor\WPDesk\Forms\Validator
    {
        return new \FlexibleShippingUspsVendor\WPDesk\Forms\Validator\NonceValidator($this->get_meta_value('action'));
    }
    public function get_template_name() : string
    {
        return 'noonce';
    }
}
