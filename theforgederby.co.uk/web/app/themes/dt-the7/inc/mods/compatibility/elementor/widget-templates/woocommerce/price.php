<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce;

use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use WC_Product;

/**
 * Class Price
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce
 */
class Price extends Abstract_Template {

	/**
	 * Render product price.
	 *
	 * @param WC_Product $product Product.
	 */
	public function render_product_price( $product ) {
		if ( $this->is_price_enabled() ) {
			echo '<span class="price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
		}
	}

	/**
	 * Return true if Price is enabled.
	 *
	 * @return bool
	 */
	public function is_price_enabled() {
		return $this->get_settings( 'show_price' ) === 'yes';
	}

	/**
	 * Add price switcher control.
	 */
	public function add_switch_control() {
		$this->widget->add_control(
			'show_price',
			[
				'label'        => __( 'Price', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);
	}

	/**
	 * Add price style controls.
	 */
	public function add_style_controls() {
		$this->widget->start_controls_section(
			'price_style',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->widget->add_control(
			'normal_price_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Normal Price', 'the7mk2' ),
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Normal Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->widget->add_control(
			'normal_price_text_color',
			[
				'label'     => __( 'Normal Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price > span.woocommerce-Price-amount.amount, {{WRAPPER}} .price > span.woocommerce-Price-amount span, {{WRAPPER}} .price, {{WRAPPER}} .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			'sale_price_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Sale Price', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => __( 'Old Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price del span',
			]
		);

		$this->widget->add_control(
			'sale_price_text_color',
			[
				'label'     => __( 'Old Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			'old_price_line_color',
			[
				'label'     => __( 'Old Price Line Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_new_price_typography',
				'label'    => __( 'New Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price ins span',
			]
		);

		$this->widget->add_control(
			'sale_new_price_text_color',
			[
				'label'     => __( 'New Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_basic_responsive_control(
			'price_space',
			[
				'label'      => __( 'Spacing Above Price', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .price' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->end_controls_section();
	}

}
