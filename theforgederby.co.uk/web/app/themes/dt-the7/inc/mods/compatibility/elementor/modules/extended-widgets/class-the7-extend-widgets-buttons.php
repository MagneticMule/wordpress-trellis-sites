<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Group_Control_Box_Shadow;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Extend_Widgets_Buttons {

	public function __construct() {
		//inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'button' => [
				'section_name' => [ 'section_style', ],
			],
			'form'   => [
				'section_name' => [ 'section_button_style', ],
			],

		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		if ( $widget->get_name() == 'button' ) {
			if ( $section_id == 'section_style' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}}; background-image:none;',
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'background_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}}; background-image:none;',
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_hover_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button, {{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}}'
					],
				];
				The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'typography_font_size', $control_data );

				//add box shadow hover, but before move shadow into normal tab
				$widget->remove_control( 'button_box_shadow_box_shadow' );
				$widget->remove_control( 'button_box_shadow_box_shadow_position' );
				$widget->remove_control( 'button_box_shadow_box_shadow_type' );

				$widget->start_injection( [
					'of' => 'button_text_color',
					'at' => 'before',
				] );

				$widget->add_group_control( Group_Control_Box_Shadow::get_type(), [
					'name'     => 'button_box_shadow',
					'selector' => '{{WRAPPER}} .elementor-button',
				] );

				$widget->end_injection();

				$widget->start_injection( [
					'of' => 'hover_color',
					'at' => 'before',
				] );

				$widget->add_group_control( Group_Control_Box_Shadow::get_type(), [
					'name'     => 'button_box_shadow_hover',
					'selector' => '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
				] );

				$widget->end_injection();
			}
		}
		if ( $widget->get_name() == 'form' ) {
			if ( $section_id == 'section_button_style' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'background-color: {{VALUE}}; background-image:none;',
						'{{WRAPPER}} .elementor-button[type="submit"]'       => 'background-color: {{VALUE}}; background-image:none;',
					]
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'background-color: {{VALUE}}; background-image:none;',
						'{{WRAPPER}} .elementor-button[type="submit"]:hover'       => 'background-color: {{VALUE}}; background-image:none;',
					]
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_hover_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button, {{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}}'
					],
				];
				The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'button_typography_font_size', $control_data );
			}
		}
	}

}