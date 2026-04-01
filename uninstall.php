<?php
/**
 * Uninstall: remove all plugin data.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

global $wpdb;

// Delete all flight_deal posts and their meta.
$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'flight_deal'" );
foreach ( $post_ids as $post_id ) {
	$wpdb->delete( $wpdb->postmeta, array( 'post_id' => absint( $post_id ) ) );
	$wpdb->delete( $wpdb->posts,    array( 'ID'      => absint( $post_id ) ) );
}

// Delete taxonomies.
$taxonomies = array( 'flight_deal_airline', 'flight_deal_destination', 'flight_deal_type' );
foreach ( $taxonomies as $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, $taxonomy );
		}
	}
}

// Delete plugin options.
delete_option( 'gfd_settings' );
delete_transient( 'gfd_flush_rewrite' );

// Flush rewrite rules.
flush_rewrite_rules();
