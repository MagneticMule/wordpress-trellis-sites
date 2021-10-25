<?php
/*
 * The7 elements product info widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || exit;

class Product_Additional_Information extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-additional-information';
	}

	protected function the7_title() {
		return __( 'Additional Information', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-info';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'info', 'data', 'product'];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_style_depends() {
		return [ 'the7-woocommerce-product-additional-information-widget' ];
	}

	protected function _register_controls() {
		$this->start_controls_section( 'section_product_attribute_title_style', [
			'label' => __( 'Attribute title', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );


		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_product_attribute_value_style', [
			'label' => __( 'Attribute value', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'attribute_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'attribute_typography',
				'selector' => '{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value',
			]
		);

		$this->add_basic_responsive_control(
			'align_items',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'	=> 'left',
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'attribute_value_link_heading',
			[
				'type'      	=> \Elementor\Controls_Manager::RAW_HTML,
				'label'     	=> __( 'Link', 'the7mk2' ),
			]
		);

		$this->start_controls_tabs( 'tabs_style' );

		$this->start_controls_tab( 'normal_tabs_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_text_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_text_decoration',
			[
				'label' => __( 'Decoration', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'the7mk2' ),
					'underline' => _x( 'Underline', 'Typography Control', 'the7mk2' ),
					'overline' => _x( 'Overline', 'Typography Control', 'the7mk2' ),
					'line-through' => _x( 'Line Through', 'Typography Control', 'the7mk2' ),
					'none' => _x( 'None', 'Typography Control', 'the7mk2' ),
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_tabs_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_tab_text_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'active_tab_text_decoration',
			[
				'label' => __( 'Decoration', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'the7mk2' ),
					'underline' => _x( 'Underline', 'Typography Control', 'the7mk2' ),
					'overline' => _x( 'Overline', 'Typography Control', 'the7mk2' ),
					'line-through' => _x( 'Line Through', 'Typography Control', 'the7mk2' ),
					'none' => _x( 'None', 'Typography Control', 'the7mk2' ),
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a:hover' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'section_product_attribute_list_style', [
			'label' => __( 'List', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_basic_responsive_control(
			'space_between',
			[
				'label' => __( 'Space Between', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .shop_attributes tr:first-child td, {{WRAPPER}} .shop_attributes tr:first-child th' => 'padding: 5{{UNIT}} 10{{UNIT}} {{SIZE}}{{UNIT}} 5{{UNIT}}',
					'{{WRAPPER}} .shop_attributes tr td, {{WRAPPER}} .shop_attributes tr th' => 'padding: {{SIZE}}{{UNIT}} 10{{UNIT}} {{SIZE}}{{UNIT}} 5{{UNIT}}',
					'{{WRAPPER}} .shop_attributes tr:last-child td, {{WRAPPER}} .shop_attributes tr:last-child th' => 'padding: {{SIZE}}{{UNIT}} 10{{UNIT}} 5{{UNIT}} 5{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'padding-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label' => __( 'Dividers', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'the7mk2' ),
				'label_on' => __( 'On', 'the7mk2' ),
				'default' => 'yes',
				'separator' => 'before',
				'prefix_class' => 'wc-product-info-',
			]
		);

		$this->add_control(
			'top_divider',
			[
				'label' => __( 'Top Divider', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'the7mk2' ),
				'label_on' => __( 'On', 'the7mk2' ),
				'default' => 'no',
				'prefix_class' => 'wc-product-info-top-border-',
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'bottm_divider',
			[
				'label' => __( 'Bottom Divider', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'the7mk2' ),
				'label_on' => __( 'On', 'the7mk2' ),
				'default' => 'no',
				'prefix_class' => 'wc-product-info-bottom-border-',
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => __( 'Style', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default' => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-top-border-yes .shop_attributes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes .shop_attributes tr:first-child td' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes .shop_attributes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes .shop_attributes tr:last-child td' => 'border-bottom-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => __( 'Weight', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		$this->print_inline_css();

		wc_display_product_attributes( $product );
	}
}
