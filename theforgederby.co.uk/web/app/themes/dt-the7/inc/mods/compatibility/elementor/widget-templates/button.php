<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Button
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Button extends Abstract_Template {

	const ICON_MANAGER  = 'icon_manager';
	const ICON_SWITCHER = 'icon_switcher';

	/**
	 * Add button style controls section.
	 *
	 * @param string $icon_controls Icon controls type. Can be Button::ICON_MANAGER or Button::ICON_SWITCHER.
	 * @param array  $condition     Section conditions.
	 * @param array  $override      Controls override.
	 */
	public function add_style_controls( $icon_controls = self::ICON_MANAGER, $condition = [], $override = [] ) {
		$this->widget->start_controls_section(
			'button_style_section',
			[
				'label'     => __( 'Button', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			]
		);

		if ( $icon_controls === self::ICON_MANAGER ) {
			$button_icon     = [
				'label'          => __( 'Icon', 'the7mk2' ),
				'type'           => Controls_Manager::ICONS,
				'default'        => [
					'value'   => '',
					'library' => '',
				],
				'skin'           => 'inline',
				'label_block'    => false,
				'style_transfer' => false,
			];
			$icon_conditions = [
				'button_icon[value]!' => '',
			];
		} else {
			$button_icon     = [
				'label'          => __( 'Icon', 'the7mk2' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __( 'Show', 'the7mk2' ),
				'label_off'      => __( 'Hide', 'the7mk2' ),
				'return_value'   => 'y',
				'default'        => 'y',
				'style_transfer' => false,
			];
			$icon_conditions = [
				'button_icon' => 'y',
			];
		}

		$fields = [
			'button_size'          => [
				'label'          => __( 'Size', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'xs',
				'options'        => The7_Elementor_Widget_Base::get_button_sizes(),
				'style_transfer' => true,
			],
			'button_icon'          => $button_icon,
			'button_icon_size'     => [
				'label'      => __( 'Icon Size', 'the7mk2' ),
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
				],
				'selectors'  => [
					'{{WRAPPER}} .box-button.elementor-button i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .box-button.elementor-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition'  => $icon_conditions,
			],
			'button_icon_position' => [
				'label'                => __( 'Icon Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'after',
				'options'              => [
					'before' => [
						'title' => __( 'Before', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'after'  => [
						'title' => __( 'After', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'before' => 'order: -1; margin: 0 var(--btn-icon-spacing) 0 0;',
					'after'  => 'order: 1; margin: 0 0 0 var(--btn-icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-button > span' => 'display: flex; align-items: center; justify-content: center; flex-flow: row nowrap;',
					'{{WRAPPER}} .box-button i'      => '{{VALUE}}',
					'{{WRAPPER}} .box-button svg'    => '{{VALUE}}',
				],
				'condition'            => $icon_conditions,
			],
			'button_icon_spacing'  => [
				'label'        => __( 'Icon Spacing', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'default'      => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units'   => [ 'px' ],
				'range'        => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'    => [
					'{{WRAPPER}} .box-button' => '--btn-icon-spacing: {{SIZE}}{{UNIT}};',
				],
				'condition'    => $icon_conditions,
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			'button_icon_divider'  => [
				'type' => Controls_Manager::DIVIDER,
			],
			'button_typography'    => [
				'type'           => Group_Control_Typography::get_type(),
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .box-button',
				'fields_options' => [
					'font_size' => [
						'selectors' => [
							'{{SELECTOR}}'     => 'font-size: {{SIZE}}{{UNIT}}',
							'{{SELECTOR}} i'   => 'font-size: {{SIZE}}{{UNIT}}',
							'{{SELECTOR}} svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
						],
					],
				],
				'control_type'   => self::CONTROL_TYPE_GROUP,
			],
			'button_text_padding'  => [
				'label'        => __( 'Text Padding', 'the7mk2' ),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', 'em', '%' ],
				'selectors'    => [
					'{{WRAPPER}} .box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			'button_min_width'     => [
				'label'        => __( 'Min Width', 'the7mk2' ),
				'type'         => Controls_Manager::NUMBER,
				'selectors'    => [
					'{{WRAPPER}} .box-button' => 'min-width: {{SIZE}}px;',
				],
				'separator'    => 'before',
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			'button_min_height'    => [
				'label'        => __( 'Min Height', 'the7mk2' ),
				'type'         => Controls_Manager::NUMBER,
				'selectors'    => [
					'{{WRAPPER}} .box-button' => 'min-height: {{SIZE}}px;',
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			'button_border'        => [
				'type'         => Group_Control_Border::get_type(),
				'name'         => 'button_border',
				'selector'     => '{{WRAPPER}} .box-button',
				'exclude'      => [ 'color' ],
				'separator'    => 'before',
				'control_type' => self::CONTROL_TYPE_GROUP,
			],
			'button_border_radius' => [
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'button_style_divider' => [
				'type' => Controls_Manager::DIVIDER,
			],
			'tabs_button_style'    => [
				'control_type' => self::CONTROL_TYPE_TABS,
				'fields'       => [
					'tab_button_normal' => [
						'label'  => __( 'Normal', 'the7mk2' ),
						'fields' => [
							'button_text_color'   => [
								'label'     => __( 'Text Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} .box-button, {{WRAPPER}} .box-button *'       => 'color: {{VALUE}};',
									'{{WRAPPER}} .box-button svg *' => 'fill: {{VALUE}};',
								],
							],
							'button_background'   => [
								'type'           => Group_Control_Background::get_type(),
								'name'           => 'button_background',
								'label'          => __( 'Background', 'the7mk2' ),
								'types'          => [ 'classic', 'gradient' ],
								'exclude'        => [ 'image' ],
								// Be careful, magic transition selector here.
								'selector'       => ' {{WRAPPER}} .box-button,  {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button',
								'fields_options' => [
									'background' => [
										'default' => 'classic',
									],
									'color'      => [
										'selectors' => [
											'{{SELECTOR}}' => 'background: {{VALUE}}',
										],
									],
								],
								'control_type'   => self::CONTROL_TYPE_GROUP,
							],
							'button_border_color' => [
								'label'     => __( 'Border Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .box-button,  {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button' => 'border-color: {{VALUE}};',
								],
								'condition' => [
									'button_border_border!' => '',
								],
							],
							'button_shadow'       => [
								'type'         => Group_Control_Box_Shadow::get_type(),
								'name'         => 'button_shadow',
								'selector'     => '{{WRAPPER}} .box-button,  {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button',
								'control_type' => self::CONTROL_TYPE_GROUP,
							],
						],
					],
					'tab_button_hover'  => [
						'label'  => __( 'Hover', 'the7mk2' ),
						'fields' => [
							'button_hover_color'        => [
								'label'     => __( 'Text Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .box-button { transition: all 0.3s ease;} {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-button:hover *, {{WRAPPER}} .box-hover:hover .box-button, {{WRAPPER}} .box-hover:hover .box-button *'             => 'color: {{VALUE}};',
									'{{WRAPPER}} .box-button:hover svg, {{WRAPPER}} .box-hover:hover .box-button svg' => 'fill: {{VALUE}};',
								],
							],
							'button_background_hover'   => [
								'type'           => Group_Control_Background::get_type(),
								'name'           => 'button_background_hover',
								'label'          => __( 'Background', 'the7mk2' ),
								'types'          => [ 'classic', 'gradient' ],
								'exclude'        => [ 'image' ],
								'selector'       => '{{WRAPPER}} .box-button { transition: all 0.3s ease;} {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button',
								'fields_options' => [
									'background' => [
										'default' => 'classic',
									],
									'color'      => [
										'selectors' => [
											'{{SELECTOR}}' => 'background: {{VALUE}}',
										],
									],
								],
								'control_type'   => self::CONTROL_TYPE_GROUP,
							],
							'button_hover_border_color' => [
								'label'     => __( 'Border Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .box-button { transition: all 0.3s ease;}  {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button' => 'border-color: {{VALUE}};',
								],
								'condition' => [
									'button_border_border!' => '',
								],
							],
							'button_hover_shadow'       => [
								'type'         => Group_Control_Box_Shadow::get_type(),
								'name'         => 'button_hover_shadow',
								'selector'     => '{{WRAPPER}} .box-button { transition: all 0.3s ease;}  {{WRAPPER}} .box-button:hover, {{WRAPPER}} .box-hover:hover .box-button',
								'control_type' => self::CONTROL_TYPE_GROUP,
							],
						],
					],
				],
			],
			'gap_above_button'     => [
				'label'      => __( 'Spacing Above Button', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .box-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
			],
		];

		$this->setup_controls( $fields, $override );

		$this->widget->end_controls_section();
	}

	/**
	 * Add button render attributes.
	 *
	 * @param string $element Element name.
	 */
	public function add_render_attributes( $element ) {
		$settings = $this->get_settings();

		if ( ! empty( $settings['button_size'] ) ) {
			$this->widget->add_render_attribute(
				$element,
				'class',
				'box-button elementor-button elementor-size-' . $settings['button_size']
			);
		}
	}

	/**
	 * Add render attributes for the case when there is no text.
	 *
	 * @param string $element Element name.
	 */
	public function add_icon_only_render_attributes( $element ) {
		$this->widget->add_render_attribute( $element, 'class', 'no-text' );
	}

	/**
	 * Determine if button icon is visible.
	 *
	 * @return bool
	 */
	public function is_icon_visible() {
		$button_icon = $this->get_settings( 'button_icon' );

		if ( is_array( $button_icon ) ) {
			return ! empty( $button_icon['value'] );
		}

		return (bool) $button_icon;
	}

	/**
	 * Output button HTML.
	 *
	 * @param string $element Element name.
	 * @param string $text    Button text. Should be escaped beforehand.
	 * @param string $tag     Button HTML tag, 'a' by default.
	 */
	public function render_button( $element, $text = '', $tag = 'a' ) {
		$settings = $this->get_settings();
		$tag      = esc_html( $tag );

		$this->add_render_attributes( $element );

		if ( ! $text ) {
			$this->add_icon_only_render_attributes( $element );
		}

		// Output native icon only if it's Button::ICON_MANAGER.
		if ( is_array( $settings['button_icon'] ) ) {
			$text .= $this->widget->get_elementor_icon_html(
				$settings['button_icon'],
				'i',
				[
					'class' => 'elementor-button-icon',
				]
			);
		}

		echo '<' . $tag . ' ' . $this->widget->get_render_attribute_string( $element ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped above
		echo $text; // Should be escaped beforehand.
		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped above
	}
}
