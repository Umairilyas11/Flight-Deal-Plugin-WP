<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class GFD_Post_Type {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
	}

	public static function register() {
		$labels = array(
			'name'               => __( 'Flight Deals', 'gofly-flight-deals' ),
			'singular_name'      => __( 'Flight Deal', 'gofly-flight-deals' ),
			'add_new'            => __( 'Add New Deal', 'gofly-flight-deals' ),
			'add_new_item'       => __( 'Add New Flight Deal', 'gofly-flight-deals' ),
			'edit_item'          => __( 'Edit Flight Deal', 'gofly-flight-deals' ),
			'new_item'           => __( 'New Flight Deal', 'gofly-flight-deals' ),
			'view_item'          => __( 'View Flight Deal', 'gofly-flight-deals' ),
			'search_items'       => __( 'Search Flight Deals', 'gofly-flight-deals' ),
			'not_found'          => __( 'No flight deals found.', 'gofly-flight-deals' ),
			'not_found_in_trash' => __( 'No flight deals found in Trash.', 'gofly-flight-deals' ),
			'all_items'          => __( 'All Flight Deals', 'gofly-flight-deals' ),
			'menu_name'          => __( 'Flight Deals', 'gofly-flight-deals' ),
		);

		$slug = function_exists( 'gofly_flight_deals_get_option' )
			? gofly_flight_deals_get_option( 'archive_slug', 'flight-deals' )
			: 'flight-deals';

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-airplane',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		);

		register_post_type( 'flight_deal', $args );
	}

	public static function register_taxonomies() {
		register_taxonomy( 'flight_deal_airline', 'flight_deal', array(
			'labels'       => array(
				'name'          => __( 'Airlines', 'gofly-flight-deals' ),
				'singular_name' => __( 'Airline', 'gofly-flight-deals' ),
				'add_new_item'  => __( 'Add New Airline', 'gofly-flight-deals' ),
				'edit_item'     => __( 'Edit Airline', 'gofly-flight-deals' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'flight-deal-airline' ),
		) );

		register_taxonomy( 'flight_deal_destination', 'flight_deal', array(
			'labels'       => array(
				'name'          => __( 'Destination Regions', 'gofly-flight-deals' ),
				'singular_name' => __( 'Destination Region', 'gofly-flight-deals' ),
				'add_new_item'  => __( 'Add New Destination', 'gofly-flight-deals' ),
				'edit_item'     => __( 'Edit Destination', 'gofly-flight-deals' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'flight-deal-destination' ),
		) );

		register_taxonomy( 'flight_deal_type', 'flight_deal', array(
			'labels'       => array(
				'name'          => __( 'Trip Types', 'gofly-flight-deals' ),
				'singular_name' => __( 'Trip Type', 'gofly-flight-deals' ),
				'add_new_item'  => __( 'Add New Trip Type', 'gofly-flight-deals' ),
				'edit_item'     => __( 'Edit Trip Type', 'gofly-flight-deals' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'flight-deal-type' ),
		) );

		if ( function_exists( 'pll_register_string' ) ) {
			$book_now = function_exists( 'gofly_flight_deals_get_option' )
				? gofly_flight_deals_get_option( 'book_now_text', 'Book Now' )
				: 'Book Now';
			pll_register_string( 'gfd_book_now_text', $book_now, 'GoFly Flight Deals' );
		}
	}
}

GFD_Post_Type::init();
