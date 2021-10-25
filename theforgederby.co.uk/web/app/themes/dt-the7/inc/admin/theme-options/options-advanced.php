<?php
/**
 * Advanced theme options.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

$options[] = array(
	'name' => _x( 'Advanced Settings', 'theme-options', 'the7mk2' ),
	'type' => 'heading',
	'id'   => 'advanced-settings',
);

$options[] = array( 'name' => _x( 'Responsiveness', 'theme-options', 'the7mk2' ), 'type' => 'block' );

$options['general-responsive'] = array(
	'name'    => _x( 'Responsive layout', 'theme-options', 'the7mk2' ),
	'id'      => 'general-responsive',
	'std'     => '1',
	'type'    => 'radio',
	'options' => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
);

$options[] = array( 'name' => _x( 'Performance', 'theme-options', 'the7mk2' ), 'type' => 'block' );

$options['general-images_lazy_loading'] = array(
	'id'      => 'general-images_lazy_loading',
	'name'    => _x( 'Images lazy loading', 'theme-options', 'the7mk2' ),
	'desc'    => _x( 'Can dramatically reduce page loading speed. Recommended.', 'theme-options', 'the7mk2' ),
	'std'     => '1',
	'type'    => 'radio',
	'options' => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
);

$options['general-smooth_scroll'] = array(
	'name'    => _x( 'Enable "scroll-behaviour: smooth" for next gen browsers', 'theme-options', 'the7mk2' ),
	'id'      => 'general-smooth_scroll',
	'std'     => 'on',
	'type'    => 'radio',
	'options' => array(
		'on'          => _x( 'Yes', 'theme-options', 'the7mk2' ),
		'off'         => _x( 'No', 'theme-options', 'the7mk2' ),
		'on_parallax' => _x( 'On only on pages with parallax', 'theme-options', 'the7mk2' ),
	),
);

$options['advanced-speed_img_resize'] = array(
	'id'      => 'advanced-speed_img_resize',
	'name'    => _x( 'Images fast resize', 'theme-options', 'the7mk2' ),
	'desc'    => _x( 'Can slightly reduce page load time.', 'theme-options', 'the7mk2' ),
	'std'     => '1',
	'type'    => 'radio',
	'options' => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
);

$options['advanced-normalize_resize_on_mobile'] = array(
	'id'      => 'advanced-normalize_resize_on_mobile',
	'name'    => _x( 'Normalize resize event on mobile devices', 'theme-options', 'the7mk2' ),
	'desc'    => _x( 'Can improve scrolling performance on mobile devices', 'theme-options', 'the7mk2' ),
	'std'     => '1',
	'type'    => 'radio',
	'options' => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
);

$options[] = array( 'type' => 'divider' );

$options['advanced-fvm_enable_integration'] = array(
	'id'        => 'advanced-fvm_enable_integration',
	'name'      => sprintf(
		_x(
			'Enable <a href="%1$s" target="_blank">Fast Velocity Minify</a> plugin integration',
			'theme-options',
			'the7mk2'
		),
		'https://wordpress.org/plugins/fast-velocity-minify/'
	),
	'std'       => '0',
	'type'      => 'radio',
	'options'   => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
	'show_hide' => array(
		'1' => 'show-fvm-options',
	),
);


$options[] = array(
	'type'  => 'js_hide_begin',
	'class' => 'advanced-fvm_enable_integration show-fvm-options',
);

$options['advanced-fvm_script_timeout'] = array(
	'id'    => 'advanced-fvm_script_timeout',
	'name'  => _x( 'Delay loading of merged scripts', 'theme-options', 'the7mk2' ),
	'std'   => '50',
	'type'  => 'number',
	'units' => 'ms',
);

$beautiful_loading_url = add_query_arg( 'page', 'options-framework', admin_url( 'admin.php', 'relative' ) );
$beautiful_loading_url .= '#section-general-beautiful_loading';

$options[] = array(
	'id'   => 'test',
	'type' => 'info',
	'desc' => sprintf(
		_x(
			'- 50ms can significantly improve google page speed score
- 1000ms and beyond is aggressive setting for particularly heavy sites
- for best visual results use in combination with <a href="%1$s">beautiful loading</a>',
			'theme-options',
			'the7mk2'
		),
		esc_url_raw( $beautiful_loading_url )
	),
);

$options[] = array( 'type' => 'js_hide_end' );

$options[] = array( 'name' => _x( 'SEO', 'theme-options', 'the7mk2' ), 'type' => 'block' );

$options['the7_opengraph_tags'] = array(
	'id'      => 'the7_opengraph_tags',
	'name'    => _x( 'The7 OpenGraph tags', 'theme-options', 'the7mk2' ),
	'desc'    => _x( 'It can be safely disabled if any SEO plugin is used.', 'theme-options', 'the7mk2' ),
	'std'     => '1',
	'type'    => 'radio',
	'options' => array(
		'1' => _x( 'Enabled', 'theme-options', 'the7mk2' ),
		'0' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
	),
);

$options[] = array(
	'name' => _x( 'Custom CSS', 'theme-options', 'the7mk2' ),
	'type' => 'heading',
	'id'   => 'custom-css',
);

$options[] = array( 'name' => _x( 'Custom CSS', 'theme-options', 'the7mk2' ), 'type' => 'block' );

$options['general-custom_css'] = array(
	'settings' => array( 'rows' => 64, 'code_style' => 'text/css' ),
	'id'       => 'general-custom_css',
	'std'      => false,
	'type'     => 'code_editor',
	'sanitize' => 'without_sanitize',
	'class'    => 'section-code_editor-long',
);

$options[] = array(
	'name' => _x( 'Custom JavaScript', 'theme-options', 'the7mk2' ),
	'type' => 'heading',
	'id'   => 'custom-javascript',
);

$options[] = array(
	'name' => _x( 'Tracking code (e.g. Google analytics) or arbitrary JavaScript', 'theme-options', 'the7mk2' ),
	'type' => 'block',
);

$options['general-tracking_code'] = array(
	'settings' => array( 'rows' => 32, 'code_style' => 'htmlmixed' ),
	'id'       => 'general-tracking_code',
	'std'      => false,
	'type'     => 'code_editor',
	'sanitize' => 'without_sanitize',
	'class'    => 'section-code_editor-long',
);
