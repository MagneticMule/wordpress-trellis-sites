<?php
/**
 * The7 Product Sorting widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Sorting
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets\Woocommerce
 */
class Product_Sorting extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-product-sorting';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return __( 'Product Sorting', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * Get the7 widget categories.
	 *
	 * @return string[]
	 */
	protected function the7_categories() {
		return [ 'woocommerce-elements' ];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/product-sorting.css'
		);
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			// Prevent select optening in editor.
			echo '<style>.elementor-widget-the7-product-sorting .orderby{ pointer-events: none; }</style>';
		}
		echo '<div class="the7-wc-catalog-ordering">';
		wc_setup_loop(
			[
				'total' => 2,
			]
		);
		woocommerce_catalog_ordering();
		wc_reset_loop();
		Icons_Manager::render_icon(
			$this->get_settings_for_display( 'icon' ),
			[
				'class'       => 'orderby-icon',
				'aria-hidden' => 'true',
			],
			'i'
		);
		echo '</div>';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'select_section',
			[
				'label' => __( 'Settings', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'text_typography',
				'label'          => __( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .orderby',
				'fields_options' => [
					'font_size'   => [
						'selectors' => [
							'{{WRAPPER}} .orderby' => 'font-size: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}} .the7-wc-catalog-ordering' => 'font-size: {{SIZE}}{{UNIT}}',
						],
					],
					'line_height' => [
						'selectors' => [
							'{{SELECTOR}}' => 'line-height: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}}'  => '--ordering-line-height: {{SIZE}}{{UNIT}}',
						],
					],
				],
			]
		);

		$this->add_control( 'typograpty_divider', [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control(
			'icon',
			[
				'label'   => __( 'Icon', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'skin'    => 'inline',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => false,
				'selectors_dictionary' => [
					'left'  => 'right: unset; left: 0;',
					'right' => 'right: 0; left: unset;',
				],
				'prefix_class'         => 'icon-align-',
				'selectors'            => [
					'{{WRAPPER}} .orderby-icon' => '{{VALUE}};',
					'{{WRAPPER}} svg'           => '{{VALUE}};',
				],
				'condition'            => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}'               => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .orderby-icon' => 'font-size: var(--icon-size); line-height: var(--ordering-line-height);',
					'{{WRAPPER}} svg'           => 'font-size: var(--icon-size); line-height: var(--ordering-line-height);',
				],
				'condition'  => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_spacing',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-spacing: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .orderby'      => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}'               => '--border-top-width: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .orderby-icon' => 'border-width: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} svg'           => 'border-width: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .orderby' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'text_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--orderby-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --orderby-padding-right: {{RIGHT}}{{UNIT}}; --orderby-padding-left: {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control( 'colors_divider', [ 'type' => Controls_Manager::DIVIDER ] );

		$this->start_controls_tabs( 'general_style_tabs' );

		$this->start_controls_tab(
			'normal_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering .orderby' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-wc-catalog-ordering' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering .orderby' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering .orderby' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering .orderby-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-wc-catalog-ordering svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_hover_color',
			[
				'label'     => __( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover .orderby' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_hover_color',
			[
				'label'     => __( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover .orderby' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_hover_color',
			[
				'label'     => __( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover .orderby' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __( 'Icon', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover .orderby-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-wc-catalog-ordering:hover svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

}
