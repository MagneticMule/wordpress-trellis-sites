<?php
/**
 * Typography option field.
 * @package The7/Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7_Option_Field_Font_Sizes extends The7_Option_Field_Composition_Abstract {

	/**
	 * Do this field need a wrap.
	 * @var bool
	 */
	protected $need_wrap = false;

	/**
	 * Sanitize value.
	 *
	 * @param array $typography Typography value.
	 *
	 * @return array
	 */
	public static function sanitize( $val ) {
		return wp_parse_args( (array) $val, wp_list_pluck( self::get_fields(), 'std' ) );
	}

	/**
	 * Return typography fields fields definition.
	 * @return array
	 */
	public static function get_fields() {
		return array(
			'font_preview' => array(
				'name' => _x( 'Preview', 'theme-options', 'the7mk2' ),
				'type' => 'web_fonts_preview',
				'std'  => 'Open Sans',
				'class' => 'font-family'
			),
			'font_size'    => array(
				'name'     => _x( 'Font size', 'theme-options', 'the7mk2' ),
				'std'      => '20px',
				'type'     => 'slider',
				'units'    => 'px|em|rem|vw',
				'class'    => 'font-size',
				'range'    => [
					'vw'  => [
						'min'   => 0.1,
						'max'   => 10,
						'step'  => 0.1,
						'value' => 2,
					],
					'rem' => [
						'min'   => 0.1,
						'max'   => 10,
						'step'  => 0.1,
						'value' => 2,
					],
					'px'  => [
						'min'   => 1,
						'max'   => 120,
						'step'  => 1,
						'value' => 20,
					],
					'em'  => [
						'min'   => 0.1,
						'max'   => 10,
						'step'  => 0.1,
						'value' => 2,
					],
				],
			),

			'line_height' => array(
				'name'     => _x( 'Line height', 'theme-options', 'the7mk2' ),
				'std'      => '30px',
				'type'     => 'slider',
				'units'    => 'px|em',
				'class'    => 'line-height',
				'range'    => [
					'px' => [
						'min'   => 1,
						'max'   => 120,
						'step'  => 1,
						'value' => 20,
					],
					'em' => [
						'min'   => 0.1,
						'max'   => 10,
						'step'  => 0.1,
						'value' => 1,
					],
				],
			),
		);
	}

	/**
	 * Return field html.
	 * @return string
	 */
	public function html() {
		$defaults = self::get_fields();
		$intersection = array_intersect_key( $defaults, $this->option['std'] );
		$typography_stored = wp_parse_args( $this->option['options'], $intersection );

		$id = $this->option['id'];

		$class = "";
		if ( isset( $this->option['class'] ) ) {
			$class .= ' ' . $this->option['class'];
		}
		if ( isset( $this->option['type'] ) ) {
			$class .= ' section-' . $this->option['type'];
		}

		$output = '<div id="section-' . esc_attr( $id ) . '" class="section section-typography' . $class . '">';


		$default_values = wp_parse_args( $this->option['std'], wp_list_pluck( $defaults, 'std' ) );
		$field_value = wp_parse_args( (array) $this->val, $default_values );
		foreach ( $defaults as $field => $default_declaration ) {
			if ( empty( $typography_stored[ $field ] ) ) {
				continue;
			}
			$field_declaration = wp_parse_args( $typography_stored[ $field ], $default_declaration );
			if ( isset( $this->option['names'][ $field ] ) ) {
				$field_declaration['name'] = $this->option['names'][ $field ];
			}
			$field_declaration['std'] = $default_values[ $field ];
			$field_declaration['id'] = $field;
			$field_object = $this->interface->get_field_object(  $this->option_name . '[' . $field . ']', $field_declaration, $field_value );
			if ( $field_object == null ) {
				continue;
			}
			$wrapped_output = '';
			if ( $field_object->need_wrap() ) {
				$wrapped_output .= $this->interface->wrap_option( $field_object );
			} else {
				$wrapped_output .= $field_object->html();
			}

			// Fix id's.
			$output .= str_replace( array(
				"section-$field",
				"id=\"$field",
			), array(
				"section-$id-$field",
				"id=\"$id-$field",
			), $wrapped_output );
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Normalize field definition.
	 *
	 * @param array $option Field definition array.
	 *
	 * @return array
	 */
	protected function normalize_option( $option ) {
		$option = wp_parse_args( $option, array(
			'std'     => array(),
			'options' => array(),
		) );

		$option['std'] = (array) $option['std'];
		$option['options'] = (array) $option['options'];

		return $option;
	}
}
