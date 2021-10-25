<?php
/**
 * Typography option field.
 * @package The7/Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7_Option_Field_Responsive_Option extends The7_Option_Field_Composition_Abstract {

	/**
	 * Responsive 'desktop' device name.
	 */
	const RESPONSIVE_DESKTOP = 'desktop';

	/**
	 * Responsive 'tablet' device name.
	 */
	const RESPONSIVE_TABLET = 'tablet';

	/**
	 * Responsive 'mobile' device name.
	 */
	const RESPONSIVE_MOBILE = 'mobile';

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
		$id = $this->option['id'];
		$option = $this->get_option()['option'];
		if ( ! isset( $option ) ) {
			return '';
		}
		$class = "";
		if ( isset( $option['class'] ) ) {
			$class .= ' ' . $option['class'];
		}
		if ( isset( $option['type'] ) ) {
			$class .= ' section-' . $option['type'];
		}

		$output = '<div id="section-' . esc_attr( $id ) . '" class="of-responsive section' . $class . '">';
		$output .= '<div class="option">';
		$output .= '<div class="of-responsive-wrapper">';

		$output .= $this->get_switcher();
		$output .= $this->get_name( $option );
		$selected_class = 'of-responsive-active';
		$field_value = self::sanitize( $this->val, $option );

		foreach ( self::get_devices() as $device ) {
            $output .= '<div class="controls responsive-option responsive-' . $device . " " . $selected_class . '">';
                $option['id'] = $device;
                $field_object = $this->interface->get_field_object( $this->option_name . '[' . $device . ']', $option, $field_value );
                // Fix id's.
                $wrapped_output = $field_object->html();
                $output .= str_replace( array(
                    "section-$device",
                    "id=\"$device\"",
                ), array(
                    "section-$id-$device",
                    "id=\"$id-$device\"",
                ), $wrapped_output );
                $output .= '<div class="clear"></div>';
			$output .= '</div>';
			$selected_class = '';
		}
		$output .= '<div class="clear"></div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	private function get_name( $option ) {
		$output = "";
		if ( ! empty( $option['name'] ) ) {
			$output .= '<div class="name">' . ( ! empty( $option['name'] ) ? esc_html( $option['name'] ) : '' );
			if ( isset( $option['desc'] ) ) {
				$explain_value = $option['desc'];
				$output .= '<div class="explain"><small>' . esc_html( $explain_value ) . '</small></div>' . "\n";
			}
			$output .= '</div>' . "\n";
		}
		return $output;
	}

	private function get_switcher() {
		ob_start();
		?>
        <div class="responsive-switchers_holder">
			<?php
			$selected_class = 'of-selected';
			foreach ( self::get_devices() as $device ) {
				?>
                <a class="responsive-switcher of-tooltip responsive-switcher-<?php echo $device . " " . $selected_class ?>"
                   data-device="<?php echo $device ?>">
                    <i class="device-<?php echo $device ?>">
						<?php echo $device ?>
                    </i>
                    <i class="tooltiptext"><?php echo $device ?></i>
                </a>
				<?php
				$selected_class = '';
			}
			?>
        </div>
		<?php
		return ob_get_clean();
	}

	public static function get_devices() {
		$devices = [
			self::RESPONSIVE_DESKTOP,
			self::RESPONSIVE_TABLET,
			self::RESPONSIVE_MOBILE,
		];

		return $devices;
	}

	/**
	 * Sanitize rep value.
	 *
	 * @param array $val value.
	 *
	 * @return array
	 */
	public static function sanitize( $val ) {
		$sanitized_options = array();
		foreach ( self::get_devices() as $device ) {
			$sanitized_options[ $device ] = "";
		}
		if ( is_array( $val ) ) {
			$sanitized_options = wp_parse_args( $val, $sanitized_options );
		}

		return $sanitized_options;
	}
}
