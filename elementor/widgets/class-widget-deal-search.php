<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class GFD_Widget_Deal_Search extends Widget_Base {

	public function get_name()  { return 'gfd-deal-search'; }
	public function get_title() { return __( 'Flight Deals Search Bar', 'gofly-flight-deals' ); }
	public function get_icon()  { return 'eicon-search'; }
	public function get_categories() { return array( 'gofly-flight-deals' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_search', array(
			'label' => __( 'Search Settings', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'origin_placeholder', array(
			'label'   => __( 'Origin Placeholder', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Origin city or code', 'gofly-flight-deals' ),
		) );

		$this->add_control( 'destination_placeholder', array(
			'label'   => __( 'Destination Placeholder', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Destination city or code', 'gofly-flight-deals' ),
		) );

		$this->add_control( 'show_date_filter', array(
			'label'   => __( 'Show Date Filter', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_airline_filter', array(
			'label'   => __( 'Show Airline Filter', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_class_filter', array(
			'label'   => __( 'Show Class Filter', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'button_text', array(
			'label'   => __( 'Button Text', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Search Deals', 'gofly-flight-deals' ),
		) );

		// Results page selector.
		$pages   = get_pages();
		$options = array( '' => __( '— Select Page —', 'gofly-flight-deals' ) );
		foreach ( $pages as $page ) {
			$options[ $page->ID ] = $page->post_title;
		}
		$this->add_control( 'results_page', array(
			'label'   => __( 'Results Page', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SELECT,
			'options' => $options,
		) );

		$this->end_controls_section();

		/* STYLE */
		$this->start_controls_section( 'section_style_search', array(
			'label' => __( 'Button Style', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'btn_bg_color', array(
			'label'     => __( 'Button Background', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-search-bar__btn' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_text_color', array(
			'label'     => __( 'Button Text Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-search-bar__btn' => 'color: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$results_url = $settings['results_page'] ? get_permalink( absint( $settings['results_page'] ) ) : get_post_type_archive_link( 'flight_deal' );
		$airlines    = get_terms( array( 'taxonomy' => 'flight_deal_airline', 'hide_empty' => false ) );
		?>
		<div class="gfd-search-bar" data-results-url="<?php echo esc_url( $results_url ); ?>">
			<div class="gfd-search-bar__fields">
				<div class="gfd-search-bar__field">
					<label><?php esc_html_e( 'From', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="origin" class="gfd-search-bar__input" placeholder="<?php echo esc_attr( $settings['origin_placeholder'] ); ?>" />
				</div>
				<div class="gfd-search-bar__field">
					<label><?php esc_html_e( 'To', 'gofly-flight-deals' ); ?></label>
					<input type="text" name="destination" class="gfd-search-bar__input" placeholder="<?php echo esc_attr( $settings['destination_placeholder'] ); ?>" />
				</div>
				<?php if ( 'yes' === $settings['show_date_filter'] ) : ?>
					<div class="gfd-search-bar__field">
						<label><?php esc_html_e( 'Departure Date', 'gofly-flight-deals' ); ?></label>
						<input type="date" name="date" class="gfd-search-bar__input" />
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_airline_filter'] && ! empty( $airlines ) ) : ?>
					<div class="gfd-search-bar__field">
						<label><?php esc_html_e( 'Airline', 'gofly-flight-deals' ); ?></label>
						<select name="airline" class="gfd-search-bar__input">
							<option value=""><?php esc_html_e( 'Any Airline', 'gofly-flight-deals' ); ?></option>
							<?php foreach ( $airlines as $airline ) : ?>
								<option value="<?php echo esc_attr( $airline->slug ); ?>"><?php echo esc_html( $airline->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_class_filter'] ) : ?>
					<div class="gfd-search-bar__field">
						<label><?php esc_html_e( 'Class', 'gofly-flight-deals' ); ?></label>
						<select name="class" class="gfd-search-bar__input">
							<option value=""><?php esc_html_e( 'Any Class', 'gofly-flight-deals' ); ?></option>
							<?php foreach ( gofly_flight_deals_travel_classes() as $val => $label ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
			</div>
			<button type="button" class="gfd-search-bar__btn">
				<span class="dashicons dashicons-search"></span>
				<?php echo esc_html( $settings['button_text'] ); ?>
			</button>
		</div>
		<?php
	}
}
