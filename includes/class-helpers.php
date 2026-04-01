<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Get plugin option with fallback default.
 */
function gofly_flight_deals_get_option( $key, $default = '' ) {
	$options = get_option( 'gfd_settings', array() );
	return isset( $options[ $key ] ) ? $options[ $key ] : $default;
}

/**
 * Format price with currency symbol.
 */
function gofly_flight_deals_format_price( $price, $currency = '' ) {
	if ( empty( $currency ) ) {
		$currency = gofly_flight_deals_get_option( 'default_currency', 'USD' );
	}
	$symbols = array(
		'USD' => '$', 'EUR' => '€', 'GBP' => '£',
		'LKR' => 'Rs', 'AED' => 'AED', 'SGD' => 'S$',
		'AUD' => 'A$', 'CAD' => 'C$', 'JPY' => '¥',
	);
	$symbol   = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency;
	$position = gofly_flight_deals_get_option( 'currency_position', 'before' );
	$formatted = number_format( (float) $price, 0 );
	return 'before' === $position ? $symbol . $formatted : $formatted . ' ' . $symbol;
}

/**
 * Get meta field value for a flight deal post.
 */
function gofly_flight_deals_get_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	return '' !== $value ? $value : $default;
}

/**
 * Check if a deal has expired.
 */
function gofly_flight_deals_is_expired( $post_id ) {
	$expiry = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_expiry' );
	if ( empty( $expiry ) ) { return false; }
	return strtotime( $expiry ) < current_time( 'timestamp' );
}

/**
 * Build the meta query for expiry filtering.
 */
function gofly_flight_deals_expiry_meta_query() {
	return array(
		'relation' => 'OR',
		array(
			'key'     => '_gfd_deal_expiry',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_gfd_deal_expiry',
			'value'   => '',
			'compare' => '=',
		),
		array(
			'key'     => '_gfd_deal_expiry',
			'value'   => date( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		),
	);
}

/**
 * Get all currencies list.
 */
function gofly_flight_deals_currencies() {
	return array(
		'USD' => 'USD – US Dollar',
		'EUR' => 'EUR – Euro',
		'GBP' => 'GBP – British Pound',
		'LKR' => 'LKR – Sri Lankan Rupee',
		'AED' => 'AED – UAE Dirham',
		'SGD' => 'SGD – Singapore Dollar',
		'AUD' => 'AUD – Australian Dollar',
		'CAD' => 'CAD – Canadian Dollar',
		'JPY' => 'JPY – Japanese Yen',
		'INR' => 'INR – Indian Rupee',
		'MYR' => 'MYR – Malaysian Ringgit',
	);
}

/**
 * Get travel classes.
 */
function gofly_flight_deals_travel_classes() {
	return array(
		'economy'  => __( 'Economy', 'gofly-flight-deals' ),
		'business' => __( 'Business', 'gofly-flight-deals' ),
		'first'    => __( 'First Class', 'gofly-flight-deals' ),
	);
}

/**
 * Get stops options.
 */
function gofly_flight_deals_stops_options() {
	return array(
		'nonstop' => __( 'Non-stop', 'gofly-flight-deals' ),
		'1stop'   => __( '1 Stop', 'gofly-flight-deals' ),
		'2plus'   => __( '2+ Stops', 'gofly-flight-deals' ),
	);
}

/**
 * Locate a plugin template, allowing theme overrides.
 */
function gofly_flight_deals_locate_template( $template_name ) {
	$theme_template  = locate_template( array(
		'gofly-flight-deals/' . $template_name,
		$template_name,
	) );
	if ( $theme_template ) { return $theme_template; }
	return GFD_PLUGIN_DIR . 'templates/' . $template_name;
}
