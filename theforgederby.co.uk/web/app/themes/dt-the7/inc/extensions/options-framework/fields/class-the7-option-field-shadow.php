<?php
/**
 * Typography option field.
 * @package The7/Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7_Option_Field_Shadow extends The7_Option_Field_Composition_Abstract {

	/**
	 * Do this field need a wrap.
	 * @var bool
	 */
	protected $need_wrap = false;

	/**
	 * Sanitize shadow value.
	 *
	 * @param array $shadow Shadow value.
	 *
	 * @return array
	 */
	public static function sanitize( $shadow ) {
		$arr = wp_parse_args( (array) $shadow, wp_list_pluck( self::get_fields(), 'std' ) );

		if (isset($arr['position'] ) && $arr['position'] === 'outline'){
			$arr['position'] = '';
		}

		return $arr;
	}

	/**
	 * Return field html.
	 * @return string
	 */
	public function html() {
		$typography_defaults = self::get_fields();
		$typography_stored = wp_parse_args( $this->option['options'], $typography_defaults );

		$id = $this->option['id'];
		$output = '<div id="section-' . esc_attr( $id ) . '" class="section section-shadow">';

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
	public static function get_fields() {
		return [
			'color' => [
				'desc' => '',
				'name' => _x( 'Color', 'theme-options', 'the7mk2' ),
				'std'  => '#ffffff',
				'type' => 'alpha_color',
			],

			'horizontal' => [
				'name'    => _x( 'Horizontal', 'theme-options', 'the7mk2' ),
				'std'     => '0',
				'type'    => 'slider',
				'options' => [ 'min' => - 100, 'max' => 100 ],
				//'sanitize' => 'font_size',
			],
			'vertical'   => [
				'name'    => _x( 'Vertical', 'theme-options', 'the7mk2' ),
				'std'     => '0',
				'type'    => 'slider',
				'options' => [ 'min' => - 100, 'max' => 100 ],
			],

			'blur' => [
				'name'    => _x( 'Blur', 'theme-options', 'the7mk2' ),
				'std'     => '10',
				'type'    => 'slider',
				'options' => [ 'min' => 0, 'max' => 100 ],
			],

			'spread' => [
				'name'    => _x( 'Spread', 'theme-options', 'the7mk2' ),
				'std'     => '0',
				'type'    => 'slider',
				'options' => [ 'min' => 0, 'max' => 100 ],
			],

			'position' => [
				'name'    => _x( 'Position', 'theme-options', 'the7mk2' ),
				'type'    => 'radio',
				'class'   => 'small',
				'std'     => 'outline',
				'options' => array(
					'outline' => _x( 'Outline', 'theme-options', 'the7mk2' ),
					'inset'   => _x( 'Inset', 'theme-options', 'the7mk2' ),
				),
			],
		];
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
