<?php
/**
 * Setup Elementor widgets.
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Plugin;
use Elementor\Widget_Base;
use ElementorPro\Modules\GlobalWidget\Widgets\Global_Widget;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Query_Control_Module;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Exend_Image_Widget;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Extend_Widgets_Buttons;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Extend_Popup;
use The7\Mods\Compatibility\Elementor\Modules\Lazy_Loading\The7_Lazy_Loading_Support;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Elementor_Widgets
 */
class The7_Elementor_Widgets {

	const ELEMENTOR_WIDGETS_PATH = '\ElementorPro\Modules\Woocommerce\Widgets\\';
	protected $widgets_collection_before = [];
	protected $widgets_collection_after = [];
	protected $unregister_widgets_collection = [];

	public static function add_global_dynamic_css( \Elementor\Core\Files\CSS\Base $css_file ) {
		$global_styles = new \The7\Mods\Compatibility\Elementor\Widgets\The7_Elementor_Style_Global_Widget();
		$css = $global_styles->generate_inline_css();

		if ( empty( $css ) ) {
			return;
		}

		$css = str_replace( array( "\n", "\r" ), '', $css );
		$css_file->get_stylesheet()->add_raw_css( $css );
	}

	public static function display_inline_global_styles() {
		if ( ! Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		$global_styles = new \The7\Mods\Compatibility\Elementor\Widgets\The7_Elementor_Style_Global_Widget();
		$css = $global_styles->generate_inline_css();
		if ( $css ) {
			printf( "<style id='the7-elementor-dynamic-inline-css' type='text/css'>\n%s\n</style>\n", $css );
		}
	}

	/**
	 * Bootstrap widgets.
	 */
	public function bootstrap() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets_before' ], 5 );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets_after' ], 50 );
		add_action( 'elementor/init', [ $this, 'elementor_add_custom_category' ] );
		add_action( 'elementor/init', [ $this, 'load_dependencies' ] );
		add_action( 'elementor/init', [ $this, 'register_assets' ] );
		add_action( 'elementor/preview/init', [ $this, 'turn_off_lazy_loading' ] );
		add_action( 'elementor/editor/init', [ $this, 'turn_off_lazy_loading' ] );
		add_action( 'elementor/element/parse_css', [ $this, 'add_widget_css' ], 10, 2 );

		if ( the7_is_elementor_schemes_disabled() || the7_is_elementor_buttons_integration_enabled()) {
			add_action( 'elementor/css-file/global/parse', [ $this, 'add_global_dynamic_css' ] );
			add_action( 'wp_head', [ $this, 'display_inline_global_styles' ], 1000 );
		}

		presscore_template_manager()->add_path( 'elementor', array( 'template-parts/elementor' ) );
	}

	public function add_widget_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}
		$css = '';
		if ( $element instanceof Global_Widget ) {
			if ( $element->get_original_element_instance() instanceof The7_Elementor_Widget_Base ) {
				$css = $element->get_original_element_instance()->generate_inline_css();
			}
		} else if ( $element instanceof The7_Elementor_Widget_Base ) {
			$css = $element->generate_inline_css();
		}

		if ( empty( $css ) ) {
			return;
		}

		$css = str_replace( array( "\n", "\r" ), '', $css );
		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * Disable lazy loading with filter.
	 */
	public function turn_off_lazy_loading() {
		add_filter( 'dt_of_get_option-general-images_lazy_loading', '__return_false' );
	}

	/**
	 * Load dependencies and populate widgets collection.
	 * @throws Exception
	 */
	public function load_dependencies() {
		require_once __DIR__ . '/modules/lazy-loading/class-the7-lazy-loading-support.php';
		new The7_Lazy_Loading_Support();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-image-widget.php';
		new The7_Exend_Image_Widget();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-popup.php';
		new The7_Extend_Popup();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-widgets-buttons.php';
		new The7_Extend_Widgets_Buttons();

		require_once __DIR__ . '/pro/modules/query-control/class-the7-group-contol-query.php';
		require_once __DIR__ . '/pro/modules/query-control/class-the7-control-query.php';
		require_once __DIR__ . '/pro/modules/query-control/class-the7-posts-query.php';

		require_once __DIR__ . '/pro/modules/query-control/class-the7-query-control-module.php';
		require_once __DIR__ . '/class-the7-elementor-widget-terms-selector-mutator.php';
		require_once __DIR__ . '/trait-with-pagination.php';
		require_once __DIR__ . '/trait-with-post-excerpt.php';

		require_once __DIR__ . '/style/posts-masonry-style.php';
		require_once __DIR__ . '/style/pagination-style.php';

		require_once __DIR__ . '/class-the7-elementor-widget-base.php';
		require_once __DIR__ . '/the7-elementor-less-vars-decorator-interface.php';
		require_once __DIR__ . '/class-the7-elementor-less-vars-decorator.php';

		require_once __DIR__ . '/class-the7-elementor-shortcode-widget-base.php';
		require_once __DIR__ . '/shortcode-adapters/trait-elementor-shortcode-adapter.php';
		require_once __DIR__ . '/shortcode-adapters/class-the7-shortcode-adapter-interface.php';
		require_once __DIR__ . '/shortcode-adapters/class-the7-shortcode-query-interface.php';

		require_once __DIR__ . '/shortcode-adapters/query-adapters/Products_Query.php';
		require_once __DIR__ . '/shortcode-adapters/query-adapters/Products_Current_Query.php';

		require_once __DIR__ . '/widgets/class-the7-elementor-style-global-widget.php';
		require_once __DIR__ . '/widget-templates/abstract-template.php';
		require_once __DIR__ . '/widget-templates/pagination.php';
		require_once __DIR__ . '/widget-templates/general.php';
		require_once __DIR__ . '/widget-templates/button.php';
		require_once __DIR__ . '/widget-templates/woocommerce/sale-flash.php';
		require_once __DIR__ . '/widget-templates/woocommerce/price.php';

		new The7_Query_Control_Module();

		$terms_selector_mutator = new The7_Elementor_Widget_Terms_Selector_Mutator();
		$terms_selector_mutator->bootstrap();

		$init_widgets = [
			'button' => [],
			'class-the7-elementor-icon-box-widget' => ['position' => 'before'],
			'class-the7-elementor-icon-box-grid-widget' => ['position' => 'before'],
			'class-the7-elementor-elements-widget' => ['position' => 'before'],
			'class-the7-elementor-posts-carousel-widget' => ['position' => 'before'],
			'class-the7-elementor-elements-breadcrumbs-widget'=> ['position' => 'before'],
			'class-the7-elementor-photo-scroller-widget' => ['position' => 'before'],
			'class-the7-elementor-nav-menu' => ['position' => 'before'],
			'class-the7-elementor-text-and-icon-carousel-widget' => ['position' => 'before'],
			'class-the7-elementor-testimonials-carousel-widget' => ['position' => 'before'],
			'class-the7-elementor-accordion-widget' => ['position' => 'before'],
			'class-the7-elementor-simple-posts-widget' => ['position' => 'before'],
			'class-the7-elementor-simple-posts-carousel' => ['position' => 'before'],
		];

		if ( class_exists( 'DT_Shortcode_Products_Carousel', false ) ) {
			$init_widgets['woocommerce/old-products-carousel'] = [];
		}
		if ( class_exists( 'DT_Shortcode_ProductsMasonry', false ) ) {
			$init_widgets['woocommerce/old-products-masonry']  = [];
		}

		if ( class_exists( 'Woocommerce' ) ) {
			$init_widgets['woocommerce/products']  = ['position' => 'before' ];

			$init_widgets['woocommerce/product-sorting']  = ['position' => 'before' ];

			$init_widgets['woocommerce/products-carousel']  = ['position' => 'before' ];
//			$init_widgets['woocommerce/products-ordering-filter']  = ['position' => 'before' ];

//			$init_widgets['woocommerce/products-taxonomy-filter']  = ['position' => 'before' ];

			$document_types = Plugin::$instance->documents->get_document_types();
			if ( array_key_exists( 'product-post', $document_types ) ) {
				$sorted_wc_widgets = [
					'woocommerce/product-add-to-cart',
					'Product_Add_To_Cart',
					'woocommerce/product-tabs',
					'Product_Data_Tabs',
					'woocommerce/product-related',
					'Product_Related',
					'woocommerce/product-upsells',
					'Product_Upsell',
					'woocommerce/product-meta',
					'Product_Meta',
					'woocommerce/product-images',
					'Product_Images',
					'woocommerce/product-additional-information',
					'Product_Additional_Information',
					'woocommerce/product-navigation',
					'woocommerce/product-price',
					'class-the7-elementor-woocommerce-simple-products',
					'class-the7-elementor-woocommerce-simple-products-carousel',
					'woocommerce/filter-attribute',
					'woocommerce/filter-active',
					'woocommerce/product-reviews',
					'class-the7-elementor-simple-product-categories',
					'class-the7-elementor-simple-product-categories-carousel',
				];
				//initialize native and the7 woocommerce widgets
				foreach ( $sorted_wc_widgets as $class_name) {
					$class_path = self::ELEMENTOR_WIDGETS_PATH . $class_name;

					if ( class_exists( $class_path ) ) {
						$native_widget = new $class_path;
						$this->collection_add_unregister_widget( $native_widget );
						$init_widgets[$class_name] = ['position' => 'after', 'widget_instance' => $native_widget];
						continue;
					}
					//widget from theme
					$init_widgets[$class_name] = ['position' => 'after'];
				}
			}
		}

		// Init all widgets.
		foreach ( $init_widgets as $widget_filename => $widget_params ) {
			$widget = null;
			if ( array_key_exists( 'widget_instance', $widget_params ) ) {
				$widget = $widget_params['widget_instance'];
			} else {
				require_once __DIR__ . '/widgets/' . $widget_filename . '.php';
				$class_name = str_replace( [ 'class-', '-', '/' ], [ '', '_', '\\' ], $widget_filename );
				$class_name = __NAMESPACE__ . '\Widgets\\' . $class_name;
				$widget     = new $class_name();
			}
			$widget_position = isset( $widget_params['position'] ) ? $widget_params['position'] : 'before';
			$this->collection_add_widget( $widget, $widget_position );
		}
	}

	/**
	 * Register common widgets assets.
	 */
	public function register_assets() {
		the7_register_style(
			'the7-carousel-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-carousel-widget'
		);

		the7_register_style(
			'the7-carousel-text-and-icon-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-carousel-text-and-icon-widget'
		);
		the7_register_style(
			'the7-vertical-menu-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-vertical-menu-widget'
		);

		the7_register_style(
			'the7-woocommerce-product-navigation-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-navigation'
		);

		the7_register_style(
			'the7-woocommerce-product-price-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-price'
		);

		the7_register_style(
			'the7-woocommerce-product-additional-information-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-additional-information-widget'
		);

		the7_register_style(
			'the7-woocommerce-filter-attribute',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-filter-attribute'
		);

		the7_register_style(
			'the7-icon-box-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-icon-box-widget'
		);
		
		the7_register_style(
			'the7-icon-box-grid-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-icon-box-grid-widget'
		);
		
		wp_register_script(
			'the7-carousel-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-carousel-widget-preview.js',
			[],
			THE7_VERSION,
			true
		);

		the7_register_style(
			'the7-woocommerce-simple-products',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-simple-products.css'
		);

		the7_register_style(
			'the7-wc-products-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-wc-products-widget.css'
		);

		the7_register_style(
			'the7-simple-posts',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-posts.css'
		);

		the7_register_style(
			'the7-simple-posts-carousel',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-posts-carousel.css'
		);

		the7_register_style(
			'the7-simple-product-categories',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-product-categories.css'
		);

		the7_register_style(
			'the7-simple-product-categories-carousel',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-product-categories-carousel.css'
		);

		the7_register_style(
			'the7-woocommerce-simple-products-carousel',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-simple-products-carousel.css'
		);

		the7_register_style(
			'the7-accordion-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-accordion-widget.css'
		);

		the7_register_script_in_footer(
			'the7-gallery-scroller',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/gallery-scroller.js',
			[ 'dt-main', 'flexslider', 'jquery-mousewheel', 'zoom' ]
		);

		the7_register_script_in_footer(
			'the7-accordion-widget',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-accordion-widget.js'
		);

		the7_register_script_in_footer(
			'the7-elementor-masonry',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-masonry-widget.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-simple-products',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-simple-products.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-simple-products-carousel',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-simple-products-carousel.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-simple-posts',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-posts.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-simple-posts-carousel',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-posts-carousel.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-filter-attribute',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-filter-attribute.js',
			[ 'jquery' ]
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-product-categories.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories-carousel',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-product-categories-carousel.js',
			[ 'dt-main' ]
		);

		// Editor scripts.
		the7_register_script_in_footer(
			'the7-elementor-editor-common',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/editor-common.js',
			[ 'dt-main' ]
		);

		// Previews.
		the7_register_script_in_footer(
			'the7-elements-carousel-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-carousel-widget-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-elements-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-widget-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-photo-scroller-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/photo-scroller-widget-preview.js',
			[ 'the7-elementor-editor-common', 'dt-photo-scroller' ]
		);

		the7_register_script_in_footer(
			'the7-single-product-tab-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/single-product-tab.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-vertical-menu-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/vertical-menu-widget-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-product-images-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/wc-widget-preview.js',
			[ 'the7-elementor-editor-common', 'the7-gallery-scroller' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-simple-products-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/woocommerce-simple-products-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-simple-products-carousel-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/woocommerce-simple-products-carousel-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-simple-posts-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-posts-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-simple-posts-carousel-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-posts-carousel-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		wp_register_script(
			'the7-woocommerce-product-review',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-woocommerce-product-review.js',
			[],
			THE7_VERSION,
			true
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-product-categories-preview.js',
			[ 'the7-elementor-editor-common' ]
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories-carousel-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-product-categories-carousel-preview.js',
			[ 'the7-elementor-editor-common' ]
		);
	}

	protected function collection_add_widget( $widget, $widget_position ) {
		if ($widget_position === 'before') {
			$this->widgets_collection_before[ $widget->get_name() ] = $widget;
		}
		else {
			$this->widgets_collection_after[ $widget->get_name() ] = $widget;
		}
	}

	/**
	 * Register widgets before all elementor widgets were initialized
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets_before( $widgets_manager ) {
		foreach ( $this->widgets_collection_before as $widget ) {
			$widgets_manager->register_widget_type( $widget );
		}
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets_after( $widgets_manager ) {
		foreach ( $this->unregister_widgets_collection as $widget ) {
			$widgets_manager->unregister_widget_type( $widget->get_name() );
		}
		foreach ( $this->widgets_collection_after as $widget ) {
			$widgets_manager->register_widget_type( $widget );
		}
	}

	/**
	 * Add 'The7 elements' category.
	 */
	public function elementor_add_custom_category() {
		Plugin::$instance->elements_manager->add_category( 'the7-elements', [
			'title' => esc_html__( 'The7 elements', 'the7mk2' ),
			'icon'  => 'fa fa-header',
		] );
	}

	protected function collection_add_unregister_widget( $widget ) {
		$this->unregister_widgets_collection[ $widget->get_name() ] = $widget;
	}

	public static function update_control_fields( $widget, $control_id, array $args ) {
		$control_data = Plugin::instance()->controls_manager->get_control_from_stack( $widget->get_unique_name(), $control_id );
		if ( ! is_wp_error( $control_data ) ) {
			$widget->update_control( $control_id, $args );
		}
	}

	public static function update_control_group_fields( Widget_Base $widget, $group_name, $control_data ) {
		$group = Plugin::$instance->controls_manager->get_control_groups( $group_name );
		if ( ! $group ) {
			return;
		}
		$fields = $group->get_fields();
		$control_prefix = $control_data['name'] . "_";

		foreach ( $fields as $field_id => $field ) {
			$args = [];
			if ( ! empty( $field['selectors'] ) ) {
				$args['selectors'] = self::handle_selectors( $field['selectors'], $control_data, $control_prefix );
			}
			if ( count( $args ) ) {
				self::update_control_fields( $widget, $control_prefix . $field_id, $args );
			}
		}
	}

	private static function handle_selectors( $selectors, $args, $controls_prefix ) {
		if ( isset($args['selector']) ) {
			$selectors = array_combine( array_map( function ( $key ) use ( $args ) {
				return str_replace( '{{SELECTOR}}', $args['selector'], $key );
			}, array_keys( $selectors ) ), $selectors );
		}
		if ( ! $selectors ) {
			return $selectors;
		}

		foreach ( $selectors as &$selector ) {
			$selector = preg_replace_callback( '/\{\{\K(.*?)(?=}})/', function ( $matches ) use ( $controls_prefix ) {
				return preg_replace_callback( '/[^ ]+(?=\.)/', function ( $sub_matches ) use ( $controls_prefix ) {
					return $controls_prefix . $sub_matches[0];
				}, $matches[1] );
			}, $selector );
		}

		return $selectors;
	}

	public static function update_responsive_control_fields( $widget, $control_id, array $args ) {
		$devices = [
			$widget::RESPONSIVE_DESKTOP,
			$widget::RESPONSIVE_TABLET,
			$widget::RESPONSIVE_MOBILE,
		];

		foreach ( $devices as $device_name ) {
			$control_args = $args;

			if ( ! empty( $args['prefix_class'] ) ) {
				$device_to_replace = $widget::RESPONSIVE_DESKTOP === $device_name ? '' : '-' . $device_name;
				$control_args['prefix_class'] = sprintf( $args['prefix_class'], $device_to_replace );
			}

			$id_suffix = $widget::RESPONSIVE_DESKTOP === $device_name ? '' : '_' . $device_name;
			self::update_control_fields( $widget, $control_id . $id_suffix, $control_args );
		}
	}
}
