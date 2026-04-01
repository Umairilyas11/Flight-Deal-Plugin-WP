<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Controls_Manager;

class GFD_Widget_Deals_Carousel extends GFD_Widget_Deals_Grid {

	public function get_name()  { return 'gfd-deals-carousel'; }
	public function get_title() { return __( 'Flight Deals Carousel', 'gofly-flight-deals' ); }
	public function get_icon()  { return 'eicon-slider-push'; }

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section( 'section_carousel', array(
			'label' => __( 'Carousel Options', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'autoplay', array(
			'label'   => __( 'Autoplay', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		) );

		$this->add_control( 'autoplay_speed', array(
			'label'     => __( 'Autoplay Speed (ms)', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 3000,
			'condition' => array( 'autoplay' => 'yes' ),
		) );

		$this->add_control( 'loop', array(
			'label'   => __( 'Loop', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'slides_to_show', array(
			'label'   => __( 'Slides to Show', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 3,
			'min'     => 1,
			'max'     => 5,
		) );

		$this->add_control( 'slides_to_scroll', array(
			'label'   => __( 'Slides to Scroll', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 1,
			'min'     => 1,
			'max'     => 5,
		) );

		$this->add_control( 'show_navigation', array(
			'label'   => __( 'Show Navigation Arrows', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_dots', array(
			'label'   => __( 'Show Dots', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$query     = $this->build_query( $settings );
		$widget_id = $this->get_id();

		$swiper_opts = wp_json_encode( array(
			'slidesPerView'  => absint( $settings['slides_to_show'] ),
			'slidesPerGroup' => absint( $settings['slides_to_scroll'] ),
			'loop'           => 'yes' === $settings['loop'],
			'navigation'     => 'yes' === $settings['show_navigation'] ? array( 'nextEl' => '#gfd-carousel-' . $widget_id . ' .swiper-button-next', 'prevEl' => '#gfd-carousel-' . $widget_id . ' .swiper-button-prev' ) : false,
			'pagination'     => 'yes' === $settings['show_dots'] ? array( 'el' => '#gfd-carousel-' . $widget_id . ' .swiper-pagination', 'clickable' => true ) : false,
			'autoplay'       => 'yes' === $settings['autoplay'] ? array( 'delay' => absint( $settings['autoplay_speed'] ) ) : false,
			'breakpoints'    => array(
				'0'    => array( 'slidesPerView' => 1 ),
				'640'  => array( 'slidesPerView' => 2 ),
				'1024' => array( 'slidesPerView' => absint( $settings['slides_to_show'] ) ),
			),
		) );
		?>
		<div id="gfd-carousel-<?php echo esc_attr( $widget_id ); ?>" class="gfd-deals-carousel" data-swiper-opts="<?php echo esc_attr( $swiper_opts ); ?>">
			<div class="swiper">
				<div class="swiper-wrapper">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							echo '<div class="swiper-slide">';
							include gofly_flight_deals_locate_template( 'partials/deal-card.php' );
							echo '</div>';
						}
						wp_reset_postdata();
					}
					?>
				</div>
				<?php if ( 'yes' === $settings['show_dots'] ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_navigation'] ) : ?>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
