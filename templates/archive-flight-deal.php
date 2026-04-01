<?php
/**
 * Template: Flight Deals Archive
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Read URL filter params.
$filter_origin      = isset( $_GET['origin'] )      ? sanitize_text_field( wp_unslash( $_GET['origin'] ) )      : '';
$filter_destination = isset( $_GET['destination'] ) ? sanitize_text_field( wp_unslash( $_GET['destination'] ) ) : '';
$filter_date        = isset( $_GET['date'] )         ? sanitize_text_field( wp_unslash( $_GET['date'] ) )         : '';
$filter_airline     = isset( $_GET['airline'] )      ? sanitize_text_field( wp_unslash( $_GET['airline'] ) )      : '';
$filter_class       = isset( $_GET['class'] )        ? sanitize_text_field( wp_unslash( $_GET['class'] ) )        : '';

// Build custom query with filters.
$args = array(
	'post_type'      => 'flight_deal',
	'post_status'    => 'publish',
	'posts_per_page' => get_option( 'posts_per_page', 12 ),
	'paged'          => max( 1, get_query_var( 'paged' ) ),
	'meta_query'     => array( 'relation' => 'AND' ),
	'tax_query'      => array( 'relation' => 'AND' ),
);

// Origin / destination / class text filters via meta query.
if ( $filter_origin ) {
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array( 'key' => '_gfd_origin_city', 'value' => $filter_origin, 'compare' => 'LIKE' ),
		array( 'key' => '_gfd_origin_code', 'value' => strtoupper( $filter_origin ), 'compare' => 'LIKE' ),
	);
}
if ( $filter_destination ) {
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array( 'key' => '_gfd_destination_city', 'value' => $filter_destination, 'compare' => 'LIKE' ),
		array( 'key' => '_gfd_destination_code', 'value' => strtoupper( $filter_destination ), 'compare' => 'LIKE' ),
	);
}
if ( $filter_class ) {
	$args['meta_query'][] = array( 'key' => '_gfd_travel_class', 'value' => $filter_class, 'compare' => '=' );
}
if ( $filter_date ) {
	$args['meta_query'][] = array( 'key' => '_gfd_departure_date', 'value' => $filter_date, 'compare' => '=', 'type' => 'DATE' );
}
if ( $filter_airline ) {
	$args['tax_query'][] = array( 'taxonomy' => 'flight_deal_airline', 'field' => 'slug', 'terms' => $filter_airline );
}

// Expiry filter.
if ( gofly_flight_deals_get_option( 'hide_expired_global', '1' ) ) {
	$args['meta_query'][] = gofly_flight_deals_expiry_meta_query();
}

$deals_query = new WP_Query( $args );
?>
<div class="gfd-archive-wrap">
	<header class="gfd-archive-header">
		<h1 class="gfd-archive-title"><?php esc_html_e( 'Flight Deals', 'gofly-flight-deals' ); ?></h1>
		<?php if ( $filter_origin || $filter_destination ) : ?>
			<p class="gfd-archive-subtitle">
				<?php
				if ( $filter_origin && $filter_destination ) {
					printf( esc_html__( 'Showing flights from %s to %s', 'gofly-flight-deals' ), '<strong>' . esc_html( strtoupper( $filter_origin ) ) . '</strong>', '<strong>' . esc_html( strtoupper( $filter_destination ) ) . '</strong>' );
				} elseif ( $filter_origin ) {
					printf( esc_html__( 'Showing flights from %s', 'gofly-flight-deals' ), '<strong>' . esc_html( strtoupper( $filter_origin ) ) . '</strong>' );
				} elseif ( $filter_destination ) {
					printf( esc_html__( 'Showing flights to %s', 'gofly-flight-deals' ), '<strong>' . esc_html( strtoupper( $filter_destination ) ) . '</strong>' );
				}
				?>
			</p>
		<?php endif; ?>
	</header>

	<?php include gofly_flight_deals_locate_template( 'partials/deal-search-bar.php' ); ?>

	<?php if ( $deals_query->have_posts() ) : ?>
		<div class="gfd-archive-results">
			<p class="gfd-archive-count">
				<?php printf( esc_html__( '%d deals found', 'gofly-flight-deals' ), $deals_query->found_posts ); ?>
			</p>
			<div class="gfd-deals-grid gfd-deals-grid--cols-3">
				<?php while ( $deals_query->have_posts() ) { $deals_query->the_post(); include gofly_flight_deals_locate_template( 'partials/deal-card.php' ); } ?>
			</div>
			<?php wp_reset_postdata(); ?>

			<!-- PAGINATION -->
			<?php
			$big = 999999999;
			echo paginate_links( array(
				'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'  => '?paged=%#%',
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total'   => $deals_query->max_num_pages,
				'prev_text' => '&laquo; ' . __( 'Prev', 'gofly-flight-deals' ),
				'next_text' => __( 'Next', 'gofly-flight-deals' ) . ' &raquo;',
			) );
			?>
		</div>
	<?php else : ?>
		<div class="gfd-no-results">
			<p><?php esc_html_e( 'No flight deals found matching your search. Try adjusting your filters.', 'gofly-flight-deals' ); ?></p>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'flight_deal' ) ); ?>" class="gfd-deal-card__btn">
				<?php esc_html_e( 'View All Deals', 'gofly-flight-deals' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
