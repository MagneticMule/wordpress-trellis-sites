<?php
/**
 * The7 Theme Options importer.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Theme_Options_Importer {

	/**
	 * @var The7_Content_Importer
	 */
	private $importer;

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	/**
	 * @var bool
	 */
	private $have_attachments;

	/**
	 * The7_Theme_Options_Importer constructor.
	 *
	 * @param The7_Content_Importer     $importer
	 * @param The7_Demo_Content_Tracker $content_tracker
	 */
	public function __construct( $importer, $content_tracker ) {
		$this->importer         = $importer;
		$this->content_tracker  = $content_tracker;
		$this->have_attachments = 'original' === $content_tracker->get( 'attachments' );
	}

	public function import( $theme_options ) {
		$known_options = get_option( 'optionsframework' );
		if ( isset( $known_options['id'] ) ) {
			$this->content_tracker->add(
				'theme_options',
				[
					$known_options['id'] => get_option( $known_options['id'] ),
				]
			);

			$this->transform_options( $theme_options );

			update_option( $known_options['id'], $theme_options );

			presscore_refresh_dynamic_css();
		}
	}

	protected function transform_options( &$theme_options ) {
		$options_definitions = _optionsframework_get_clean_options();
		foreach ( $options_definitions as $option_definition ) {
			if ( ! isset( $option_definition['id'], $theme_options[ $option_definition['id'] ] ) ) {
				continue;
			}

			$option_id = $option_definition['id'];
			$method    = 'fix_' . $option_definition['type'];

			if ( method_exists( $this, $method ) ) {
				$theme_options[ $option_id ] = $this->$method( $option_definition, $theme_options[ $option_id ] );
			}
		}
	}

	protected function fix_upload( $field_definition, $value ) {
		if ( isset( $field_definition['mode'] ) && $field_definition['mode'] === 'full' ) {
			list( $img_src, $img_id ) = $value;

			if ( $img_id ) {
				if ( $this->have_attachments ) {
					$img_id = $this->importer->get_processed_post( $img_id );
					$value  = [
						wp_get_attachment_url( $img_id ),
						$img_id,
					];
				} else {
					$logo = $this->is_logo( $field_definition['id'] );
					if ( $logo ) {
						$value = [
							$this->get_logo_placeholder( $logo ),
							0,
						];
					}
				}
			}
		}

		return $value;
	}

	/**
	 * Fix 'background_img' field value.
	 *
	 * Localize 'image' url.
	 *
	 * @param array $field_definition Field definition.
	 * @param array $value Option value.
	 *
	 * @return array
	 */
	protected function fix_background_img( $field_definition, $value ) {
		if ( isset( $value['image'] ) && $value['image'] ) {
			$wp_uploads     = wp_get_upload_dir();
			$value['image'] = $wp_uploads['baseurl'] . substr( $value['image'], strlen( '/wp-content/uploads' ) );
		}

		return $value;
	}

	protected function is_logo( $option_id ) {
		preg_match( '/-logo.*_(hd|regular)/', $option_id, $matches );

		return isset( $matches[1] ) ? $matches[1] : false;
	}

	protected function get_logo_placeholder( $type ) {
		if ( $type === 'hd' ) {
			return '/images/logo-small-dummy-hd.png?w=84&h=84';
		}

		return '/images/logo-small-dummy.png?w=42&h=42';
	}
}
