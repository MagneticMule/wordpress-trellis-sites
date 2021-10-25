<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Remover {

	private $demo_type;

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	public function __construct( $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->demo_type = $content_tracker->get_demo_type();
	}

	public function remove_content() {
		$this->remove_posts();
		$this->remove_terms();
	}

	public function revert_site_settings() {
		$this->revert_dashboard_settings();
		$this->revert_wp_settings();
		$this->revert_menus();
		$this->revert_widgets();
	}

	public function remove_theme_options() {
		$this->revert_the7_options();
		$this->remove_ultimate_google_fonts();
		$this->revert_elementor_options();
	}

	public function remove_rev_sliders() {
		if ( ! class_exists( 'RevSliderSlider' ) ) {
			return;
		}

		$history_sliders = $this->content_tracker->get( 'rev_sliders' );

		if ( empty( $history_sliders ) ) {
			return;
		}

		$slider = new RevSliderSlider();
		foreach ( $history_sliders as $k => $slider_id ) {
			$slider->initByID( $slider_id );
			$slider->deleteSlider();

			unset( $history_sliders[ $k ] );
		}
		$this->content_tracker->remove( 'rev_sliders' );
	}

	protected function remove_posts() {
		global $wpdb;

		$demo = $this->demo_type;
		$post_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_the7_imported_item' AND meta_value='{$demo}'" );

		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	protected function remove_terms() {
		global $wpdb;

		$demo = $this->demo_type;
		$term_ids = $wpdb->get_col( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key='_the7_imported_item' AND meta_value='{$demo}'" );

		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id );
			if ( $term && ! is_wp_error( $term ) ) {
				wp_delete_term( $term_id, $term->taxonomy );
			}
		}
	}

	protected function revert_dashboard_settings() {
		$settings = $this->content_tracker->get( 'the7_dashboard_settings' );
		if ( ! $settings ) {
			return;
		}

		foreach ( $settings as $name => $value ) {
			The7_Admin_Dashboard_Settings::set( $name, $value );
		}

		$this->content_tracker->remove( 'the7_dashboard_settings' );
	}

	protected function revert_wp_settings() {
		$wp_settings = $this->content_tracker->get( 'wp_settings' );
		if ( ! $wp_settings ) {
			return;
		}

		foreach ( $wp_settings as $key => $value ) {
			update_option( $key, $value );
		}

		$this->content_tracker->remove( 'wp_settings' );
	}

	protected function revert_menus() {
		$menu_locations = $this->content_tracker->get( 'menu_locations' );
		if ( ! $menu_locations ) {
			return;
		}

		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menu_locations as $location => $menu_id ) {
			$locations[ $location ] = $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );

		$this->content_tracker->remove( 'menu_locations' );
	}

	protected function revert_widgets() {
		$widgets_settings = $this->content_tracker->get( 'widgets_settings' );
		if ( ! $widgets_settings ) {
			return;
		}

		foreach ( $widgets_settings as $setting => $value ) {
			update_option( $setting, $value );
		}

		$this->content_tracker->remove( 'widgets_settings' );
	}

	protected function revert_the7_options() {
		$theme_options = $this->content_tracker->get( 'theme_options' );
		if ( ! $theme_options ) {
			return;
		}

		foreach ( $theme_options as $options_key => $options ) {
			update_option( $options_key, maybe_unserialize( $options ) );
		}

		presscore_refresh_dynamic_css();

		$this->content_tracker->remove( 'theme_options' );
	}

	protected function remove_ultimate_google_fonts() {
		$imported_font_families = $this->content_tracker->get( 'ultimate_imported_google_font_families' );
		if ( ! $imported_font_families ) {
			return;
		}

		$current_fonts = get_option( 'ultimate_selected_google_fonts', [] );
		foreach ( $current_fonts as $i => $font ) {
			if ( in_array( $font['font_family'], $imported_font_families, true ) ) {
				unset( $current_fonts[ $i ] );
			}
		}
		update_option( 'ultimate_selected_google_fonts', $current_fonts );

		$this->content_tracker->remove( 'ultimate_imported_google_font_families' );
	}

	protected function revert_elementor_options() {
		$origin_elementor_options = $this->content_tracker->get( 'origin_elementor_options' );

		if ( ! $origin_elementor_options ) {
			return;
		}

		foreach ( $origin_elementor_options as $option => $value ) {
			if ( $value === null ) {
				delete_option( $option );
			} else {
				update_option( $option, $value );
			}
		}

		$this->content_tracker->remove( 'origin_elementor_options' );
	}
}