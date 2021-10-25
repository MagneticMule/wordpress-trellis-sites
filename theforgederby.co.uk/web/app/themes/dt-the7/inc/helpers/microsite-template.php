<?php
/**
 * Microsite template helpers.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'presscore_microsite_hide_header' ) ) :

	// Microsite header classes filter
	function presscore_microsite_hide_header( $classes = array() ) {
		$classes[] = 'hidden-header';
		return $classes;
	}

endif;

if ( ! function_exists( 'presscore_microsite_disable_headers' ) ) :

	// Microsite header classes filter
	function presscore_microsite_disable_headers( $classes = array() ) {
		$classes[] = 'disable-headers';
		return $classes;
	}

endif;

if ( ! function_exists( 'presscore_microsite_top_bar_class_filter' ) ):

	/**
	 * Add custom classes to the top bar.
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function presscore_microsite_top_bar_class_filter( $classes ) {
		// Hide top bar.
		$classes[] = 'hide-top-bar';

		return $classes;
	}

endif;

if ( ! function_exists( 'presscore_microsite_logo_meta_convert' ) ) :

	/**
	 * Convert logo from page meta to theme options format and override $name option.
	 *
	 * @since  3.0.0
	 * @param  string $meta_id
	 * @param  array &$options
	 * @param  string $name
	 * @return boolean
	 */
	function presscore_microsite_logo_meta_convert( $meta_id, &$options, $name ) {
		global $post;

		$meta_logo = get_post_meta( $post->ID, $meta_id, true );
		if ( $meta_logo ) {
			$options[ $name ] = array( '', absint( $meta_logo[0] ) );

			return true;
		}

		// Empty logo.
		$options[ $name ] = array( '', 0 );

		return false;
	}

endif;

if ( ! function_exists( 'presscore_microsite_is_custom_logo' ) ) :

	/**
	 * @param string $meta_id
	 *
	 * @return bool
	 */
	function presscore_microsite_is_custom_logo( $meta_id ) {
		global $post;

		return ( 'custom' === get_post_meta( $post->ID, $meta_id, true ) ? true : false );
	}

endif;

if ( ! function_exists( 'presscore_microsite_theme_options_filter' ) ) :

	/**
	 * Microsite theme options filter.
	 *
	 * @param array $options
	 * @param string $name
	 *
	 * @return array
	 */
	function presscore_microsite_theme_options_filter( $options = array(), $name = '' ) {
		global $post;

		$field_prefix = '_dt_microsite_';

		/**
		 * Logo.
		 */
		$logo_options_meta = array(
			'header-logo_regular' => array(
				'value_meta_id' => 'main_logo_regular',
				'type_meta_id' => 'main_logo_type'
			),
		    'header-logo_hd' => array(
			    'value_meta_id' => 'main_logo_hd',
			    'type_meta_id' => 'main_logo_type'
		    ),
			'header-style-transparent-logo_regular' => array(
				'value_meta_id' => 'transparent_logo_regular',
				'type_meta_id' => 'transparent_logo_type'
			),
			'header-style-transparent-logo_hd' => array(
				'value_meta_id' => 'transparent_logo_hd',
				'type_meta_id' => 'transparent_logo_type'
			),
			'header-style-mixed-logo_regular' => array(
				'value_meta_id' => 'mixed_logo_regular',
				'type_meta_id' => 'mixed_logo_type'
			),
			'header-style-mixed-logo_hd' => array(
				'value_meta_id' => 'mixed_logo_hd',
				'type_meta_id' => 'mixed_logo_type'
			),
			'header-style-mixed-transparent-top_line-logo_regular' => array(
				'value_meta_id' => 'mixed_transparent_logo_regular',
				'type_meta_id' => 'mixed_transparent_logo_type'
			),
			'header-style-mixed-transparent-top_line-logo_hd' => array(
				'value_meta_id' => 'mixed_transparent_logo_hd',
				'type_meta_id' => 'mixed_transparent_logo_type'
			),
			'header-style-floating-logo_regular' => array(
				'value_meta_id' => 'floating_logo_regular',
				'type_meta_id' => 'floating_logo_type',
			),
			'header-style-floating-logo_hd' => array(
				'value_meta_id' => 'floating_logo_hd',
				'type_meta_id' => 'floating_logo_type'
			),
			'header-style-mobile-logo_regular' => array(
				'value_meta_id' => 'mobile_logo_regular',
				'type_meta_id' => 'mobile_logo_type'
			),
			'header-style-mobile-logo_hd' => array(
				'value_meta_id' => 'mobile_logo_hd',
				'type_meta_id' => 'mobile_logo_type'
			),
			'header-style-transparent-mobile-logo_regular' => array(
				'value_meta_id' => 'transparent_mobile_logo_regular',
				'type_meta_id' => 'transparent_mobile_logo_type'
			),
			'header-style-transparent-mobile-logo_hd' => array(
				'value_meta_id' => 'transparent_mobile_logo_hd',
				'type_meta_id' => 'transparent_mobile_logo_type'
			),
			'bottom_bar-logo_regular' => array(
				'value_meta_id' => 'bottom_logo_regular',
				'type_meta_id' => 'bottom_logo_type'
			),
			'bottom_bar-logo_hd' => array(
				'value_meta_id' => 'bottom_logo_hd',
				'type_meta_id' => 'bottom_logo_type'
			),
		);

		if ( array_key_exists( $name, $logo_options_meta ) ) {
			$logo_mode_meta_id = $field_prefix . $logo_options_meta[ $name ]['type_meta_id'];
			if ( presscore_microsite_is_custom_logo( $logo_mode_meta_id ) ) {
				$logo_value_meta_id = $field_prefix . $logo_options_meta[ $name ]['value_meta_id'];
				presscore_microsite_logo_meta_convert( $logo_value_meta_id, $options, $name );
			}
		}

		/**
		 * Logo mode.
		 */
		$logo_mode_meta = array(
			'header-style-floating-choose_logo' => 'floating_logo_type',
			'header-style-transparent-choose_logo' => 'transparent_logo_type',
		);

		if ( array_key_exists( $name, $logo_mode_meta ) ) {
			$logo_mode_meta_id = $field_prefix . $logo_mode_meta[ $name ];
			if ( presscore_microsite_is_custom_logo( $logo_mode_meta_id ) ) {
				$options[ $name ] = 'custom';
			}
		}

		/**
		 * Favicon.
		 */
		$favicon_meta = array(
			'general-favicon'    => 'favicon',
			'general-favicon_hd' => 'favicon_hd',
		);
		if ( array_key_exists( $name, $favicon_meta ) && presscore_microsite_is_custom_logo( "{$field_prefix}favicon_type" ) ) {
			$favicon = get_post_meta( $post->ID, "{$field_prefix}{$favicon_meta[$name]}", true );
			if ( $favicon ) {
				$icon_image = wp_get_attachment_image_src( $favicon[0], 'full' );

				if ( $icon_image ) {
					$options[ $name ] = $icon_image[0];
				}
			}
		}

		return $options;
	}

