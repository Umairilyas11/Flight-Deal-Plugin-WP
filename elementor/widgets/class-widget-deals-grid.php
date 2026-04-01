<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class GFD_Widget_Deals_Grid extends Widget_Base {

	public function get_name()  { return 'gfd-deals-grid'; }
	public function get_title() { return __( 'Flight Deals Grid', 'gofly-flight-deals' ); }
	public function get_icon()  { return 'eicon-gallery-grid'; }
	public function get_categories() { return array( 'gofly-flight-deals' ); }
	public function get_keywords()   { return array( 'flight', 'deals', 'grid', 'gofly' ); }

	protected function register_controls() {
		/* ─── QUERY TAB ─── */
		$this->start_controls_section( 'section_query', array(
			'label' => __( 'Query', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'posts_per_page', array(
			'label'   => __( 'Number of Deals', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 6,
			'min'     => 1,
			'max'     => 48,
		) );

		$airlines = array();
		foreach ( get_terms( array( 'taxonomy' => 'flight_deal_airline', 'hide_empty' => false ) ) as $term ) {
			$airlines[ $term->term_id ] = $term->name;
		}
		$this->add_control( 'filter_airline', array(
			'label'    => __( 'Filter by Airline', 'gofly-flight-deals' ),
			'type'     => Controls_Manager::SELECT2,
			'options'  => $airlines,
			'multiple' => true,
			'label_block' => true,
		) );

		$destinations = array();
		foreach ( get_terms( array( 'taxonomy' => 'flight_deal_destination', 'hide_empty' => false ) ) as $term ) {
			$destinations[ $term->term_id ] = $term->name;
		}
		$this->add_control( 'filter_destination', array(
			'label'    => __( 'Filter by Destination', 'gofly-flight-deals' ),
			'type'     => Controls_Manager::SELECT2,
			'options'  => $destinations,
			'multiple' => true,
			'label_block' => true,
		) );

		$trip_types = array();
		foreach ( get_terms( array( 'taxonomy' => 'flight_deal_type', 'hide_empty' => false ) ) as $term ) {
			$trip_types[ $term->term_id ] = $term->name;
		}
		$this->add_control( 'filter_type', array(
			'label'    => __( 'Filter by Trip Type', 'gofly-flight-deals' ),
			'type'     => Controls_Manager::SELECT2,
			'options'  => $trip_types,
			'multiple' => true,
			'label_block' => true,
		) );

		$this->add_control( 'featured_only', array(
			'label'     => __( 'Show Featured Only', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => '',
		) );

		$this->add_control( 'hide_expired', array(
			'label'     => __( 'Hide Expired Deals', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
		) );

		$this->add_control( 'orderby', array(
			'label'   => __( 'Order By', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'date',
			'options' => array(
				'date'        => __( 'Date Added', 'gofly-flight-deals' ),
				'price_asc'   => __( 'Price: Low to High', 'gofly-flight-deals' ),
				'price_desc'  => __( 'Price: High to Low', 'gofly-flight-deals' ),
			),
		) );

		$this->end_controls_section();

		/* ─── LAYOUT TAB ─── */
		$this->start_controls_section( 'section_layout', array(
			'label' => __( 'Layout', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'columns', array(
			'label'   => __( 'Columns', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
		) );

		$this->add_control( 'show_badge', array(
			'label'   => __( 'Show Deal Badge', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_airline_logo', array(
			'label'   => __( 'Show Airline Logo', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_original_price', array(
			'label'   => __( 'Show Original (Strikethrough) Price', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_stops', array(
			'label'   => __( 'Show Stops Info', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'card_border_radius', array(
			'label'      => __( 'Card Border Radius', 'gofly-flight-deals' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 12 ),
			'selectors'  => array( '{{WRAPPER}} .gfd-deal-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_control( 'card_box_shadow', array(
			'label'   => __( 'Card Box Shadow', 'gofly-flight-deals' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->end_controls_section();

		/* ─── STYLE TAB ─── */
		$this->start_controls_section( 'section_style', array(
			'label' => __( 'Style', 'gofly-flight-deals' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'card_bg_color', array(
			'label'     => __( 'Card Background Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'price_color', array(
			'label'     => __( 'Price Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card__price' => 'color: {{VALUE}};' ),
		) );

		$this->add_control( 'badge_bg_color', array(
			'label'     => __( 'Badge Background Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card__badge' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'badge_text_color', array(
			'label'     => __( 'Badge Text Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card__badge' => 'color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_bg_color', array(
			'label'     => __( 'Button Background Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card__btn' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_text_color', array(
			'label'     => __( 'Button Text Color', 'gofly-flight-deals' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gfd-deal-card__btn' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'title_typography',
			'label'    => __( 'Route Typography', 'gofly-flight-deals' ),
			'selector' => '{{WRAPPER}} .gfd-deal-card__route',
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'price_typography',
			'label'    => __( 'Price Typography', 'gofly-flight-deals' ),
			'selector' => '{{WRAPPER}} .gfd-deal-card__price',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$query    = $this->build_query( $settings );
		$classes  = 'gfd-deals-grid gfd-deals-grid--cols-' . absint( $settings['columns'] );
		if ( 'yes' === $settings['card_box_shadow'] ) { $classes .= ' gfd-deals-grid--shadow'; }
		echo '<div class="' . esc_attr( $classes ) . '">';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				include gofly_flight_deals_locate_template( 'partials/deal-card.php' );
			}
			wp_reset_postdata();
		} else {
			echo '<p class="gfd-no-results">' . esc_html__( 'No flight deals found.', 'gofly-flight-deals' ) . '</p>';
		}
		echo '</div>';
	}

	protected function build_query( $settings ) {
		$args = array(
			'post_type'      => 'flight_deal',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $settings['posts_per_page'] ),
			'tax_query'      => array( 'relation' => 'AND' ),
			'meta_query'     => array( 'relation' => 'AND' ),
		);

		// Taxonomy filters.
		foreach ( array(
			'filter_airline'      => 'flight_deal_airline',
			'filter_destination'  => 'flight_deal_destination',
			'filter_type'         => 'flight_deal_type',
		) as $control => $taxonomy ) {
			if ( ! empty( $settings[ $control ] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', (array) $settings[ $control ] ),
				);
			}
		}

		// Featured filter.
		if ( 'yes' === $settings['featured_only'] ) {
			$args['meta_query'][] = array( 'key' => '_gfd_is_featured', 'value' => '1' );
		}

		// Expiry filter.
		if ( 'yes' === $settings['hide_expired'] ) {
			$args['meta_query'][] = gofly_flight_deals_expiry_meta_query();
		}

		// Ordering.
		if ( 'price_asc' === $settings['orderby'] ) {
			$args['meta_key'] = '_gfd_price';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'ASC';
		} elseif ( 'price_desc' === $settings['orderby'] ) {
			$args['meta_key'] = '_gfd_price';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		return new WP_Query( $args );
	}
}
