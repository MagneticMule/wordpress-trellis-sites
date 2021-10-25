<?php

defined( 'ABSPATH' ) || exit;

class The7_Option_Field_Web_Fonts extends The7_Option_Field_Composition_Abstract {

	/**
	 * Do this field need a wrap.
	 * @var bool
	 */
	protected $need_wrap = false;

	public function html() {
		$font_obj = new The7_Less_Vars_Value_Font( $this->val );
		$font_val['font_family'] = $font_obj->get_family();
		$font_val['font_style'] = $font_obj->get_style();
		$font_val['font_weight'] = $font_obj->get_weight();

		$id = $this->option['id'];
		$output = '<div id="section-' . esc_attr( $id ) . '" class="section section-web-fonts">';
		$typography_defaults = self::get_webfonts_fields();
		foreach ( $typography_defaults as $field => $default_declaration ) {
			$field_declaration = $default_declaration;
			if ('font_family' === $field) {
				$intersection = array_intersect_key( $this->option , array( 'std' => '', 'fonts' =>'' ));
				$field_declaration = wp_parse_args(  $intersection, $default_declaration );
			}

			$field_declaration['id'] = $field;

			$field_object = $this->interface->get_field_object( $this->option_name . '[' . $field . ']', $field_declaration, $font_val);

			if ($field_object == null){
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
	 * Sanitize  value.
	 *
	 * @param array|String $font Font value, can contain font weight and style.
	 *
	 * @return String
	 */
	public static function sanitize( $font ) {
		if ( is_array( $font ) ) {
			$defaults =  array(
				'font_family' => '',
				'font_weight' => '',
				'font_style' => '',
			);
			$parsed_font = wp_parse_args($font, $defaults);
			return array_map( 'esc_attr', $parsed_font );
		}

		return esc_attr( $font );
	}

	/**
	 * Encode sanitized fonts array.
	 *
	 * @param array $font
	 *
	 * @return string
	 */
	public static function encode( $font ) {
		if (is_string($font)){
			return $font;
		}
		if ( ! $font['font_weight'] && $font['font_style'] ) {
			return $font['font_family'] . ":" . $font['font_style'];
		}
		else if(! $font['font_weight'] && !$font['font_style'] ){
			return $font['font_family'];
		}

		return $font['font_family'] . ":" . $font['font_weight'] . $font['font_style'];
	}

	public static function get_webfonts_fields() {
		return array(
			'font_family'            => array(
				'name'  => _x( 'Font family', 'theme-options', 'the7mk2' ),
				'type'  => 'web_fonts_wrapped',
				'std'   => 'Open Sans',
				'fonts' => 'all',
				'class' => 'font-family',
			),
			'font_weight'         => array(
				'name'    => _x( 'Weight', 'theme-options', 'the7mk2' ),
				'type'    => 'select',
				'std'     => '',
				'options' => array(
					''    => 'Default',
					'100' => '100',
					'200' => '200',
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
					'900' => '900',
					'normal' => 'Normal',
					'bold' => 'Bold',
				),
				'class'   => 'mini',
			),
			'font_style'         => array(
				'name'    => _x( 'Style', 'theme-options', 'the7mk2' ),
				'type'    => 'select',
				'std'     => '',
				'options' => array(
					''    => 'Default',
					'normal' => __( 'Normal', 'theme-options', 'the7mk2' ),
					'italic' => __( 'Italic', 'theme-options', 'the7mk2' ),
					'oblique' => __( 'Oblique','theme-options', 'the7mk2' ),
				),
				'class'   => 'mini',
			),
		);
	}
}
