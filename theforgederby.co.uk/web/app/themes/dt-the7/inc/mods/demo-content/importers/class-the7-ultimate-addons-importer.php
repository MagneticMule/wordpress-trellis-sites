<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Ultimate_Addons_Importer {

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	/**
	 * The7_Ultimate_Addons_Importer constructor.
	 *
	 * @param The7_Demo_Content_Tracker $content_tracker
	 */
	public function __construct( $content_tracker ) {
		$this->content_tracker = $content_tracker;
	}

	/**
	 * @return void
	 */
	public function import_google_fonts( $demo_fonts ) {
		$site_fonts = get_option( 'ultimate_selected_google_fonts', array() );

		// If fonts not set yet.
		if ( empty( $site_fonts ) ) {
			update_option( 'ultimate_selected_google_fonts', $demo_fonts );

			return;
		}

		// Find fonts indexes.
		$site_fonts_index = wp_list_pluck( $site_fonts, 'font_family' );
		$fonts_imported = [];

		foreach ( $demo_fonts as $demo_font ) {
			$site_font_index = array_search( $demo_font['font_family'], $site_fonts_index );

			// Simply add new font if not registered yet.
			if ( false === $site_font_index ) {
				$site_fonts[] = $demo_font;
				$fonts_imported[] = $demo_font['font_family'];
				continue;
			}

			// Set variants.
			$variants = &$site_fonts[ $site_font_index ]['variants'];
			if ( $demo_font['variants'] && $variants ) {
				$demo_variants = wp_list_pluck( $demo_font['variants'], 'variant_selected', 'variant_value' );
				foreach ( $variants as &$variant ) {
					$variant_value = $variant['variant_value'];
					if ( array_key_exists( $variant_value, $demo_variants ) && 'true' === $demo_variants[ $variant_value ] ) {
						$variant['variant_selected'] = 'true';
					}
				}
				unset( $variant );
			}

			// Set subsets.
			$subsets = &$site_fonts[ $site_font_index ]['subsets'];
			if ( $demo_font['subsets'] && $subsets ) {
				$demo_subsets = wp_list_pluck( $demo_font['subsets'], 'subset_selected', 'subset_value' );
				foreach ( $subsets as &$subset ) {
					$subset_value = $subset['subset_value'];
					if ( array_key_exists( $subset_value, $demo_subsets ) && 'true' === $demo_subsets[ $subset_value ] ) {
						$subset['subset_selected'] = 'true';
					}
				}
				unset( $subset );
			}
		}
		unset( $variants, $subsets );

		update_option( 'ultimate_selected_google_fonts', $site_fonts );

		$this->content_tracker->add( 'ultimate_imported_google_font_families', $fonts_imported );
	}

	/**
	 * @return bool
	 */
	public function import_icon_fonts( $demo_icons, $from_folder ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return false;
		}

		$uploads     = wp_get_upload_dir();
		$uploads     = trailingslashit( $uploads['basedir'] );
		$from_folder = untrailingslashit( $from_folder );

		if ( array_key_exists( 'Defaults', $demo_icons ) ) {
			unset( $demo_icons['Defaults'] );

			The7_Icon_Manager::add_ua_default_icons();
		}

		// Extract icons zip.
		foreach ( $demo_icons as $info ) {
			if ( empty( $info['include'] ) ) {
				continue;
			}

			$zip_name   = basename( $info['include'] );
			$extract_to = $uploads . dirname( $info['include'] );
			$res        = unzip_file( "{$from_folder}/{$zip_name}.zip", $extract_to );

			if ( is_wp_error( $res ) ) {
				return false;
			}
		}

		// Import db fields.
		$smile_fonts = get_option( 'smile_fonts' );
		if ( ! is_array( $smile_fonts ) ) {
			$smile_fonts = array();
		}

		$smile_fonts = array_merge( $smile_fonts, $demo_icons );
		update_option( 'smile_fonts', $smile_fonts );

		return true;
	}

}
