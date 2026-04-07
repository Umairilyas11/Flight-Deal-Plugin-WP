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

// Add image field to "Add New Airline" form.
add_action( 'flight_deal_airline_add_form_fields', function() {
    ?>
    <div class="form-field">
        <label><?php esc_html_e( 'Airline Logo', 'gofly-flight-deals' ); ?></label>
        <div class="gfd-term-image-wrap">
            <input type="hidden" name="gfd_term_image_id" id="gfd_term_image_id" value="" />
            <div id="gfd_term_image_preview"></div>
            <button type="button" class="button gfd-upload-term-image"><?php esc_html_e( 'Upload Logo', 'gofly-flight-deals' ); ?></button>
        </div>
    </div>
    <?php
} );

// Add image field to "Edit Airline" form.
add_action( 'flight_deal_airline_edit_form_fields', function( $term ) {
    $image_id  = get_term_meta( $term->term_id, 'gfd_airline_image_id', true );
    $image_url = $image_id ? wp_get_attachment_image_url( absint( $image_id ), 'thumbnail' ) : '';
    ?>
    <tr class="form-field">
        <th><label><?php esc_html_e( 'Airline Logo', 'gofly-flight-deals' ); ?></label></th>
        <td>
            <div class="gfd-term-image-wrap">
                <input type="hidden" name="gfd_term_image_id" id="gfd_term_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
                <div id="gfd_term_image_preview">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" style="max-height:60px;width:auto;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button gfd-upload-term-image"><?php esc_html_e( 'Upload Logo', 'gofly-flight-deals' ); ?></button>
                <?php if ( $image_id ) : ?>
                    <button type="button" class="button gfd-remove-term-image"><?php esc_html_e( 'Remove', 'gofly-flight-deals' ); ?></button>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
} );

// Save image on term create.
add_action( 'created_flight_deal_airline', function( $term_id ) {
    if ( isset( $_POST['gfd_term_image_id'] ) ) {
        update_term_meta( $term_id, 'gfd_airline_image_id', absint( $_POST['gfd_term_image_id'] ) );
    }
} );

// Save image on term update.
add_action( 'edited_flight_deal_airline', function( $term_id ) {
    if ( isset( $_POST['gfd_term_image_id'] ) ) {
        update_term_meta( $term_id, 'gfd_airline_image_id', absint( $_POST['gfd_term_image_id'] ) );
    }
} );

// Show image column in airlines list.
add_filter( 'manage_edit-flight_deal_airline_columns', function( $columns ) {
    $new = array( 'cb' => $columns['cb'], 'gfd_logo' => __( 'Logo', 'gofly-flight-deals' ) );
    return array_merge( $new, $columns );
} );

add_filter( 'manage_flight_deal_airline_custom_column', function( $content, $column, $term_id ) {
    if ( 'gfd_logo' === $column ) {
        $image_id = get_term_meta( $term_id, 'gfd_airline_image_id', true );
        if ( $image_id ) {
            return wp_get_attachment_image( absint( $image_id ), array( 60, 30 ), false, array( 'style' => 'height:30px;width:auto;object-fit:contain;' ) );
        }
        return '—';
    }
    return $content;
}, 10, 3 );