<?php
/**
 * Partial: Deal Inquiry Popup Modal
 * Rendered once in wp_footer when a CF7 form ID is configured.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$cf7_form_id   = gofly_flight_deals_get_option( 'inquiry_cf7_id', 0 );
$popup_title   = gofly_flight_deals_get_option( 'inquiry_popup_title', __( 'Inquire About This Deal', 'gofly-flight-deals' ) );

if ( empty( $cf7_form_id ) || ! function_exists( 'wpcf7_contact_form' ) ) {
	return;
}
?>
<div id="gfd-inquiry-popup" class="gfd-popup" role="dialog" aria-modal="true" aria-labelledby="gfd-popup-title" hidden>
	<div class="gfd-popup__overlay" id="gfd-popup-overlay"></div>
	<div class="gfd-popup__box">

		<div class="gfd-popup__header">
			<div class="gfd-popup__header-content">
				<span class="gfd-popup__icon">&#9992;</span>
				<div>
					<h2 class="gfd-popup__title" id="gfd-popup-title"><?php echo esc_html( $popup_title ); ?></h2>
					<!-- Deal summary injected by JS -->
					<p class="gfd-popup__subtitle" id="gfd-popup-subtitle"></p>
				</div>
			</div>
			<button type="button" class="gfd-popup__close" id="gfd-popup-close" aria-label="<?php esc_attr_e( 'Close popup', 'gofly-flight-deals' ); ?>">
				&#10005;
			</button>
		</div>

		<div class="gfd-popup__body">
			<?php echo do_shortcode( '[contact-form-7 id="' . absint( $cf7_form_id ) . '"]' ); ?>
		</div>

	</div>
</div>
