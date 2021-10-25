<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo {

	const DEMO_STATUS_FULL_IMPORT = 'full_import';
	const DEMO_STATUS_PARTIAL_IMPORT = 'partial_import';
	const DEMO_STATUS_NOT_IMPORTED = 'not_imported';

	/**
	 * @var bool
	 */
	public $post_types_imported;

	/**
	 * @var bool
	 */
	public $theme_options_imported;

	/**
	 * @var bool
	 */
	public $attachments_imported;

	/**
	 * @var bool
	 */
	public $sliders_imported;

	/**
	 * @var string
	 */
	protected $import_status;

	/**
	 * @var The7_Demo_Content_TGMPA
	 */
	protected $plugins;

	/**
	 * @var array
	 */
	protected $fields = [];

	public function __construct( $demo ) {
		$this->setup_fields( $demo );
		$this->refresh_import_status();
	}

	public function refresh_import_status() {
		$history = The7_Demo_Content_Tracker::get_demo_history( $this->id );

		$this->post_types_imported    = isset( $history['post_types'] );
		$this->theme_options_imported = isset( $history['theme_options'] );
		$this->attachments_imported   = isset( $history['attachments'] );
		$this->sliders_imported       = isset( $history['rev_sliders'] );

		$this->import_status = $this->get_import_status();
	}

	/**
	 * @return string
	 */
	public function get_import_status_text() {
		$text = '';
		if ( $this->import_status === static::DEMO_STATUS_FULL_IMPORT ) {
			$text = '(' . esc_html__( 'fully imported', 'the7mk2' ) . ')';
		} elseif ( $this->import_status === static::DEMO_STATUS_PARTIAL_IMPORT ) {
			$text = '(' . esc_html__( 'partially imported', 'the7mk2' ) . ')';
		}

		return '<span class="demo-import-status">' . $text . '</span>';
	}

	/**
	 * @return bool
	 */
	public function import_allowed() {
		return $this->plugins()->is_installed( 'pro-elements' );
	}

	/**
	 * @return bool
	 */
	public function partially_imported() {
		return in_array(
			$this->import_status,
			[ static::DEMO_STATUS_PARTIAL_IMPORT, static::DEMO_STATUS_FULL_IMPORT ],
			true
		);
	}

	/**
	 * @return bool
	 */
	public function require_revslider() {
		return in_array( 'revslider', $this->required_plugins, true );
	}

	/**
	 * @return The7_Demo_Content_TGMPA
	 */
	public function plugins() {
		if ( null === $this->plugins ) {
			$this->plugins = new The7_Demo_Content_TGMPA( $this->required_plugins );
		}

		return $this->plugins;
	}

	/**
	 * @param string $prop
	 *
	 * @return mixed|null
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->fields ) ) {
			return $this->fields[ $prop ];
		}

		return null;
	}

	/**
	 * @return string
	 */
	protected function get_import_status() {
		$entire_demo_is_imported    = $this->post_types_imported && $this->theme_options_imported && $this->attachments_imported;
		$demo_is_partially_imported = $this->post_types_imported || $this->theme_options_imported || $this->attachments_imported;

		if ( $this->plugins()->is_required( 'revslider' ) ) {
			$entire_demo_is_imported    &= $this->sliders_imported;
			$demo_is_partially_imported |= $this->sliders_imported;
		}

		if ( $entire_demo_is_imported ) {
			return static::DEMO_STATUS_FULL_IMPORT;
		}

		if ( $demo_is_partially_imported ) {
			return static::DEMO_STATUS_PARTIAL_IMPORT;
		}

		return static::DEMO_STATUS_NOT_IMPORTED;
	}

	/**
	 * @param array $fields
	 */
	protected function setup_fields( $fields ) {
		$allowed_fields = [
			'title'               => '',
			'id'                  => '',
			'include_attachments' => false,
			'screenshot'          => '',
			'link'                => '',
			'attachments_batch'   => 27,
			'required_plugins'    => [],
			'tags'                => [],
		];

		$fields       = array_intersect_key( (array) $fields, $allowed_fields );
		$this->fields = wp_parse_args( $fields, $allowed_fields );
	}

}