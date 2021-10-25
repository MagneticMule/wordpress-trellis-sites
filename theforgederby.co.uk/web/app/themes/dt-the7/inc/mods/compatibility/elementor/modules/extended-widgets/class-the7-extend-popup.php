<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Extend_Popup {

	public function __construct() {
		//inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'popup' => [
				'section_name' => [ 'popup_layout', ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		if ( $section_id == 'popup_layout' ) {
			$control_data = [
				'selectors' => [
					'{{WRAPPER}} .dialog-message' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dialog-widget-content' => 'width: {{SIZE}}{{UNIT}};',
				]
			];

			The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'width', $control_data );

			$control_params = [
				'label'                                                  => __( 'sidebar helper', 'the7mk2' ),
				'type'                                                   => Controls_Manager::HIDDEN,
				'condition'                                              => [
					'height_type' => 'fit_to_screen',
				],
				'default'                                                => 'y',
				'return_value'                                           => 'y',
				'selectors' => [
					'body:not(.admin-bar) {{WRAPPER}}' => 'top:0;',
					'body.admin-bar {{WRAPPER}}' => 'position: fixed;',
					'{{WRAPPER}} .dialog-widget-content' => 'position: absolute;height: 100%;',
					'{{WRAPPER}} .dialog-message ' => 'position: absolute;height: 100%;width: 100%;'
				],
			];

			$widget->start_injection( [
				'of'       => 'height_type',
				'at'       => 'after',
			] );

			$widget->add_control( 'sidebar_helper', $control_params );
			$widget->end_injection();

		}
	}

}