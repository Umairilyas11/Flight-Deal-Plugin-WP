<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class GFD_Admin_Columns {

	public static function init() {
		add_filter( 'manage_flight_deal_posts_columns', array( __CLASS__, 'set_columns' ) );
		add_action( 'manage_flight_deal_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_filter( 'manage_edit-flight_deal_sortable_columns', array( __CLASS__, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'handle_sort' ) );
	}

	public static function set_columns( $columns ) {
		$new = array();
		$new['cb']              = $columns['cb'];
		$new['gfd_logo']        = __( 'Logo', 'gofly-flight-deals' );
		$new['title']           = __( 'Route', 'gofly-flight-deals' );
		$new['gfd_origin']      = __( 'Origin', 'gofly-flight-deals' );
		$new['gfd_destination'] = __( 'Destination', 'gofly-flight-deals' );
		$new['gfd_airline']     = __( 'Airline', 'gofly-flight-deals' );
		$new['gfd_price']       = __( 'Price', 'gofly-flight-deals' );
		$new['gfd_class']       = __( 'Class', 'gofly-flight-deals' );
		$new['gfd_departure']   = __( 'Departure', 'gofly-flight-deals' );
		$new['gfd_expiry']      = __( 'Expires', 'gofly-flight-deals' );
		$new['gfd_featured']    = __( 'Featured', 'gofly-flight-deals' );
		$new['date']            = $columns['date'];
		return $new;
	}

	public static function render_column( $column, $post_id ) {
		switch ( $column ) {
			case 'gfd_logo':
				$logo_id = gofly_flight_deals_get_meta( $post_id, '_gfd_airline_logo' );
				if ( $logo_id ) {
					echo wp_get_attachment_image( $logo_id, array( 50, 30 ), false, array( 'style' => 'height:30px;width:auto;object-fit:contain;' ) );
				} else {
					echo '<span class="dashicons dashicons-airplane"></span>';
				}
				break;
			case 'gfd_origin':
				$city = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_city' );
				$code = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_code' );
				echo esc_html( $city ) . ( $code ? ' <strong>(' . esc_html( $code ) . ')</strong>' : '' );
				break;
			case 'gfd_destination':
				$city = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_city' );
				$code = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_code' );
				echo esc_html( $city ) . ( $code ? ' <strong>(' . esc_html( $code ) . ')</strong>' : '' );
				break;
			case 'gfd_airline':
				echo esc_html( gofly_flight_deals_get_meta( $post_id, '_gfd_airline_name' ) );
				break;
			case 'gfd_price':
				$price    = gofly_flight_deals_get_meta( $post_id, '_gfd_price' );
				$currency = gofly_flight_deals_get_meta( $post_id, '_gfd_currency' );
				echo $price ? '<strong>' . esc_html( gofly_flight_deals_format_price( $price, $currency ) ) . '</strong>' : '—';
				break;
			case 'gfd_class':
				$classes = gofly_flight_deals_travel_classes();
				$val     = gofly_flight_deals_get_meta( $post_id, '_gfd_travel_class' );
				echo isset( $classes[ $val ] ) ? esc_html( $classes[ $val ] ) : '—';
				break;
			case 'gfd_departure':
				$date = gofly_flight_deals_get_meta( $post_id, '_gfd_departure_date' );
				echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—';
				break;
			case 'gfd_expiry':
				$expiry = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_expiry' );
				if ( ! $expiry ) {
					echo '—';
				} elseif ( gofly_flight_deals_is_expired( $post_id ) ) {
					echo '<span class="gfd-badge gfd-badge--expired">' . esc_html__( 'Expired', 'gofly-flight-deals' ) . '</span>';
				} else {
					echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expiry ) ) );
				}
				break;
			case 'gfd_featured':
				$featured = gofly_flight_deals_get_meta( $post_id, '_gfd_is_featured' );
				echo $featured ? '<span class="gfd-badge gfd-badge--featured">&#9733; ' . esc_html__( 'Yes', 'gofly-flight-deals' ) . '</span>' : '<span style="color:#ccc;">—</span>';
				break;
		}
	}

	public static function sortable_columns( $columns ) {
		$columns['gfd_price']     = 'gfd_price';
		$columns['gfd_departure'] = 'gfd_departure';
		$columns['gfd_expiry']    = 'gfd_expiry';
		return $columns;
	}

	public static function handle_sort( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) { return; }
		$orderby = $query->get( 'orderby' );
		if ( 'gfd_price' === $orderby ) {
			$query->set( 'meta_key', '_gfd_price' );
			$query->set( 'orderby', 'meta_value_num' );
		} elseif ( 'gfd_departure' === $orderby ) {
			$query->set( 'meta_key', '_gfd_departure_date' );
			$query->set( 'orderby', 'meta_value' );
		} elseif ( 'gfd_expiry' === $orderby ) {
			$query->set( 'meta_key', '_gfd_deal_expiry' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}

GFD_Admin_Columns::init();
