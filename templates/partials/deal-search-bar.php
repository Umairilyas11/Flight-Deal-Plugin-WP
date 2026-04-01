<?php
/**
 * Partial: Deal Search Bar (used in archive template)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$current_origin      = isset( $_GET['origin'] )      ? sanitize_text_field( wp_unslash( $_GET['origin'] ) )      : '';
$current_destination = isset( $_GET['destination'] ) ? sanitize_text_field( wp_unslash( $_GET['destination'] ) ) : '';
$current_date        = isset( $_GET['date'] )         ? sanitize_text_field( wp_unslash( $_GET['date'] ) )         : '';
$current_airline     = isset( $_GET['airline'] )      ? sanitize_text_field( wp_unslash( $_GET['airline'] ) )      : '';
$current_class       = isset( $_GET['class'] )        ? sanitize_text_field( wp_unslash( $_GET['class'] ) )        : '';

$airlines = get_terms( array( 'taxonomy' => 'flight_deal_airline', 'hide_empty' => true ) );
?>
<div class="gfd-search-bar gfd-search-bar--archive" data-results-url="<?php echo esc_url( get_post_type_archive_link( 'flight_deal' ) ); ?>">
	<div class="gfd-search-bar__fields">
		<div class="gfd-search-bar__field">
			<label><?php esc_html_e( 'From', 'gofly-flight-deals' ); ?></label>
			<input type="text" name="origin" class="gfd-search-bar__input" placeholder="<?php esc_attr_e( 'Origin city or code', 'gofly-flight-deals' ); ?>" value="<?php echo esc_attr( $current_origin ); ?>" />
		</div>
		<div class="gfd-search-bar__field">
			<label><?php esc_html_e( 'To', 'gofly-flight-deals' ); ?></label>
			<input type="text" name="destination" class="gfd-search-bar__input" placeholder="<?php esc_attr_e( 'Destination city or code', 'gofly-flight-deals' ); ?>" value="<?php echo esc_attr( $current_destination ); ?>" />
		</div>
		<div class="gfd-search-bar__field">
			<label><?php esc_html_e( 'Departure', 'gofly-flight-deals' ); ?></label>
			<input type="date" name="date" class="gfd-search-bar__input" value="<?php echo esc_attr( $current_date ); ?>" />
		</div>
		<?php if ( ! empty( $airlines ) && ! is_wp_error( $airlines ) ) : ?>
			<div class="gfd-search-bar__field">
				<label><?php esc_html_e( 'Airline', 'gofly-flight-deals' ); ?></label>
				<select name="airline" class="gfd-search-bar__input">
					<option value=""><?php esc_html_e( 'Any Airline', 'gofly-flight-deals' ); ?></option>
					<?php foreach ( $airlines as $airline ) : ?>
						<option value="<?php echo esc_attr( $airline->slug ); ?>" <?php selected( $current_airline, $airline->slug ); ?>><?php echo esc_html( $airline->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>
		<div class="gfd-search-bar__field">
			<label><?php esc_html_e( 'Class', 'gofly-flight-deals' ); ?></label>
			<select name="class" class="gfd-search-bar__input">
				<option value=""><?php esc_html_e( 'Any Class', 'gofly-flight-deals' ); ?></option>
				<?php foreach ( gofly_flight_deals_travel_classes() as $val => $label ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $current_class, $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<button type="button" class="gfd-search-bar__btn">
		<span class="dashicons dashicons-search"></span>
		<?php esc_html_e( 'Search Deals', 'gofly-flight-deals' ); ?>
	</button>
</div>
