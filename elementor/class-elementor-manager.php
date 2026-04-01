<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class GFD_Elementor_Manager {

	public function __construct() {
		// Register widget category.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

		// Register widgets — support both old and new Elementor hook names.
		add_action( 'elementor/widgets/register',             array( $this, 'register_widgets' ) );
		add_action( 'elementor/widgets/widgets_registered',   array( $this, 'register_widgets' ) );
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category( 'gofly-flight-deals', array(
			'title' => __( 'GoFly Flight Deals', 'gofly-flight-deals' ),
			'icon'  => 'fa fa-plane',
		) );
	}

	public function register_widgets( $widgets_manager ) {
		// Prevent double-registration.
		static $registered = false;
		if ( $registered ) { return; }
		$registered = true;

		require_once GFD_PLUGIN_DIR . 'elementor/widgets/class-widget-deals-grid.php';
		require_once GFD_PLUGIN_DIR . 'elementor/widgets/class-widget-deals-carousel.php';
		require_once GFD_PLUGIN_DIR . 'elementor/widgets/class-widget-deal-search.php';

		// Elementor 3.5+ uses register(), older uses register_widget_type().
		if ( method_exists( $widgets_manager, 'register' ) ) {
			$widgets_manager->register( new GFD_Widget_Deals_Grid() );
			$widgets_manager->register( new GFD_Widget_Deals_Carousel() );
			$widgets_manager->register( new GFD_Widget_Deal_Search() );
		} else {
			$widgets_manager->register_widget_type( new GFD_Widget_Deals_Grid() );
			$widgets_manager->register_widget_type( new GFD_Widget_Deals_Carousel() );
			$widgets_manager->register_widget_type( new GFD_Widget_Deal_Search() );
		}
	}
}
