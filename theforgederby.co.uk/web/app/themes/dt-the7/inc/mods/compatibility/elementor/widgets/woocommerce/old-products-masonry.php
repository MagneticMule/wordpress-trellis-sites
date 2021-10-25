<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use DT_VCResponsiveColumnsParam;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\DT_Shortcode_Products_Masonry_Adapter;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\The7_Shortcode_Adapter_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Shortcode_Adaptor_Widget_Base;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;

defined( 'ABSPATH' ) || exit;

class Old_Products_Masonry extends The7_Elementor_Shortcode_Adaptor_Widget_Base {

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		// Setup shortcode adapter.
		require_once __DIR__ . '/../../shortcode-adapters/class-the7-elementor-products-masonry-adapter.php';
		$this->setup_shortcode_adapter( new DT_Shortcode_Products_Masonry_Adapter() );
	}

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-woo-masonry';
	}

	protected function the7_title() {
		return __( 'Old Product Masonry & Grid', 'the7mk2' );
	}

	protected function the7_icon() {
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

	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-widget-preview' ];
		}

		if ( $this->get_settings_for_display( 'query_post_type' ) === 'recently_viewed' ) {
			return [ 'the7-cookies' ];
		}

		return [];
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		parent::render();

		$post_type  = $this->get_settings_for_display( 'query_post_type' );
		$is_preview = $this->is_preview_mode();

		if ( ! $is_preview && $post_type === 'recently_viewed' ) {
			Recently_Viewed_Products::track_via_js();
		}
	}

	/**
	 * Register widget controls.
	 */
	protected function _register_controls() {
		$this->add_layout_content_controls();
		$this->register_query_controls();
		$this->add_filter_bar_content_controls();
		$this->add_pagination_content_controls();
	}

	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => __( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'mode',
			[
				'label'   => __( 'Mode', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'masonry',
				'options' => [
					'masonry' => 'Masonry',
					'grid'    => 'Grid',
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Text & button position:', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content_below_img',
				'options' => [
					'content_below_img' => 'Text & button below image',
					'btn_on_img'        => 'Text below image, button on image',
					'btn_on_img_hover'  => 'Text below image, button on image hover',
				],
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


		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition'      => [
					'responsiveness' => 'browser_width_based',
				],
			]
		);
		$this->add_control(
			'pwb_column_min_width',
			[
				'label'      => __( 'Column minimum width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 300,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition'  => [
					'responsiveness' => 'post_width_based',
				],
				'separator'  => 'before',
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
			]
		);

		$this->end_controls_section();
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

	protected function add_filter_bar_content_controls() {
		$this->start_controls_section(
			'categorization_section',
			[
				'label'     => __( 'Filter bar', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'query_post_type!' => [ 'current_query', 'related', 'recently_viewed' ],
				],
			]
		);

		$this->add_control(
			'show_categories_filter',
			[
				'label'        => __( 'Taxonomy filter', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'gap_below_category_filter_adapter',
			[
				'label'       => __( 'Gap', 'the7mk2' ),
				'description' => __( 'Leave empty to use default gap', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .filter' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'query_post_type!' => [ 'current_query', 'related' ],
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
				'label'     => __( 'Pagination mode', 'the7mk2' ),
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

		// Disabled pagination.
		$this->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total number of posts', 'the7mk2' ),
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
				'label'       => __( 'Number of posts to display on one page', 'the7mk2' ),
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
				'label'       => __( 'Total number of posts', 'the7mk2' ),
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
				'label'       => __( 'Number of posts to display on one page', 'the7mk2' ),
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
				'label'       => __( 'Total number of posts', 'the7mk2' ),
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
				'label'       => __( 'Number of posts to display on one page', 'the7mk2' ),
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
				'label'       => __( 'Total number of posts', 'the7mk2' ),
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
				'label'       => __( 'Number of posts to display on one page', 'the7mk2' ),
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
			'show_all_pages',
			[
				'label'        => __( 'Show all pages in paginator', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'conditions'   => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'loading_mode',
							'operator' => 'in',
							'value'    => [ 'standard', 'js_pagination' ],
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

		$this->add_control(
			'gap_before_pagination_adapter',
			[
				'label'       => __( 'Spacing', 'the7mk2' ),
				'description' => __( 'Leave empty to use default spacing', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .paginator' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'loading_mode' => [ 'standard', 'js_pagination', 'js_more' ],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_adapted_settings() {
		$settings = $this->get_settings_for_display();

		$settings['bwb_columns'] = DT_VCResponsiveColumnsParam::encode_columns(
			[
				'desktop'  => $settings['widget_columns'],
				'h_tablet' => $settings['widget_columns_tablet'],
				'v_tablet' => $settings['widget_columns_tablet'],
				'phone'    => $settings['widget_columns_mobile'],
			]
		);

		$gaps = [
			'gap_between_posts'         => 'gap_between_posts_adapter',
			'gap_below_category_filter' => 'gap_below_category_filter_adapter',
			'st_gap_before_pagination'  => 'gap_before_pagination_adapter',
			'jsp_gap_before_pagination' => 'gap_before_pagination_adapter',
			'jsm_gap_before_pagination' => 'gap_before_pagination_adapter',
		];

		foreach ( $gaps as $shortcode_setting => $widget_setting ) {
			if ( isset( $settings[ $widget_setting ]['size'] ) ) {
				$settings[ $shortcode_setting ] = $settings[ $widget_setting ]['size'];
			}
		}

		$settings['st_show_all_pages']  = $settings['show_all_pages'];
		$settings['jsp_show_all_pages'] = $settings['show_all_pages'];

		// Only standard pagination for current query.
		if ( $settings['query_post_type'] === 'current_query' ) {
			$settings['loading_mode'] = 'standard';
		}

		if ( ! isset( $settings['loading_mode'] ) ) {
			$settings['loading_mode'] = 'disabled';
		}

		return $settings;
	}

}
