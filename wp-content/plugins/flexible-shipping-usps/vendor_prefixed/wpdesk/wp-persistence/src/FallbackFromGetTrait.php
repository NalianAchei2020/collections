<?php

namespace FlexibleShippingUspsVendor\WPDesk\Persistence;

use FlexibleShippingUspsVendor\Psr\Container\NotFoundExceptionInterface;
trait FallbackFromGetTrait
{
    public function get_fallback(string $id, $fallback = null)
    {
        try {
            return $this->get($id);
        } catch (\FlexibleShippingUspsVendor\Psr\Container\NotFoundExceptionInterface $e) {
            return $fallback;
        }
    }
}
