<?php
/**
 * Plugin Name:       GoFly Flight Deals
 * Plugin URI:        https://yoursite.com/gofly-flight-deals
 * Description:       Adds a Flight Deals custom post type with Elementor widgets for the GoFly theme.
 * Version:           1.0.3
 * Author:            Your Name
 * License:           GPL-2.0+
 * Text Domain:       gofly-flight-deals
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GFD_VERSION',     '1.0.3' );
define( 'GFD_PLUGIN_FILE', __FILE__ );
define( 'GFD_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'GFD_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

require_once GFD_PLUGIN_DIR . 'includes/class-helpers.php';
require_once GFD_PLUGIN_DIR . 'includes/class-post-type.php';
require_once GFD_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once GFD_PLUGIN_DIR . 'includes/class-admin-columns.php';
require_once GFD_PLUGIN_DIR . 'includes/class-settings.php';

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'gofly-flight-deals', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

function gofly_flight_deals_load_elementor() {
	if ( ! did_action( 'elementor/loaded' ) ) { return; }
	if ( ! class_exists( '\Elementor\Plugin' ) ) { return; }
	if ( defined( 'GFD_ELEMENTOR_LOADED' ) ) { return; }
	define( 'GFD_ELEMENTOR_LOADED', true );
	require_once GFD_PLUGIN_DIR . 'elementor/class-elementor-manager.php';
	new GFD_Elementor_Manager();
}
add_action( 'elementor/loaded',                    'gofly_flight_deals_load_elementor' );
add_action( 'elementor/widgets/widgets_registered','gofly_flight_deals_load_elementor' );
add_action( 'init',                                'gofly_flight_deals_load_elementor', 20 );

/**
 * Frontend assets.
 */
add_action( 'wp_enqueue_scripts', function() {
	
	if ( ! gofly_flight_deals_is_plugin_page() ) { return; }
	wp_enqueue_style( 'gfd-frontend', GFD_PLUGIN_URL . 'assets/css/frontend.css', array(), GFD_VERSION );
	// Inject dynamic color CSS variables from settings.
$color_primary      = gofly_flight_deals_get_option( 'color_primary',      '#0057b8' );
$color_primary_dark = gofly_flight_deals_get_option( 'color_primary_dark', '#004499' );
$color_accent       = gofly_flight_deals_get_option( 'color_accent',       '#ff6b00' );
$inline_css = "
    :root {
        --gfd-primary:      {$color_primary};
        --gfd-primary-dark: {$color_primary_dark};
        --gfd-accent:       {$color_accent};
    }
";
wp_add_inline_style( 'gfd-frontend', $inline_css );
	wp_enqueue_script( 'gfd-frontend', GFD_PLUGIN_URL . 'assets/js/frontend.js', array(), GFD_VERSION, true );
	wp_localize_script( 'gfd-frontend', 'gfdData', array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'gfd_nonce' ),
		'cf7FormId'  => gofly_flight_deals_get_option( 'inquiry_cf7_id', 0 ),
	) );
} );

/**
 * Render the inquiry popup modal once in the footer.
 * Only on plugin pages and only when a CF7 form is configured.
 */
add_action( 'wp_footer', function() {
	if ( ! gofly_flight_deals_is_plugin_page() ) { return; }
	$cf7_id = gofly_flight_deals_get_option( 'inquiry_cf7_id', 0 );
	if ( empty( $cf7_id ) ) { return; }
	include gofly_flight_deals_locate_template( 'partials/deal-inquiry-popup.php' );
} );

/**
 * Admin assets.
 */
add_action( 'admin_enqueue_scripts', function() {
	$screen = get_current_screen();
	if ( ! $screen ) { return; }
	$allowed_post_types = array( 'flight_deal' );
$allowed_screens    = array( 'flight_deal_page_gfd-settings', 'edit-flight_deal_airline' );
if ( ! in_array( $screen->post_type, $allowed_post_types, true ) && ! in_array( $screen->id, $allowed_screens, true ) ) { return; }
	wp_enqueue_style( 'gfd-admin', GFD_PLUGIN_URL . 'assets/css/admin.css', array(), GFD_VERSION );
	wp_enqueue_media();
	wp_enqueue_script( 'gfd-admin', GFD_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-datepicker' ), GFD_VERSION, true );
	wp_enqueue_style( 'jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', array(), '1.13.2' );
} );

function gofly_flight_deals_is_plugin_page() {
	return is_singular( 'flight_deal' )
		|| is_post_type_archive( 'flight_deal' )
		|| is_tax( array( 'flight_deal_airline', 'flight_deal_destination', 'flight_deal_type' ) )
		|| gofly_flight_deals_page_has_widget();
}

function gofly_flight_deals_page_has_widget() {
	global $post;
	if ( ! $post ) { return false; }

	// Check post_content (classic editor / shortcodes).
	if ( strpos( $post->post_content, 'gfd-deals-grid' ) !== false
		|| strpos( $post->post_content, 'gfd-deals-carousel' ) !== false
		|| strpos( $post->post_content, 'gfd-deal-search' ) !== false ) {
		return true;
	}

	// Check Elementor data stored in post meta (Elementor doesn't write to post_content).
	$elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
	if ( $elementor_data ) {
		return strpos( $elementor_data, 'gfd-deals-grid' ) !== false
			|| strpos( $elementor_data, 'gfd-deals-carousel' ) !== false
			|| strpos( $elementor_data, 'gfd-deal-search' ) !== false;
	}

	return false;
}

register_activation_hook( __FILE__, function() {
	GFD_Post_Type::register();
	GFD_Post_Type::register_taxonomies();
	flush_rewrite_rules();
	set_transient( 'gfd_flush_rewrite_on_init', true, 60 );
} );

register_deactivation_hook( __FILE__, function() { flush_rewrite_rules(); } );

add_action( 'init', function() {
	if ( get_transient( 'gfd_flush_rewrite_on_init' ) ) {
		flush_rewrite_rules();
		delete_transient( 'gfd_flush_rewrite_on_init' );
	}
}, 99 );
