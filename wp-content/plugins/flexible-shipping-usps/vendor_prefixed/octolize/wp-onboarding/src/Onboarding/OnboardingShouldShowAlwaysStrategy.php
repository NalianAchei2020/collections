<?php

/**
 * @package Octolize\Onboarding
 */
namespace FlexibleShippingUspsVendor\Octolize\Onboarding;

/**
 * Always display strategy.
 */
class OnboardingShouldShowAlwaysStrategy implements \FlexibleShippingUspsVendor\Octolize\Onboarding\OnboardingShouldShowStrategy
{
    public function should_display() : bool
    {
        return \true;
    }
}
