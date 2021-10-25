<?php
/**
 * Class Products
 *
 * @package The7\Adapters\Elementor\Widgets\Woocommerce
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\General;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Sale_Flash;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Price;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use WC_Product;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products
 *
 * @package The7\Adapters\Elementor\Widgets\Woocommerce
 */
class Products_Carousel extends Products {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-wc-products-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function the7_title() {
		return __( 'Products Carousel', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function the7_icon() {
		return 'eicon-posts-carousel';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-products-carousel.less';
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
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-carousel-widget-preview' ];
		}

		return [];
	}

	protected function get_container_data_atts() {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'scroll-mode'          => $settings['slide_to_scroll'] === 'all' ? 'page' : '1',
			'col-num'              => $settings['widget_columns'],
			'wide-col-num'         => $settings['widget_columns_wide_desktop'] ?: $settings['widget_columns'],
			'laptop-col'           => $settings['widget_columns_tablet'],
			'h-tablet-columns-num' => $settings['widget_columns_tablet'],
			'v-tablet-columns-num' => $settings['widget_columns_tablet'],
			'phone-columns-num'    => $settings['widget_columns_mobile'],
			'auto-height'          => $settings['adaptive_height'] ? 'true' : 'false',
			'col-gap'              => $settings['gap_between_posts']['size'],
			'col-gap-tablet'       => $settings['gap_between_posts_tablet']['size'],
			'col-gap-mobile'       => $settings['gap_between_posts_mobile']['size'],
			'stage-padding'        => $settings['stage_padding']['size'],
			'speed'                => $settings['speed'],
			'autoplay'             => $settings['autoplay'] ? 'true' : 'false',
			'autoplay_speed'       => $settings['autoplay_speed'],
			'arrows'               => $settings['arrows'] !== 'never' ? 'true' : 'false',
			'arrows_tablet'        => $settings['arrows_tablet'] !== 'never' ? 'true' : 'false',
			'arrows_mobile'        => $settings['arrows_mobile'] !== 'never' ? 'true' : 'false',
			'bullet'               => $settings['show_bullets'] ? 'true' : 'false',
			'bullet_tablet'        => $settings['show_bullets_tablet'] ? 'true' : 'false',
			'bullet_mobile'        => $settings['show_bullets_mobile'] ? 'true' : 'false',
		];

		return ' ' . presscore_get_inlide_data_attr( $data_atts );
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		$this->print_inline_css();

