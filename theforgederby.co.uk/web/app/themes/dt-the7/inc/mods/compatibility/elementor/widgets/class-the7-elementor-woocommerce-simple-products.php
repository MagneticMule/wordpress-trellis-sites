<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Controls_Stack;
use Elementor\Core\Responsive\Responsive;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\DT_Shortcode_Products_Masonry_Adapter;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\The7_Shortcode_Adapter_Interface;
use Elementor\Group_Control_Border;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Current_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Woocommerce_Simple_Products extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-woo-simple-products';
	}

	protected function the7_title() {
		return __( 'Simple Products', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-products';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-simple-products.less';
	}

	public function get_style_depends() {
	   return [ 'the7-woocommerce-simple-products' ];
	}

	public function get_script_depends() {
		$scripts = [
			'the7-woocommerce-simple-products',
		];

		if ( $this->is_preview_mode() ) {
			$scripts[] = 'the7-woocommerce-simple-products-preview';
		}

		return $scripts;
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content.
		$this->register_query_controls();
		$this->add_layout_content_controls();
		$this->add_product_content_controls();
		$this->add_pagination_content_controls();

		// Style.
		$this->add_layout_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_add_to_cart' => 'yes',
			]
		);
	}

	protected function register_query_controls() {

		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'current_query_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __(
					'Note that the amount of posts per page is the product of "Products per row" and "Rows per page" settings from "Appearance"->"Customize"->"WooCommerce"->"Products Catalog".',
					'the7mk2'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'query_post_type' => 'current_query',
				],
			]
		);

		$this->add_group_control(
			The7_Group_Control_Query::get_type(),
			[
				'name'            => The7_Shortcode_Adapter_Interface::QUERY_CONTROL_NAME,
				'query_post_type' => 'product',
				'presets'         => [ 'include', 'exclude', 'order' ],
				'fields_options'  => [
					'post_type' => [
						'default' => 'product',
						'options' => [
							'current_query'   => __( 'Current Query', 'the7mk2' ),
							'product'         => __( 'Latest Products', 'the7mk2' ),
							'sale'            => __( 'Sale', 'the7mk2' ),
							'top'             => __( 'Top rated products', 'the7mk2' ),
							'best_selling'    => __( 'Best selling', 'the7mk2' ),
							'featured'        => __( 'Featured', 'the7mk2' ),
							'by_id'           => _x( 'Manual Selection', 'Posts Query Control', 'the7mk2' ),
							'related'         => __( 'Related Products', 'the7mk2' ),
							'recently_viewed' => __( 'Recently Viewed', 'the7mk2' ),
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

		$this->end_controls_section();
	}

	protected function add_pagination_content_controls() {

		$this->start_controls_section(
			'pagination',
			[
				'label' => __( 'Pagination', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$post_types_without_pagination = [
			'current_query',
			'related',
			'recently_viewed',
		];

		$this->add_control(
			'loading_mode',
			[
				'label'     => __( 'Pagination Mode', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'disabled',
				'options'   => [
					'disabled'        => 'Disabled',
					'standard'        => 'Standard',
					'js_pagination'   => 'JavaScript pages',
					'js_more'         => '"Load more" button',
					'js_lazy_loading' => 'Infinite scroll',
				],
				'condition' => [
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		// Load more additional settings.
		$this->add_control(
			'pagination_load_more_text',
			[
				'label'       => __( 'Button Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Load more', 'the7mk2' ),
				'placeholder' => '',
				'condition'   => [
					'loading_mode' => 'js_more',
					'query_post_type!' => $post_types_without_pagination,				],
			]
		);

		$this->add_control(
			'pagination_show_load_more_icon',
			[
				'label'        => __( 'Icon', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'loading_mode' => 'js_more',
					'query_post_type!' => $post_types_without_pagination,				],
			]
		);

		$this->add_control(
			'pagination_load_more_icon',
			[
				'label'     => '',
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-arrow-circle-down',
					'library' => 'fa-solid',
				],
				'condition' => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					'query_post_type!' => $post_types_without_pagination,				],
			]
		);

		$this->add_control(
			'pagination_load_more_icon_position',
			[
				'label'     => __( 'Icon Position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'toggle'    => false,
				'default'   => 'before',
				'options'   => [
					'before' => __( 'Before', 'the7mk2' ),
					'after'  => __( 'After', 'the7mk2' ),
				],
				'condition' => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					'query_post_type!' => $post_types_without_pagination,				],
			]
		);

		$this->add_control(
			'pagination_load_more_icon_spacing',
			[
				'label'      => __( 'Icon Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 200,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator a.button-load-more i:first-child' => 'margin: 0 {{SIZE}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .paginator a.button-load-more i:last-child'  => 'margin: 0 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					'query_post_type!' => $post_types_without_pagination,				],
			]
		);

		// Disabled pagination.
		$this->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'loading_mode',
									'operator' => '==',
									'value'    => 'disabled',
								],
								[
									'name'     => 'query_post_type',
									'operator' => '!==',
									'value'    => 'current_query',
								],
							],
						],
						[
							'name'     => 'query_post_type',
							'operator' => 'in',
							'value'    => [ 'related', 'recently_viewed' ],
						],
					],
				],
			]
		);

		// Standard pagination.
		$this->add_control(
			'st_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __(
					'Leave empty to use value from the WP Reading settings. Set "-1" to show all posts.',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'standard',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		// JS pagination.
		$this->add_control(
			'jsp_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_pagination',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		$this->add_control(
			'jsp_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_pagination',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		// JS load more.
		$this->add_control(
			'jsm_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_more',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		$this->add_control(
			'jsm_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_more',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		// JS infinite scroll.
		$this->add_control(
			'jsl_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_lazy_loading',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		$this->add_control(
			'jsl_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'     => 'js_lazy_loading',
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		$this->add_control(
			'pagination_scroll',
			[
				'label'        => __( 'Scroll to Top', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'description' => __( 'When enabled, scrolls page to top of widget.', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition' => [
					'loading_mode'                   => 'js_pagination',
				],
			]
		);

		$this->add_control(
			'pagination_scroll_offset',
			[
				'label'       => __( 'Scroll offset (px)', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Negative value will scroll page above top of widget; positive - below it.', 'the7mk2' ),
				'default'     => 0,
				'condition'   => [
					'pagination_scroll' => 'y',
				],
			]
		);

		// Posts offset.
		$this->add_control(
			'posts_offset',
			[
				'label'       => __( 'Posts Offset', 'the7mk2' ),
				'description' => __(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					'query_post_type!' => $post_types_without_pagination,
				],
			]
		);

		$this->add_control(
			'show_all_pages',
			[
				'label'        => __( 'Show All Pages In Paginator', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'conditions'   => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'loading_mode',
									'operator' => 'in',
									'value'    => [ 'standard', 'js_pagination' ],
								],
								[
									'name'     => 'query_post_type',
									'operator' => '!in',
									'value'    => [ 'related', 'recently_viewed' ],
								],
							],
						],
						[
							'name'     => 'query_post_type',
							'operator' => 'in',
							'value'    => [ 'current_query' ],
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

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
				'selectors'   => [
					'{{WRAPPER}} .dt-css-grid' => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
				'selectors'      => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},1fr)',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_control(
			'gap_between_posts',
			[
				'label'      => __( 'Columns Gap', 'the7mk2' ),
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
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '20',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor' ),
				'label_on'  => __( 'On', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}
	
	protected function add_product_content_controls() {

		$this->start_controls_section(
			'product_content_section',
			[
				'label' => __( 'Product Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'     => __( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'options'   => [
					'box'  => __( 'Whole box', 'the7mk2' ),
					'button' => __( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'show_product_image',
			[
				'label'        => __( 'Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => __( 'Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
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
					'show_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_width',
			[
				'label'     => __( 'Title Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_title' => 'y',
				],
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_title'  => 'y',
					'title_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'     => __( 'Rating', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'     => __( 'Short Description', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
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
					'show_description' => 'yes',
				],
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
					'show_description'  => 'yes',
					'description_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'show_add_to_cart',
			[
				'label'     => __( 'Add To Cart', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function add_layout_style_controls() {

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

		$this->start_controls_section(
			'section_design_box',
			[
				'label' => __( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => __( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'prefix_class' => 'auto-height-',
			]
		);
		
		$this->add_basic_responsive_control(
			'box_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .wf-cell' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'adaptive_height!' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'content_position',
			[
				'label'                => __( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'   => 'align-items: flex-start;align-content: flex-start;',
					'center' => 'align-items: center;align-content: center;',
					'bottom'  => 'align-items: flex-end;align-content: flex-end;',
				],
				'selectors'    => [
					'{{WRAPPER}} .wf-cell' => '{{VALUE}}',
				],
				'condition' => [
					'adaptive_height!' => 'y',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell',
				'exclude'	=> [
					'color'
				]
			]
		);

	    $this->add_basic_responsive_control(
	     	'box_border_radius',
	     	[
	     		'label' => __('Border Radius', 'the7mk2'),
	     		'type' => Controls_Manager::DIMENSIONS,
	     		'size_units' => ['px', '%'],
	     		'selectors' =>  [
	     			'{{WRAPPER}} .wf-cell' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
	     		]
	     	]
	    );

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .wf-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_box_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'box_border_color',
            [
                'label'     => __( 'Border Color', 'the7mk2' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wf-cell' => 'border-color: {{VALUE}}',
                ]
            ]
        );

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bg_hover_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'box_hover_border_color',
            [
                'label'     => __( 'Border Color', 'the7mk2' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wf-cell:hover' => 'border-color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_hover_shadow',
				'label' => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'widget_divider_section',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// Featured Image.
		$this->start_controls_section(
			'fetatured_image_style',
			[
				'label'     => __( 'Featured Image', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_product_image' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'align_image',
			[
				'label'                => __( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'prefix_class'         => 'img-align%s-',
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap;',
					'left'  => 'flex-flow: row nowrap;',
					'right' => 'flex-flow: row nowrap;',
				],
				'selectors'            => [
					'{{WRAPPER}} .wrapper.post' => '{{VALUE}}',
				],
				'condition'            => [
					'show_product_image' => 'y',
				],
			]
		);

		$img_position_options            = [
			'start'  => __( 'Start', 'the7mk2' ),
			'center' => __( 'Center', 'the7mk2' ),
			'end'    => __( 'End', 'the7mk2' ),
		];
		$img_position_options_on_devices = [ '' => __( 'Default', 'the7mk2' ) ] + $img_position_options;


		$this->add_basic_responsive_control(
			'image_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $img_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $img_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $img_position_options_on_devices,
					],
				],
				'prefix_class'         => 'image-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-related-product-thumb, {{WRAPPER}} .product-content' => '{{VALUE}}',
				],
				'condition'            => [
					'show_product_image' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_size',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 130,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}'                                                                      => '--image-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-related-product-thumb'                                                 => 'width: var(--image-size);',
				],
			]
		);

		$this->add_control(
			'item_preserve_ratio',
			[
				'label'        => __( 'Preserve Image Proportions', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'y',
				'prefix_class' => 'preserve-img-ratio-',
			]
		);

		$this->add_basic_responsive_control(
			'item_ratio',
			[
				'label'      => __( 'Image Ratio', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 1,
				],
				'range'      => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'item_preserve_ratio',
							'operator' => '!=',
							'value'    => 'y',
						],
					],
				],
				'selectors'  => [
					'{{WRAPPER}}:not(.preserve-img-ratio-y) .img-ratio-wrapper' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'label' => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-related-product-thumb img',
			]
		);

		$this->add_basic_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'the7mk2' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .the7-related-product-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .the7-related-product-thumb .layzr-bg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_space',
			[
				'label'     => __( 'Image Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'                                                      => '--image-spacing: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}}.img-align-tablet-left .the7-related-product-thumb'  => 'margin: 0 var(--image-spacing) 0 0',
					'(tablet) {{WRAPPER}}.img-align-tablet-right .the7-related-product-thumb' => 'margin: 0 0 0 var(--image-spacing)',
					'(tablet) {{WRAPPER}}.img-align-tablet-left .product-content, {{WRAPPER}}.img-align-tablet-right .product-content'  => 'width: calc(100% - var(--image-size) - var(--image-spacing))',
					'(tablet) {{WRAPPER}}.img-align-tablet-top .product-content'   => 'width: 100%;',
					'(mobile) {{WRAPPER}}.img-align-mobile-left .the7-related-product-thumb'  => 'margin: 0 var(--image-spacing) 0 0',
					'(mobile) {{WRAPPER}}.img-align-mobile-right .the7-related-product-thumb' => 'margin: 0 0 0 var(--image-spacing)',
					'(tablet) {{WRAPPER}}.img-align-tablet-top .the7-related-product-thumb'   => 'margin: 0 0 var(--image-spacing) 0',
					'(mobile) {{WRAPPER}}.img-align-mobile-left .product-content, {{WRAPPER}}.img-align-mobile-right .product-content'  => 'width: calc(100% - var(--image-size) - var(--image-spacing))',

					'(mobile) {{WRAPPER}}.img-align-mobile-top .the7-related-product-thumb'   => 'margin: 0 0 var(--image-spacing) 0',
					'(mobile) {{WRAPPER}}.img-align-mobile-top .product-content'   => 'width: 100%;',
				],
			]
		);

		$this->end_controls_section();

		// Title Style.
		$this->start_controls_section(
			'content_area_style',
			[
				'label'     => __( 'Content Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'content_alignment',
			[
				'label'        => __( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
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
				'prefix_class' => 'slide-h-position%s-',
				'default'      => 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'selectors'    => [
					'{{WRAPPER}} .product-content' => '{{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'content_area_padding',
			[
				'label'      => __( 'Content Area Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .product-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .heading',
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.wf-cell:hover .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Price Style.
		$this->start_controls_section(
			'price_style',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'normal_price_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Normal Price', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Normal Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->add_control(
			'normal_price_text_color',
			[
				'label'     => __( 'Normal Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Sale Price', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => __( 'Old Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price del',
			]
		);

		$this->add_control(
			'sale_price_text_color',
			[
				'label'     => __( 'Old Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'old_price_line_color',
			[
				'label'     => __( 'Old Price Line Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_new_price_typography',
				'label'    => __( 'New Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price ins',
			]
		);

		$this->add_control(
			'sale_new_price_text_color',
			[
				'label'     => __( 'New Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'price_space',
			[
				'label'      => __( 'Price Top Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .price' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'show_rating_style',
			[
				'label'     => __( 'Rating', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
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
				'condition' => [
					'show_rating' => 'yes',
				],
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
				'condition' => [
					'show_rating' => 'yes',
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
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'gap_above_rating',
			[
				'label'      => __( 'Rating Top Spacing', 'the7mk2' ),
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

		$this->start_controls_section(
			'short_description',
			[
				'label'     => __( 'Short Description', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .short-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.wf-cell:hover .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'gap_above_description',
			[
				'label'      => __( 'Description Top Spacing', 'the7mk2' ),
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
					'{{WRAPPER}} .short-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		

		$this->start_controls_section(
			'pagination_style_tab',
			[
				'label'     => __( 'Pagination', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'loading_mode' => [ 'standard', 'js_pagination', 'js_more' ],
				],
			]
		);

		$this->add_control(
			'pagination_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'center',
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
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .paginator' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_style',
			[
				'label'          => __( 'Pointer', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'underline',
				'options'        => [
					'none'        => __( 'None', 'the7mk2' ),
					'underline'   => __( 'Underline', 'the7mk2' ),
					'overline'    => __( 'Overline', 'the7mk2' ),
					'double-line' => __( 'Double Line', 'the7mk2' ),
					'framed'      => __( 'Framed', 'the7mk2' ),
					'background'  => __( 'Background', 'the7mk2' ),
					'text'        => __( 'Text', 'the7mk2' ),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'pagination_animation_line',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				],
				'condition' => [
					'pagination_style' => [ 'underline', 'overline', 'double-line' ],
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->add_control(
			'pagination_animation_framed',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				],
				'condition' => [
					'pagination_style' => 'framed',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->add_control(
			'pagination_animation_background',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'                   => 'Fade',
					'grow'                   => 'Grow',
					'shrink'                 => 'Shrink',
					'sweep-left'             => 'Sweep Left',
					'sweep-right'            => 'Sweep Right',
					'sweep-up'               => 'Sweep Up',
					'sweep-down'             => 'Sweep Down',
					'shutter-in-vertical'    => 'Shutter In Vertical',
					'shutter-out-vertical'   => 'Shutter Out Vertical',
					'shutter-in-horizontal'  => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none'                   => 'None',
				],
				'condition' => [
					'pagination_style' => 'background',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->add_control(
			'pagination_animation_text',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => [
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				],
				'condition' => [
					'pagination_style' => 'text',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'label'    => __( 'Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .paginator a, {{WRAPPER}} .paginator .button-load-more',
				'exclude'  => [
					'text_decoration',
				],
			]
		);

		$this->add_control(
			'pagination_underline_height',
			[
				'label'      => __( 'Pointer Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator' => '--filter-pointer-border-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'pagination_style!' => [ 'background', 'none' ],
				],
			]
		);

		$this->start_controls_tabs( 'pagination_elements_style' );

		$this->start_controls_tab(
			'pagination_normal_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'pagination_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator' => '--filter-title-color-normal: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_pointer_normal_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator' => '--filter-pointer-bg-color-normal: {{VALUE}};',
				],
				'condition' => [
					'loading_mode' => 'js_more',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_hover_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'pagination_text_hover_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-title-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_pointer_hover_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-pointer-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_active_style',
			[
				'label'     => __( 'Active', 'the7mk2' ),
				'condition' => [
					'loading_mode!' => 'js_more',
				],
			]
		);

		$this->add_control(
			'pagination_text_active_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-title-color-active: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_pointer_active_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a'                  => '--filter-pointer-bg-color-active: {{VALUE}};',
					'{{WRAPPER}} .paginator a.button-load-more' => '--filter-pointer-bg-color-active: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_bg_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
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
					'{{WRAPPER}} .paginator' => '--filter-pointer-bg-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'pagination_style' => 'background',
				],
			]
		);

		$this->add_control(
			'pagination_element_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pagination_element_margin',
			[
				'label'      => __( 'Margin', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'gap_before_pagination',
			[
				'label'      => __( 'Spacing Above Pagination', 'the7mk2' ),
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
					'{{WRAPPER}} .paginator' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'loading_mode' => [ 'standard', 'js_pagination', 'js_more' ],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$query_builder = new Products_Query( $settings, 'query_' );
		$query         = $query_builder->create();

		if ( ! $query->have_posts() ) {
			return;
		}

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );
		$this->add_pagination_render_attributes_for( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

			if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
				echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
			}

			$posts_limit = $this->get_pagination_posts_limit();
			$columns = [
				'd'  => $settings['widget_columns'],
				't'  => $settings['widget_columns_tablet'],
				'p'  => $settings['widget_columns_mobile'],
				'wd' => $settings['widget_columns_wide_desktop'],
			];

			echo '<div class="dt-css-grid custom-pagination-handler" data-columns="' . esc_attr( wp_json_encode( $columns ) ) . '">';
				$index = 0;
				while ( $query->have_posts() ) {
					$query->the_post();
					$index++;

					$product = wc_get_product();

					// It can be empty if query is not about products.
					if ( ! $product ) {
						continue;
					}

					$visibility = 'visible';
					if ( $posts_limit >= 0 && $query->current_post >= $posts_limit ) {
						$visibility = 'hidden';
					}

					$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'link_wrapper', $index );

					$this->add_render_attribute( $repeater_setting_key, 'class', [ 
						'wf-cell',
						$visibility
					] );

					$post_class_array = [
						'post',
						'visible',
						'wrapper'
					];

					if ( ! has_post_thumbnail() ) {
						$post_class_array[] = 'no-img';
					}

					$link_key = 'link_' . $index;

					$link_attridutes        = $this->get_custom_link_attributes( $settings );
					$this->add_link_attributes( $link_key, $link_attridutes, true );
					$btn_attributes = $this->get_render_attribute_string( $link_key );

					if ( 'button' === $settings['link_click'] ) {
						$wrapper       			= '<div '. $this->get_render_attribute_string( $repeater_setting_key ) .'>';
						$wrapper_close 			= '</div>';
					} else {
						$wrapper       			= '<a '. $btn_attributes . $this->get_render_attribute_string( $repeater_setting_key ) .'>';
						$wrapper_close 			= '</a>';
					}

					echo $wrapper;
						echo '<article class="'. esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) .'">';
							if ( $settings['show_product_image'] ) {
								$post_media = $this->product_image( $settings, $product );
								echo '<div class="the7-related-product-thumb"><div class="the7-product-thumb">';
								echo $post_media;
								echo '</div></div>';
							}

							echo '<div class="product-content">';
								if ( $settings['show_title'] ) {
									echo $this->display_product_title( $settings, $settings['product_title_tag'], $product );
								}

								if ( $settings['show_price'] ) {
									echo '<span class="price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
								}

								if ( $settings['show_rating'] && wc_review_ratings_enabled() ) {
									$price_html = wc_get_rating_html( $product->get_average_rating() );
									if ( $price_html ) {
										echo '<div class="star-rating-wrap">' . $price_html . '</div>'; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
								}

								if ( $settings['show_description'] ) {
									echo $this->get_short_description( $product );
								}

								if ( $settings['show_add_to_cart'] ) {
									echo '<div class="woo-buttons">';
									$this->display_add_to_cart( $product );
									echo '</div>';
								}
							echo '</div>';
						echo '</article>';
					echo $wrapper_close;
				}

				wp_reset_postdata();

			echo '</div>';

			$this->display_pagination( $settings['loading_mode'], $query );

		echo '</div>';

		$post_type  = $this->get_settings_for_display( 'query_post_type' );
		$is_preview = $this->is_preview_mode();

		if ( ! $is_preview && $post_type === 'recently_viewed' ) {
			Recently_Viewed_Products::track_via_js();
		}
	}

	protected function get_custom_link_attributes( $settings ) {
		return [
			'url'   => get_the_permalink(),
			'target' => '',
		];
	}
	
	/**
	 * @param string    $loading_mode
	 * @param \WP_Query $query
	 */
	protected function display_pagination( $loading_mode, \WP_Query $query ) {
		if ( 'standard' === $loading_mode ) {
			$this->display_standard_pagination( $query->max_num_pages, $this->get_pagination_wrap_class() );
		} elseif ( in_array( $loading_mode, [ 'js_more', 'js_lazy_loading' ], true ) ) {
			$this->display_load_more_button( $this->get_pagination_wrap_class( 'paginator-more-button' ) );
		} elseif ( 'js_pagination' === $loading_mode ) {
			echo '<div class="' . esc_attr( $this->get_pagination_wrap_class() ) . '" role="navigation"></div>';
		}
	}

	/**
	 * @param int    $max_num_pages
	 * @param string $class
	 */
	protected function display_standard_pagination( $max_num_pages, $class = 'paginator' ) {
		$add_pagination_filter = has_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );
		remove_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );

		$num_pages  = $this->get_settings_for_display( 'show_all_pages' ) ? 9999 : 5;
		$item_class = 'page-numbers filter-item';
		$no_next    = '';
		$no_prev    = '';
		$prev_text  = '<i class="dt-icon-the7-arrow-35-1" aria-hidden="true"></i>';
		$next_text  = '<i class="dt-icon-the7-arrow-35-2" aria-hidden="true"></i>';

		dt_paginator( null, compact( 'max_num_pages', 'class', 'num_pages', 'item_class', 'no_next', 'no_prev', 'prev_text', 'next_text' ) );

		$add_pagination_filter && add_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );
	}

	protected function display_load_more_button( $class = 'paginator-more-button' ) {
		echo dt_get_next_page_button(
			2,
			$class,
			$cur_page = 1,
			'highlighted filter-item',
			$this->get_settings_for_display( 'pagination_load_more_text' ),
			$this->get_elementor_icon_html( $this->get_settings_for_display( 'pagination_load_more_icon' ) ),
			$this->get_settings_for_display( 'pagination_load_more_icon_position' )
		);
	}

	/**
	 * @param string $class
	 *
	 * @return string
	 */
	protected function get_pagination_wrap_class( $class = '' ) {
		$settings = $this->get_settings_for_display();

		$wrap_class = [ 'paginator', 'filter-decorations', $class ];
		if ( $settings['pagination_style'] ) {
			$wrap_class[] = 'filter-pointer-' . $settings['pagination_style'];

			foreach ( $settings as $key => $value ) {
				if ( 0 === strpos( $key, 'pagination_animation' ) && $value ) {
					$wrap_class[] = 'filter-animation-' . $value;
					break;
				}
			}
		}

		return implode( ' ', array_filter( $wrap_class ) );
	}

	protected function display_add_to_cart( $product ) {
		$settings = $this->get_settings_for_display();

		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$this->add_product_add_to_cart_button_render_attributes( 'box-button', $product );

		$tag = 'div';
		if ( 'button' === $settings['link_click'] ) {
			$tag = 'a';
		}

		$this->template( Button::class )->render_button( 'box-button', esc_html( $product->add_to_cart_text() ), $tag );
	}

	protected function get_short_description( $product ) {

		$settings = $this->get_settings_for_display();

		$short_description = $product->get_short_description();
		if ( ! $short_description ) {
			return;
		}

		if ( $settings['description_words_limit'] && $settings['description_width'] === 'normal' ) {
			$short_description = wp_trim_words( $short_description, $settings['description_words_limit'] );
		}

		$output = '<p class="short-description">';
		$output .= wp_kses_post( $short_description );
		$output .= '</p>';

		return $output;
	}

	protected function product_image( $settings, $product ) {

		if ( $product->get_image_id() ) {

			$link_wrapper = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>';
			$link         = $product->get_permalink();

			$thumb_args = [
				'img_id'       => $product->get_image_id(),
				'class'        => implode( ' ', [ 'product-thumb', 'img-ratio-wrapper' ] ),
				'lazy_loading' => false,
				'custom'       => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				'wrap'   		=> $link_wrapper,
				'echo'         => false,
			];

			if ( $settings['link_click'] == 'box' ) {
				$thumb_args['wrap'] = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></div>';
			} else {
				$thumb_args['href'] = $link;
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		} else {
			$image = sprintf(
				'<img class="%s" src="%s" width="%s" height="%s">',
				'preload-me',
				get_template_directory_uri() . '/images/gray-square.svg',
				1500,
				1500
			);

			$post_media = sprintf(
				'<a %s>%s</a>',
				the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				$image
			);
		}

		return $post_media;
	}

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = Utils::validate_html_tag( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function display_product_title( $settings, $tag, $product ) {

		$tag        = esc_html( $tag );
		$title_link = [
			'href'  => $product->get_permalink(),
			'class' => 'product-title',
		];
		$title      = $product->get_name();
		if ( $settings['excerpt_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['excerpt_words_limit'] );
		}

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper     	= '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</a>';
		} else {
			$title_link['href'] 		= '';
			$title_link_wrapper    		= '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</span>';
		}

		$output = '<' . $tag . ' class="heading">';
		$output .=  sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * Return container class attribute.
	 *
	 * @param array $class
	 *
	 * @return string
	 */
	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'the7-related-products',
			'the7-elementor-widget',
			'loading-effect-none',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$loading_mode = $settings['loading_mode'];
		if ( 'standard' !== $loading_mode ) {
			$class[] = 'jquery-filter';
		}

		if ( 'js_lazy_loading' === $loading_mode ) {
			$class[] = 'lazy-loading-mode';
		}

		if ( $loading_mode === 'js_pagination' && $settings['show_all_pages'] ) {
			$class[] = 'show-all-pages';
		}

		if ( $settings['divider'] ) {
			$class[] = 'widget-divider-on';
		}

		if ( $settings['pagination_scroll'] == 'y') {
			$class[] = 'enable-pagination-scroll';
		}

		if ( $settings['title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		if ( ! $settings['show_product_image'] ) {
			$class[] = 'hide-product-image';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	protected function get_pagination_posts_limit() {

		$settings = $this->get_settings_for_display();

		$posts_limit = '-1';
		switch ( $settings['loading_mode'] ) {
			case 'js_pagination':
				$posts_limit = $settings['jsp_posts_per_page'];
				break;
			case 'js_more':
				$posts_limit = $settings['jsm_posts_per_page'];
				break;
			case 'js_lazy_loading':
				$posts_limit = $settings['jsl_posts_per_page'];
				break;
		}

		if ( ! $posts_limit ) {
			$posts_limit = get_option( 'posts_per_page' );
		}

		return $posts_limit;
	}

	/**
	 * @return array
	 */
	protected function add_pagination_render_attributes_for( $element ) {
		$settings = $this->get_settings_for_display();

		$loading_mode = $settings['loading_mode'];

		$data_pagination_mode = 'none';
		if ( in_array( $loading_mode, [ 'js_more', 'js_lazy_loading' ], true ) ) {
			$data_pagination_mode = 'load-more';
		} elseif ( $loading_mode === 'js_pagination' ) {
			$data_pagination_mode = 'pages';
		} elseif ( $loading_mode === 'standard' ) {
			$data_pagination_mode = 'standard';
		}

		$this->add_render_attribute( $element, 'data-cur-page', the7_get_paged_var() );
		$this->add_render_attribute( $element, 'data-post-limit', $this->get_pagination_posts_limit() );
		$this->add_render_attribute( $element, 'data-pagination-mode', $data_pagination_mode );
		$this->add_render_attribute( $element, 'data-scroll-offset', $settings['pagination_scroll_offset'] );
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-related-products',
			'~"%s"'
		);
		foreach ( $this->get_supported_devices() as $device => $dep ) {
			$less_vars->start_device_section( $device );
			$less_vars->add_keyword(
				'grid-columns',
				$this->get_responsive_setting( 'widget_columns' ) ?: 3
			);
			$less_vars->close_device_section();
		}
		$less_vars->add_keyword('grid-wide-columns', $settings['widget_columns_wide_desktop']);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}
	}
}
