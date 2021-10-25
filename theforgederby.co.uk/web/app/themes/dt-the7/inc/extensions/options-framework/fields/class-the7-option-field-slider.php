<?php

defined( 'ABSPATH' ) || exit;

class The7_Option_Field_Slider extends The7_Option_Field_Abstract {

	/**
	 * Encode decoded number array.
	 *
	 * @param array $number
	 *
	 * @return string
	 */
	public static function encode( $number ) {
		return $number['val'] . $number['units'];
	}

	public function html() {

		$slider_opts = array(
			'max'   => isset( $this->option['options']['max'] ) ? $this->option['options']['max'] : 100,
			'min'   => isset( $this->option['options']['min'] ) ? $this->option['options']['min'] : 0,
			'step'  => isset( $this->option['options']['step'] ) ? $this->option['options']['step'] : 1,
			'value' => isset( $this->val ) ? $this->val : 100,
		);

		$slider_atts = array(
			'type' => "text",
			'name' => $this->option_name,
		);


		$slider_val = $slider_opts['value'];

		// Units HTML.
		$units_html = '';
		if ( isset( $this->option['units'] ) ) {
			$units = $this->option['units'];
			$number = self::sanitize( $this->val, $units, $slider_opts );
			$cur_units = $number['units'];
			$slider_val = $number['val'];

			//sanitize value for specific unit
			if ( isset( $this->option['range'][ $cur_units ] ) ) {
				$cur_range = $this->option['range'][ $cur_units ];
				$number = self::sanitize( $this->val, $units, $cur_range );
				$slider_opts['max'] = isset( $cur_range['max'] ) ? $cur_range['max'] : 100;
				$slider_opts['min'] = isset( $cur_range['min'] ) ? $cur_range['min'] : 0;
				$slider_opts['step'] = isset( $cur_range['step'] ) ? $cur_range['step'] : 1;
				$slider_val = $number['val'];
			}

			$decoded_units = self::decode_units( $units );
			$units_wrap_class = 'dt_spacing-units-wrap';
			$units_name = $this->option_name ? "{$this->option_name}[units]" : '';
			if ( count( $decoded_units ) > 1 ) {
				$units_wrap_class .= ' select';
				foreach ( $decoded_units as $u ) {
					$units_str = '';
					if ( isset( $this->option['range'][ $u ] ) ) {
						$units_opt = $this->option['range'][ $u ];
						$units_opts = array(
							'max'   => isset( $units_opt['max'] ) ? $units_opt['max'] : 100,
							'min'   => isset( $units_opt['min'] ) ? $units_opt['min'] : 0,
							'step'  => isset( $units_opt['step'] ) ? $units_opt['step'] : 1,
							'value' => isset( $units_opt['value'] ) ? $units_opt['value'] : 10,
						);

						foreach ( $units_opts as $name => $opt_val ) {
							$units_str .= ' data-' . $name . '="' . esc_attr( $opt_val ) . '"';
						}
					}

					$units_html .= '<option value="' . esc_attr( $u ) . '" ' . selected( $u, $cur_units, false ) . $units_str . '>' . esc_html( $u ) . '</option>';
				}
				$units_html = '<select class="dt_spacing-units" name="' . $units_name . '" data-units="' . esc_attr( $cur_units ) . '">' . $units_html . '</select>';
			} else {
				$units_html = '<span class="dt_spacing-units" data-units="' . esc_attr( $cur_units ) . '"><input type="hidden" name="' . $units_name . '" value="' . esc_attr( $cur_units ) . '"/>' . esc_html( $cur_units ) . '</span>';
			}
			$units_html = '<div class="' . $units_wrap_class . '">' . $units_html . '</div>';

			$slider_atts['name'] = $this->option_name ? "{$this->option_name}[val]" : '';
		}


		$classes = array( 'of-slider' );
		if ( ! empty( $this->option['options']['java_hide_if_not_max'] ) ) {
			$classes[] = 'of-js-hider';
			$classes[] = 'js-hide-if-not-max';
		} else if ( ! empty( $this->option['options']['java_hide_global_not_max'] ) ) {
			$classes[] = 'of-js-hider-global';
			$classes[] = 'js-hide-if-not-max';
		}
		$classes = implode( ' ', $classes );

		$output = '<div class="' . $classes . '"></div>';

		$str = '';

		$slider_atts['value'] = esc_attr( $slider_val );
		$slider_opts['value'] = $slider_atts['value'];

		foreach ( $slider_opts as $name => $opt_val ) {
			$str .= ' data-' . $name . '="' . esc_attr( $opt_val ) . '"';
		}


		$input_atts = '';
		foreach ( $slider_atts as $att => $val ) {
			if ( is_bool( $val ) ) {
				$input_atts .= $att ? $att : '';
			} else {
				$input_atts .= " {$att}=\"{$val}\"";
			}
		}

		$output .= '<input class="of-slider-value"' . $str . ' ' . $input_atts . '/>';
		$output .= $units_html;

		return $output;
	}

	/**
	 * Splits $units string to array.
	 *
	 * @param array|string $units
	 *
	 * @return array
	 */
	public static function decode_units( $units ) {
		$decoded = array();
		if ( ! is_array( $units ) ) {
			$decoded = array_map( 'trim', explode( '|', $units ) );
		}

		return $decoded;
	}

	/**
	 * Sanitize number string. Returns array of sanitized values in format array( 'val' => '', 'units' => '' ).
	 *
	 * @param string       $number Number string with units.
	 * @param array|string $units  Supported units .
	 * @param null|array   $min    defines Minimal and Maxumal values.
	 *
	 * @return array
	 */
	public static function sanitize( $number, $units, $range = null ) {
		$decoded_number = self::decode( $number );
		if (is_string($units)) {
			$units = self::decode_units( $units );
		}
		$cur_units = current( $units );
		if ( in_array( $decoded_number['units'], $units, true ) ) {
			$cur_units = $decoded_number['units'];
		}
		$cur_val = $decoded_number['val'];

		if ( $range !== null && $cur_val !== '' ) {
			if ( isset( $range[ $cur_units ]['min'] ) ) {
				$cur_val = max( $cur_val, isset( $range[ $cur_units ]['min'] ) );
			} else {
				$cur_val = max( $cur_val, isset( $range['min'] ) );
			}
			if ( isset( $range[ $cur_units ]['max'] ) ) {
				$cur_val = min( $cur_val, isset( $range[ $cur_units ]['max'] ) );
			} else {
				$cur_val = min( $cur_val, isset( $range['max'] ) );
			}
		}

		return array(
			'val'   => $cur_val,
			'units' => $cur_units,
		);
	}

	/**
	 * Split number string to array( 'val' => '', 'units' => '' ).
	 *
	 * @param string $slider_value
	 *
	 * @return array
	 */
	public static function decode( $slider_value ) {
		preg_match( '/(-?\d+\.?\d*)(.*)/', $slider_value, $matches );
		$cur_val = '';
		if ( isset( $matches[1] ) ) {
			$cur_val = $matches[1];
		}
		$cur_units = '';
		if ( ! empty( $matches[2] ) ) {
			$cur_units = $matches[2];
		}

		return array(
			'val'   => $cur_val,
			'units' => $cur_units,
		);
	}
}