<?php
/**
 * WooCommerce integration with Elementor.
 * @package The7\Elementor\Pro\WooCommerce
 */

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce;

use Elementor\Plugin;
use Elementor\Widget_Base;
use ElementorPro\Modules\Woocommerce\Widgets\Products_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Woocommerce_Support
 * @package The7\Elementor\Pro\WooCommerce
 */
class Woocommerce_Support {

	/**
	 * Defines the list of the widgets where The7 theme templates would not be applied.
	 * @var array $excluded_widgets
	 */
	protected static $excluded_widgets = [
		'woocommerce-archive-description',
		'woocommerce-breadcrumb',
		'wc-categories',
		'woocommerce-category-image',
		'woocommerce-menu-cart',
		'woocommerce-product-additional-information',
		'woocommerce-product-content',
		'woocommerce-product-data-tabs',
		'woocommerce-product-images',
		'woocommerce-product-meta',
		'woocommerce-product-price',
		'woocommerce-product-rating',
		'woocommerce-product-short-description',
		'woocommerce-product-stock',
		'woocommerce-product-title',
		'wc-products',
		'wc-single-elements',
		'woocommerce-product-add-to-cart',
		'wc-add-to-cart',
	];

	/**
	 * Current widget stack.
	 * @var Widget_Base|null
	 */
	protected $current_widget = null;

	/**
	 * Customisation flag.
	 * @var bool
	 */
	protected $the7_customisation_was_removed = false;

	/**
	 * Template product conetnt fix flag.
	 * @var bool
	 */
	protected $template_products_content_fix_applied = false;

