<?php
/**
 * The7 less vars value font.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Less_Vars_Value_Font
 */
class The7_Less_Vars_Value_Font extends The7_Less_Vars_Builder {
	protected $family;
	protected $style;
	protected $weight;
	protected $trail;

	public function __construct( $font ) {
		$this->trail = 'Helvetica, Arial, Verdana, sans-serif';
		$this->init( $font );
	}

	public function weight( $weight ) {
		$this->weight = $this->sanitize_weight( $weight );
	}

	public function style( $style ) {
		$this->style = $this->sanitize_style( $style );
	}

	public function trail( $trail ) {
		$this->trail = $trail;
	}

	public function get() {
		$family = '';
		if ( $this->family ) {
			$family = '"' . $this->family . '"';
		}

		return array(
			$this->get_wrapped( $family . ( ( $family && $this->trail ) ? ', ' : '' ) . $this->trail ),
			$this->weight,
			$this->style,
		);
	}

	public function get_family(){
		return $this->family;
	}

	public function get_style(){
		return $this->style;
	}

	public function get_weight(){
		return $this->weight;
	}

	protected function init( $font ) {
		preg_match( '/^([\w\s-]+):?(bold|normal|\d*)(\w*)/', $font, $matches );

		for ( $i = 0; $i < 4; $i++ ) {
			$matches[ $i ] = ( isset( $matches[ $i ] ) ? $matches[ $i ] : '' );
		}

		$this->family = $matches[1];
		$this->weight = $this->sanitize_weight( $matches[2] );
		$this->style = $this->sanitize_style( $matches[3] );
	}

	protected function sanitize_weight( $weight ) {
		if ( ! $weight ) {
			return '~""';
		}

		return $weight;
	}

	protected function sanitize_style( $style ) {
		$known_styles = array('normal', 'italic', 'oblique');
		if (in_array($style, $known_styles)){
			return $style;
		}
		return  '~""';
	}
}
