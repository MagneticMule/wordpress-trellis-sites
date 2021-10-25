<?php
/**
 * Buttons options.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading definition.
 */
$options[] = array( 'name' => _x( 'Buttons', 'theme-options', 'the7mk2' ), 'type' => 'heading', 'id' => 'buttons' );

$buttons_integration_is_enabled = the7_is_elementor_buttons_integration_enabled();

if ( $buttons_integration_is_enabled && the7_is_elementor_kit_custom_styles_enabled() ) {
	$options[] = array(
		'id'   => 'test',
		'type' => 'info',
		'desc' => sprintf( _x( 'In order to style elementor buttons, make sure the <b>Disable Default Colors</b> and <b>Disable Default Fonts</b> options are <b>activated</b> in the <a href="%s">elementor settings</a>', 'theme-options', 'the7mk2' ), admin_url( 'admin.php?page=elementor#tab-general' ) ),
	);
}

/**
 * Buttons color.
 */
$options[] = array( 'name' => _x( 'Buttons color', 'theme-options', 'the7mk2' ), 'type' => 'block' );

$options['buttons-color_mode'] = array(
	'name'    => _x( 'Background color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-color_mode',
	'std'     => 'accent',
	'type'    => 'radio',
	'class'   => 'small',
	'options' => array(
		'disabled' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
		'accent'   => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'    => _x( 'Custom color', 'theme-options', 'the7mk2' ),
		'gradient' => _x( 'Custom gradient', 'theme-options', 'the7mk2' ),
	),
);

$options['buttons-color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);

$options['buttons-color_gradient'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-color_gradient',
	'std'        => '135deg|#ffffff 30%|#000000 100%',
	'type'       => 'gradient_picker',
	'dependency' => array(
		'field'    => 'buttons-color_mode',
		'operator' => '==',
		'value'    => 'gradient',
	),
);

$options['buttons-hover_color_mode'] = array(
	'name'    => _x( 'Background hover color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-hover_color_mode',
	'std'     => 'accent',
	'type'    => 'radio',
	'class'   => 'small',
	'divider' => 'top',
	'options' => array(
		'disabled' => _x( 'Disabled', 'theme-options', 'the7mk2' ),
		'accent'   => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'    => _x( 'Custom color', 'theme-options', 'the7mk2' ),
		'gradient' => _x( 'Custom gradient', 'theme-options', 'the7mk2' ),
	),
);

$options['buttons-hover_color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-hover_color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-hover_color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);

$options['buttons-hover_color_gradient'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-hover_color_gradient',
	'std'        => '135deg|#ffffff 30%|#000000 100%',
	'type'       => 'gradient_picker',
	'dependency' => array(
		'field'    => 'buttons-hover_color_mode',
		'operator' => '==',
		'value'    => 'gradient',
	),
);
$options['buttons-border-color_mode'] = array(
	'name'    => _x( 'Border color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-border-color_mode',
	'std'     => 'accent',
	'type'    => 'radio',
	'class'   => 'small',
	'divider' => 'top',
	'options' => array(
		'accent' => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'  => _x( 'Custom color', 'theme-options', 'the7mk2' ),
	),
);
$options['buttons-border-color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-border-color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-border-color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);
$options['buttons-hover-border-color_mode'] = array(
	'name'    => _x( 'Border hover color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-hover-border-color_mode',
	'std'     => 'accent',
	'type'    => 'radio',
	'class'   => 'small',
	'divider' => 'top',
	'options' => array(
		'accent' => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'  => _x( 'Custom color', 'theme-options', 'the7mk2' ),
	),
);
$options['buttons-hover-border-color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-hover-border-color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-hover-border-color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);
$options['buttons-text_color_mode'] = array(
	'name'    => _x( 'Text color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-text_color_mode',
	'std'     => 'color',
	'type'    => 'radio',
	'class'   => 'small',
	'divider' => 'top',
	'options' => array(
		'accent' => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'  => _x( 'Custom color', 'theme-options', 'the7mk2' ),
	),
);

$options['buttons-text_color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-text_color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-text_color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);

$options['buttons-text_hover_color_mode'] = array(
	'name'    => _x( 'Text hover color', 'theme-options', 'the7mk2' ),
	'id'      => 'buttons-text_hover_color_mode',
	'std'     => 'color',
	'type'    => 'radio',
	'class'   => 'small',
	'divider' => 'top',
	'options' => array(
		'accent' => _x( 'Accent', 'theme-options', 'the7mk2' ),
		'color'  => _x( 'Custom color', 'theme-options', 'the7mk2' ),
	),
);

$options['buttons-text_hover_color'] = array(
	'name'       => '&nbsp;',
	'id'         => 'buttons-text_hover_color',
	'std'        => '#ffffff',
	'type'       => 'alpha_color',
	'dependency' => array(
		'field'    => 'buttons-text_hover_color_mode',
		'operator' => '==',
		'value'    => 'color',
	),
);


/**
 * Buttons shadow style.
 */

$options[] = array(
	'name' => _x( 'Buttons shadow', 'theme-options', 'the7mk2' ),
	'type' => 'block',
);

$options[] = array( 'name' => _x( 'Normal', 'theme-options', 'the7mk2' ), 'type' => 'title' );

$options['button-shadow'] = array(
	'name' => _x( 'Shadow', 'theme-options', 'the7mk2' ),
	'id'   => 'button-shadow',
	'type' => 'shadow',
	'std'  => array(
		'color' => 'rgba(255,255,255,0)',
	),
);

$options[] = array( 'type' => 'divider' );

$options[] = array( 'name' => _x( 'Hover', 'theme-options', 'the7mk2' ), 'type' => 'title' );

$options['button-shadow-hover'] = array(
	'name' => _x( 'Shadow hover', 'theme-options', 'the7mk2' ),
	'id'   => 'button-shadow-hover',
	'type' => 'shadow',
	'std'  => array(
		'color' => 'rgba(255,255,255,0)',
	),
);

/**
 * buttons sizes
 */
$buttons = [
	'buttons-s'  => [
		'block_name' => _x( 'Extra small buttons', 'theme-options', 'the7mk2' ),
		'std'        => [
			'typography' => [
				'font_family'    => 'Open Sans',
				'font_size'      => 12,
				'text_transform' => 'none',
				'letter_spacing' => 0
			],
			'icon-size' => 12,
			'padding' => '8px 14px 7px 14px',
			'border-radius' => '4px',
			'border-width' => '0px'
		],
	],
	'buttons-m'  => [
		'block_name' => _x( 'Small buttons', 'theme-options', 'the7mk2' ),
		'std'        => [
			'typography' => [
				'font_family'    => 'Open Sans',
				'font_size'      => 12,
				'text_transform' => 'none',
				'letter_spacing' => 0
			],
			'icon-size' => 12,
			'padding' => '12px 18px 11px 18px',
			'border-radius' => '4px',
			'border-width' => '0px'
		],
	],
	'buttons-l'  => [
		'block_name' => _x( 'Medium buttons', 'theme-options', 'the7mk2' ),
		'std'        => [
			'typography' => [
				'font_family'    => 'Open Sans',
				'font_size'      => 12,
				'text_transform' => 'none',
				'letter_spacing' => 0
			],
			'icon-size' => 12,
			'padding' => '17px 24px 16px 24px',
			'border-radius' => '4px',
			'border-width' => '0px'
		],
	],
	'buttons-lg' => [
		'block_name'        => _x( 'Large buttons', 'theme-options', 'the7mk2' ),
		'elementor-buttons' => true,
		'std'        => [
			'typography' => [
				'font_family'    => 'Open Sans',
				'font_size'      => 18,
				'text_transform' => 'none',
				'letter_spacing' => 0
			],
			'icon-size' => 18,
			'padding' => '20px 40px 20px 40px',
			'border-radius' => '5px',
			'border-width' => '0px'
		],
	],
	'buttons-xl' => [
		'block_name'        => _x( 'Extra large buttons', 'theme-options', 'the7mk2' ),
		'elementor-buttons' => true,
		'std'        => [
			'typography' => [
				'font_family'    => 'Open Sans',
				'font_size'      => 20,
				'text_transform' => 'none',
				'letter_spacing' => 0
			],
			'icon-size' => 20,
			'padding' => '25px 50px 25px 50px',
			'border-radius' => '6px',
			'border-width' => '0px'
		],
	],
];

foreach ( $buttons as $key => $button ) {

	if ( isset( $button['elementor-buttons'] ) && $button['elementor-buttons'] === true && ! $buttons_integration_is_enabled ) {
		continue;
	}

	$std = $button['std'];

	$options[] = array( 'name' => $button['block_name'], 'type' => 'block' );

	$options["{$key}-typography"] = array(
		'id'   => "{$key}-typography",
		'type' => 'typography',
		'std'  => $std['typography'],
	);

	$options["{$key}-min-width"] = array(
		'name'       => _x( 'Min width', 'theme-options', 'the7mk2' ),
		'id'    => "{$key}-min-width",
		'std'   => "1px",
		'type'  => 'slider',
		'units' => 'px',
		'options' => array( 'min' => 0, 'max' => 200 ),
	);

	$options["{$key}-min-height"] = array(
		'name'       => _x( 'Min height', 'theme-options', 'the7mk2' ),
		'id'    => "{$key}-min-height",
		'std'   => "1px",
		'type'  => 'slider',
		'units' => 'px',
		'options' => array( 'min' => 0, 'max' => 200 ),
	);

	$options["{$key}-custom-icon-size"] = array(
		'name' => _x( 'Custom icon size', 'theme-options', 'the7mk2' ),
		'type' => 'checkbox',
		'id'   => "{$key}-custom-icon-size",
		'std'  => 0,
	);

	$options["{$key}-icon-size"] = array(
		'name'       => _x( 'Icon size', 'theme-options', 'the7mk2' ),
		'type'       => 'slider',
		'id'         => "{$key}-icon-size",
		'std'        => $std['icon-size'],
		'options'    => array( 'min' => 1, 'max' => 120 ),
		'dependency' => array(
			'field'    => "{$key}-custom-icon-size",
			'operator' => '==',
			'value'    => '1',
		),
	);

	$options["{$key}_padding"] = array(
		'id'   => "{$key}_padding",
		'name' => _x( 'Padding', 'theme-options', 'the7mk2' ),
		'type' => 'spacing',
		'std'  => $std['padding'],
		'single-unit' => true,
		'units'  => 'px|%|em',
	);

	$options["{$key}_border_radius"] = array(
		'name'  => _x( 'Border radius', 'theme-options', 'the7mk2' ),
		'id'    => "{$key}_border_radius",
		'std'   => $std['border-radius'],
		'type'  => 'number',
		'units' => 'px|%|em',
	);

	$options["{$key}_border_width"] = array(
		'name'  => _x( 'Border width', 'theme-options', 'the7mk2' ),
		'id'    => "{$key}_border_width",
		'std'   => $std['border-width'],
		'type'  => 'number',
		'units' => 'px',
	);
}