	/**
	 * Woocommerce_Support constructor.
	 */
	public function __construct() {
		add_filter( 'wc_get_template', [ $this, 'filter_woocommerce_templates' ], 50, 5 );
		add_filter( 'wc_get_template_part', [ $this, 'filter_woocommerce_template_part' ], 50, 3 );

		add_action( 'elementor/widget/before_render_content', [ $this, 'before_render_content' ] );
		add_filter( 'elementor/widget/render_content', [ $this, 'after_render_content' ], 10, 2 );
		add_filter( 'elementor/widget/render_content', [ $this, 'fix_pages_widget_preview' ], 10, 2 );

		// Modify product controls.
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 10, 3 );
	}

	/**
	 * Maybe remove The7 product customisation before rendering the widget.
	 *
	 * @param Widget_Base $widget The widget.
	 */
	public function before_render_content( Widget_Base $widget ) {
		$this->current_widget = $widget;
		$widget_name = $widget->get_name();
		if ( $this->is_ignore_theme_templates() ) {
			remove_filter( 'woocommerce_output_related_products_args', 'dt_woocommerce_related_products_args' );
			// Add image from theme.
			add_action( 'woocommerce_before_shop_loop_item_title', 'presscore_wc_template_loop_product_thumbnail', 10 );
			remove_action( 'dt_woocommerce_shop_loop_images', 'dt_woocommerce_get_alt_product_thumbnail', 11 );

			add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 5 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

			remove_action( 'woocommerce_shop_loop_item_title', 'dt_woocommerce_template_loop_product_title', 10 );
			remove_action( 'woocommerce_shop_loop_item_desc', 'dt_woocommerce_template_loop_product_short_desc', 15 );

			remove_action( 'woocommerce_before_single_product_summary', 'dt_woocommerce_hide_related_products' );
			remove_action( 'woocommerce_single_product_summary', 'dt_woocommerce_share_buttons_action', 60 );

			// Revert category.
			add_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
			add_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
			add_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
			add_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
			remove_action( 'woocommerce_shop_loop_subcategory_title', 'dt_woocommerce_template_loop_category_title', 10 );
			$this->the7_customisation_was_removed = true;
		} elseif ( $this->the7_customisation_was_removed && strpos( $widget_name, 'the7' ) === 0 ) {
			$this->add_the7_woocommerce_customsation();
		}

		// Fix initialization of nested elements located in the_content, force init frontend module.
		if ( in_array( $widget_name, [
				'woocommerce-product-data-tabs',
				'the7-woocommerce-product-data-tabs',
			], true ) && Plugin::instance()->editor->is_edit_mode() ) {
			Plugin::instance()->frontend->add_content_filter();
			$this->apply_template_products_content_fix();
		}
	}

	/**
	 * Determine if the current widget should not have The7 customisation.
	 * @return bool
	 */
	protected function is_ignore_theme_templates() {
		$current_widget = $this->get_current_widget();

		if ( ( $current_widget instanceof Products_Base ) || ( $current_widget && in_array( $current_widget->get_name(), self::$excluded_widgets, true ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Return the current widget.
	 * @return Widget_Base|null
	 */
	protected function get_current_widget() {
		return $this->current_widget;
	}

	/**
	 * Add The7 WooCommerce customisation.
	 */
	protected function add_the7_woocommerce_customsation() {
		add_filter( 'woocommerce_output_related_products_args', 'dt_woocommerce_related_products_args' );

		remove_action( 'woocommerce_before_shop_loop_item_title', 'presscore_wc_template_loop_product_thumbnail', 10 );
		add_action( 'dt_woocommerce_shop_loop_images', 'dt_woocommerce_get_alt_product_thumbnail', 11 );

		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		add_action( 'woocommerce_shop_loop_item_title', 'dt_woocommerce_template_loop_product_title', 10 );
		add_action( 'woocommerce_shop_loop_item_desc', 'dt_woocommerce_template_loop_product_short_desc', 15 );

		add_action( 'woocommerce_before_single_product_summary', 'dt_woocommerce_hide_related_products' );
		add_action( 'woocommerce_single_product_summary', 'dt_woocommerce_share_buttons_action', 60 );

		// Revert category.
		remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
		remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
		remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
		add_action( 'woocommerce_shop_loop_subcategory_title', 'dt_woocommerce_template_loop_category_title', 10 );

		$this->the7_customisation_was_removed = false;
	}

	/**
	 * Fix product content preview for templates by faking ajax call.
	 */
	protected function apply_template_products_content_fix() {
		$this->template_products_content_fix_applied = true;

		add_filter( 'wp_doing_ajax', [ $this, 'template_product_content_fix' ], 777 );
	}

	/**
	 * Maybe restore The7 products customisation after rendering the widget.
	 *
	 * @param string      $widget_content The content of the widget.
	 * @param Widget_Base $widget         The widget.
	 *
	 * @return mixed
	 */
	public function after_render_content( $widget_content, Widget_Base $widget ) {
		if ( $this->is_ignore_theme_templates() ) {
			$this->add_the7_woocommerce_customsation();
		}
		$this->current_widget = null;

		$this->release_template_products_content_fix();

		return $widget_content;
	}

	/**
	 * Release content preview fix if it was added prevously.
	 */
	protected function release_template_products_content_fix() {
		if ( $this->template_products_content_fix_applied ) {
			remove_filter( 'wp_doing_ajax', [ $this, 'template_product_content_fix' ], 777 );
		}
	}

	/**
	 * Uses with 'wp_doing_ajax' filter.
	 * @return bool
	 */
	public function template_product_content_fix() {
		return true;
	}

	/**
	 * Fix WooCommerce templates loader.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public function filter_woocommerce_templates( $template, $template_name, $args, $template_path, $default_path ) {
		if ( ( $this->is_ignore_theme_templates() && strpos( $template, PRESSCORE_THEME_DIR ) !== false ) || WC_TEMPLATE_DEBUG_MODE ) {
			// Get default template/.
			$default_path = WC()->plugin_path() . '/templates/';
			if ( version_compare( WC()->version, '3.7.0', '>=' ) ) {
				if ( false !== strpos( $template_name, 'product_cat' ) || false !== strpos( $template_name, 'product_tag' ) ) {
					$cs_template = str_replace( '_', '-', $template_name );
				}
			}
			// Get default template/.
			if ( empty( $cs_template ) ) {
				$template = $default_path . $template_name;
			} else {
				$template = $default_path . $cs_template;
			}
		}

		return $template;
	}

	/**
	 * Fix WooCommerce template parts loader.
	 *
	 * @param string $template Template.
	 * @param string $slug     Slug.
	 * @param string $name     Name.
	 *
	 * @return string
	 */
	public function filter_woocommerce_template_part( $template, $slug, $name ) {
		if ( $this->is_ignore_theme_templates() && strpos( $template, PRESSCORE_THEME_DIR ) !== false ) {
			$fallback = WC()->plugin_path() . "/templates/{$slug}-{$name}.php";
			$template = file_exists( $fallback ) ? $fallback : '';
		}

		return $template;
	}

	/**
	 * Update widget controls.
	 *
	 * @param Widget_Base $widget The widget.
	 */
	public function update_controls( $widget, $section_id, $args ) {

		$widgets = [
			'woocommerce-product-rating' => [
				'section_name' => [ 'section_product_rating_style', ],
			],
		];

		if ( array_key_exists( $widget->get_name(), $widgets ) ) {
			$curr_section = $widgets[ $widget->get_name() ]['section_name'];
			if ( in_array( $section_id, $curr_section ) ) {
				//fix alignment responsiveness in product rating widget
				if ( $section_id == 'section_product_rating_style' ) {
					$start = is_rtl() ? 'end' : 'start';
					$end = is_rtl() ? 'start' : 'end';

					$control_data = [
						'selectors'            => [
							'{{WRAPPER}} .woocommerce-product-rating' => 'justify-content: {{VALUE}}',
						],
						'selectors_dictionary' => [
							'left'   => 'flex-' . $start,
							'right'  => 'flex-' . $end,
							'center' => 'center',
							'justify' => 'space-between',
						],
						'prefix_class' => 'elementor-product-rating%s-align-',
					];

					The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'alignment', $control_data );
				}
			}
		}


		if ( ! $widget instanceof Products_Base ) {
			return;
		}

		$control_data = [
			'selectors' => [
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'background: {{VALUE}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_color', $control_data );

		$control_data = [
			'options' => [
				''       => __( 'Default', 'the7mk2' ),
				'none'   => __( 'None', 'the7mk2' ),
				'solid'  => _x( 'Solid', 'Border Control', 'the7mk2' ),
				'double' => _x( 'Double', 'Border Control', 'the7mk2' ),
				'dotted' => _x( 'Dotted', 'Border Control', 'the7mk2' ),
				'dashed' => _x( 'Dashed', 'Border Control', 'the7mk2' ),
				'groove' => _x( 'Groove', 'Border Control', 'the7mk2' ),
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'button_border_border', $control_data );

		$control_data = [
			'condition' => [
				'border!' => [ '', 'none' ],
			],
		];

		The7_Elementor_Widgets::update_control_fields( $widget, 'button_border_width', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'background: {{VALUE}}',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'onsale_text_background_color', $control_data );
	}

	/**
	 * Fix widgets preview in editor mode.
	 *
	 * @param string      $widget_content The widget content.
	 * @param Widget_Base $widget         The widget.
	 *
	 * @return string
	 */
	public function fix_pages_widget_preview( $widget_content, Widget_Base $widget ) {
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			$widget_name = $widget->get_name();

			if ( in_array( $widget_name, [ 'wc-elements', 'woocommerce-product-images' ], true ) ) {
				ob_start();
				?>
                <script>
                    elementorFrontend.hooks.addAction('frontend/element_ready/<?php echo $widget_name; ?>.default', function ($scope, jQuery) {
                        $scope.find(".woocommerce-product-gallery").wc_product_gallery();
                    });
                </script>
				<?php

				return $widget_content . ob_get_clean();
			}


		}

		return $widget_content;
	}

}
