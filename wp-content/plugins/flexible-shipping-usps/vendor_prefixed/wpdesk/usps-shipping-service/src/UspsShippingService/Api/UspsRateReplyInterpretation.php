<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API.
 */
abstract class UspsRateReplyInterpretation
{
    /**
     * Is tax enabled.
     *
     * @var bool
     */
    protected $is_tax_enabled;
    /**
     * Reply.
     *
     * @var Rate
     */
    protected $request;
    /**
     * UspsRateReplyInterpretation constructor.
     *
     * @param Rate $request Rate request.
     * @param bool $is_tax_enabled Is tax enabled.
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate $request, $is_tax_enabled)
    {
        $this->request = $request;
        $this->is_tax_enabled = $is_tax_enabled;
    }
    /**
     * Has reply error.
     *
     * @return bool
     */
    public function has_reply_error()
    {
        return !$this->request->isSuccess();
    }
    /**
     * Has reply warning.
     *
     * @return bool
     */
    public function has_reply_warning()
    {
        return \false;
    }
    /**
     * Get reply error message.
     *
     * @return string
     */
    public function get_reply_message()
    {
        return '';
    }
    /**
     * Get response from USPS.
     *
     * @return SingleRate[]
     */
    public abstract function get_rates();
}
