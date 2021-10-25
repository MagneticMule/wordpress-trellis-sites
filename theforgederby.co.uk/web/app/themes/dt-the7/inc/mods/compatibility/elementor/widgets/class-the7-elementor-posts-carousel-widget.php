<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\With_Post_Excerpt;
use The7_Query_Builder;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7_Related_Query_Builder;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Posts_Carousel_Widget extends The7_Elementor_Widget_Base {

	use With_Post_Excerpt;
	use Posts_Masonry_Style;

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_elements_carousel';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return __( 'Posts Carousel', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-carousel-widget-preview' ];
		}

		return [];
	}

	public function get_style_depends() {
		return [ 'the7-carousel-widget' ];
	}

	public function _register_controls() {
		// Content Tab.
		$this->add_query_content_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();
		$this->add_scrolling_controls();
		$this->add_arrows_controls();
		$this->add_bullets_controls();

		// Style Tab.
		$this->add_skin_style_controls();
		$this->add_box_style_controls();
		$this->add_image_style_controls();
		$this->add_hover_icon_style_controls();
		$this->add_content_style_controls();
		$this->add_post_title_style_controls();
		$this->add_post_meta_style_controls();
		$this->add_text_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
			],
			[
				'button_icon'      => [
					'default' => [
						'value'   => 'dt-icon-the7-arrow-552',
						'library' => 'the7-icons',
					],
				],
				'gap_above_button' => [
					'label'     => __( 'Spacing Above Button', 'the7mk2' ),
					'selectors' => [
						'{{WRAPPER}} .box-button' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
				],
			]
		);
		$this->add_arrows_style_controls();
		$this->add_bullets_style_controls();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! in_array( $settings['post_type'], [ 'current_query', 'related' ] ) && ! post_type_exists(
				$settings['post_type']
			) ) {
			echo the7_elementor_get_message_about_disabled_post_type();

			return;
		}


		$this->remove_image_hooks();
		$this->print_inline_css();

		$query = $this->get_query();

		echo '<div ' . $this->container_class(
				[ 'owl-carousel', 'portfolio-carousel-shortcode', 'elementor-owl-carousel-call' ]
			) . $this->get_container_data_atts() . '>';

		$is_overlay_post_layout = $this->is_overlay_post_layout( $settings );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_class_array = [
					'post',
					'visible',
				];

				if ( ! has_post_thumbnail() ) {
					$post_class_array[] = 'no-img';
				}

				$icons_html = $this->get_hover_icons_html_template( $settings );
				if ( ! $icons_html && $is_overlay_post_layout ) {
					$post_class_array[] = 'forward-post';
				}

				echo '<article class="' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';

				$details_btn = '';
				if ( $settings['show_read_more_button'] ) {
					$details_btn = $this->get_details_btn( $settings );
				}

				$post_title = '';
				if ( $settings['show_post_title'] ) {
					$post_title = $this->get_post_title( $settings, $settings['title_tag'] );
				}

				$post_excerpt = '';
				if ( $settings['post_content'] === 'show_excerpt' ) {
					$post_excerpt = $this->get_post_excerpt( $settings['excerpt_words_limit'] );
				}

				$link_attributes = $this->get_link_attributes( $settings );

				$post_image_settings                             = $settings;
				$post_image_settings['responsiveness']           = 'browser_width_based';
				$post_image_settings['all_posts_the_same_width'] = true;
				$post_image_settings['layout']                   = false;

				presscore_get_template_part(
					'elementor',
					'the7-elements/tpl-layout',
					$settings['post_layout'],
					[
						'settings'     => $settings,
						'post_title'   => $post_title,
						'post_media'   => $this->get_post_image( $post_image_settings ),
						'post_meta'    => $this->get_post_meta_html_based_on_settings( $settings ),
						'details_btn'  => $details_btn,
						'post_excerpt' => $post_excerpt,
						'icons_html'   => $icons_html,
						'follow_link'  => $link_attributes['href'],
					]
				);

				echo '</article>';
			}
		}

		wp_reset_postdata();
		

		echo '</div>';
		echo '<div class="owl-nav disabled">';
		echo '<a class="owl-prev" role="button">';
		Icons_Manager::render_icon( $settings['prev_icon'] );
		echo '</a>';
		echo '<a class="owl-next" role="button">';
		Icons_Manager::render_icon( $settings['next_icon'] );
		echo '</a>';
		echo '</div>';

		$this->add_image_hooks();
	}

	protected function get_details_btn( $settings ) {
		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$link_attributes               = $this->get_link_attributes( $settings );
		$link_attributes['aria-label'] = the7_get_read_more_aria_label();

		$this->add_render_attribute( 'box-button', $link_attributes );

		ob_start();
		$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['read_more_button_text'] ) );

		return ob_get_clean();
	}

	/**
	 * Return container class attribute.
	 *
	 * @param array $class
	 *
	 * @return string
	 */
	protected function container_class( $class = [] ) {
		$class[] = 'portfolio-shortcode';
		$class[] = 'the7-elementor-widget';

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = 'content-bg-on';

		$layout_classes = array(
			'classic'           => 'classic-layout-list',
			'bottom_overlap'    => 'bottom-overlap-layout-list',
			'gradient_overlap'  => 'gradient-overlap-layout-list',
			'gradient_overlay'  => 'gradient-overlay-layout-list',
			'gradient_rollover' => 'content-rollover-layout-list',
		);
		

		$layout = $settings['post_layout'];
		if ( array_key_exists( $layout, $layout_classes ) ) {
			$class[] = $layout_classes[ $layout ];
		}

		$gradient_layout = in_array( $layout, [ 'gradient_overlay', 'gradient_rollover' ], true );

		if ( $gradient_layout ) {
			if ( $settings['post_content'] !== 'show_excerpt' && ! $settings['show_read_more_button'] ) {
				$class[] = 'disable-layout-hover';
			}

			$class[] = 'description-on-hover';
		} else {
			$class[] = 'description-under-image';
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

		if ( $settings['image_scale_animation_on_hover'] === 'quick_scale' ) {
			$class[] = 'quick-scale-img';
		} elseif ( $settings['image_scale_animation_on_hover'] === 'slow_scale' ) {
			$class[] = 'scale-img';
		}

		if ( ! $settings['post_date'] && ! $settings['post_terms'] && ! $settings['post_comments'] && ! $settings['post_author'] ) {
			$class[] = 'meta-info-off';
		}

		if ( ! $settings['overlay_background_background'] && ! $settings['overlay_hover_background_background'] ) {
			$class[] = 'enable-bg-rollover';
		}

		if ( 'gradient_overlay' === $settings['post_layout'] ) {
			$class[] = presscore_tpl_get_hover_anim_class( $settings['go_animation'] );
		}

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

		return sprintf( ' class="%s" ', esc_attr( implode( ' ', $class ) ) );
	}

	protected function get_query() {
		$settings = $this->get_settings_for_display();
		$post_type    = $settings['post_type'];

		if ( $post_type === 'current_query' ) {
			return $GLOBALS['wp_query'];
		}

		// Loop query.
		$query_args = [
			'posts_offset'   => $settings['posts_offset'],
			'post_type'      => $post_type,
			'order'          => $settings['order'],
			'orderby'        => $settings['orderby'],
			'posts_per_page' => $settings['dis_posts_total'],
		];

		if ( $post_type === 'related' ) {
			$query_builder = new The7_Related_Query_Builder( $query_args );
		} else {
			$query_builder = new The7_Query_Builder( $query_args );
		}

		$query_builder->from_terms( $settings['taxonomy'], $settings['terms'] );

		return $query_builder->query();
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
	 * Return shortcode less file absolute path to output inline.
	 *
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-elements-carousel-widget.less';
	}

	/**
	 * Return less imports.
	 *
	 * @return array
	 */
	protected function get_less_imports() {
		$settings           = $this->get_settings_for_display();
		$dynamic_import_top = [];

		switch ( $settings['post_layout'] ) {
			case 'bottom_overlap':
				$dynamic_import_top[] = 'post-layouts/bottom-overlap-layout.less';
				break;
			case 'gradient_overlap':
				$dynamic_import_top[] = 'post-layouts/gradient-overlap-layout.less';
				break;
			case 'gradient_overlay':
				$dynamic_import_top[] = 'post-layouts/gradient-overlay-layout.less';
				break;
			case 'gradient_rollover':
				$dynamic_import_top[] = 'post-layouts/content-rollover-layout.less';
				break;
			case 'classic':
			default:
				$dynamic_import_top[] = 'post-layouts/classic-layout.less';
		}

		$dynamic_import_bottom = [];

		return compact( 'dynamic_import_top', 'dynamic_import_bottom' );
	}

	/**
	 * Specify a vars to be inserted in to a less file.
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.portfolio-shortcode',
			'~"%s"'
		);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}

		$less_vars->add_pixel_number(
			'elementor-container-width',
			the7_elementor_get_content_width_string()
		);

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

	protected function add_query_content_controls() {
		$this->start_controls_section(
			'query_section',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'   => __( 'Source', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => the7_elementor_elements_widget_post_types() + [ 'related' => __( 'Related', 'the7mk2' ) ],
				'classes' => 'select2-medium-width',
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'     => __( 'Select Taxonomy', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'post_type!' => [ '', 'current_query' ],
				],
			]
		);

		$this->add_control(
			'terms',
			[
				'label'     => __( 'Select Terms', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'multiple'  => true,
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'taxonomy!'  => '',
					'post_type!' => [ 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => __( 'Order', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => [
					'asc'  => __( 'Ascending', 'the7mk2' ),
					'desc' => __( 'Descending', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => __( 'Order By', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => [
					'date'          => __( 'Date', 'the7mk2' ),
					'title'         => __( 'Name', 'the7mk2' ),
					'ID'            => __( 'ID', 'the7mk2' ),
					'modified'      => __( 'Modified', 'the7mk2' ),
					'comment_count' => __( 'Comment count', 'the7mk2' ),
					'menu_order'    => __( 'Menu order', 'the7mk2' ),
					'rand'          => __( 'Rand', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'post_type!' => 'current_query',
				],
			]
		);

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
					'post_type!' => 'current_query',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'article_links',
			[
				'label'        => __( 'Links To A Single Post', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'article_links_goes_to',
			[
				'label'     => __( 'Links Lead To', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'posts',
				'options'   => [
					'posts'                => __( 'Posts', 'the7mk2' ),
					'external_or_posts'    => __( 'External links or posts', 'the7mk2' ),
					'external_or_disabled' => __( 'External links or disabled', 'the7mk2' ),
				],
				'condition' => [
					'post_type'     => 'dt_portfolio',
					'article_links' => 'y',
				],
			]
		);

		$this->add_control(
			'show_details_icon',
			[
				'label'        => __( 'Hover Icon', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'project_link_icon',
			[
				'label'     => __( 'Choose Icon', 'the7mk2' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'icomoon-the7-font-the7-plus-02',
					'library' => 'the7-cons',
				],
				'condition' => [
					'show_details_icon' => 'y',
				],
			]
		);

		$this->add_control(
			'show_post_title',
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
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_control(
			'post_content',
			[
				'label'        => __( 'Excerpt', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'show_excerpt',
				'default'      => 'show_excerpt',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire excerpt.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_control(
			'post_terms',
			[
				'label'        => __( 'Category', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_terms_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_terms' => 'y',
				],
			]
		);

		$this->add_control(
			'post_author',
			[
				'label'        => __( 'Author', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_author_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_author' => 'y',
				],
			]
		);

		$this->add_control(
			'post_date',
			[
				'label'        => __( 'Date', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_date_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_date' => 'y',
				],
			]
		);

		$this->add_control(
			'post_comments',
			[
				'label'        => __( 'Comments count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_comments_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_comments' => 'y',
				],
			]
		);

		$this->add_control(
			'show_read_more_button',
			[
				'label'        => __( 'Read More', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => __( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read more', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
				],
			]
		);

		$this->end_controls_section();
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
			'widget_columns_wide_desktop',
			[
				'label'       => __( 'Columns On A Wide Desktop', 'the7mk2' ),
				'description' => sprintf(
					__(
					// translators: %s: elementor content width
						'Apply when browser width is bigger than %s ("Content Width" Elementor setting).',
						'the7mk2'
					),
					the7_elementor_get_content_width_string()
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
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
				'min'            => 0,
				'max'            => 12,
			]
		);

		$this->add_basic_responsive_control(
			'gap_between_posts',
			[
				'label'      => __( 'Gap Between Columns (px)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
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

	protected function add_skin_style_controls() {
		$this->start_controls_section(
			'skins_style_section',
			[
				'label' => __( 'Skin', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'post_layout',
			[
				'label'   => __( 'Choose Skin', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'classic'           => __( 'Classic', 'the7mk2' ),
					'bottom_overlap'    => __( 'Overlapping content area', 'the7mk2' ),
					'gradient_overlap'  => __( 'Blurred content area', 'the7mk2' ),
					'gradient_overlay'  => __( 'Simple overlay on hover', 'the7mk2' ),
					'gradient_rollover' => __( 'Blurred bottom overlay on hover', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'classic_image_visibility',
			[
				'label'        => __( 'Image Visibility', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'show',
				'default'      => 'show',
				'condition'    => [
					'post_layout' => 'classic',
				],
			]
		);

		$this->add_basic_responsive_control(
			'classic_image_max_width',
			[
				'label'      => __( 'Max Image Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .classic-layout-list .post-thumbnail' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'post_layout'              => 'classic',
					'classic_image_visibility' => 'show',
				],
			]
		);

		$this->add_basic_responsive_control(
			'bo_content_overlap',
			[
				'label'      => __( 'Content Box Overlap', 'the7mk2' ) . ' (px)',
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bottom-overlap-layout-list article:not(.no-img) .post-entry-content'      => 'margin-top: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bottom-overlap-layout-list article:not(.no-img) .project-links-container' => 'height: calc(100% - {{SIZE}}{{UNIT}});',
				],
				'condition'  => [
					'post_layout' => 'bottom_overlap',
				],
			]
		);

		$this->add_control(
			'go_animation',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'              => __( 'Fade', 'the7mk2' ),
					'direction_aware'   => __( 'Direction aware', 'the7mk2' ),
					'redirection_aware' => __( 'Reverse direction aware', 'the7mk2' ),
					'scale_in'          => __( 'Scale in', 'the7mk2' ),
				],
				'condition' => [
					'post_layout' => 'gradient_overlay',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_box_style_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => __( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} article' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} article' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
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
					'{{WRAPPER}} article' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab(
			'classic_style_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Box_Shadow::get_type(),
		// 	[
		// 		'name'     => 'box_shadow',
		// 		'selector' => '{{WRAPPER}} .the7-elementor-widget:not(.class-1) article',
		// 	]
		// );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .the7-elementor-widget:not(.class-1) article',
				'fields_options'  => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
							'{{WRAPPER}} .owl-stage-outer' => '--shadow-horizontal: {{HORIZONTAL}}px; --shadow-vertical: {{VERTICAL}}px; --shadow-blur: {{BLUR}}px; --shadow-spread: {{SPREAD}}px',      
						],
					],
				],
			]
		);
		

		$this->add_control(
			'box_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'classic_style_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .the7-elementor-widget:not(.class-1) article:hover',
			]
		);

		$this->add_control(
			'box_background_color_hover',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_hover_icon_style_controls() {
		$this->start_controls_section(
			'icon_style_section',
			[
				'label'      => __( 'Hover Icon', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '!==',
									'value'    => 'classic',
								],
								[
									'name'     => 'show_details_icon',
									'operator' => '==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '==',
									'value'    => 'classic',
								],
								[
									'name'     => 'show_details_icon',
									'operator' => '==',
									'value'    => 'y',
								],
								[
									'name'     => 'classic_image_visibility',
									'operator' => '==',
									'value'    => 'show',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'project_icon_size',
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
					'{{WRAPPER}} .project-links-container a > span:before' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .project-links-container a > svg'         => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'project_icon_bg_size',
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
					'{{WRAPPER}} .project-links-container a > span:before' => 'line-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .project-links-container a'               => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
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
					'{{WRAPPER}} .project-links-container a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'project_icon_border_radius',
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
					'{{WRAPPER}} .project-links-container a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'project_icon_margin',
			[
				'label'      => __( 'Icon Margin', 'the7mk2' ),
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
					'{{WRAPPER}} .project-links-container a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icons_colors',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'project_icon_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a:not(:hover) > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .project-links-container a > span'             => 'color: {{VALUE}};',
					'{{WRAPPER}} .project-links-container a > svg'         => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_color',
			[
				'label'     => __( 'Icon Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_bg_color',
			[
				'label'     => __( 'Icon Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icons_hover_colors',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'project_icon_color_hover',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a:hover > span'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .project-links-container a:hover > svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_color_hover',
			[
				'label'     => __( 'Icon Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a { transition: all 0.3s;}
					{{WRAPPER}} .project-links-container a:hover' => 'border-color: {{VALUE}};',

				],
			]
		);

		$this->add_control(
			'project_icon_bg_color_hover',
			[
				'label'     => __( 'Icon Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .project-links-container a { transition: all 0.3s;}
					{{WRAPPER}} .project-links-container a:hover' => 'background: {{VALUE}}; box-shadow: none;',

				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

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
							'name'     => 'post_layout',
							'operator' => '!==',
							'value'    => 'classic',
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '==',
									'value'    => 'classic',
								],
								[
									'name'     => 'classic_image_visibility',
									'operator' => '==',
									'value'    => 'show',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label'       => __( 'Image Ratio', 'the7mk2' ),
				'description' => __( 'Lieve empty to use original proportions', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => '',
					'unit' => 'px',
				],
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
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
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail'         => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail > a'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail > a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_scale_animation_on_hover',
			[
				'label'   => __( 'Scale Animation On Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'quick_scale',
				'options' => [
					'disabled'    => __( 'Disabled', 'the7mk2' ),
					'quick_scale' => __( 'Quick scale', 'the7mk2' ),
					'slow_scale'  => __( 'Slow scale', 'the7mk2' ),
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
						'label' => __( 'Background Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:before,
				{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:before,
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:after,
				{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'img_shadow',
				'selector'  => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail,
				{{WRAPPER}} .description-on-hover article .post-thumbnail
				',
				'condition' => [
					'post_layout!' => [ 'gradient_rollover', 'gradient_overlay' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail img,
				{{WRAPPER}} .description-on-hover article .post-thumbnail img
				',
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
					'{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail img,
					{{WRAPPER}} .description-on-hover article .post-thumbnail img' => 'opacity: {{SIZE}}{{UNIT}}',
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
						'label' => __( 'Background Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}} { transition: all 0.3s; }
							{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:before,
							{{WRAPPER}} .gradient-overlap-layout-list article .post-thumbnail > .post-thumbnail-rollover:before,
							{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:before { transition: opacity 0.3s;}
							{{WRAPPER}} .post-thumbnail:hover > .post-thumbnail-rollover:before,
							{{WRAPPER}} .post-thumbnail:not(:hover) > .post-thumbnail-rollover:after {transition-delay: 0.15s;}
							{{SELECTOR}}' => 'background: {{VALUE}};',
						],


					],
				],
				'selector'       => '
					{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:after,
					{{WRAPPER}} .gradient-overlap-layout-list article .post-thumbnail > .post-thumbnail-rollover:after,
					{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'img_hover_shadow',
				'selector'  => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail,
				{{WRAPPER}} .description-on-hover article:hover .post-thumbnail
				',
				'condition' => [
					'post_layout!' => [ 'gradient_rollover', 'gradient_overlay' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail img,
				{{WRAPPER}} .description-on-hover article:hover .post-thumbnail img
				',
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
					'{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail img,
					{{WRAPPER}} .description-on-hover article:hover .post-thumbnail img' => 'opacity: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'img_bottom_margin',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
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
					'{{WRAPPER}} .post-thumbnail-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'post_layout' => 'classic',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_style_controls() {
		$this->start_controls_section(
			'content_style_section',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_content_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--content-bg-color:{{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'bo_content_width',
			[
				'label'      => __( 'Content Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .description-under-image .post-entry-content'               => 'max-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .description-on-hover .post-entry-content .post-entry-body' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_padding',
			[
				'label'      => __( 'Content Padding', 'the7mk2' ),
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
					'{{WRAPPER}} article .post-entry-content'                       => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .content-rollover-layout-list .post-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_box_alignment',
			[
				'label'                => __( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'left',
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .description-under-image .post-entry-content'                       => 'align-self: {{VALUE}};',
					'{{WRAPPER}} .description-on-hover .post-entry-content .post-entry-body'         => 'align-self: {{VALUE}};',
					'{{WRAPPER}} .description-on-hover .post-entry-content .project-links-container' => 'justify-content: {{VALUE}};',
				],
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
					'{{WRAPPER}} .post-entry-content'                       => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .classic-layout-list .post-thumbnail-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_post_title_style_controls() {
		$this->start_controls_section(
			'post_title_style_section',
			[
				'label'     => __( 'Post Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_title_typography',
				'label'          => __( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .ele-entry-title',
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
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a'       => 'color: {{VALUE}}',
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a:hover' => 'color: {{VALUE}};',
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
					'#page {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_title_bottom_margin',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
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
					'{{WRAPPER}} .ele-entry-title'                                                => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content-rollover-layout-list.meta-info-off .post-entry-wrapper' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_post_meta_style_controls() {
		$this->start_controls_section(
			'post_meta_style_section',
			[
				'label'      => __( 'Meta Information', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_date',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_terms',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_author',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_comments',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'post_meta_separator',
			[
				'label'       => __( 'Separator Between', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '•',
				'placeholder' => '',
				'selectors'   => [
					'{{WRAPPER}} .entry-meta .meta-item:not(:first-child):before' => 'content: "{{VALUE}}";',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_meta_typography',
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
				'selector'       => '{{WRAPPER}} .entry-meta, {{WRAPPER}} .entry-meta > span',
			]
		);

		$this->add_control(
			'post_meta_font_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta > a, {{WRAPPER}} .entry-meta > span'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .entry-meta > a:after, {{WRAPPER}} .entry-meta > span:after' => 'background: {{VALUE}}; -webkit-box-shadow: none; box-shadow: none;',
				],
			]
		);

		$this->add_control(
			'post_meta_bottom_margin',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
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
					'{{WRAPPER}} .entry-meta'                                       => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .content-rollover-layout-list .post-entry-wrapper' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_text_style_controls() {
		$this->start_controls_section(
			'post_text_style_section',
			[
				'label'     => __( 'Text', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_content_typography',
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
				'selector'       => '{{WRAPPER}} .entry-excerpt *',
			]
		);

		$this->add_control(
			'post_content_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-excerpt' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_content_bottom_margin',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
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
					'{{WRAPPER}} .entry-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
		$this->add_basic_responsive_control( 'arrows', [
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

		$this->add_basic_responsive_control(
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
				'label'     => __( 'Arrow Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'left',
			]
		);

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
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
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'right',
			]
		);

		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
			'r_arrow_h_offset',
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

		$this->add_basic_responsive_control(
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
						'icon'  => 'eicon-h-align-center',
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
}
