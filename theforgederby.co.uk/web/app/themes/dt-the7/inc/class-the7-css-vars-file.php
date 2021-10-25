<?php
/**
 * Class The7_CSS_Vars_File.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_CSS_Vars_File
 */
class The7_CSS_Vars_File {

	/**
	 * CSS file path.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * The7_CSS_Vars_File constructor.
	 *
	 * @param string $file CSS file path.
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Determine eather string ends with $needle.
	 *
	 * @param string $haystack String to look in.
	 * @param string $needle String to look for.
	 *
	 * @return bool
	 */
	private function ends_with( $haystack, $needle ) {
		return substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
	}

	/**
	 * Create a file with css vars based on provided less vars.
	 *
	 * @param array              $less_vars Less vars list.
	 * @param The7_Less_Compiler $compiler  Less compiler.
	 */
	public function generate_based_on_less_vars( $less_vars, The7_Less_Compiler $compiler ) {
		$resp_devices = The7_Option_Field_Responsive_Option::get_devices();
		$css_vars     = array_fill_keys( $resp_devices, [] );

		foreach ( $less_vars as $less_var => $_ ) {
			$css_var_name = str_replace( [ '_', '-dt-' ], [ '-', '-' ], "--the7-{$less_var}" );
			$handled      = false;
			foreach ( $resp_devices as $device ) {
				if ( $this->ends_with( $css_var_name, '-' . $device ) ) {
					// TODO: remove this when we replace old css names.
					if ( $device === The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ) {
						$css_vars[ $device ][] = "{$css_var_name}: " . '@' . $less_var . ';';
					}
					$css_var_name          = substr( $css_var_name, 0, -strlen( '-' . $device ) );
					$css_vars[ $device ][] = "{$css_var_name}: @{$less_var};";
					$handled               = true;
					break;
				}
			}
			if ( ! $handled ) {
				$css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ][] = "{$css_var_name}: @{$less_var};";
			}
		}

		$this->add_predefined_vars( $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ] );

		foreach ( $resp_devices as $device ) {
			sort( $css_vars[ $device ] );
		}

		$file_content  = ':root {' . PHP_EOL;
		$file_content .= implode( PHP_EOL, $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ] ) . PHP_EOL;

		if ( ! empty( $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_TABLET ] ) ) {
			$file_content .= '@media screen and (max-width: @elementor-lg-breakpoint - 1) {' . PHP_EOL;
			$file_content .= implode( PHP_EOL, $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_TABLET ] ) . PHP_EOL;
			$file_content .= '}' . PHP_EOL;
		}

		if ( ! empty( $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_MOBILE ] ) ) {
			$file_content .= '@media screen and (max-width: @elementor-md-breakpoint - 1) {' . PHP_EOL;
			$file_content .= implode( PHP_EOL, $css_vars[ The7_Option_Field_Responsive_Option::RESPONSIVE_MOBILE ] ) . PHP_EOL;
			$file_content .= '}' . PHP_EOL;
		}

		// :root end;
		$file_content .= '}' . PHP_EOL;

		$compiler->put_contents( $this->file, $compiler->compile( $file_content ) );
	}

	/**
	 * Add predefined css vars for backward compatibility at most.
	 *
	 * @param array $css_vars CSS vars.
	 */
	protected function add_predefined_vars( &$css_vars ) {
		$css_vars = array_merge(
			$css_vars,
			[
				'--the7-base-border-radius: @border-radius-size;',
				'--the7-filter-pointer-border-width: @filter-decoration-line-size;',
				'--the7-filter-pointer-bg-radius: @filter-border-radius;',
				'--the7-general-border-radius: @border-radius-size;',
				'--the7-text-big-font-size: @text-big;',
				'--the7-big-button-border-radius: @dt-btn-l-border-radius;',
				'--the7-medium-button-border-radius: @dt-btn-m-border-radius;',
				'--the7-small-button-border-radius: @dt-btn-s-border-radius;',
			]
		);
	}
}
