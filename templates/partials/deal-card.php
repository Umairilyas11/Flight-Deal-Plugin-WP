<?php
/**
 * Partial: Deal Card
 * Available vars: $post (current WP_Post), $settings (Elementor widget settings, if set)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$post_id        = get_the_ID();
$origin_city    = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_city' );
$origin_code    = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_code' );
$dest_city      = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_city' );
$dest_code      = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_code' );
$airline        = gofly_flight_deals_get_meta( $post_id, '_gfd_airline_name' );
$airline_logo   = gofly_flight_deals_get_meta( $post_id, '_gfd_airline_logo' );
$price          = gofly_flight_deals_get_meta( $post_id, '_gfd_price' );
$currency       = gofly_flight_deals_get_meta( $post_id, '_gfd_currency' );
$orig_price     = gofly_flight_deals_get_meta( $post_id, '_gfd_original_price' );
$travel_class   = gofly_flight_deals_get_meta( $post_id, '_gfd_travel_class' );
$stops          = gofly_flight_deals_get_meta( $post_id, '_gfd_stops' );
$departure      = gofly_flight_deals_get_meta( $post_id, '_gfd_departure_date' );
$flexibility    = gofly_flight_deals_get_meta( $post_id, '_gfd_travel_date_flexibility' );
$badge          = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_badge' );
$booking_url    = gofly_flight_deals_get_meta( $post_id, '_gfd_booking_url' );
$is_featured    = gofly_flight_deals_get_meta( $post_id, '_gfd_is_featured' );
$expiry         = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_expiry' );

$book_now_text = gofly_flight_deals_get_option( 'book_now_text', __( 'Book Now', 'gofly-flight-deals' ) );

// Elementor widget settings.
$show_badge          = ! isset( $settings ) || ( isset( $settings['show_badge'] )          && 'yes' === $settings['show_badge'] );
$show_logo           = ! isset( $settings ) || ( isset( $settings['show_airline_logo'] )   && 'yes' === $settings['show_airline_logo'] );
$show_orig_price     = ! isset( $settings ) || ( isset( $settings['show_original_price'] ) && 'yes' === $settings['show_original_price'] );
$show_stops          = ! isset( $settings ) || ( isset( $settings['show_stops'] )          && 'yes' === $settings['show_stops'] );

$classes_map  = gofly_flight_deals_travel_classes();
$stops_map    = gofly_flight_deals_stops_options();
$class_label  = isset( $classes_map[ $travel_class ] ) ? $classes_map[ $travel_class ] : '';
$stops_label  = isset( $stops_map[ $stops ] )          ? $stops_map[ $stops ]          : '';

$card_classes = 'gfd-deal-card';
if ( $is_featured ) { $card_classes .= ' gfd-deal-card--featured'; }

$ga_enabled = gofly_flight_deals_get_option( 'ga_book_now', '0' );
$ga_attrs   = '';
if ( '1' === $ga_enabled ) {
	$ga_attrs = ' data-gfd-ga-event="book_now" data-gfd-deal-id="' . esc_attr( $post_id ) . '" data-gfd-destination="' . esc_attr( $dest_city ) . '"';
}
?>
<article class="<?php echo esc_attr( $card_classes ); ?>" data-expiry="<?php echo esc_attr( $expiry ); ?>">

	<?php if ( $show_badge && $badge ) : ?>
		<span class="gfd-deal-card__badge"><?php echo esc_html( $badge ); ?></span>
	<?php endif; ?>

	<div class="gfd-deal-card__header">
		<?php if ( $show_logo && $airline_logo ) : ?>
			<div class="gfd-deal-card__logo">
				<?php echo wp_get_attachment_image( $airline_logo, array( 80, 40 ), false, array( 'alt' => esc_attr( $airline ) ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $airline ) : ?>
			<span class="gfd-deal-card__airline"><?php echo esc_html( $airline ); ?></span>
		<?php endif; ?>
	</div>

	<div class="gfd-deal-card__route">
		<span class="gfd-deal-card__origin">
			<strong><?php echo esc_html( $origin_code ?: $origin_city ); ?></strong>
			<?php if ( $origin_code && $origin_city ) : ?><small><?php echo esc_html( $origin_city ); ?></small><?php endif; ?>
		</span>
		<span class="gfd-deal-card__arrow">&#9992;</span>
		<span class="gfd-deal-card__dest">
			<strong><?php echo esc_html( $dest_code ?: $dest_city ); ?></strong>
			<?php if ( $dest_code && $dest_city ) : ?><small><?php echo esc_html( $dest_city ); ?></small><?php endif; ?>
		</span>
	</div>

	<div class="gfd-deal-card__meta">
		<?php if ( $class_label ) : ?>
			<span class="gfd-deal-card__tag gfd-deal-card__tag--class"><?php echo esc_html( $class_label ); ?></span>
		<?php endif; ?>
		<?php if ( $show_stops && $stops_label ) : ?>
			<span class="gfd-deal-card__tag gfd-deal-card__tag--stops"><?php echo esc_html( $stops_label ); ?></span>
		<?php endif; ?>
		<?php if ( $departure ) : ?>
			<span class="gfd-deal-card__tag gfd-deal-card__tag--date">
				<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $departure ) ) ); ?>
			</span>
		<?php endif; ?>
		<?php if ( $flexibility ) : ?>
			<span class="gfd-deal-card__tag gfd-deal-card__tag--flex"><?php echo esc_html( $flexibility ); ?></span>
		<?php endif; ?>
	</div>

	<div class="gfd-deal-card__pricing">
		<div class="gfd-deal-card__price">
			<?php echo esc_html( gofly_flight_deals_format_price( $price, $currency ) ); ?>
		</div>
		<?php if ( $show_orig_price && $orig_price && $orig_price > $price ) : ?>
			<div class="gfd-deal-card__original-price">
				<?php echo esc_html( gofly_flight_deals_format_price( $orig_price, $currency ) ); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $expiry ) : ?>
		<div class="gfd-deal-card__expiry" data-expiry="<?php echo esc_attr( $expiry ); ?>">
			<span class="gfd-deal-card__expiry-label"><?php esc_html_e( 'Expires in:', 'gofly-flight-deals' ); ?></span>
			<span class="gfd-deal-card__countdown"></span>
		</div>
	<?php endif; ?>

	<div class="gfd-deal-card__footer">
		<?php if ( $booking_url ) : ?>
			<a href="<?php echo esc_url( $booking_url ); ?>"
			   class="gfd-deal-card__btn"
			   target="_blank"
			   rel="noopener noreferrer"
			   <?php echo $ga_attrs; // already escaped above ?>>
				<?php echo esc_html( $book_now_text ); ?>
			</a>
		<?php else : ?>
			<a href="<?php the_permalink(); ?>" class="gfd-deal-card__btn gfd-deal-card__btn--secondary">
				<?php esc_html_e( 'View Deal', 'gofly-flight-deals' ); ?>
			</a>
		<?php endif; ?>
	</div>

</article>
