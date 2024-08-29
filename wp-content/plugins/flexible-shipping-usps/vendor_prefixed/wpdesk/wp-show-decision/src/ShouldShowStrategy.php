<?php

namespace FlexibleShippingUspsVendor\WPDesk\ShowDecision;

/**
 * Should something be shown?
 */
interface ShouldShowStrategy
{
    /**
     * @return bool
     */
    public function shouldDisplay();
}
