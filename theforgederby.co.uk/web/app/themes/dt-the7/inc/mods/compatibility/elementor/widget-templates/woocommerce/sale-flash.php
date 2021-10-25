<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce;

use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sale_Flash
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Sale_Flash extends Abstract_Template {

	/**
	 * Render Sale Flash HTML if it's enabled or Elementor is in the edit mode.
	 */
	public function render_sale_flash() {
		if ( $this->is_sale_flash_enabled() ) {
			woocommerce_show_product_loop_sale_flash();
		}
	}

	/**
	 * Return true if Sale Flash is enabled.
	 *
	 * @return bool
	 */
	public function is_sale_flash_enabled() {
		return $this->get_settings( 'show_onsale_flash' ) === 'yes';
	}

	/**
	 * Add Sale Flash switcher.
	 */
	public function add_switch_control() {
		$this->widget->add_control(
			'show_onsale_flash',
			[
				'label'        => __( 'Sale Flash', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'No', 'the7mk2' ),
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'separator'    => 'before',
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);
	}

	/**
	 * Add style controls.
	 */
	public function add_style_controls() {
		$this->widget->start_controls_section(
			'onsale_style',
			[
				'label'     => __( 'Sale Flash', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_onsale_flash' => 'yes',
				],
			]
		);

		$this->widget->add_control(
			'onsale_position_heading',
			[
				'label'     => __( 'Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'onsale_horizontal_position',
			[
				'label'                => __( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .onsale' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'right: auto; left: 0; --sale-h-position: 0',
					'center' => 'left: auto; right: 50%; --sale-h-position: 50%; transform: translate(var(--sale-h-position), var(--sale-v-position))',
					'right'  => 'left: auto; right: 0; --sale-h-position: 0',
				],
				'default'              => 'left',
				'toggle'               => false,
				'prefix_class'         => 'onsale-h-position-',
			]
		);

		$this->widget->add_basic_responsive_control(
			'onsale_horizontal_distance',
			[
				'label'      => __( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -5,
						'max' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .onsale' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_control(
			'onsale_vertical_position',
			[
				'label'                => __( 'Vertical Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .onsale' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'    => 'bottom: auto; top: 0; --sale-v-position: 0',
					'center' => 'top: auto; bottom: 50%; --sale-v-position: 50%; transform: translate(var(--sale-h-position), var(--sale-v-position))',
					'bottom' => 'top: auto; bottom: 0; --sale-v-position: 0',
				],
				'default'              => 'top',
				'toggle'               => false,
				'prefix_class'         => 'onsale-v-position-',
			]
		);

		$this->widget->add_basic_responsive_control(
			'onsale_vertical_distance',
			[
				'label'      => __( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -5,
						'max' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .onsale' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_control(
			'onsale_background_size_heading',
			[
				'label'     => __( 'Background Size', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_basic_responsive_control(
			'onsale_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};padding-top: 0; padding-bottom: 0;',
				],
			]
		);

		$this->widget->add_basic_responsive_control(
			'onsale_width',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .onsale' => 'min-width: {{SIZE}}{{UNIT}}; padding-left: 0; padding-right: 0;',
				],
			]
		);

		$this->widget->add_control(
			'onsale_style_heading',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'onsale_typography',
				'selector' => '{{WRAPPER}} .onsale',
				'exclude'  => [ 'line_height' ],
			]
		);

		$this->widget->add_control(
			'onsale_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .onsale' => 'color: {{VALUE}}',
				],
			]
		);

		$this->widget->add_control(
			'onsale_text_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .onsale' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'onsale_border',
				'label'    => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .onsale',
			]
		);

		$this->widget->add_control(
			'onsale_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'onsale_shadow',
				'selector' => '{{WRAPPER}} .onsale',
			]
		);

		$this->widget->end_controls_section();
	}
}
