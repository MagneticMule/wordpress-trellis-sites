<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

function the7_elementor_elements_widget_post_types() {
	$post_types = array_intersect_key(
		get_post_types( [], 'object' ),
		[
			'post'            => '',
			'dt_portfolio'    => '',
			'dt_team'         => '',
			'dt_testimonials' => '',
			'dt_gallery'      => '',
		]
	);

	$supported_post_types = [];
	foreach ( $post_types as $post_type ) {
		$supported_post_types[ $post_type->name ] = $post_type->label;
	}

	$supported_post_types['current_query'] = __( 'Archive (current query)', 'the7mk2' );

	return $supported_post_types;
}

function the7_get_public_post_types( $args = [] ) {
	$post_type_args = [
		// Default is the value $public.
		'show_in_nav_menus' => true,
	];

	// Keep for backwards compatibility
	if ( ! empty( $args['post_type'] ) ) {
		$post_type_args['name'] = $args['post_type'];
		unset( $args['post_type'] );
	}

	$post_type_args = wp_parse_args( $post_type_args, $args );

	$_post_types = get_post_types( $post_type_args, 'objects' );

	$post_types = [];

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	/**
	 * Public Post types
	 *
	 * Allow 3rd party plugins to filters the public post types the7 widgets should work on
	 *
	 * @param array $post_types The7 widgets supported public post types.
	 */
	return apply_filters( 'the7_get_public_post_types', $post_types );
}

function the7_get_taxonomies( $args = [], $output = 'names', $operator = 'and' ) {
	global $wp_taxonomies;

	$field = ( 'names' === $output ) ? 'name' : false;

	// Handle 'object_type' separately.
	if ( isset( $args['object_type'] ) ) {
		$object_type = (array) $args['object_type'];
		unset( $args['object_type'] );
	}

	$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, $operator );

	if ( isset( $object_type ) ) {
		foreach ( $taxonomies as $tax => $tax_data ) {
			if ( ! array_intersect( $object_type, $tax_data->object_type ) ) {
				unset( $taxonomies[ $tax ] );
			}
		}
	}

	if ( $field ) {
		$taxonomies = wp_list_pluck( $taxonomies, $field );
	}

	return $taxonomies;
}

/**
 * @return string
 */
function the7_elementor_get_message_about_disabled_post_type() {
	return '<p>' . esc_html__( 'The corresponding post type is disabled. Please make sure to 1) install The7 Elements plugin under The7 > Plugins and 2) enable desired post types under The7 > My The7, in the Settings section.', 'the7mk2' ) . '</p>';
}

/**
 * Return Elementor content width as a string.
 *
 * @return string
 */
function the7_elementor_get_content_width_string() {
	$content_width = \The7_Elementor_Compatibility::get_elementor_settings( 'container_width' );

	if ( isset( $content_width['size'], $content_width['unit'] ) ) {
		return $content_width['size'] . $content_width['unit'];
	}

	return (string) $content_width;
}

/**
 * Return description string for the wide columns control in widgets.
 *
 * @since 9.15.0
 *
 * @return string
 */
function the7_elementor_get_wide_columns_control_description() {
	// translators: %s: elementor content width.
	$description = __( 'Apply when browser width is bigger than %s ("Content Width" Elementor setting).', 'the7mk2' );

	return sprintf( $description, the7_elementor_get_content_width_string() );
}

/**
 * @since 9.4.0
 *
 * @return bool
 */
function the7_is_elementor_schemes_disabled() {
	$custom_colors_disabled      = get_option( 'elementor_disable_color_schemes' );
	$typography_schemes_disabled = get_option( 'elementor_disable_typography_schemes' );

	return the7_is_elementor3() || ( $custom_colors_disabled && $typography_schemes_disabled );
}
