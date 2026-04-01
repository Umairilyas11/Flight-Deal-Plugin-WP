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
		$sanitized['default_currency']   = isset( $input['default_currency'] )   ? sanitize_text_field( $input['default_currency'] )   : 'USD';
		$sanitized['currency_position']  = isset( $input['currency_position'] )  ? sanitize_text_field( $input['currency_position'] )  : 'before';
		$sanitized['book_now_text']       = isset( $input['book_now_text'] )       ? sanitize_text_field( $input['book_now_text'] )       : 'Book Now';
		$sanitized['hide_expired_global'] = isset( $input['hide_expired_global'] ) ? '1' : '0';
		$sanitized['archive_slug']        = isset( $input['archive_slug'] )        ? sanitize_title( $input['archive_slug'] )            : 'flight-deals';
		$sanitized['ga_book_now']         = isset( $input['ga_book_now'] )         ? '1' : '0';
		// Flush rewrite on slug change.
		$old = get_option( 'gfd_settings', array() );
		if ( isset( $old['archive_slug'] ) && $old['archive_slug'] !== $sanitized['archive_slug'] ) {
			set_transient( 'gfd_flush_rewrite', true, 60 );
		}
		return $sanitized;
	}

	public static function render_page() {
		$opts = get_option( 'gfd_settings', array() );
		?>
		<div class="wrap gfd-settings-wrap">
			<h1><?php esc_html_e( 'GoFly Flight Deals — Settings', 'gofly-flight-deals' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'gfd_settings_group' ); ?>
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
					<tr>
						<th scope="row"><label for="gfd_book_now_text"><?php esc_html_e( '"Book Now" Button Text', 'gofly-flight-deals' ); ?></label></th>
						<td><input type="text" name="gfd_settings[book_now_text]" id="gfd_book_now_text" value="<?php echo esc_attr( $opts['book_now_text'] ?? 'Book Now' ); ?>" class="regular-text" /></td>
					</tr>
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
						<th scope="row"><?php esc_html_e( 'GA4 Event on "Book Now"', 'gofly-flight-deals' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="gfd_settings[ga_book_now]" value="1" <?php checked( $opts['ga_book_now'] ?? '0', '1' ); ?> />
								<?php esc_html_e( 'Push a Google Analytics 4 event when users click Book Now', 'gofly-flight-deals' ); ?>
							</label>
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

// Flush rewrite if slug changed.
add_action( 'admin_init', function() {
	if ( get_transient( 'gfd_flush_rewrite' ) ) {
		flush_rewrite_rules();
		delete_transient( 'gfd_flush_rewrite' );
	}
} );
