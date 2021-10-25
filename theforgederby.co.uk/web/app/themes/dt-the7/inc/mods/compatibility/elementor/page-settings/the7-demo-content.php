<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Page_Settings;

use Elementor\Controls_Manager;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

ob_start();
\The7_Demo_Content_Meta_Box::render( $document->get_main_id() );
$metabox = ob_get_clean();

return [
	'args'     => [
		'label'   => __( 'The7 Demo Content', 'the7mk2' ),
		'tab'     => Controls_Manager::TAB_SETTINGS,
		'classes' => 'the7-demo-content-box',
	],
	'controls' => [
		'the7_demo_keep_the_post' => [
			'meta' => '_the7_imported_item',
			'args' => [
				'type'      => Controls_Manager::RAW_HTML,
				'separator' => 'none',
				'classes'   => 'the7-demo-content-box',
				'raw'       => $metabox,
			],
		],
	],
];
