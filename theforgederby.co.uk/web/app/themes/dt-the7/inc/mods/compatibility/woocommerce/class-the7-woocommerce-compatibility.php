<?php
/**
 * WooCommerce compatibility class.
 *
 * @package the7
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

class The7_Woocommerce_Compatibility {

	public function bootstrap() {
		require_once __DIR__ . '/admin/mod-wc-shortcodes.php';
		require_once __DIR__ . '/admin/mod-wc-admin-functions.php';

		require_once __DIR__ . '/front/mod-wc-class-template-config.php';
		require_once __DIR__ . '/front/mod-wc-template-functions.php';
		require_once __DIR__ . '/front/mod-wc-template-config.php';
		require_once __DIR__ . '/front/class-the7-wc-mini-cart.php';
		require_once __DIR__ . '/front/recently-viewed-products.php';

		// Add wooCommerce support.
		add_theme_support(
			'woocommerce',
			[
				'gallery_thumbnail_image_width' => 200,
			]
		);

		if ( of_get_option( 'woocommerce-product_zoom' ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}

		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_filter( 'woocommerce_show_admin_notice', array( $this, 'hide_admin_notoces' ), 10, 2 );

		The7_WC_Mini_Cart::init();

		presscore_template_manager()->add_path( 'woocommerce', 'inc/mods/compatibility/woocommerce/front/templates' );

		// Fix for elementor modules/woocommerce/module.php:335.
		add_action( 'init', [ $this, 'register_wc_hooks' ], 10 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );
	}

	public function register_wc_hooks(){
		require_once __DIR__ . '/front/mod-wc-template-hooks.php';
	}

	public static function enqueue_scripts( ) {
		wp_enqueue_script( 'dt-woocommerce' );
	}

	/**
	 * Hide some admin notices. The less you know, the better.
	 *
	 * @param bool   $show   Show or not the notice.
	 * @param string $notice Notice id.
	 *
	 * @return bool
	 */
	public function hide_admin_notoces( $show, $notice ) {
		if ( $notice === 'template_files' ) {
			return false;
		}

		return $show;
	}
}
