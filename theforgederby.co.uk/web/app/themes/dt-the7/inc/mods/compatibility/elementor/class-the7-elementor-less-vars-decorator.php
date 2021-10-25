<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use \The7_Less_Vars_Manager_Interface;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Less_Vars_Decorator implements The7_Elementor_Less_Vars_Decorator_Interface {

	protected $device = '';

	protected $device_suffix = '';

	protected $less_vars_object;

	public function __construct( The7_Less_Vars_Manager_Interface $less_vars_object ) {
		$this->less_vars_object = $less_vars_object;
	}

	public function add_set_of_responsive_keywords( $base_var, $values, $defaults = [] ) {
		$sufixes = [
			'',
			'-tablet',
			'-mobile',
		];
		foreach ( $sufixes as $i => $sufix ) {
			if ( isset( $values[ $i ] ) ) {
				$value = $this->maybe_transform_value( $values[ $i ] );
				if ( $value === '' && isset( $defaults[ $i ] ) ) {
					$value = $defaults[ $i ];
				}
				$this->add_keyword( $base_var . $sufix, $value );
			}
		}
	}

	/**
	 * @param array $items
	 */
	public function import( $items ) {
		$this->less_vars_object->import( $items );
	}

	/**
	 * @param string $var
	 *
	 * @return mixed
	 */
	public function get_var( $var ) {
		return $this->less_vars_object->get_var( $var );
	}

	/**
	 * @return array
	 */
	public function get_vars() {
		return $this->less_vars_object->get_vars();
	}

	/**
	 * @param array       $var
	 * @param array       $value
	 * @param string|null $wrap
	 */
	public function add_image( $var, $value, $wrap = null ) {
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_image( $var, $value, $wrap );
	}

	/**
	 * @param string|array $var
	 * @param string|array $value
	 * @param string|null  $wrap
	 */
	public function add_hex_color( $var, $value, $wrap = null ) {
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_hex_color( $var, $value, $wrap );
	}

	/**
	 * @param string|array $var
	 * @param string       $value
	 * @param string|null  $wrap
	 */
	public function add_rgb_color( $var, $value, $wrap = null ) {
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_rgb_color( $var, $value, $wrap );
	}

	/**
	 * @param string|array $var
	 * @param string|array $value
	 * @param int|null     $opacity
	 * @param string|null  $wrap
	 */
	public function add_rgba_color( $var, $value, $opacity = null, $wrap = null ) {
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_rgba_color( $var, $value, $opacity, $wrap );
	}

	/**
	 * @param      $var
	 * @param      $value
	 * @param null $wrap
	 */
	public function add_pixel_number( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_pixel_number( $var, $value, $wrap );
	}

	/**
	 * @param      $var
	 * @param      $value
	 * @param null $wrap
	 */
	public function add_percent_number( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_percent_number( $var, $value, $wrap );
	}

	/**
	 * Register less var in pixels or percents.
	 *
	 * @param string      $var
	 * @param string      $value
	 * @param string|null $wrap
	 */
	public function add_pixel_or_percent_number( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_pixel_or_percent_number( $var, $value, $wrap );
	}

	/**
	 * Register less var in pixels or percents.
	 *
	 * @param string      $var
	 * @param string      $value
	 * @param string|null $wrap
	 */
	public function add_unitized_number( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_unitized_number( $var, $value, $wrap );
	}

	/**
	 * @param      $var
	 * @param      $value
	 * @param null $wrap
	 */
	public function add_number( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_number( $var, $value, $wrap );
	}

	/**
	 * @param      $var
	 * @param      $value
	 * @param null $wrap
	 */
	public function add_font( $var, $value, $wrap = null ) {
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_font( $var, $value, $wrap );
	}

	/**
	 * @param      $var
	 * @param      $value
	 * @param null $wrap
	 */
	public function add_keyword( $var, $value, $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$var = $this->get_responsive_var( $var );
		$this->less_vars_object->add_keyword( $var, $value, $wrap );
	}

	/**
	 * Register less vars for paddings.
	 *
	 * @param array       $vars
	 * @param string      $value
	 * @param string|null $wrap
	 * @param string      $units
	 */
	public function add_paddings( $vars, $value, $units = '', $wrap = null ) {
		$value = $this->maybe_transform_value( $value );
		$vars = $this->get_responsive_var( $vars );
		$this->less_vars_object->add_paddings( $vars, $value, $units, $wrap );
	}

	/**
	 * Transform elementor widget setting value to string if needed.
	 *
	 * @param mixed $val
	 *
	 * @return mixed
	 */
	public function maybe_transform_value( $val ) {
		if ( isset( $val['unit'], $val['top'], $val['right'], $val['bottom'], $val['left'] ) ) {
			$unit = $val['unit'];

			return "{$val['top']}{$unit} {$val['right']}{$unit} {$val['bottom']}{$unit} {$val['left']}{$unit}";
		}

		if ( isset( $val['size'], $val['unit'] ) ) {
			return $val['size'] === '' ? '' : $val['size'] . $val['unit'];
		}

		return $val;
	}

	public function get_current_device() {
		return $this->device;
	}

	public function start_device_section( $device ) {
		if ( $device ) {
			$this->device        = $device;
			$this->device_suffix = '-' . ltrim( $device, '-' );
		}
	}

	public function close_device_section() {
		$this->device = '';
		$this->device_suffix = '';
	}

	/**
	 * @param array|string $var
	 *
	 * @return array|string
	 */
	protected function get_responsive_var( $var ) {
		if ( is_array( $var ) ) {
			foreach ( $var as &$v ) {
				$v .= $this->device_suffix;
			}
			unset( $v );

			return $var;
		}

		return $var . $this->device_suffix;
	}
}
