<?php
/**
 * Settings sidebar.
 *
 * @package WPDesk\FlexibleShippingUsps
 */

/**
 * Params.
 *
 * @var string $pro_url
 */
?>
<div class="wpdesk-metabox">
	<div class="wpdesk-stuffbox">
		<h3 class="title"><?php esc_html_e( 'Get USPS WooCommerce Live Rates PRO!', 'flexible-shipping-usps' ); ?></h3>
		<div class="inside">
			<div class="main">
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Handling Fees', 'flexible-shipping-usps' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Automatic Box Packing', 'flexible-shipping-usps' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Premium Support', 'flexible-shipping-usps' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Multicurrency Support', 'flexible-shipping-usps' ); ?></li>
				</ul>

				<a class="button button-primary" href="<?php echo esc_url( $pro_url ); ?>"
				   target="_blank"><?php esc_html_e( 'Upgrade Now &rarr;', 'flexible-shipping-usps' ); ?></a>
			</div>
		</div>
	</div>
</div>
