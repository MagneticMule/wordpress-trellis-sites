<?php
/**
 * Less related functions.
 *
 * @package the7
 * @since   1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function returns less vars array to use with phpless.
 *
 * @param The7_Less_Vars_Manager $less_vars_manager Less vars manager or null.
 *
 * @return array
 */
function presscore_compile_less_vars( $less_vars_manager = null ) {
	if ( $less_vars_manager === null ) {
		$less_vars_manager = the7_get_new_less_vars_manager();
	}

	do_action( 'presscore_setup_less_vars', $less_vars_manager );

	return apply_filters( 'presscore_compiled_less_vars', $less_vars_manager->get_vars() );
}

if ( ! function_exists( 'the7_get_new_less_vars_manager' ) ) {

	/**
	 * Factory for The7_Less_Vars_Manager.
	 *
	 * @since 5.7.0
	 *
	 * @return The7_Less_Vars_Manager
	 */
	function the7_get_new_less_vars_manager() {
		return new The7_Less_Vars_Manager( new The7_Less_Vars_Storage(), new The7_Less_Vars_Factory() );
	}
}

if ( ! function_exists( 'the7_get_new_shortcode_less_vars_manager' ) ) {

	/**
	 * Factory function for shortcode less manager.
	 *
	 * @since 7.7.0
	 *
	 * @return The7_Less_Vars_Shortcode_Manager
	 */
	function the7_get_new_shortcode_less_vars_manager() {
		return new The7_Less_Vars_Shortcode_Manager( new Presscore_Lib_SimpleBag(), new The7_Less_Vars_Factory() );
	}

}

/**
 * Helper that returns array of accent less vars.
 *
 * @since 6.6.0
 *
 * @param  The7_Less_Vars_Manager_Interface $less_vars
 *
 * @return array Returns array like array( 'first-color', 'seconf-color' )
 */
function the7_less_get_accent_colors( The7_Less_Vars_Manager_Interface $less_vars ) {
	$accent_less_vars_names = array( 'accent-bg-color', 'accent-bg-color-2' );
	switch ( of_get_option( 'general-accent_color_mode' ) ) {
		case 'gradient':
			$gradient_obj = the7_less_create_gradient_obj( of_get_option( 'general-accent_bg_color_gradient' ) );
			list( $first_color, $gradient ) = the7_less_prepare_gradient_var( $gradient_obj );
			$accent_colors = array( $first_color, $gradient_obj );
			$less_vars->add_rgba_color( $accent_less_vars_names[0], $first_color );
			$less_vars->add_keyword( $accent_less_vars_names[1], $gradient );
			break;
		case 'color':
		default:
			$accent_colors = array( of_get_option( 'general-accent_bg_color' ), the7_less_create_gradient_obj() );
			$less_vars->add_hex_color( $accent_less_vars_names, $accent_colors );
	}

	return $accent_colors;
}

/**
 * Prepare gradient string to be exported as a less var.
 *
 * @since 6.6.0
 *
 * @param string|array|The7_Less_Gradient $gradient
 *
 * @return array
 */
function the7_less_prepare_gradient_var( $gradient ) {
	if ( is_a( $gradient, 'The7_Less_Gradient' ) ) {
		$gradient_obj = $gradient;
	} else {
		$gradient_obj = the7_less_create_gradient_obj( $gradient );
	}

	return array(
		$gradient_obj->get_color_stop( 1 )->get_color(),
		$gradient_obj->get_string(),
	);
}

/**
 * Return new The7_Less_Gradient object.
 *
 * @since 6.6.0
 *
 * @param string|array $gradient
 *
 * @return The7_Less_Gradient
 */
function the7_less_create_gradient_obj( $gradient = null ) {
	if ( is_array( $gradient ) && isset( $gradient[0], $gradient[1] ) ) {
		$gradient = "135deg|{$gradient[0]} 30%|{$gradient[1]} 100%";
	}

	return new The7_Less_Gradient( $gradient );
}
/**
 * @param array $array
 *
 * @return array
 */
function the7_array_filter_non_empty_string( array $array ) {
	return array_filter( $array, static function($var) {
		return $var !== '';
	} );
}


/**
 * Helper function to fill less for responsive fonts
 *
 * @since 9.5.1
 *
 * @param  The7_Less_Vars_Manager_Interface $less_vars
 *
 * @return none
 */
