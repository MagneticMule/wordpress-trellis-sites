<?php
/**
 * Options to inject in header.
 */

defined( 'ABSPATH' ) || exit;

$new_options = [];

$new_options[] = [
	'name'  => _x( 'WPML language switcher', 'theme-options', 'the7mk2' ),
	'id'    => 'microwidgets-language-block',
	'class' => 'block-disabled',
	'type'  => 'block',
];

presscore_options_apply_template(
	$new_options,
	'basic-header-element',
	'header-elements-language',
	[
		'caption' => false,
		'icon'    => false,
		'url'     => false,
	]
);

// Add new options.
if ( isset( $options ) ) {
	$options = dt_array_push_after( $options, $new_options, 'header-before-elements-placeholder' );
}

// Cleanup.
unset( $new_options );
