<?php
/**
 * Template: Single Flight Deal
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

while ( have_posts() ) :
	the_post();
	$post_id      = get_the_ID();
	$origin_city  = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_city' );
	$origin_code  = gofly_flight_deals_get_meta( $post_id, '_gfd_origin_code' );
	$dest_city    = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_city' );
	$dest_code    = gofly_flight_deals_get_meta( $post_id, '_gfd_destination_code' );
	$airline      = gofly_flight_deals_get_meta( $post_id, '_gfd_airline_name' );
	$airline_logo = gofly_flight_deals_get_meta( $post_id, '_gfd_airline_logo' );
	$price        = gofly_flight_deals_get_meta( $post_id, '_gfd_price' );
	$currency     = gofly_flight_deals_get_meta( $post_id, '_gfd_currency' );
	$orig_price   = gofly_flight_deals_get_meta( $post_id, '_gfd_original_price' );
	$travel_class = gofly_flight_deals_get_meta( $post_id, '_gfd_travel_class' );
	$stops        = gofly_flight_deals_get_meta( $post_id, '_gfd_stops' );
	$departure    = gofly_flight_deals_get_meta( $post_id, '_gfd_departure_date' );
	$return_date  = gofly_flight_deals_get_meta( $post_id, '_gfd_return_date' );
	$flexibility  = gofly_flight_deals_get_meta( $post_id, '_gfd_travel_date_flexibility' );
	$duration     = gofly_flight_deals_get_meta( $post_id, '_gfd_duration_nights' );
	$badge        = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_badge' );
	$booking_url = gofly_flight_deals_get_meta( $post_id, '_gfd_booking_url' );
if ( empty( $booking_url ) ) {
    $booking_url = gofly_flight_deals_get_option( 'default_booking_url', '' );
}
	$expiry       = gofly_flight_deals_get_meta( $post_id, '_gfd_deal_expiry' );

	$classes_map  = gofly_flight_deals_travel_classes();
	$stops_map    = gofly_flight_deals_stops_options();
	$class_label  = isset( $classes_map[ $travel_class ] ) ? $classes_map[ $travel_class ] : '';
	$stops_label  = isset( $stops_map[ $stops ] ) ? $stops_map[ $stops ] : '';
	$book_now_text = gofly_flight_deals_get_option( 'book_now_text', __( 'Book Now', 'gofly-flight-deals' ) );
	$ga_enabled   = gofly_flight_deals_get_option( 'ga_book_now', '0' );
?>
<div class="gfd-single-wrap">

	<!-- HERO -->
	<div class="gfd-single-hero">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="gfd-single-hero__bg"><?php the_post_thumbnail( 'full' ); ?></div>
		<?php endif; ?>
		<div class="gfd-single-hero__content">
			<?php if ( $badge ) : ?>
				<span class="gfd-deal-card__badge"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<h1 class="gfd-single-hero__route">
				<span class="gfd-single-hero__city">
					<?php echo esc_html( $origin_city ); ?>
					<?php if ( $origin_code ) : ?><span class="gfd-single-hero__code"><?php echo esc_html( $origin_code ); ?></span><?php endif; ?>
				</span>
				<span class="gfd-single-hero__arrow">&#9992;</span>
				<span class="gfd-single-hero__city">
					<?php echo esc_html( $dest_city ); ?>
					<?php if ( $dest_code ) : ?><span class="gfd-single-hero__code"><?php echo esc_html( $dest_code ); ?></span><?php endif; ?>
				</span>
			</h1>
		</div>
	</div>

	<!-- MAIN CONTENT -->
	<div class="gfd-single-main">
		<div class="gfd-single-details">

			<!-- AIRLINE -->
			<div class="gfd-single-airline">
				<?php if ( $airline_logo ) : ?>
					<?php echo wp_get_attachment_image( $airline_logo, array( 120, 60 ), false, array( 'alt' => esc_attr( $airline ) ) ); ?>
				<?php endif; ?>
				<span><?php echo esc_html( $airline ); ?></span>
			</div>

			<!-- PRICING -->
			<div class="gfd-single-pricing">
				<div class="gfd-single-pricing__price">
					<?php echo esc_html( gofly_flight_deals_format_price( $price, $currency ) ); ?>
				</div>
				<?php if ( $orig_price && $orig_price > $price ) : ?>
					<div class="gfd-single-pricing__orig">
						<?php echo esc_html( gofly_flight_deals_format_price( $orig_price, $currency ) ); ?>
					</div>
					<div class="gfd-single-pricing__save">
						<?php
						$saving = $orig_price - $price;
						printf( esc_html__( 'Save %s!', 'gofly-flight-deals' ), esc_html( gofly_flight_deals_format_price( $saving, $currency ) ) );
						?>
					</div>
				<?php endif; ?>
			</div>

			<!-- TAGS -->
			<div class="gfd-single-tags">
				<?php if ( $class_label ) : ?>
					<span class="gfd-deal-card__tag gfd-deal-card__tag--class"><?php echo esc_html( $class_label ); ?></span>
				<?php endif; ?>
				<?php if ( $stops_label ) : ?>
					<span class="gfd-deal-card__tag gfd-deal-card__tag--stops"><?php echo esc_html( $stops_label ); ?></span>
				<?php endif; ?>
				<?php if ( $duration ) : ?>
					<span class="gfd-deal-card__tag gfd-deal-card__tag--nights"><?php printf( esc_html__( '%d Nights', 'gofly-flight-deals' ), absint( $duration ) ); ?></span>
				<?php endif; ?>
			</div>

			<!-- DATES -->
			<div class="gfd-single-dates">
				<?php if ( $departure ) : ?>
					<div class="gfd-single-dates__item">
						<strong><?php esc_html_e( 'Departure:', 'gofly-flight-deals' ); ?></strong>
						<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $departure ) ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $return_date ) : ?>
					<div class="gfd-single-dates__item">
						<strong><?php esc_html_e( 'Return:', 'gofly-flight-deals' ); ?></strong>
						<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $return_date ) ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $flexibility ) : ?>
					<div class="gfd-single-dates__item gfd-single-dates__item--flex">
						<span class="dashicons dashicons-calendar-alt"></span> <?php echo esc_html( $flexibility ); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- EXPIRY COUNTDOWN -->
			<?php if ( $expiry ) : ?>
				<div class="gfd-single-expiry" data-expiry="<?php echo esc_attr( $expiry ); ?>">
					<strong><?php esc_html_e( 'Deal expires in:', 'gofly-flight-deals' ); ?></strong>
					<span class="gfd-deal-card__countdown gfd-single-expiry__countdown"></span>
				</div>
			<?php endif; ?>

			<!-- DESCRIPTION -->
			<?php if ( get_the_content() ) : ?>
				<div class="gfd-single-description">
					<h3><?php esc_html_e( 'About This Deal', 'gofly-flight-deals' ); ?></h3>
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<!-- CTA -->
			<?php if ( $booking_url ) : ?>
				<div class="gfd-single-cta">
					<a href="<?php echo esc_url( $booking_url ); ?>"
					   class="gfd-deal-card__btn gfd-deal-card__btn--large"
					   target="_blank"
					   rel="noopener noreferrer"
					   <?php echo '1' === $ga_enabled ? 'data-gfd-ga-event="book_now" data-gfd-deal-id="' . esc_attr( $post_id ) . '"' : ''; ?>>
						<?php echo esc_html( $book_now_text ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- RELATED DEALS -->
	<?php
	$destination_terms = wp_get_post_terms( $post_id, 'flight_deal_destination', array( 'fields' => 'ids' ) );
	if ( ! empty( $destination_terms ) && ! is_wp_error( $destination_terms ) ) :
		$related_args = array(
			'post_type'      => 'flight_deal',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'tax_query'      => array( array(
				'taxonomy' => 'flight_deal_destination',
				'field'    => 'term_id',
				'terms'    => $destination_terms,
			) ),
			'meta_query'     => array( gofly_flight_deals_expiry_meta_query() ),
		);
		$related_query = new WP_Query( $related_args );
		if ( $related_query->have_posts() ) :
			?>
			<div class="gfd-related-deals">
				<h3><?php esc_html_e( 'Related Deals', 'gofly-flight-deals' ); ?></h3>
				<div class="gfd-deals-grid gfd-deals-grid--cols-3">
					<?php while ( $related_query->have_posts() ) { $related_query->the_post(); include gofly_flight_deals_locate_template( 'partials/deal-card.php' ); } ?>
				</div>
			</div>
			<?php
			wp_reset_postdata();
		endif;
	endif;
	?>
</div>
<?php
endwhile;
get_footer();
?>