function the7_less_add_responsive_font( The7_Less_Vars_Manager_Interface $less_vars, $opt_name, $id ) {
	$devices = [
		The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP,
		The7_Option_Field_Responsive_Option::RESPONSIVE_TABLET,
		The7_Option_Field_Responsive_Option::RESPONSIVE_MOBILE,
	];

	$typography = The7_Option_Field_Typography::sanitize( of_get_option( $opt_name ) );
	$less_vars->add_font( array(
		"{$id}-font-family",
		"{$id}-font-weight",
		"{$id}-font-style",
	), $typography['font_family'] );
	foreach ($devices as $device){
		if ( isset($typography['responsive_font_size'][$device]) ) {
			$less_vars->add_unitized_number( "{$id}-font-size-{$device}", $typography['responsive_font_size'][$device] );
		}
		if ( isset($typography['responsive_line_height'][$device]) ) {
			$less_vars->add_unitized_number( "{$id}-line-height-{$device}", $typography['responsive_line_height'][$device] );
		}
	}
	$less_vars->add_keyword( "{$id}-text-transform", $typography['text_transform'] );
}

/**
 *  Helper function to fill less for normal fonts
 *
 * @since 9.5.1
 *
 * @param  The7_Less_Vars_Manager_Interface $less_vars
 *
 * @return none
 */
function the7_less_add_font( The7_Less_Vars_Manager_Interface $less_vars, $opt_name, $id ) {
	$typography = The7_Option_Field_Typography::sanitize( of_get_option($opt_name) );
	$less_vars->add_font( array(
		"{$id}-font-family",
		"{$id}-font-weight",
		"{$id}-font-style",
	), $typography['font_family'] );
	$less_vars->add_pixel_number( "{$id}-font-size", $typography['font_size'] );
	$less_vars->add_pixel_number( "{$id}-line-height", $typography['line_height'] );
	$less_vars->add_keyword( "{$id}-text-transform", $typography['text_transform'] );
}

/**
 *  Helper function to fill less for button size
 *
 * @since 9.5.1
 *
 * @param  The7_Less_Vars_Manager_Interface $less_vars
 *
 * @return none
 */
function the7_less_add_button_size( The7_Less_Vars_Manager_Interface $less_vars, $opt_prefix_name, $id) {
	$button_typography = The7_Option_Field_Typography::sanitize( of_get_option( "{$opt_prefix_name}-typography" ) );
	$less_vars->add_font(
		[
			"dt-{$id}-font-family",
			"dt-{$id}-font-weight",
			"dt-{$id}-font-style",
		],
		$button_typography['font_family']
	);
	$less_vars->add_paddings(
		[
			"{$id}-padding-top",
			"{$id}-padding-right",
			"{$id}-padding-bottom",
			"{$id}-padding-left",
		],
		of_get_option( "{$opt_prefix_name}_padding" ),
		'px|%|em'
	);
	$less_vars->add_keyword( "dt-{$id}-text-transform", $button_typography['text_transform'] );
	$less_vars->add_pixel_number( "dt-{$id}-font-size", $button_typography['font_size'] );
	$less_vars->add_pixel_number( "dt-{$id}-min-height", of_get_option( "{$opt_prefix_name}-min-height") );
	$less_vars->add_pixel_number( "dt-{$id}-min-width", of_get_option( "{$opt_prefix_name}-min-width") );

	if ( the7_is_elementor_buttons_integration_enabled() ) {
		$less_vars->add_number( "dt-{$id}-line-height", 1 );
	} else {
		$less_vars->add_pixel_number( "dt-{$id}-line-height", (int) $button_typography['font_size'] + 2 );
	}

	$less_vars->add_pixel_number( "dt-{$id}-letter-spacing", $button_typography['letter_spacing'] );

	if (of_get_option( "{$opt_prefix_name}-custom-icon-size" )) {
		$icon_size = of_get_option( "{$opt_prefix_name}-icon-size" );
	}
	else {
		$icon_size = $button_typography['font_size'];
	}
	$less_vars->add_pixel_number( "dt-{$id}-icon-size", $icon_size );
	$less_vars->add_unitized_number( "dt-{$id}-border-radius", of_get_option( "{$opt_prefix_name}_border_radius" ) );
	$less_vars->add_pixel_number( "dt-{$id}-border-width", of_get_option( "{$opt_prefix_name}_border_width" ) );
}

/**
 *  Helper function to fill less for shadows
 *
 * @since 9.5.1
 *
 * @param  The7_Less_Vars_Manager_Interface $less_vars
 *
 * @return none
 */
function the7_less_add_shadow( The7_Less_Vars_Manager_Interface $less_vars, $opt_name, $id) {
	$buttons_shadow = The7_Option_Field_Shadow::sanitize( of_get_option( $opt_name ));
	$less_vars->add_rgba_color( "{$id}-color", $buttons_shadow['color']);
	$less_vars->add_pixel_number( "{$id}-horizontal", $buttons_shadow['horizontal']);
	$less_vars->add_pixel_number( "{$id}-vertical", $buttons_shadow['vertical']);
	$less_vars->add_pixel_number( "{$id}-blur", $buttons_shadow['blur']);
	$less_vars->add_pixel_number( "{$id}-spread", $buttons_shadow['spread']);
	$less_vars->add_keyword( "{$id}-position", $buttons_shadow['position']);
}