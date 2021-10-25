<?php
/**
 * Typography option field.
 * @package The7/Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7_Option_Field_Typography extends The7_Option_Field_Composition_Abstract {

	/**
	 * Do this field need a wrap.
	 * @var bool
	 */
	protected $need_wrap = false;

	/**
	 * Return field html.
	 * @return string
	 */
	public function html() {
		$typography_defaults = self::get_typography_fields();
		$intersection = array_intersect_key( $typography_defaults, $this->option['std'] );
		$typography_stored = wp_parse_args( $this->option['options'], $intersection );

		$id = $this->option['id'];
		$output = '<div id="section-' . esc_attr( $id ) . '" class="section section-typography">';

		$default_values = wp_parse_args( $this->option['std'], wp_list_pluck( $typography_defaults, 'std' ) );
		$field_value = wp_parse_args( (array) $this->val, $default_values );
		foreach ( $typography_defaults as $field => $default_declaration ) {
			if ( empty( $typography_stored[ $field ] ) ) {
				continue;
			}
			$field_declaration = wp_parse_args( $typography_stored[ $field ], $default_declaration );
			$field_declaration['std'] = $default_values[ $field ];
			$field_declaration['id'] = $field;
			$field_object = $this->interface->get_field_object( $this->option_name . '[' . $field . ']', $field_declaration, $field_value );
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
	 * Return typography fields fields definition.
	 * @return array
	 */
	public static function get_typography_fields() {
		return array(
			'font_family'            => array(
				'name'  => _x( 'Font family', 'theme-options', 'the7mk2' ),
				'type'  => 'web_fonts',
				'std'   => 'Open Sans',
				'fonts' => 'all',
				'class' => 'font-family',
			),
			'responsive_font_size'   => array(
				'type'   => 'responsive_option',
				'std'    => array(
					The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP => "20px",
				),
				'option' => array(
					'name'     => _x( 'Font size', 'theme-options', 'the7mk2' ),
					'type'     => 'slider',
					'units'    => 'px|em|rem|vw',
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
							'max'   => 100,
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
					'class'    => 'font-size',
				),
			),
			'responsive_line_height' => array(
				'type'   => 'responsive_option',
				'std'    => array(
					The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP => "30px",
				),
				'option' => array(
					'name'     => _x( 'Line height', 'theme-options', 'the7mk2' ),
					'type'     => 'slider',
					'units'    => 'px|em',
					'range'    => [
						'px' => [
							'min'   => 1,
							'max'   => 100,
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
					'class'    => 'line-height',
				),
			),
			'text_transform'         => array(
				'name'    => _x( 'Text transformation', 'theme-options', 'the7mk2' ),
				'type'    => 'select',
				'std'     => 'none',
				'options' => array(
					'none'       => 'None',
					'uppercase'  => 'Uppercase',
					'lowercase'  => 'Lowercase',
					'capitalize' => 'Capitalize',
				),
				'class'   => 'mini text-transform',
			),
			//for non responsive fields, backward compat
			'font_size'              => array(
				'name'     => _x( 'Font size', 'theme-options', 'the7mk2' ),
				'std'      => 20,
				'type'     => 'slider',
				'options'  => array(
					'min' => 1,
					'max' => 120,
				),
				'sanitize' => 'font_size',
				'class'    => 'font-size',
			),
			'line_height'            => array(
				'name'     => _x( 'Line height', 'theme-options', 'the7mk2' ),
				'std'      => 30,
				'type'     => 'slider',
				'options'  => array(
					'min' => 1,
					'max' => 120,
				),
				'sanitize' => 'font_size',
				'class'    => 'line-height',
			),
			'letter_spacing'            => array(
				'name'     => _x( 'Letter-spacing', 'theme-options', 'the7mk2' ),
				'std'      => 0,
				'type'     => 'slider',
				'options'  => array(
					'min' => -5,
					'max' => 10,
					'step'  => 0.1,
				),
				'class'    => 'letter-spacing',
			),
		);
	}

	/**
	 * Sanitize typography value.
	 *
	 * @param array $typography Typography value.
	 *
	 * @return array
	 */
	public static function sanitize( $typography ) {
		return wp_parse_args( (array) $typography, wp_list_pluck( self::get_typography_fields(), 'std' ) );
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
