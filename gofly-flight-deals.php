<?php
/**
 * Plugin Name:       GoFly Flight Deals
 * Plugin URI:        https://yoursite.com/gofly-flight-deals
 * Description:       Adds a Flight Deals custom post type with Elementor widgets for the GoFly theme.
 * Version:           1.0.1
 * Author:            Your Name
 * License:           GPL-2.0+
 * Text Domain:       gofly-flight-deals
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GFD_VERSION',     '1.0.1' );
define( 'GFD_PLUGIN_FILE', __FILE__ );
define( 'GFD_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'GFD_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

/**
 * Load all includes immediately — not on plugins_loaded —
 * so helpers are available before any hook fires.
 */
require_once GFD_PLUGIN_DIR . 'includes/class-helpers.php';
require_once GFD_PLUGIN_DIR . 'includes/class-post-type.php';
require_once GFD_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once GFD_PLUGIN_DIR . 'includes/class-admin-columns.php';
require_once GFD_PLUGIN_DIR . 'includes/class-settings.php';

/**
 * Text domain.
 */
add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'gofly-flight-deals', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

/**
 * Elementor integration — hooked multiple ways to catch it
 * regardless of plugin load order.
 */
function gofly_flight_deals_load_elementor() {
	if ( ! did_action( 'elementor/loaded' ) ) { return; }
	if ( ! class_exists( '\Elementor\Plugin' ) ) { return; }
	if ( defined( 'GFD_ELEMENTOR_LOADED' ) ) { return; }
	define( 'GFD_ELEMENTOR_LOADED', true );
	require_once GFD_PLUGIN_DIR . 'elementor/class-elementor-manager.php';
	new GFD_Elementor_Manager();
}
add_action( 'elementor/loaded',               'gofly_flight_deals_load_elementor' );
add_action( 'elementor/widgets/widgets_registered', 'gofly_flight_deals_load_elementor' ); // fallback for older Elementor
add_action( 'init',                           'gofly_flight_deals_load_elementor', 20 );

/**
 * Frontend assets.
 */
add_action( 'wp_enqueue_scripts', function() {
	if ( ! gofly_flight_deals_is_plugin_page() ) { return; }
	wp_enqueue_style( 'gfd-frontend', GFD_PLUGIN_URL . 'assets/css/frontend.css', array(), GFD_VERSION );
	wp_enqueue_script( 'gfd-frontend', GFD_PLUGIN_URL . 'assets/js/frontend.js', array(), GFD_VERSION, true );
	wp_localize_script( 'gfd-frontend', 'gfdData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'gfd_nonce' ),
	) );
} );

/**
 * Admin assets.
 */
add_action( 'admin_enqueue_scripts', function() {
	$screen = get_current_screen();
	if ( ! $screen ) { return; }
	if ( 'flight_deal' !== $screen->post_type && 'flight_deal_page_gfd-settings' !== $screen->id ) { return; }
	wp_enqueue_style( 'gfd-admin', GFD_PLUGIN_URL . 'assets/css/admin.css', array(), GFD_VERSION );
	wp_enqueue_media();
	wp_enqueue_script( 'gfd-admin', GFD_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-datepicker' ), GFD_VERSION, true );
	wp_enqueue_style( 'jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', array(), '1.13.2' );
} );

/**
 * Helper: is the current page a plugin page?
 */
function gofly_flight_deals_is_plugin_page() {
	return is_singular( 'flight_deal' )
		|| is_post_type_archive( 'flight_deal' )
		|| is_tax( array( 'flight_deal_airline', 'flight_deal_destination', 'flight_deal_type' ) )
		|| gofly_flight_deals_page_has_widget();
}

function gofly_flight_deals_page_has_widget() {
	global $post;
	if ( ! $post ) { return false; }
	return strpos( $post->post_content, 'gfd-deals-grid' ) !== false
		|| strpos( $post->post_content, 'gfd-deals-carousel' ) !== false
		|| strpos( $post->post_content, 'gfd-deal-search' ) !== false;
}

/**
 * Activation.
 */
register_activation_hook( __FILE__, function() {
	GFD_Post_Type::register();
	GFD_Post_Type::register_taxonomies();
	flush_rewrite_rules();
} );

/**
 * Deactivation.
 */
register_deactivation_hook( __FILE__, function() {
	flush_rewrite_rules();
} );

/**
 * Flush rewrite rules once after activation via transient
 * (catches cases where activation hook fires too early).
 */
add_action( 'init', function() {
	if ( get_transient( 'gfd_flush_rewrite_on_init' ) ) {
		flush_rewrite_rules();
		delete_transient( 'gfd_flush_rewrite_on_init' );
	}
}, 99 );

register_activation_hook( __FILE__, function() {
	set_transient( 'gfd_flush_rewrite_on_init', true, 60 );
} );
