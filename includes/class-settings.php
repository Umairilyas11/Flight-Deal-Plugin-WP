<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class GFD_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=flight_deal',
			__( 'Flight Deals Settings', 'gofly-flight-deals' ),
			__( 'Settings', 'gofly-flight-deals' ),
			'manage_options',
			'gfd-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings() {
		register_setting( 'gfd_settings_group', 'gfd_settings', array( __CLASS__, 'sanitize_settings' ) );
	}

	public static function sanitize_settings( $input ) {
		$sanitized = array();
		$sanitized['default_currency']    = isset( $input['default_currency'] )    ? sanitize_text_field( $input['default_currency'] )    : 'USD';
		$sanitized['currency_position']   = isset( $input['currency_position'] )   ? sanitize_text_field( $input['currency_position'] )   : 'before';
		$sanitized['book_now_text']        = isset( $input['book_now_text'] )        ? sanitize_text_field( $input['book_now_text'] )        : 'Inquire Now';
		$sanitized['default_booking_url']  = isset( $input['default_booking_url'] )  ? esc_url_raw( $input['default_booking_url'] )          : '';
		$sanitized['hide_expired_global']  = isset( $input['hide_expired_global'] )  ? '1' : '0';
		$sanitized['archive_slug']         = isset( $input['archive_slug'] )         ? sanitize_title( $input['archive_slug'] )             : 'flight-deals';
		$sanitized['ga_book_now']          = isset( $input['ga_book_now'] )          ? '1' : '0';
		$sanitized['inquiry_cf7_id']       = isset( $input['inquiry_cf7_id'] )       ? absint( $input['inquiry_cf7_id'] )                   : 0;
		$sanitized['inquiry_popup_title']  = isset( $input['inquiry_popup_title'] )  ? sanitize_text_field( $input['inquiry_popup_title'] ) : 'Inquire About This Deal';
		$sanitized['color_primary']      = isset( $input['color_primary'] )      ? sanitize_hex_color( $input['color_primary'] )      : '#0057b8';
$sanitized['color_primary_dark'] = isset( $input['color_primary_dark'] ) ? sanitize_hex_color( $input['color_primary_dark'] ) : '#004499';
$sanitized['color_accent']       = isset( $input['color_accent'] )       ? sanitize_hex_color( $input['color_accent'] )       : '#ff6b00';
		$old = get_option( 'gfd_settings', array() );
		if ( isset( $old['archive_slug'] ) && $old['archive_slug'] !== $sanitized['archive_slug'] ) {
			set_transient( 'gfd_flush_rewrite', true, 60 );
		}
		return $sanitized;
	}

	public static function render_page() {
		$opts = get_option( 'gfd_settings', array() );

		// Get all CF7 forms for the selector.
		$cf7_forms = array();
		if ( post_type_exists( 'wpcf7_contact_form' ) ) {
			$forms = get_posts( array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			) );
			foreach ( $forms as $form ) {
				$cf7_forms[ $form->ID ] = $form->post_title;
			}
		}
		?>
		<div class="wrap gfd-settings-wrap">
			<h1><?php esc_html_e( 'GoFly Flight Deals — Settings', 'gofly-flight-deals' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'gfd_settings_group' ); ?>

				<h2 class="title"><?php esc_html_e( 'Currency', 'gofly-flight-deals' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="gfd_default_currency"><?php esc_html_e( 'Default Currency', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<select name="gfd_settings[default_currency]" id="gfd_default_currency">
								<?php foreach ( gofly_flight_deals_currencies() as $code => $label ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $opts['default_currency'] ?? 'USD', $code ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="gfd_currency_position"><?php esc_html_e( 'Currency Symbol Position', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<select name="gfd_settings[currency_position]" id="gfd_currency_position">
								<option value="before" <?php selected( $opts['currency_position'] ?? 'before', 'before' ); ?>><?php esc_html_e( 'Before price ($299)', 'gofly-flight-deals' ); ?></option>
								<option value="after"  <?php selected( $opts['currency_position'] ?? 'before', 'after' ); ?>><?php esc_html_e( 'After price (299 USD)', 'gofly-flight-deals' ); ?></option>
							</select>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Booking', 'gofly-flight-deals' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="gfd_book_now_text"><?php esc_html_e( 'CTA Button Text', 'gofly-flight-deals' ); ?></label></th>
						<td><input type="text" name="gfd_settings[book_now_text]" id="gfd_book_now_text" value="<?php echo esc_attr( $opts['book_now_text'] ?? 'Inquire Now' ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="gfd_default_booking_url"><?php esc_html_e( 'Default Booking URL', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<input type="url" name="gfd_settings[default_booking_url]" id="gfd_default_booking_url" value="<?php echo esc_url( $opts['default_booking_url'] ?? '' ); ?>" class="regular-text" placeholder="https://" />
							<p class="description"><?php esc_html_e( 'Used on any deal that does not have its own Booking URL set.', 'gofly-flight-deals' ); ?></p>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Inquiry Popup (Contact Form 7)', 'gofly-flight-deals' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="gfd_inquiry_cf7_id"><?php esc_html_e( 'CF7 Inquiry Form', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<?php if ( empty( $cf7_forms ) ) : ?>
								<p class="description" style="color:#d63638;">
									<?php esc_html_e( 'Contact Form 7 is not active or no forms found. Please install CF7 and create a form first.', 'gofly-flight-deals' ); ?>
								</p>
							<?php else : ?>
								<select name="gfd_settings[inquiry_cf7_id]" id="gfd_inquiry_cf7_id">
									<option value="0"><?php esc_html_e( '— Select a form —', 'gofly-flight-deals' ); ?></option>
									<?php foreach ( $cf7_forms as $id => $title ) : ?>
										<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $opts['inquiry_cf7_id'] ?? 0, $id ); ?>><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description">
									<?php esc_html_e( 'Select the CF7 form to show in the inquiry popup. The form must include these hidden fields:', 'gofly-flight-deals' ); ?>
									<br><code>[hidden origin-city]</code>
									<code>[hidden destination-city]</code>
									<code>[hidden deal-price]</code>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="gfd_inquiry_popup_title"><?php esc_html_e( 'Popup Title', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<input type="text" name="gfd_settings[inquiry_popup_title]" id="gfd_inquiry_popup_title" value="<?php echo esc_attr( $opts['inquiry_popup_title'] ?? 'Inquire About This Deal' ); ?>" class="regular-text" />
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'General', 'gofly-flight-deals' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Hide Expired Deals Globally', 'gofly-flight-deals' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="gfd_settings[hide_expired_global]" value="1" <?php checked( $opts['hide_expired_global'] ?? '1', '1' ); ?> />
								<?php esc_html_e( 'Automatically hide deals past their expiry date', 'gofly-flight-deals' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="gfd_archive_slug"><?php esc_html_e( 'Deals Archive Slug', 'gofly-flight-deals' ); ?></label></th>
						<td>
							<input type="text" name="gfd_settings[archive_slug]" id="gfd_archive_slug" value="<?php echo esc_attr( $opts['archive_slug'] ?? 'flight-deals' ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'After changing, go to Settings → Permalinks and save to flush rewrite rules.', 'gofly-flight-deals' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'GA4 Event on Button Click', 'gofly-flight-deals' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="gfd_settings[ga_book_now]" value="1" <?php checked( $opts['ga_book_now'] ?? '0', '1' ); ?> />
								<?php esc_html_e( 'Push a Google Analytics 4 event when users click the CTA button', 'gofly-flight-deals' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<h2 class="title"><?php esc_html_e( 'Colors', 'gofly-flight-deals' ); ?></h2>
<table class="form-table" role="presentation">
    <tr>
        <th scope="row"><label for="gfd_color_primary"><?php esc_html_e( 'Primary Color', 'gofly-flight-deals' ); ?></label></th>
        <td>
            <input type="color" name="gfd_settings[color_primary]" id="gfd_color_primary" value="<?php echo esc_attr( $opts['color_primary'] ?? '#0057b8' ); ?>" />
            <p class="description"><?php esc_html_e( 'Buttons, price text, links, popup header.', 'gofly-flight-deals' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gfd_color_primary_dark"><?php esc_html_e( 'Primary Hover Color', 'gofly-flight-deals' ); ?></label></th>
        <td>
            <input type="color" name="gfd_settings[color_primary_dark]" id="gfd_color_primary_dark" value="<?php echo esc_attr( $opts['color_primary_dark'] ?? '#004499' ); ?>" />
            <p class="description"><?php esc_html_e( 'Hover state of buttons.', 'gofly-flight-deals' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gfd_color_accent"><?php esc_html_e( 'Accent Color', 'gofly-flight-deals' ); ?></label></th>
        <td>
            <input type="color" name="gfd_settings[color_accent]" id="gfd_color_accent" value="<?php echo esc_attr( $opts['color_accent'] ?? '#ff6b00' ); ?>" />
            <p class="description"><?php esc_html_e( 'Deal badges, expiry countdown, featured card border.', 'gofly-flight-deals' ); ?></p>
        </td>
    </tr>
</table>							
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

GFD_Settings::init();

add_action( 'admin_init', function() {
	if ( get_transient( 'gfd_flush_rewrite' ) ) {
		flush_rewrite_rules();
		delete_transient( 'gfd_flush_rewrite' );
	}
} );