		$settings = $this->get_settings_for_display();
		$this->add_main_wrapper_class_render_attribute_for( 'inner-wrapper' );
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ). '>';
			if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
				echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
			}

			echo '<div ' . $this->get_render_attribute_string( 'inner-wrapper' ) . $this->get_container_data_atts() . '>';
			// Loop query.
			$query = $this->get_query();

			// Related to print_render_attribute_string( 'woo_buttons_on_img' ); .
			$this->setup_woo_buttons_on_image_attributes();

			// Start loop.

				global $product;

				while ( $query->have_posts() ) {
					$query->the_post();

					$product = wc_get_product();

					if ( ! $product ) {
						continue;
					}
					//$this->setup_article_wrapper_attributes();
					$article_class = '';
					if ( $settings['image_hover_trigger'] === 'box' ) {
						$article_class = 'trigger-img-hover';
					}

					echo '<article ';
					wc_product_class(
						[
							'post',
							'visible',
							$article_class
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
				}


			echo '</div>';
			echo '<div class="owl-nav disabled">';
				echo '<a class="owl-prev" role="button">';
				Icons_Manager::render_icon( $settings['prev_icon'] );
				echo '</a>';
				echo '<a class="owl-next" role="button">';
				Icons_Manager::render_icon( $settings['next_icon'] );
				echo '</a>';
			echo '</div>';
		echo '</div>';
		$this->remove_hooks();
	}

	/**
	 * Setup article wrapper attribute.
	 */
	protected function setup_article_wrapper_attributes() {
		global $post;

		$settings = $this->get_settings_for_display();


		if ( $settings['image_hover_trigger'] === 'box' ) {
			$class[] = 'trigger-img-hover';
		}
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


	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content.
		$this->add_query_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();
		$this->add_scrolling_controls();
		$this->add_arrows_controls();
		$this->add_bullets_controls();


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
		$this->add_arrows_style_controls();
		$this->add_bullets_style_controls();
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

	protected function add_scrolling_controls() {

			$this->start_controls_section(
				'scrolling_section',
				[
					'label' => __( 'Scrolling', 'the7mk2' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'slide_to_scroll',
				[
					'label'   => __( 'Scroll Mode', 'the7mk2' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'single',
					'options' => [
						'single' => 'One slide at a time',
						'all'    => 'All slides',
					],
				]
			);

			$this->add_control(
				'speed',
				[
					'label'       => __( 'Transition Speed (ms)', 'the7mk2' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => '600',
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'        => __( 'Autoplay Slides', 'the7mk2' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'y',
					'default'      => '',
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'       => __( 'Autoplay Speed (ms)', 'the7mk2' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 6000,
					'min'         => 100,
					'max'         => 10000,
					'step'        => 10,
					'condition'   => [
						'autoplay' => 'y',
					],
				]
			);

			$this->end_controls_section();
		}

	protected function add_arrows_controls() {

			$this->start_controls_section(
				'arrows_section',
				[
					'label' => __( 'Arrows', 'the7mk2' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_responsive_control( 'arrows', [
				'label'        => __( 'Show Arrows', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'never'  => __( 'Never', 'the7mk2' ),
					'always' => __( 'Always', 'the7mk2' ),
					'hover'  => __( 'On Hover', 'the7mk2' ),
				],
				'device_args' => [
					'tablet' => [
						'options' => [
							'default'  => __( 'No change', 'the7mk2' ),
							'never'  => __( 'Never', 'the7mk2' ),
							'always' => __( 'Always', 'the7mk2' ),
							'hover'  => __( 'On Hover', 'the7mk2' ),
						],
					],
					'mobile' => [
						'options' => [
							'default'  => __( 'No change', 'the7mk2' ),
							'never'  => __( 'Never', 'the7mk2' ),
							'always' => __( 'Always', 'the7mk2' ),
							'hover'  => __( 'On Hover', 'the7mk2' ),
						],
					],
				],
				'default'      => 'always',
			] );

			$this->end_controls_section();
		}

	protected function add_bullets_controls() {
			$this->start_controls_section(
				'bullets_section',
				[
					'label' => __( 'Bullets', 'the7mk2' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_responsive_control(
				'show_bullets',
				[
					'label'        => __( 'Show Bullets', 'the7mk2' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'y',
					'default'      => 'y',
				]
			);
			$this->end_controls_section();
		}

	/**
	 * Register layout controls.
	 */
	protected function add_layout_content_controls() {

		$this->start_controls_section(
			'layout_content_section',
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
			'widget_columns_wide_desktop',
			[
				'label'       => __( 'Columns On A Wide Desktop', 'the7mk2' ),
				'description' => sprintf(
				// translators: %s: elementor content width.
					__( 'Apply when browser width is bigger than %s ("Content Width" Elementor setting).', 'the7mk2' ),
					the7_elementor_get_content_width_string()
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
			]
		);

		$this->add_responsive_control(
			'gap_between_posts',
			[
				'label'      => __( 'Columns Gap (px)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '40',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
			]
		);

		$this->add_control(
			'stage_padding',
			[
				'label'      => __( 'Stage Padding (px)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => __( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

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

	protected function add_arrows_style_controls() {
		$this->start_controls_section(
			'arrows_style',
			[
				'label'      => __( 'Arrows', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'arrows',
							'operator' => '!=',
							'value' => 'never',
						],
						[
							'name'  => 'arrows_tablet',
							'operator' => '!=',
							'value' => 'never',
						],
						[
							'name'  => 'arrows_mobile',
							'operator' => '!=',
							'value' => 'never',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __( 'Arrow Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'next_icon',
			[
				'label'   => __( 'Next Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icomoon-the7-font-the7-arrow-09',
					'library' => 'the7-icons',
				],
				'skin' => 'inline',
				'label_block' => false,
				'classes' => [ 'elementor-control-icons-svg-uploader-hidden' ],
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label'   => __( 'Previous Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icomoon-the7-font-the7-arrow-08',
					'library' => 'the7-icons',
				],
				'skin' => 'inline',
				'label_block' => false,
				'classes' => [ 'elementor-control-icons-svg-uploader-hidden' ],
			]
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			[
				'label'      => __( 'Arrow Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
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
					'{{WRAPPER}} .owl-nav i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .owl-nav a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrows_background_heading',
			[
				'label'     => __( 'Arrow style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'arrow_bg_width',
			[
				'label'      => __( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 30,
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
					'{{WRAPPER}} .owl-nav a' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_bg_height',
			[
				'label'      => __( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 30,
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
					'{{WRAPPER}} .owl-nav a' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Arrow Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 500,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-nav a' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrow_border_width',
			[
				'label'      => __( 'Arrow Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 25,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-nav a' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
			]
		);

		$this->start_controls_tabs( 'arrows_style_tabs' );

		$this->start_controls_tab(
			'arrows_colors',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label'       => __( 'Icon Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a i, {{WRAPPER}} .owl-nav a i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'       => __( 'Border Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a'       => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-nav a'       => 'background: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover_colors',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_color_hover',
			[
				'label'       => __( 'Icon Color Hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a:hover i'                                                                             => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a i:before { transition: color 0.3s; } {{WRAPPER}} .owl-nav a:hover i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color_hover',
			[
				'label'       => __( 'Border Color Hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'
					{{WRAPPER}} .owl-nav a { transition: all 0.3s; }
					{{WRAPPER}} .owl-nav a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color_hover',
			[
				'label'     => __( 'Background Hover Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'
					{{WRAPPER}} .owl-nav a { transition: all 0.3s; }
					{{WRAPPER}} .owl-nav a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'left_arrow_position_heading',
			[
				'label'     => __( 'Left Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'l_arrow_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'center',
			]
		);

		$this->add_responsive_control(
			'l_arrow_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'left',
			]
		);

		$this->add_responsive_control(
			'l_arrow_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_responsive_control(
			'l_arrow_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'right_arrow_position_heading',
			[
				'label'     => __( 'Right Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'r_arrow_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'center',
			]
		);

		$this->add_responsive_control(
			'r_arrow_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'right',
			]
		);

		$this->add_responsive_control(
			'r_arrow_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_responsive_control(
			'r_arrow_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -15,
				],
				'size_units' => [ 'px'],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_bullets_style_controls() {
		$this->start_controls_section(
			'bullets_style_block',
			[
				'label'      => __( 'Bullets', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_bullets',
							'value' => 'y',
						],
						[
							'name'  => 'show_bullets_tablet',
							'value' => 'y',
						],
						[
							'name'  => 'show_bullets_mobile',
							'value' => 'y',
						],
					],
				],
			]
		);


		$this->add_control(
			'bullets_Style_heading',
			[
				'label'     => __( 'Bullets Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bullets_style',
			[
				'label'   => __( 'Choose Bullets Style', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'small-dot-stroke',
				'options' => [
					'small-dot-stroke' => 'Small dot stroke',
					'scale-up'         => 'Scale up',
					'stroke'           => 'Stroke',
					'fill-in'          => 'Fill in',
					'ubax'             => 'Square',
					'etefu'            => 'Rectangular',
				],
			]
		);

		$this->add_control(
			'bullet_size',
			[
				'label'      => __( 'Bullets Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
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
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'bullet_gap',
			[
				'label'      => __( 'Gap Between Bullets', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
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
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'bullet_style_tabs' );

		$this->start_controls_tab(
			'bullet_colors',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bullet_color',
			[
				'label'       => __( 'Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'bullet_hover_colors',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bullet_color_hover',
			[
				'label'       => __( 'Hover Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-hover-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'bullet_active_colors',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bullet_color_active',
			[
				'label'       => __( 'Active Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-active-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'bullets_position_heading',
			[
				'label'     => __( 'Bullets Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bullets_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'bottom',
			]
		);
		$this->add_control(
			'bullets_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'center',
			]
		);

		$this->add_control(
			'bullets_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'bullets_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->end_controls_section();
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

		

		$class[] = the7_array_match(
			$settings['layout'],
			[
				'content_below_img' => 'cart-btn-below-img',
				'btn_on_img'        => 'cart-btn-on-img',
			]
		);

		

		$class[] = the7_array_match(
			$settings['image_hover_style'],
			[
				'quick_scale' => 'quick-scale-img',
				'slow_scale'  => 'scale-img',
				'hover_image' => 'wc-img-hover',
			]
		);

		

		$this->add_render_attribute( 'wrapper', 'class', $class );
	}

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-products-carousel',
			'elementor-owl-carousel-call',
			'loading-effect-none',
			'classic-layout-list'
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = the7_array_match(
			$settings['layout'],
			[
				'content_below_img' => 'cart-btn-below-img',
				'btn_on_img'        => 'cart-btn-on-img',
			]
		);

		

		$class[] = the7_array_match(
			$settings['image_hover_style'],
			[
				'quick_scale' => 'quick-scale-img',
				'slow_scale'  => 'scale-img',
				'hover_image' => 'wc-img-hover',
			]
		);

		$class[] = presscore_array_value(
			$settings['bullets_style'],
			[
				'scale-up'         => 'bullets-scale-up',
				'stroke'           => 'bullets-stroke',
				'fill-in'          => 'bullets-fill-in',
				'small-dot-stroke' => 'bullets-small-dot-stroke',
				'ubax'             => 'bullets-ubax',
				'etefu'            => 'bullets-etefu',
			]
		);

		if ( $settings['arrow_bg_color'] === $settings['arrow_bg_color_hover'] ) {
			$class[] = 'disable-arrows-hover-bg';
		}

		if ( $settings['product_title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		if ( ! $settings['show_product_image'] ) {
			$class[] = 'hide-product-image';
		}

		$class[] = presscore_array_value(
			$settings['arrows'],
			[
				'never'         => 'carousel-nav-display-never',
				'always'        => 'carousel-nav-display-always',
				'hover'         => 'carousel-nav-display-hover',
			]
		);
		$class[] = presscore_array_value(
			$settings['arrows_tablet'],
			[
				'never'         => 'carousel-nav-display-tablet-never',
				'always'        => 'carousel-nav-display-tablet-always',
				'hover'         => 'carousel-nav-display-tablet-hover',
			]
		);
		$class[] = presscore_array_value(
			$settings['arrows_mobile'],
			[
				'never'         => 'carousel-nav-display-mobile-never',
				'always'        => 'carousel-nav-display-mobile-always',
				'hover'         => 'carousel-nav-display-mobile-hover',
			]
		);

		$this->add_render_attribute( $element, 'class', $class );
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-products-carousel',
			'~"%s"'
		);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}

		if ( $settings['arrows'] !== 'never' || $settings['arrows_tablet'] !== 'never' || $settings['arrows_mobile'] !== 'never' ) {
			foreach ( $this->get_supported_devices() as $device => $dep ) {
				$less_vars->start_device_section( $device );

				$less_vars->add_keyword(
					'arrow-right-v-position',
					$this->get_responsive_setting( 'r_arrow_v_position' ) ?: 'center'
				);
				$less_vars->add_keyword(
					'arrow-right-h-position',
					$this->get_responsive_setting( 'r_arrow_h_position' ) ?: 'right'
				);
				$less_vars->add_unitized_number(
					'r-arrow-v-position',
					$this->get_responsive_setting( 'r_arrow_v_offset' )
				);
				$less_vars->add_unitized_number(
					'r-arrow-h-position',
					$this->get_responsive_setting( 'r_arrow_h_offset' )
				);

				$less_vars->add_keyword(
					'arrow-left-v-position',
					$this->get_responsive_setting( 'l_arrow_v_position' ) ?: 'center'
				);
				$less_vars->add_keyword(
					'arrow-left-h-position',
					$this->get_responsive_setting( 'l_arrow_h_position' ) ?: 'left'
				);
				$less_vars->add_unitized_number(
					'l-arrow-v-position',
					$this->get_responsive_setting( 'l_arrow_v_offset' )
				);
				$less_vars->add_unitized_number(
					'l-arrow-h-position',
					$this->get_responsive_setting( 'l_arrow_h_offset' )
				);

				$less_vars->close_device_section();
			}
		}

		$less_vars->add_rgba_color( 'bullet-color', $settings['bullet_color'] );
		$less_vars->add_rgba_color( 'bullet-color-hover', $settings['bullet_color_hover'] );
		$less_vars->add_keyword( 'bullets-v-position', $settings['bullets_v_position'] );
		$less_vars->add_keyword( 'bullets-h-position', $settings['bullets_h_position'] );
		$less_vars->add_pixel_number( 'bullet-v-position', $settings['bullets_v_offset'] );
		$less_vars->add_pixel_number( 'bullet-h-position', $settings['bullets_h_offset'] );
		
		$less_vars->add_pixel_number( 'arrow-bg-width', $settings['arrow_bg_width'] );
	}
}
