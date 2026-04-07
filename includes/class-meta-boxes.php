<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class GFD_Meta_Boxes {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_flight_deal', array( __CLASS__, 'save_meta' ), 10, 2 );
	}

	public static function add_meta_boxes() {
		add_meta_box(
			'gfd_flight_deal_details',
			__( 'Flight Deal Details', 'gofly-flight-deals' ),
			array( __CLASS__, 'render_meta_box' ),
			'flight_deal',
			'normal',
			'high'
		);
	}

	public static function render_meta_box( $post ) {
		wp_nonce_field( 'gfd_save_meta', 'gfd_meta_nonce' );
		$m = function( $key, $default = '' ) use ( $post ) {
			return esc_attr( gofly_flight_deals_get_meta( $post->ID, $key, $default ) );
		};
		$currencies      = gofly_flight_deals_currencies();
		$classes         = gofly_flight_deals_travel_classes();
		$stops           = gofly_flight_deals_stops_options();
		$airline_logo_id  = gofly_flight_deals_get_meta( $post->ID, '_gfd_airline_logo' );
		$airline_logo_url = $airline_logo_id ? wp_get_attachment_image_url( absint( $airline_logo_id ), 'thumbnail' ) : '';
		$is_featured      = gofly_flight_deals_get_meta( $post->ID, '_gfd_is_featured' );
		?>
		<div class="gfd-meta-box-wrap">
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Origin City', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_origin_city" value="<?php echo $m('_gfd_origin_city'); ?>" placeholder="e.g. Colombo" />
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Origin IATA Code', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_origin_code" value="<?php echo $m('_gfd_origin_code'); ?>" maxlength="3" placeholder="CMB" class="gfd-iata" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Destination City', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_destination_city" value="<?php echo $m('_gfd_destination_city'); ?>" placeholder="e.g. Dubai" />
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Destination IATA Code', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_destination_code" value="<?php echo $m('_gfd_destination_code'); ?>" maxlength="3" placeholder="DXB" class="gfd-iata" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
    				<label><?php esc_html_e( 'Airline', 'gofly-flight-deals' ); ?></label>
    					<?php
    						$selected_terms = wp_get_post_terms( $post->ID, 'flight_deal_airline', array( 'fields' => 'ids' ) );
    						$selected_term  = ! empty( $selected_terms ) && ! is_wp_error( $selected_terms ) ? $selected_terms[0] : 0;
    						$airline_terms  = get_terms( array( 'taxonomy' => 'flight_deal_airline', 'hide_empty' => false ) );
    					?>
   	 				<select name="gfd_airline_term_id">
						<option value="0"><?php esc_html_e( '— Select Airline —', 'gofly-flight-deals' ); ?></option>
        					<?php foreach ( $airline_terms as $term ) : ?>
            			<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_term, $term->term_id ); ?>>
                			<?php echo esc_html( $term->name ); ?>
            			</option>
						<?php endforeach; ?>
    				</select>
    					<p class="description"><?php esc_html_e( 'Manage airlines under Flight Deals → Airlines.', 'gofly-flight-deals' ); ?></p>
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--third">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Deal Price', 'gofly-flight-deals' ); ?></label>
					<input type="number" name="_gfd_price" value="<?php echo $m('_gfd_price'); ?>" min="0" step="0.01" placeholder="299" />
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Currency', 'gofly-flight-deals' ); ?></label>
					<select name="_gfd_currency">
						<?php foreach ( $currencies as $code => $label ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $m('_gfd_currency'), $code ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Original Price (strikethrough)', 'gofly-flight-deals' ); ?></label>
					<input type="number" name="_gfd_original_price" value="<?php echo $m('_gfd_original_price'); ?>" min="0" step="0.01" placeholder="Optional" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--third">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Travel Class', 'gofly-flight-deals' ); ?></label>
					<select name="_gfd_travel_class">
						<?php foreach ( $classes as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m('_gfd_travel_class'), $val ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Stops', 'gofly-flight-deals' ); ?></label>
					<select name="_gfd_stops">
						<?php foreach ( $stops as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m('_gfd_stops'), $val ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Duration (Nights)', 'gofly-flight-deals' ); ?></label>
					<input type="number" name="_gfd_duration_nights" value="<?php echo $m('_gfd_duration_nights'); ?>" min="0" placeholder="e.g. 7" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Departure Date', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_departure_date" value="<?php echo $m('_gfd_departure_date'); ?>" class="gfd-datepicker" placeholder="YYYY-MM-DD" />
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Return Date (optional)', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_return_date" value="<?php echo $m('_gfd_return_date'); ?>" class="gfd-datepicker" placeholder="YYYY-MM-DD" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Date Flexibility Note', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_travel_date_flexibility" value="<?php echo $m('_gfd_travel_date_flexibility'); ?>" placeholder="e.g. Valid Oct–Dec" />
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Deal Badge Text', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_deal_badge" value="<?php echo $m('_gfd_deal_badge'); ?>" placeholder="e.g. Hot Deal" />
				</div>
			</div>
			<div class="gfd-meta-row gfd-meta-row--half">
				<div class="gfd-field">
					<label><?php esc_html_e( 'Deal Expiry Date', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="_gfd_deal_expiry" value="<?php echo $m('_gfd_deal_expiry'); ?>" class="gfd-datepicker" placeholder="YYYY-MM-DD" />
					<p class="description"><?php esc_html_e( 'Deal will be hidden after this date.', 'gofly-flight-deals' ); ?></p>
				</div>
				<div class="gfd-field">
					<label><?php esc_html_e( 'Booking / Affiliate URL', 'gofly-flight-deals' ); ?></label>
					<input type="url" name="_gfd_booking_url" value="<?php echo esc_url( gofly_flight_deals_get_meta( $post->ID, '_gfd_booking_url' ) ); ?>" placeholder="https://" />
				</div>
			</div>
			<div class="gfd-meta-row">
				<div class="gfd-field gfd-field--checkbox">
					<label>
						<input type="checkbox" name="_gfd_is_featured" value="1" <?php checked( $is_featured, '1' ); ?> />
						<?php esc_html_e( 'Featured Deal — highlight on frontend', 'gofly-flight-deals' ); ?>
					</label>
				</div>
			</div>
		</div>
		<?php
	}

	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['gfd_meta_nonce'] ) ) { return; }
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gfd_meta_nonce'] ) ), 'gfd_save_meta' ) ) { return; }
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

		$text_fields = array(
			'_gfd_origin_city', '_gfd_origin_code', '_gfd_destination_city', '_gfd_destination_code',
			 '_gfd_travel_class', '_gfd_stops', '_gfd_travel_date_flexibility',
			'_gfd_deal_badge', '_gfd_currency', '_gfd_departure_date', '_gfd_return_date', '_gfd_deal_expiry',
		);
		foreach ( $text_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}

		// absint for numeric / attachment ID fields.
		$absint_fields = array( '_gfd_price', '_gfd_original_price', '_gfd_duration_nights' );
		foreach ( $absint_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				// Price fields can be decimals — store as sanitized float string.
				if ( in_array( $field, array( '_gfd_price', '_gfd_original_price' ), true ) ) {
					update_post_meta( $post_id, $field, floatval( $_POST[ $field ] ) );
				} else {
					update_post_meta( $post_id, $field, absint( $_POST[ $field ] ) );
				}
			}
		}

		// Use esc_url_raw() instead of sanitize_url() for WP < 5.9 compatibility.
		if ( isset( $_POST['_gfd_booking_url'] ) ) {
			update_post_meta( $post_id, '_gfd_booking_url', esc_url_raw( wp_unslash( $_POST['_gfd_booking_url'] ) ) );
		}

		$featured = isset( $_POST['_gfd_is_featured'] ) ? '1' : '0';
		update_post_meta( $post_id, '_gfd_is_featured', $featured );
		// Save airline taxonomy selection.
		if ( isset( $_POST['gfd_airline_term_id'] ) ) {
    		$term_id = absint( $_POST['gfd_airline_term_id'] );
    	if ( $term_id ) {
        	wp_set_post_terms( $post_id, array( $term_id ), 'flight_deal_airline' );
        	// Also save the name and image ID as meta for easy retrieval in templates.
       		 $term = get_term( $term_id, 'flight_deal_airline' );
        if ( $term && ! is_wp_error( $term ) ) {
            update_post_meta( $post_id, '_gfd_airline_name', $term->name );
            $image_id = get_term_meta( $term_id, 'gfd_airline_image_id', true );
            update_post_meta( $post_id, '_gfd_airline_logo', $image_id );
        }
    		} else {
       			wp_set_post_terms( $post_id, array(), 'flight_deal_airline' );
    		}
		}
	}
}

GFD_Meta_Boxes::init();
