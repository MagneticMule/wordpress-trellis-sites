<?php
/*
 * The7 elements product meta widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Core\Responsive\Responsive;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Group_Control_Image_Size;
use The7_Less_Vars_Value_Font;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_accordion_Widget extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-accordion';
	}

	protected function the7_title() {
		return __( 'Accordion & Toggle', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-accordion';
	}

	protected function the7_keywords() {
		return [ 'accordion' ];
	}

	public function get_style_depends() {
		return [ 'the7-accordion-widget' ];
	}

	public function get_script_depends() {
		return ['the7-accordion-widget'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Accordion', 'the7mk2' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title & Description', 'the7mk2' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'the7mk2' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content', 'the7mk2' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'accordion_tab_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => '',
					'library' => '',
				],
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$tab_default_content = __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' );

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Accordion Items', 'the7mk2' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Accordion #1', 'the7mk2' ),
						'tab_content' => $tab_default_content
					],
					[
						'tab_title' => __( 'Accordion #2', 'the7mk2' ),
						'tab_content' => $tab_default_content
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'the7mk2' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
            'th7_accordion_type',
            [
                'label'       => esc_html__('Accordion Type', 'the7mk2'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'accordion',
                'separator' => 'before',
                'label_block' => false,
                'options'     => [
                    'accordion' => esc_html__('Accordion', 'the7mk2'),
                    'toggle'    => esc_html__('Toggle', 'the7mk2'),
                ],
            ]
        );

		$this->add_control(
			'active_default',
			[
				'label' => __( 'Closed By Default', 'the7mk2' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
                'return_value' => 'yes',
				'condition' => [
					'th7_accordion_type' => 'accordion',
				],
			]
		);

		$this->add_control(
			'animate_on_loading',
			[
				'label' => __( 'Animate On Loading', 'the7mk2' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
                'return_value' => 'yes',
				'condition' => [
					'th7_accordion_type' => 'accordion',
					'active_default!' => 'yes',
				],
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Indicator', 'the7mk2' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label' => __( 'Active Indicator', 'the7mk2' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default' => [
					'value' => 'fas fa-caret-up',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Borders', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-item' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-item' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-content' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'borders_between_titles',
			[
				'label' => __( 'Borders Between Titles', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'border_below_active_title',
			[
				'label' => __( 'Border Below Active Title', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'top_bottom_borders',
			[
				'label' => __( 'Top & Bottom Borders', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'left_right_borders',
			[
				'label' => __( 'Left & Right Borders', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_title',
			[
				'label' => __( 'Title', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_style' );

		$this->start_controls_tab( 'normal_tabs_title_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_background',
			[
				'label' => __( 'Background', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_tabs_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_hover_background',
			[
				'label' => __( 'Background', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_tabs_style',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_active_background',
			[
				'label' => __( 'Background', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_title_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion div.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'div',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h1_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h1.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h1',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h2_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h2.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h3_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h3.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h3',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h4_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h4.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h4',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h5_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h5.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h5',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_h6_typography',
				'selector' => '{{WRAPPER}} .the7-adv-accordion h6.elementor-tab-title',
				'condition'              => [
					'title_html_tag' => 'h6',
				],
			]
		);

		$this->add_basic_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'the7mk2' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		// Icon Style
		$this->start_controls_section(
			'section_toggle_style_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => __( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'right',
				'toggle' => false,
			]
		);

		$this->add_basic_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_border_width',
			[
				'label' => __( 'Border Width', 'the7mk2' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'the7mk2' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_tabs_style' );

		$this->start_controls_tab( 'normal_icon_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label'       => __( 'Icon Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'default'	  => '',
				'alpha'       => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'border-style: solid;border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_arrow_icon_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_hover_color',
			[
				'label'       => __( 'Icon Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon.elementor-accordion-tab-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'border-style: solid;border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_arrow_icon_style',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_active_color',
			[
				'label'       => __( 'Icon Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon.elementor-accordion-tab-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_active_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'border-style: solid;border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_icon_active_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon.elementor-accordion-tab-icon .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'icon_space',
			[
				'label' => __( 'Spacing', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon.elementor-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-icon.elementor-accordion-tab-icon.elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Arrow Style
		$this->start_controls_section(
			'section_toggle_arrow_icon',
			[
				'label' => __( 'Indicator', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'arrow_align',
			[
				'label' => __( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
			]
		);

		$this->add_basic_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Size', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon:not(.elementor-accordion-tab-icon)' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-accordion-icon:not(.elementor-accordion-tab-icon) svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab( 'normal_arrow_title_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon:not(.elementor-accordion-tab-icon)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title .elementor-accordion-icon:not(.elementor-accordion-tab-icon) svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_arrow_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon:not(.elementor-accordion-tab-icon)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon:not(.elementor-accordion-tab-icon) svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_arrow_style',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_active_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon:not(.elementor-accordion-tab-icon)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon:not(.elementor-accordion-tab-icon) svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'arrow_space',
			[
				'label' => __( 'Spacing', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion-icon:not(.elementor-accordion-tab-icon).elementor-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-accordion-icon:not(.elementor-accordion-tab-icon).elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_content',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label' => __( 'Background', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .elementor-tab-content',
			]
		);

		$this->add_basic_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'the7mk2' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );

		$this->print_inline_css();
		
		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			$settings['icon'] = 'fa fa-plus';
			$settings['icon_active'] = 'fa fa-minus';
			$settings['icon_align'] = $this->get_settings( 'icon_align' );
		}

		$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		$has_icon = ( ! $is_new || ! empty( $settings['selected_icon']['value'] ) );
		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'wrapper', [
			'class'					=> 'elementor-accordion the7-adv-accordion',
			'data-accordion-type'	=> $settings['th7_accordion_type']
		]);

		if ( ! $settings['borders_between_titles'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'ac_bb_title' );
		}

		if ( ! $settings['border_below_active_title'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'ac_bb_active_title' );
		}

		if ( ! $settings['top_bottom_borders'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'ac_top_bottom_borders' );
		}

		if ( ! $settings['left_right_borders'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'ac_left_right_borders' );
		}

		if ( $settings['animate_on_loading'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', 'animate-on-loading' );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?> role="tablist">
			<?php
			foreach ( $settings['tabs'] as $index => $item ) :
				$tab_count = $index + 1;

				$has_tab_icon = empty( $item['accordion_tab_icon']['value'] ) ? '' : $item['accordion_tab_icon']['value'];
				$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
				$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

				$this->add_render_attribute( $tab_title_setting_key, [
					'id' => 'elementor-tab-title-' . $id_int . $tab_count,
					'class' => [ 'elementor-tab-title the7-accordion-header' ],
					'data-tab' => $tab_count,
					'role' => 'tab',
					'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
				] );

				$this->add_render_attribute( $tab_content_setting_key, [
					'id' => 'elementor-tab-content-' . $id_int . $tab_count,
					'class' => [ 'elementor-tab-content', 'elementor-clearfix' ],
					'data-tab' => $tab_count,
					'role' => 'tabpanel',
					'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
				] );

				if ( $tab_count == 1 ) {

                    if ( $settings['th7_accordion_type'] == 'accordion' ) {
	                    if ( $settings['active_default'] == 'yes'){
		                    $this->add_render_attribute( $tab_title_setting_key, 'class', 'deactive-default' );
		                    $this->add_render_attribute( $tab_content_setting_key, 'class', 'deactive-default' );
                        }
	                    else{
		                    $this->add_render_attribute( $tab_title_setting_key, 'class', 'active-default' );
		                    $this->add_render_attribute( $tab_content_setting_key, 'class', 'active-default' );

		                    if ( $settings['animate_on_loading'] !== 'yes') {
			                    $this->add_render_attribute( $tab_title_setting_key, 'class', 'animation-disable' );
			                    $this->add_render_attribute( $tab_content_setting_key, 'class', 'animation-disable' );
			                    $this->add_render_attribute( 'wrapper', 'class', 'animate-on-loading' );
		                    }
                        }
					} else {
						$this->add_render_attribute( $tab_title_setting_key, 'class', 'deactive-default' );
						$this->add_render_attribute( $tab_content_setting_key, 'class', 'deactive-default' );
					}
				}

				$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );

				$title_html_tag = Utils::validate_html_tag( $settings['title_html_tag'] );
				?>
				<div class="elementor-accordion-item">
					<<?php echo $title_html_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>>

						<?php if ( $has_icon ) : ?>
							<span class="elementor-accordion-icon elementor-accordion-icon-<?php echo esc_attr( $settings['arrow_align'] ); ?>" aria-hidden="true">
							<?php
							if ( $is_new || $migrated ) { ?>
								<span class="elementor-accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
								<?php if ( ! empty( $settings['selected_active_icon']['value'] ) ) : ?>
									<span class="elementor-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
								<?php else : ?>
									<span class="elementor-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
								<?php endif; ?>
							<?php } else { ?>
								<i class="elementor-accordion-icon-closed <?php echo esc_attr( $settings['icon'] ); ?>"></i>
								<i class="elementor-accordion-icon-opened <?php echo esc_attr( $settings['icon_active'] ); ?>"></i>
							<?php } ?>
							</span>
						<?php endif; ?>
						<?php
						if ( $has_tab_icon ) : ?>
							<span class="elementor-accordion-tab-icon elementor-accordion-icon elementor-accordion-icon-<?php echo esc_attr( $settings['icon_align'] ); ?>" aria-hidden="true">
								<div class="elementor-icon"><?php Icons_Manager::render_icon( $item['accordion_tab_icon'] ); ?></div>
							</span>
						<?php endif; ?>
						<a class="elementor-accordion-title" href=""><?php echo wp_kses_post( $item['tab_title'] ); ?></a>
					</<?php echo $title_html_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
