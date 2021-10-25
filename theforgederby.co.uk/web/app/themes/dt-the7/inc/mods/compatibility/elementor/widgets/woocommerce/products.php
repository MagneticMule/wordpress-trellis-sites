<?php
/**
 * Class Products
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets\Woocommerce
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\General;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Sale_Flash;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Price;
use WC_Product;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets\Woocommerce
 */
class Products extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-wc-products';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function the7_title() {
		return __( 'Products', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function the7_icon() {
		return 'eicon-products';
	}

	/**
	 * Get the7 widget categories.
	 *
	 * @return string[]
	 */
	protected function the7_categories() {
		return [ 'woocommerce-elements' ];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'the7-wc-products-widget' ];
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		$scripts = [ 'the7-elementor-masonry' ];

		if ( $this->is_preview_mode() ) {
			$scripts[] = 'the7-elements-widget-preview';
		}

		return $scripts;
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		$this->print_inline_css();

		$settings = $this->get_settings_for_display();

		$this->setup_wrapper_class();
		$this->setup_wrapper_data_attributes();

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
			if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
				echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
			}

		if ( 'grid' === $settings['mode'] ) {
			$class = 'dt-css-grid custom-pagination-handler';

			if ( 'browser_width_based' === $settings['responsiveness'] ) {
				$class .= ' custom-wide-columns';
			}
		} else {
			$class = 'iso-container dt-isotope custom-iso-columns';
		}

		$this->add_render_attribute( 'inner_wrapper', 'class', $class );

		echo '<div ' . $this->get_render_attribute_string( 'inner_wrapper' ) . '>';

		// Loop query.
		$query = $this->get_query();

		wc_setup_loop(
			[
				'is_search'    => $query->is_search(),
				'is_filtered'  => is_filtered(),
				'total'        => $query->found_posts,
				'total_pages'  => $query->max_num_pages,
				'per_page'     => $query->get( 'posts_per_page' ),
				'current_page' => max( 1, $query->get( 'paged', 1 ) ),
			]
		);

		$post_limit = $this->template( Pagination::class )->get_post_limit();

		// Related to print_render_attribute_string( 'woo_buttons_on_img' ); .
		$this->setup_woo_buttons_on_image_attributes();

		// Only standard pagination for current query.
		if ( $settings['query_post_type'] === 'current_query' ) {
			$this->template( Pagination::class )->set_loading_mode( 'standard' );
		}

		// Start loop.
		if ( $query->have_posts() ) {

			global $product;

			while ( $query->have_posts() ) {
				$query->the_post();

				$product = wc_get_product();

				if ( ! $product ) {
					continue;
				}

				// Post visibility on the first page.
				$visibility = 'visible';
				if ( $post_limit >= 0 && $query->current_post >= $post_limit ) {
					$visibility = 'hidden';
				}

				$this->add_render_attribute( 'article_wrapper', 'class', $visibility, true );
				$this->setup_article_wrapper_attributes();

				echo '<div ' . $this->get_render_attribute_string( 'article_wrapper' ) . '>';
				echo '<article ';
				wc_product_class(
					[
						'post',
						'project-odd',
						'visible',
					]
				);
				echo ' >';
				?>

				<figure class="woocom-project">
					<div <?php $this->print_render_attribute_string( 'woo_buttons_on_img' ); ?>>

						<?php
						if ( $settings['layout'] !== 'content_below_img' || $settings['show_product_image'] ) {
							$this->render_product_image( $product );
						}
						?>

					</div>
					<figcaption class="woocom-list-content">

						<?php
						if ( $settings['show_product_title'] ) {
							$this->render_product_title( $product );
						}

						$this->template( Price::class )->render_product_price( $product );

						if ( $settings['show_rating'] && wc_review_ratings_enabled() ) {
							$price_html = wc_get_rating_html( $product->get_average_rating() );
							if ( $price_html ) {
								echo '<div class="star-rating-wrap">' . $price_html . '</div>'; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}

						if ( $settings['show_short_description'] ) {
							$this->render_short_description( $product );
						}

						if ( $settings['show_add_to_cart'] && $settings['layout'] === 'content_below_img' ) {
							$this->render_add_to_cart_content_button( $product );
						}
						?>

					</figcaption>
				</figure>

				<?php
				echo '</article>';
				echo '</div>';
			}
		}

		wc_reset_loop();
		wp_reset_postdata();

		echo '</div>';

		$this->template( Pagination::class )->render( $query->max_num_pages );

		echo '</div>';

		$this->remove_hooks();
	}

	/**
	 * Setup wrapper class attribute.
	 */
	protected function setup_wrapper_class() {
		$class = [
			'products-shortcode',
			'loading-effect-none',
		];

		$settings = $this->get_settings_for_display();

		// Unique class.
		$class[] = $this->get_unique_class();

		$mode_classes = [
			'masonry' => 'mode-masonry',
			'grid'    => 'mode-grid dt-css-grid-wrap',
		];

		$mode = $settings['mode'];
		if ( array_key_exists( $mode, $mode_classes ) ) {
			$class[] = $mode_classes[ $mode ];
		}

		$class[] = the7_array_match(
			$settings['layout'],
			[
				'content_below_img' => 'cart-btn-below-img',
				'btn_on_img'        => 'cart-btn-on-img',
			]
		);

		$loading_mode = $settings['loading_mode'];
		if ( 'standard' !== $loading_mode ) {
			$class[] = 'jquery-filter';
		}

		if ( 'js_lazy_loading' === $loading_mode ) {
			$class[] = 'lazy-loading-mode';
		}

		if ( $settings['show_all_pages'] ) {
			$class[] = 'show-all-pages';
		}

		$class[] = the7_array_match(
			$settings['image_hover_style'],
			[
				'quick_scale' => 'quick-scale-img',
				'slow_scale'  => 'scale-img',
				'hover_image' => 'wc-img-hover',
			]
		);

		if ( 'browser_width_based' === $settings['responsiveness'] ) {
			$class[] = 'resize-by-browser-width';
		}

		$this->add_render_attribute( 'wrapper', 'class', $class );
	}

	/**
	 * Setup wrapper data attributes.
	 */
	protected function setup_wrapper_data_attributes() {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'data-padding'  => $this->combine_slider_value( $settings['gap_between_posts_adapter'] ),
			'data-cur-page' => the7_get_paged_var(),
		];

		$target_width = $settings['pwb_column_min_width'];
		if ( ! empty( $target_width['size'] ) ) {
			$data_atts['data-width'] = absint( $target_width['size'] );
		}

		if ( ! empty( $settings['pwb_columns'] ) ) {
			$data_atts['data-columns'] = absint( $settings['pwb_columns'] );
		}

		if ( 'browser_width_based' === $settings['responsiveness'] ) {
			$columns = [
				'wide-desktop' => $settings['widget_columns_wide_desktop'] ?: $settings['widget_columns'],
				'desktop'      => $settings['widget_columns'],
				'v-tablet'     => $settings['widget_columns_tablet'],
				'phone'        => $settings['widget_columns_mobile'],
			];

			foreach ( $columns as $column => $val ) {
				$data_atts[ 'data-' . $column . '-columns-num' ] = esc_attr( $val );
			}
		}

		$data_atts = $this->template( Pagination::class )->add_containter_data( $data_atts );

		foreach ( $data_atts as $key => $value ) {
			$this->add_render_attribute( 'wrapper', $key, $value );
		}
	}

	/**
	 * Setup image wrapper render attributes.
	 */
	protected function setup_woo_buttons_on_image_attributes() {
		if ( $this->get_settings_for_display( 'image_hover_trigger' ) === 'image' ) {
			$this->add_render_attribute( 'woo_buttons_on_img', 'class', 'trigger-img-hover' );
		}

		$this->add_render_attribute( 'woo_buttons_on_img', 'class', 'woo-buttons-on-img' );
	}

	/**
	 * Setup article wrapper attribute.
	 */
	protected function setup_article_wrapper_attributes() {
		global $post;

		$settings = $this->get_settings_for_display();

		$class[] = 'wf-cell';

		if ( $settings['mode'] === 'masonry' ) {
			$class[] = 'iso-item';
		}

		if ( $settings['image_hover_trigger'] === 'box' ) {
			$class[] = 'trigger-img-hover';
		}

		$this->add_render_attribute( 'article_wrapper', 'class', $class );
//		$this->add_render_attribute( 'article_wrapper', 'data-name', get_the_title() );
//		$this->add_render_attribute( 'article_wrapper', 'data-date', get_the_date( 'c' ) );
		$this->add_render_attribute( 'article_wrapper', 'data-post-id', $post->ID, true );
	}

	/**
	 * Remove nasty hooks.
	 */
	protected function remove_hooks() {
		if ( $this->get_settings_for_display( 'query_post_type' ) === 'top' ) {
			remove_filter( 'posts_clauses', [ 'WC_Shortcodes', 'order_by_rating_post_clauses' ] );
		}
	}

	/**
	 * TODO: use it somehow.
	 *
	 * @return string
	 */
	protected function get_loading_mode() {
		$settings = $this->get_settings_for_display();

		// Only standard pagination for current query.
		if ( $settings['query_post_type'] === 'current_query' ) {
			return 'standard';
		}

		return $settings['loading_mode'];
	}

	/**
	 * Return products query.
	 *
	 * @return mixed|WP_Query
	 */
	protected function get_query() {
		$settings = $this->get_settings_for_display();

		if ( 'current_query' === $settings['query_post_type'] ) {
			return $GLOBALS['wp_query'];
		}

		if ( ! empty( $_GET['orderby'] ) ) {
			$settings['query_orderby'] = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			if ( $settings['query_orderby'] === 'price' ) {
				$settings['query_order'] = 'ASC';
			}
		}

		$query = new Products_Query( $settings, 'query_' );

		$query_args = $query->parse_query_args();

		if ( ! empty( $_GET['term'] ) ) {
			$query_args['tax_query']['relation'] = 'AND';
			$query_args['tax_query'][]           = [
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => [ sanitize_text_field( wp_unslash( $_GET['term'] ) ) ],
				'operator' => 'IN',
			];
		}

		$products_query = new WP_Query( $query_args );

		WC()->query->remove_ordering_args();

		return $products_query;
	}

	protected function render_product_image( $product ) {
		$settings = $this->get_settings_for_display();

		$this->template( Sale_Flash::class )->render_sale_flash();

		$class = [ 'alignnone', 'img-wrap', 'img-ratio-wrapper' ];

		if ( presscore_lazy_loading_enabled() ) {
			$class[] = 'layzr-bg';
		}

		echo '<div class="img-border">';
		echo '<a href="' . esc_url( get_permalink() ) . '" class="' . esc_attr( implode( ' ', $class ) ) . '">';

		woocommerce_template_loop_product_thumbnail();
		if ( $settings['image_hover_style'] === 'hover_image' ) {
			echo the7_wc_get_the_first_product_gallery_image_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</a>';
		echo '</div>';

		if ( $settings['layout'] === 'btn_on_img' ) {
			$this->render_add_to_cart_on_image_button( $product );
		}

		if ( ! $product->is_in_stock() ) {
			echo '<span class="out-stock-label">' . esc_html__( 'Out Of Stock', 'the7mk2' ) . '</span>';
		}

		the7_ti_wishlist_button();
	}

	/**
	 * Render product title.
	 */
	public function render_product_title( $product ) {
		$settings = $this->get_settings_for_display();

		$html_tag = Utils::validate_html_tag( $this->get_settings_for_display( 'product_title_tag' ) );

		$class = implode(
			' ',
			[
				'product-title',
				( $settings['product_title_width'] === 'crp-to-line' ? 'one-line' : '' ),
			]
		);

		echo "<{$html_tag} class=\"{$class}\">"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$product_name = $product->get_name();
		if ( $settings['product_title_words_limit'] && $settings['product_title_width'] === 'normal' ) {
			$product_name = wp_trim_words( $product_name, $settings['product_title_words_limit'] );
		}

		printf(
			'<a href="%s" title="%s" rel="bookmark">%s</a>',
			esc_url( $product->get_permalink() ),
			the_title_attribute( [ 'echo' => false ] ),
			esc_html( $product_name )
		);
		echo "</{$html_tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = Utils::validate_html_tag( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}
	protected function render_short_description( $product ) {
		$settings = $this->get_settings_for_display();

		$class = implode(
			' ',
			[
				'woocommerce-product-details__short-description',
				( $settings['description_width'] === 'crp-to-line' ? 'one-line' : '' ),
			]
		);

		$short_description = $product->get_short_description();
		if ( $settings['description_words_limit'] && $settings['description_width'] === 'normal' ) {
			$short_description = wp_trim_words( $short_description, $settings['description_words_limit'] );
		}

		printf(
			'<div class="%s">%s</div>',
			esc_attr( $class ),
			wp_kses_post( $short_description )
		);
	}

	protected function render_add_to_cart_content_button( $product ) {
		if ( ! $product ) {
			return;
		}

		echo '<div class="woo-buttons">';

		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$this->add_product_add_to_cart_button_render_attributes( 'box-button', $product );

		$button_text = esc_html( $product->add_to_cart_text() );
		if ( $this->template( Button::class )->is_icon_visible() ) {
			$button_text .= $this->get_add_to_cart_icon( $product, 'elementor-button-icon' );
		}

		$this->template( Button::class )->render_button( 'box-button', $button_text );

		echo '</div>';
	}

	protected function render_add_to_cart_on_image_button( $product ) {
		if ( ! $product ) {
			return;
		}

		$this->add_product_add_to_cart_button_render_attributes( 'button', $product );
		$button_text = $this->get_add_to_cart_icon( $product, 'popup-icon' );
		if ( $this->get_settings_for_display( 'expand_product_icon_on_hover' ) ) {
			$button_text = sprintf(
				'<span class="filter-popup">%s</span>%s',
				esc_html( $product->add_to_cart_text() ),
				$button_text
			);
		}

		echo '<div class="woo-buttons">';
		echo '<a ' . $this->get_render_attribute_string( 'button' ) . '>' . $button_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Return add to cart icon HTML.
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $class   Custom class.
	 *
	 * @return string
	 */
	protected function get_add_to_cart_icon( $product, $class = '' ) {
		$icon_class = '';
		if ( function_exists( 'the7_get_wc_product_add_to_cart_icon' ) ) {
			$icon_class = the7_get_wc_product_add_to_cart_icon( $product );
		}

		return sprintf( '<i class="%s"></i>', esc_attr( $icon_class . ' ' . $class ) );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content.
		$this->add_query_controls();
		$this->add_layout_controls();
		$this->add_content_controls();
		$this->template( Pagination::class )->add_content_controls( 'query_post_type' );

		// Style.
		$this->add_widget_title_style_controls();
		$this->template( General::class )->add_box_style_controls();
		$this->add_image_style_controls();
		$this->add_content_style_controls();
		$this->template( Sale_Flash::class )->add_style_controls();
		$this->add_icon_on_image_style_controls();
		$this->add_title_style_controls();
		$this->template( Price::class )->add_style_controls();
		$this->add_rating_style_controls();
		$this->add_short_description_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_SWITCHER,
			[
				'layout'           => 'content_below_img',
				'show_add_to_cart' => 'y',
			]
		);
		$this->template( Pagination::class )->add_style_controls( 'query_post_type' );
	}
		protected function add_widget_title_style_controls() {

		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => __( 'Widget Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_title_align',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .rp-heading' => 'text-align: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widget_title_typography',
				'selector' => '{{WRAPPER}} .rp-heading',
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'widget_title_bottom_margin',
			[
				'label'      => __( 'Spacing Below Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rp-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register layout controls.
	 */
	protected function add_layout_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => __( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'show_widget_title',
			[
				'label'        => __( 'Widget Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Widget title',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'mode',
			[
				'label'   => __( 'Mode', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'masonry' => 'Masonry',
					'grid'    => 'Grid',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'responsiveness',
			[
				'label'     => __( 'Responsiveness mode', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'browser_width_based',
				'options'   => [
					'browser_width_based' => 'Browser width based',
					'post_width_based'    => 'Post width based',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop',
			[
				'label'       => __( 'Columns On A Wide Desktop', 'the7mk2' ),
				'description' => the7_elementor_get_wide_columns_control_description(),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
				'selectors'   => [
					'{{WRAPPER}} .custom-wide-columns' => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type' => 'template',
				'condition'   => [
					'responsiveness' => 'browser_width_based',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},minmax(0, 1fr))',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
				'condition'      => [
					'responsiveness' => 'browser_width_based',
				],
			]
		);

		$this->add_control(
			'pwb_column_min_width',
			[
				'label'       => __( 'Column minimum width', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'unit' => 'px',
					'size' => 300,
				],
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
				],
				'render_type' => 'template',
				'condition'   => [
					'responsiveness' => 'post_width_based',
				],
			]
		);

		$this->add_control(
			'pwb_columns',
			[
				'label'     => __( 'Desired columns number', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 12,
				'condition' => [
					'mode'           => 'masonry',
					'responsiveness' => 'post_width_based',
				],
			]
		);

		$this->add_control(
			'gap_between_posts_adapter',
			[
				'label'       => __( 'Gap between columns', 'the7mk2' ),
				'description' => __(
					'Please note that this setting affects post paddings. So, for example: a value 10px will give you 20px gaps between posts)',
					'the7mk2'
				),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition'   => [
					'mode' => 'masonry',
				],
			]
		);

		$this->add_basic_responsive_control(
			'columns_gap',
			[
				'label'      => __( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '30',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'mode' => 'grid',
				],
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '30',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'mode' => 'grid',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register query controls.
	 */
	protected function add_query_controls() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			The7_Group_Control_Query::get_type(),
			[
				'name'            => 'query',
				'query_post_type' => 'product',
				'presets'         => [ 'include', 'exclude', 'order' ],
				'fields_options'  => [
					'post_type' => [
						'default' => 'product',
						'options' => [
							'current_query' => __( 'Current Query', 'the7mk2' ),
							'product'       => __( 'Latest Products', 'the7mk2' ),
							'sale'          => __( 'Sale', 'the7mk2' ),
							'top'           => __( 'Top rated products', 'the7mk2' ),
							'best_selling'  => __( 'Best selling', 'the7mk2' ),
							'featured'      => __( 'Featured', 'the7mk2' ),
							'by_id'         => _x( 'Manual Selection', 'Posts Query Control', 'the7mk2' ),
							'related'       => __( 'Related Products', 'the7mk2' ),
						],
					],
					'orderby'   => [
						'default' => 'date',
						'options' => [
							'date'       => __( 'Date', 'the7mk2' ),
							'title'      => __( 'Title', 'the7mk2' ),
							'price'      => __( 'Price', 'the7mk2' ),
							'popularity' => __( 'Popularity', 'the7mk2' ),
							'rating'     => __( 'Rating', 'the7mk2' ),
							'rand'       => __( 'Random', 'the7mk2' ),
							'menu_order' => __( 'Menu Order', 'the7mk2' ),
						],
					],
					'exclude'   => [
						'options' => [
							'current_post'     => __( 'Current Post', 'the7mk2' ),
							'manual_selection' => __( 'Manual Selection', 'the7mk2' ),
							'terms'            => __( 'Term', 'the7mk2' ),
						],
					],
					'include'   => [
						'options' => [
							'terms' => __( 'Term', 'the7mk2' ),
						],
					],
				],
				'exclude'         => [
					'posts_per_page',
					'exclude_authors',
					'authors',
					'offset',
					'related_fallback',
					'related_ids',
					'query_id',
					'avoid_duplicates',
					'ignore_sticky_posts',
				],
			]
		);

		$this->add_control(
			'external_filtering',
			[
				'label'        => __( 'External Filtering', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
				'condition'    => [
					'query_post_type!' => [ 'current_query', 'related' ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register content controls.
	 */
	protected function add_content_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Product Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_product_title',
			[
				'label'        => __( 'Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'product_title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h4',
				'condition' => [
					'show_product_title' => 'y',
				],
			]
		);

		$this->add_control(
			'product_title_width',
			[
				'label'     => __( 'Title Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_product_title' => 'y',
				],
				'render_type'    => 'template'
			]
		);

		$this->add_control(
			'product_title_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_product_title'  => 'y',
					'product_title_width' => 'normal',
				],
			]
		);

		$this->template( Price::class )->add_switch_control();

		$this->add_control(
			'show_rating',
			[
				'label'        => __( 'Rating', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_short_description',
			[
				'label'        => __( 'Short Description', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'description_width',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_short_description' => 'y',
				],
				'render_type'    => 'template'
			]
		);

		$this->add_control(
			'description_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_short_description' => 'y',
					'description_width'      => 'normal',
				],
			]
		);

		$this->template( Sale_Flash::class )->add_switch_control();

		$this->add_control(
			'skins_heading',
			[
				'label'     => __( 'Skin', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Choose Skin', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content_below_img',
				'options' => [
					'content_below_img' => __( 'Classic', 'the7mk2' ),
					'btn_on_img'        => __( 'Icon on image', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'show_product_image',
			[
				'label'        => __( 'Product Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'layout' => 'content_below_img',
				],
			]
		);

		$this->add_control(
			'show_add_to_cart',
			[
				'label'        => __( 'Button', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'layout' => 'content_below_img',
				],
			]
		);

		$this->add_control(
			'product_icon_visibility',
			[
				'label'        => __( 'Visibility', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'always',
				'options'      => [
					'always'   => __( 'Always', 'the7mk2' ),
					'on-hover' => __( 'On box hover', 'the7mk2' ),
				],
				'prefix_class' => 'cart-btn-',
				'condition'    => [
					'layout' => 'btn_on_img',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register content style controls.
	 */
	protected function add_content_style_controls() {
		$this->start_controls_section(
			'content_style_section',
			[
				'label' => __( 'Content Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'post_content_alignment',
			[
				'label'     => __( 'Text Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'default'   => 'left',
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocom-list-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_padding',
			[
				'label'      => __( 'Content Area Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .woocom-list-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add image style controls.
	 */
	protected function add_image_style_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label'      => __( 'Image', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'layout',
							'operator' => '==',
							'value'    => 'btn_on_img',
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'layout',
									'operator' => '==',
									'value'    => 'content_below_img',
								],
								[
									'name'     => 'show_product_image',
									'operator' => '==',
									'value'    => 'y',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'item_preserve_ratio',
			[
				'label'        => __( 'Preserve Image Proportions', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'y',
				'return_value' => 'y',
				'prefix_class' => 'preserve-img-ratio-',
			]
		);

		$this->add_basic_responsive_control(
			'item_ratio',
			[
				'label'     => __( 'Image Ratio', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0.66,
				],
				'range'     => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'condition' => [
					'item_preserve_ratio!' => 'y',
				],
				'selectors' => [
					'{{WRAPPER}}:not(.preserve-img-ratio-y) .img-ratio-wrapper' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
				],
			]
		);

		$this->add_control(
			'img_border',
			[
				'label'      => __( 'Border', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .img-border' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'img_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .img-border' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_hover_style',
			[
				'label'   => __( 'Hover Style', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover_image',
				'options' => [
					''            => __( 'No Hover', 'the7mk2' ),
					'quick_scale' => __( 'Quick scale', 'the7mk2' ),
					'slow_scale'  => __( 'Slow scale', 'the7mk2' ),
					'hover_image' => __( 'Hover Image', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'image_hover_trigger',
			[
				'label'     => __( 'Enable Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'image',
				'options'   => [
					'image' => __( 'On image hover', 'the7mk2' ),
					'box'   => __( 'On box hover', 'the7mk2' ),
				],
				'condition' => [
					'image_hover_style!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label'     => __( 'Background Overlay', 'the7mk2' ),
						'selectors' => [
							'{{SELECTOR}}' => 'content: ""',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .img-wrap:before, {{WRAPPER}} .img-wrap:after',
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label'     => __( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .img-border' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .img-border',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '{{WRAPPER}} .img-wrap img',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => __( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .img-wrap' => 'opacity: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label'     => __( 'Background Overlay', 'the7mk2' ),
						'selectors' => [
							'{{SELECTOR}}' => 'content: ""',
						],
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}} { transition: all 0.3s; }
							{{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .img-wrap:after',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => __( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img:hover .img-border' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '{{WRAPPER}} .wf-cell:hover {z-index: 1;} {{WRAPPER}} .woo-buttons-on-img .img-border { transition: all 0.3s; } {{WRAPPER}} .woo-buttons-on-img:hover .img-border',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} .woo-buttons-on-img:hover .img-wrap img',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => __( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woo-buttons-on-img:hover .img-wrap' => 'opacity: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register title style controls.
	 */
	protected function add_title_style_controls() {
		$this->start_controls_section(
			'post_title_style_section',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_product_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_title',
				'label'          => __( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .product-title',
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs( 'post_title_style_tabs' );

		$this->start_controls_tab(
			'post_title_normal_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'custom_title_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .product-title a'       => 'color: {{VALUE}}',
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .product-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_title_hover_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_title_color_hover',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .product-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_title_bottom_margin',
			[
				'label'      => __( 'Spacing Above Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .product-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_rating_style_controls() {
		$this->start_controls_section(
			'show_rating_style',
			[
				'label'     => __( 'Rating', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'stars_size',
			[
				'label'     => __( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .star-rating' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'empty_star_color',
			[
				'label'     => __( 'Empty Star Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .star-rating:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'full_star_color',
			[
				'label'     => __( 'Filled Star Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .star-rating span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'gap_above_rating',
			[
				'label'      => __( 'Spacing Above Rating', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .star-rating-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register short description style controls.
	 */
	protected function add_short_description_style_controls() {
		$this->start_controls_section(
			'short_description_style_section',
			[
				'label'     => __( 'Short Description', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_short_description' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'short_description_typography',
				'label'          => __( 'Typography', 'the7mk2' ),
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .woocommerce-product-details__short-description',
			]
		);

		$this->add_control(
			'short_description_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-details__short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'short_description_bottom_margin',
			[
				'label'      => __( 'Spacing Above Description', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-details__short-description' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add Icon On Image style controls.
	 */
	protected function add_icon_on_image_style_controls() {
		$this->start_controls_section(
			'icon_on_image_style',
			[
				'label'     => __( 'Icon On Image', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => 'btn_on_img',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_on_image_icon_size',
			[
				'label'      => __( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons i:before' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_on_image_background_size',
			[
				'label'      => __( 'Background Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons' => '--image-button-background-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_on_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk1' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons'     => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a'   => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a i' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_on_image_style' );

		$this->start_controls_tab(
			'tab_icon_on_image_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_on_image_icon_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_on_image_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a { background-color: transparent;} {{WRAPPER}} .woo-buttons-on-img .woo-buttons a:hover'   => 'background: {{VALUE}};',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a:focus'   => 'background: {{VALUE}};',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a i'       => 'background: {{VALUE}};',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons:hover a i' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_on_image_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_on_image_icon_hover_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons i { transition: all .3s; }
					{{WRAPPER}} .woo-buttons-on-img .woo-buttons:hover i, {{WRAPPER}} .woo-buttons a:focus i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_on_image_background_hover_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons:hover a'   => 'background: {{VALUE}};',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons:hover a i' => 'background: {{VALUE}};',
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a:focus'   => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'expand_product_icon_on_hover',
			[
				'label'        => __( 'Expand Icon On Hover', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'selectors'    => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons .filter-popup' => 'display: inline-block',
				],
				'condition'    => [
					'layout' => 'btn_on_img',
				],
			]
		);

		$this->add_control(
			'icon_on_image_text_heading',
			[
				'label'     => __( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'expand_product_icon_on_hover' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_on_image_text_typography',
				'label'     => __( 'Typography', 'the7mk2' ),
				'selector'  => '{{WRAPPER}} .woo-buttons-on-img .woo-buttons .filter-popup',
				// Text should use icon line height to be vertically centered.
				'exclude'   => [
					'line_height',
				],
				'condition' => [
					'expand_product_icon_on_hover' => 'y',
				],
			]
		);

		$this->add_control(
			'icon_on_image_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons .filter-popup' => 'color: {{VALUE}};',
				],
				'condition' => [
					'expand_product_icon_on_hover' => 'y',
				],
			]
		);

		$this->add_control(
			'icon_on_image_text_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons a { background-color: transparent;} {{WRAPPER}} .woo-buttons-on-img .woo-buttons:hover a' => 'background: {{VALUE}};',
				],
				'condition' => [
					'expand_product_icon_on_hover' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_on_image_text_padding',
			[
				'label'              => __( 'Padding', 'the7mk2' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'allowed_dimensions' => 'horizontal',
				'default'            => [
					'top'      => '0',
					'right'    => '',
					'bottom'   => '0',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'          => [
					'{{WRAPPER}} .woo-buttons-on-img .woo-buttons .filter-popup' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
				'condition'          => [
					'expand_product_icon_on_hover' => 'y',
				],
			]
		);

		$this->add_control(
			'product_icon_heading',
			[
				'label'     => __( 'Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_icon_h_position',
			[
				'label'        => __( 'Horizontal Position', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'right',
				'options'      => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'icon-position-',
				'selectors'    => [
					'{{WRAPPER}}.icon-position-left .woo-buttons a .popup-icon' => 'right: unset; left: 0px;',
					'{{WRAPPER}}.icon-position-left .woo-buttons a'             => 'float: none;',
				],
			]
		);

		$this->add_basic_responsive_control(
			'product_icon_h_offset',
			[
				'label'      => __( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-h-offset: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'product_icon_v_position',
			[
				'label'        => __( 'Vertical Position', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'bottom',
				'options'      => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'icon-position-',
			]
		);

		$this->add_basic_responsive_control(
			'product_icon_v_offset',
			[
				'label'      => __( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-v-offset: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}
}
