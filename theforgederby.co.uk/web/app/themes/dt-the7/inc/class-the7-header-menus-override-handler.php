<?php
/**
 * Used in conjunction with 'Menus' metabox (and elementor tab in page settings).
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Header_Menus_Override_Handler {

	/**
	 * @var int
	 */
	private $post_id;

	/**
	 * Header_Menu_Override constructor.
	 *
	 * @param int $post_id
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	public function bootstrap() {
		if ( ! $this->post_id ) {
			return;
		}

		add_filter( 'presscore_nav_menu_args', [ $this, 'nav_menu_args_filter' ] );
		add_filter( 'presscore_pre_nav_menu', [ $this, 'presscore_pre_nav_menu_filter' ], 10, 2 );
		add_filter( 'presscore_has_mobile_menu', [ $this, 'presscore_has_mobile_menu_filter' ] );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function nav_menu_args_filter( $args = array() ) {
		$location  = $args['theme_location'];
		$page_menu = (int) get_post_meta( $this->post_id, "_dt_microsite_{$location}_menu", true );

		if ( $page_menu > 0 ) {
			$args['menu'] = $page_menu;
		}

		return $args;
	}

	/**
	 * @param string|null $nav_menu
	 * @param array       $args
	 *
	 * @return mixed
	 */
	public function presscore_pre_nav_menu_filter( $nav_menu, $args = array() ) {
		$location  = $args['theme_location'];
		$page_menu = (int) get_post_meta( $this->post_id, "_dt_microsite_{$location}_menu", true );
		if ( $page_menu < 0 && isset( $args['fallback_cb'] ) && is_callable( $args['fallback_cb'] ) ) {
			$args['echo'] = false;

			return call_user_func( $args['fallback_cb'], $args );
		}

		return $nav_menu;
	}

	/**
	 * @param bool $has_menu
	 *
	 * @return bool
	 */
	public function presscore_has_mobile_menu_filter( $has_menu ) {
		$page_menu = (int) get_post_meta( $this->post_id, '_dt_microsite_mobile_menu', true );
		if ( 0 !== $page_menu ) {
			return true;
		}

		return $has_menu;
	}
}
