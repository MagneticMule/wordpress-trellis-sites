<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_WP_Settings_Importer {

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	/**
	 * @var array[]
	 */
	private $origin_settings;

	/**
	 * @var The7_Content_Importer
	 */
	private $importer;

	/**
	 * The7_WP_Settings_Importer constructor.
	 *
	 * @param The7_Content_Importer     $importer
	 * @param The7_Demo_Content_Tracker $content_tracker
	 */
	public function __construct( $importer, $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->importer        = $importer;

		$this->origin_settings = [
			'wp_settings'    => [],
			'menu_locations' => [],
		];
	}

	public function import_settings( $settings ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'show_on_front'  => false,
				'page_on_front'  => false,
				'page_for_posts' => false,
			)
		);

		if ( 'page' === $settings['show_on_front'] ) {
			$page_on_front = $this->importer->get_processed_post( $settings['page_on_front'] );
			if ( 'page' === get_post_type( $page_on_front ) ) {
				$this->update_wp_setting( 'show_on_front', 'page' );
				$this->update_wp_setting( 'page_on_front', $page_on_front );
			}

			$page_for_posts = $this->importer->get_processed_post( $settings['page_for_posts'] );
			if ( 'page' === get_post_type( $page_for_posts ) ) {
				$origin_wp_settings['page_for_posts'] = get_option( 'page_for_posts' );

				$this->update_wp_setting( 'page_for_posts', $page_for_posts );
			}

			$this->save_in_tracker( 'wp_settings' );
		}
	}

	public function import_menu_locations( $menu_locations ) {
		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menu_locations as $location => $menu_id ) {
			if ( isset( $locations[ $location ] ) ) {
				$this->origin_settings['menu_locations'][ $location ] = $locations[ $location ];
			}

			$locations[ $location ] = $this->importer->get_processed_term( $menu_id );
		}

		$this->save_in_tracker( 'menu_locations' );

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	public function import_widgets( $widgets_settings ) {
		foreach ( $widgets_settings as $widget_id => $settings ) {
			$this->update_widget_setting( $widget_id, $this->filter_widget_settings( $widget_id, $settings ) );
		}

		$this->save_in_tracker( 'widgets_settings' );
	}

	protected function update_wp_setting( $key, $value ) {
		$this->origin_settings['wp_settings'][ $key ] = get_option( $key );

		update_option( $key, $value );
	}

	protected function update_widget_setting( $key, $value ) {
		$this->origin_settings['widgets_settings'][ $key ] = get_option( $key );

		update_option( $key, $value );
	}

	protected function filter_widget_settings( $widget_id, $settings ) {
		if ( in_array(
			$widget_id,
			array( 'widget_presscore-custom-menu-one', 'widget_presscore-custom-menu-two' ),
			true
		) ) {
			foreach ( $settings as &$widget_settings ) {
				if ( isset( $widget_settings['menu'] ) ) {
					$widget_settings['menu'] = $this->importer->get_processed_term( $widget_settings['menu'] );
				}
			}
			unset( $widget_settings );
		}

		return $settings;
	}

	protected function save_in_tracker( $key ) {
		if ( isset( $this->origin_settings[ $key ] ) ) {
			$this->content_tracker->add( $key, $this->origin_settings[ $key ] );
		}
	}
}
