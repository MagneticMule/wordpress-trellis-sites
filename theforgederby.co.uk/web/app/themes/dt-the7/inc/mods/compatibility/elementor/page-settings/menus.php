<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Page_Settings;

use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;

use Elementor\Controls_Manager;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

$template_option_name = The7_Elementor_Compatibility::instance()->page_settings->template_option_name;
$template_condition   = [ PageTemplatesModule::TEMPLATE_CANVAS ];
$nav_menus            = the7_microsite_get_nav_menu_options_for_select();

return [
	'args'     => [
		'label'      => __( 'Menus', 'the7mk2' ),
		'tab'        => Controls_Manager::TAB_SETTINGS,
		'conditions' => [
			'relation' => 'or',
			'terms'    => [
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_template_applied',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => $template_option_name,
							'operator' => '!in',
							'value'    => $template_condition,
						],
					],
				],
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_template_applied',
							'operator' => '==',
							'value'    => '',
						],
						[
							'name'     => $template_option_name,
							'operator' => '!in',
							'value'    => $template_condition,
						],
					],
				],
			],
		],
	],
	'controls' => [
		'the7_main_menu_override'        => [
			'meta' => '_dt_microsite_primary_menu',
			'args' => [
				'label'   => __( 'Primary menu', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => ( $nav_menus + [ '' => _x( 'Primary Menu location', 'admin', 'the7mk2' ) ] ),
			],
		],
		'the7_split_left_menu_override'  => [
			'meta' => '_dt_microsite_split_left_menu',
			'args' => [
				'label'   => __( 'Split Menu Left', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => ( $nav_menus + [ '' => _x( 'Split Menu Left location', 'admin', 'the7mk2' ) ] ),
			],
		],
		'the7_split_right_menu_override' => [
			'meta' => '_dt_microsite_split_right_menu',
			'args' => [
				'label'   => __( 'Split Menu Right', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => ( $nav_menus + [ '' => _x( 'Split Menu Right location', 'admin', 'the7mk2' ) ] ),
			],
		],
		'the7_mobile_menu_override'      => [
			'meta' => '_dt_microsite_mobile_menu',
			'args' => [
				'label'   => __( 'Mobile Menu', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => ( $nav_menus + [ '' => _x( 'Mobile Menu location', 'admin', 'the7mk2' ) ] ),
			],
		],
	],
];
