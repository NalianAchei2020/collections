<?php

namespace WPDesk\FlexibleShippingUsps;

use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display settings sidebar.
 */
class SettingsSidebar implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_shipping_usps_settings_sidebar', [ $this, 'display_settings_sidebar_when_no_pro_version' ] );
	}

	/**
	 * Maybe display settings sidebar.
	 *
	 * @return void
	 */
	public function display_settings_sidebar_when_no_pro_version() {
		if ( ! defined( 'FLEXIBLE_SHIPPING_USPS_PRO_VERSION' ) ) {
			$pro_url  = 'https://octol.io/usps-up-box';
			include __DIR__ . '/views/settings-sidebar-html.php';
		}
	}

}