endif;

if ( ! function_exists( 'presscore_microsite_add_options_filters' ) ) :

	function presscore_microsite_add_options_filters() {
		global $post;

		if ( ! $post || ! presscore_is_microsite() ) {
			return;
		}

		// add filter for theme options here
		add_filter( 'dt_of_get_option', 'presscore_microsite_theme_options_filter', 15, 2 );
	}

	add_action( 'presscore_config_before_base_init', 'presscore_microsite_add_options_filters' );

endif;

if ( ! function_exists( 'presscore_microsite_add_menu_hooks' ) ) {

	function presscore_microsite_add_menu_hooks() {
		$post_id = presscore_config()->get( 'post_id' );

		if ( ! $post_id || ! in_array( get_page_template_slug( $post_id ), [ 'template-microsite.php', 'elementor_header_footer', '' ], true ) ) {
			return;
		}

		$menus_override_handler = new The7_Header_Menus_Override_Handler( $post_id );
		$menus_override_handler->bootstrap();
	}

	add_action( 'presscore_config_base_init', 'presscore_microsite_add_menu_hooks' );

}

if ( ! function_exists( 'presscore_microsite_setup' ) ) :

	function presscore_microsite_setup() {
		global $post;

		if ( ! $post || ! presscore_is_microsite() ) {
			return;
		}

		// Hide template parts.
		$config             = presscore_config();
		$hidden_parts       = get_post_meta( $post->ID, '_dt_microsite_hidden_parts', false );
		$hide_header        = in_array( 'header', $hidden_parts, true );
		$hide_floating_menu = in_array( 'floating_menu', $hidden_parts, true );
		$hide_top_bar       = in_array( 'top_bar', $hidden_parts, true );

		if ( $hide_header ) {
			add_filter( 'body_class', 'presscore_microsite_hide_header' );

			if ( $hide_floating_menu ) {
				add_filter( 'presscore_show_header', '__return_false' );
				add_filter( 'body_class', 'presscore_microsite_disable_headers' );
			}
		}

		// Hide top bar.
		if ( $hide_top_bar ) {
			add_filter( 'presscore_top_bar_class', 'presscore_microsite_top_bar_class_filter' );
		}

		// hide bottom bar
		if ( in_array( 'bottom_bar', $hidden_parts ) ) {
			add_filter( 'presscore_show_bottom_bar', '__return_false' );
		} else {
			add_filter( 'presscore_show_bottom_bar', '__return_true' );
		}

		// hide content
		if ( in_array( 'content', $hidden_parts ) ) {
			add_filter( 'presscore_is_content_visible', '__return_false' );
		}

		$loading = get_post_meta( $post->ID, '_dt_microsite_page_loading', true );
		$config->set( 'template.beautiful_loading.enabled', ( $loading ? $loading : 'enabled' ) );

		$layout = get_post_meta( $post->ID, '_dt_microsite_page_layout', true );
		$config->set( 'template.layout', ( $layout ? $layout : 'wide' ) );

		$config->set( 'header.floating_navigation.enabled', ! $hide_floating_menu );
	}

	add_action( 'presscore_config_base_init', 'presscore_microsite_setup' );

endif;

if ( ! function_exists( 'presscore_is_microsite' ) ) :

	/**
	 * @since 3.0.0
	 * @return boolean
	 */
	function presscore_is_microsite() {
		return ( 'microsite' === presscore_config()->get( 'template' ) );
	}

endif;

/**
 * Used to populate 'select' options in 'Menus' metabox and elementor page settings tab.
 *
 * @return array
 */
function the7_microsite_get_nav_menu_options_for_select() {
	$options = [ -1 => _x( 'Menu based on public pages', 'backend metabox', 'the7mk2' ) ];
	$nav_menus = wp_get_nav_menus();
	foreach ( $nav_menus as $nav_menu ) {
		$options[ $nav_menu->term_id ] = wp_html_excerpt( $nav_menu->name, 40, '&hellip;' );
	}

	return $options;
}
function presscore_microsite_disable_headers( $classes = array() ) {
	$classes[] = 'disable-headers';
	return array_diff( $classes, [ 'sticky-mobile-header' ] );
}
