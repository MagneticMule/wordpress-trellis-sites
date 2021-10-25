<?php

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Reviews extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-review';
	}

	public function the7_title() {
		return __( 'Product Reviews', 'the7mk2' );
	}

	public function the7_icon() {
		return 'eicon-comments';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function the7_keywords() {
		return [ 'comments', 'post', 'response', 'form' ];
	}

	public function get_script_depends() {
		return [ 'the7-woocommerce-product-review' ];
	}

	public function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		$settings = $this->get_settings();
		$this->add_render_attribute( 'the7-elementor-widget', 'class', [
			'the7-elementor-widget',
			'the7-elementor-product-comments',
			$settings['show_labels'] ? 'show-labels' : 'hide-labels',
			$settings['mark_required'] ? 'show-required' : 'hide-required',
		] );
		if ( $product->get_review_count()  == 0) {
			if ($settings['hide_comments_if_empty'] !== 'y') {
				$this->add_render_attribute( 'the7-elementor-widget', 'class', 'hide-comments' );
			}
		}
		?>
        <div <?php $this->print_render_attribute_string( 'the7-elementor-widget' ) ?>>
			<?php
			if ( ! comments_open() && ( $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode() ) ) :
				?>
                <div class="elementor-alert elementor-alert-danger" role="alert">
                    <span class="elementor-alert-title">
                        <?php esc_html_e( 'Comments are closed.', 'the7mk2' ); ?>
                    </span>
                    <span class="elementor-alert-description">
                        <?php esc_html_e( 'Switch on comments from either the discussion box on the WordPress post edit screen or from the WordPress discussion settings.', 'the7mk2' ); ?>
                    </span>
                </div>
			<?php
			else :
				add_filter( 'woocommerce_review_gravatar_size', [ $this, 'woocommerce_review_gravatar_size' ] );
				add_filter( 'woocommerce_product_review_comment_form_args', [ $this, 'modify_comment_fields' ], 100 );
				if ( $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode() ) {
					add_action( 'comment_form_logged_in_after', [ $this, 'display_fake_notice' ] );
				}

				comments_template();
				remove_filter( 'woocommerce_product_review_comment_form_args', [
					$this,
					'modify_comment_fields',
				], 100 );
				remove_filter( 'comment_form_logged_in_after', [ $this, 'display_fake_notice' ] );
				remove_filter( 'woocommerce_review_gravatar_size', [ $this, 'woocommerce_review_gravatar_size' ] );
			endif;
			?>
        </div>
		<?php
	}

	public function display_fake_notice() {
		?>
        <p class="comment-notes elementor-field-group"><?php echo __( 'This notice would be visible only when not logged in', 'the7mk2' ); ?></p>
		<?php
	}

	public function modify_comment_fields( $comment_form ) {
		$settings = $this->get_settings();
		$comment_form['label_submit'] = $settings['button_text'];
		ob_start();
		?>
        <p class="elementor-field-group elementor-field-type-submit elementor-column">
            <input name="%1$s" type="submit" id="%2$s"
                   class="%3$s elementor-button elementor-size-<?php echo $settings['button_size']; ?>" value="%4$s"/>
        </p>
		<?php
		$comment_form['submit_button'] = ob_get_clean();
		$comment_form['comment_field'] = '';

		if ( ! empty( $settings['form_fields'] ) ) {
			foreach ( $settings['form_fields'] as $field ) {
				unset( $comment_form['fields'][ $field['field_type'] ] );
				$comment_form['comment_field'] .= $this->get_field( $field, $settings );
			}
		}

		return $comment_form;
	}

	protected function get_field( $item, $settings ) {
		$repeater_id = 'elementor-repeater-item-' . esc_attr( $item['_id'] );
		$item_wrapper = 'elementor-field-group comment-form-' . $item['field_type'] . ' ' . $repeater_id;
		switch ( $item['field_type'] ) {
			case 'rating':
			{
				if ( wc_review_ratings_enabled() ) {
					return '<p class="' . $item_wrapper . '"> 
                                <label for="rating">' . esc_html( $item['field_label'] ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label>
                                <select name="rating" id="rating" required>
                                    <option value="">' . esc_html__( 'Rate&hellip;', 'the7mk2' ) . '</option>
                                    <option value="5">' . esc_html__( 'Perfect', 'the7mk2' ) . '</option>
                                    <option value="4">' . esc_html__( 'Good', 'the7mk2' ) . '</option>
                                    <option value="3">' . esc_html__( 'Average', 'the7mk2' ) . '</option>
                                    <option value="2">' . esc_html__( 'Not that bad', 'the7mk2' ) . '</option>
                                    <option value="1">' . esc_html__( 'Very poor', 'the7mk2' ) . '</option>
                                </select>
                              </p>';
				}

				return '';
			}
			case 'review':
			{
				return '<p class="elementor-field-group comment-form-comment ' . $repeater_id . '">
                            <label for="comment">' . esc_html( $item['field_label'] ) . '&nbsp;<span class="required">*</span>
                            </label>
                            <textarea id="comment" name="comment" class="elementor-field elementor-field-textual elementor-size-' . $settings['input_size'] . '" cols="45" placeholder= "' . __( $item['placeholder'], 'the7mk2' ) . '" rows="' . $item['rows'] . '" required></textarea>
                        </p>';
			}
			default:
			{
				$name_email_required = (bool) get_option( 'require_name_email', 1 );
				$commenter = wp_get_current_commenter();
				$fields = array(
					'author' => array(
						'label'       => $item['field_label'],
						'placeholder' => $item['placeholder'],
						'type'        => 'text',
						'value'       => $commenter['comment_author'],
						'required'    => $name_email_required,
					),
					'email'  => array(
						'label'       => $item['field_label'],
						'placeholder' => $item['placeholder'],
						'type'        => 'email',
						'value'       => $commenter['comment_author_email'],
						'required'    => $name_email_required,
					),
				);
				foreach ( $fields as $key => $field ) {
					if ( $key === $item['field_type'] ) {
						ob_start();
						?>
                        <p class="<?php echo $item_wrapper; ?>">
                            <label for="<?php echo esc_attr( $key ); ?>"><?php
								echo esc_html( $field['label'] );
								echo( $field['required'] ? '&nbsp;<span class="required">*</span>' : '' );
								?>
                            </label>
							<?php
							$field_html = '<input class="elementor-field-textual elementor-size-%1$s" id="%2$s" placeholder="%3$s" name="%2$s" type="%4$s" value="%5$s" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' />';
							echo sprintf( $field_html, $settings['input_size'], esc_attr( $key ), esc_attr( $field['placeholder'] ), esc_attr( $field['type'] ), esc_attr( $field['value'] ) );
							?>
                        </p>
						<?php
						return ob_get_clean();
					}
				}
			}
		}

		return '';
	}

	public function woocommerce_review_gravatar_size() {
		return '240';
	}

	protected function _register_controls() {
		// Content.
		$this->add_layout_controls();
		$this->add_form_fields_controls();
		$this->add_button_controls();

		// Style.
		$this->add_content_style_controls();
		$this->add_form_style_controls();
		$this->add_field_style_controls();
		$this->add_buttons_style_controls();
		$this->add_comments_style_controls();
		$this->add_rating_style_controls();
	}
	protected function add_layout_controls() {
		$this->start_controls_section( 'section_layout', [
			'label' => __( 'Layout', 'the7mk2' ),
		] );

		$text_columns = range( 1, 2 );
		$text_columns = array_combine( $text_columns, $text_columns );
		$text_columns[''] = __( 'Default', 'the7mk2' );

		$this->add_basic_responsive_control( 'text_columns', [
			'label'     => __( 'Columns', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $text_columns,
			'selectors' => [
				'{{WRAPPER}} .woocommerce-Reviews'                                                                                             => 'columns: {{VALUE}}; display: block;',
				'{{WRAPPER}} ol.commentlist li, {{WRAPPER}} .woocommerce-Reviews > *, {{WRAPPER}} #comments > *, {{WRAPPER}} .comment-respond' => 'break-inside: avoid;',
				'.is-safari {{WRAPPER}} #comments, .is-safari {{WRAPPER}} #review_form_wrapper' => 'display: inline-block; width: 100%;',
				'(tablet) .elementor-widget-the7-woocommerce-product-review:not(.elementor-tablet-review-col-2) .the7-elementor-product-comments.hide-comments .woocommerce-Reviews  #comments' => 'display: none!important;',
				'(tablet) .elementor-widget-the7-woocommerce-product-review.elementor-tablet-review-col-2 .woocommerce-Reviews  #comments' => 'display: block!important;',
                '(mobile) .elementor-widget-the7-woocommerce-product-review:not(.elementor-mobile-review-col-2) .the7-elementor-product-comments.hide-comments .woocommerce-Reviews  #comments' => 'display: none!important;',
				'(mobile) .elementor-widget-the7-woocommerce-product-review.elementor-mobile-review-col-2 .woocommerce-Reviews  #comments' => 'display: block!important;',
			],
			'prefix_class' => 'elementor%s-review-col-',
		] );

		$this->add_basic_responsive_control( 'review_column_gap', [
			'label'      => __( 'Rows(Columns) Gap', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em', 'vw' ],
			'range'      => [
				'px' => [
					'max' => 100,
				],
				'%'  => [
					'max'  => 10,
					'step' => 0.1,
				],
				'vw' => [
					'max'  => 10,
					'step' => 0.1,
				],
				'em' => [
					'max'  => 10,
					'step' => 0.1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 40,
			],
			'selectors'  => [
				'{{WRAPPER}} .woocommerce-Reviews'                                                                         => 'column-gap: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}'                                                                                              => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				//global responsive styles
				'(tablet) .elementor-widget-the7-woocommerce-product-review:not(.elementor-tablet-review-col-2) #comments' => 'padding-bottom: var(--grid-column-gap)',
				'(tablet) .elementor-widget-the7-woocommerce-product-review.elementor-tablet-review-col-2 #comments'       => 'padding-bottom: 0',

				'(mobile) .elementor-widget-the7-woocommerce-product-review:not(.elementor-mobile-review-col-2) #comments' => 'padding-bottom: var(--grid-column-gap)',
				'(mobile) .elementor-widget-the7-woocommerce-product-review.elementor-mobile-review-col-2 #comments'       => 'padding-bottom: 0',

				'(mobile) .the7-elementor-product-comments .comment_container .comment-text'             => 'grid-template-areas: "star" "title" "desc"; grid-template-columns: 1fr',
				'(mobile) .the7-elementor-product-comments .comment_container .star-rating'              => 'order: 0',
				'(mobile) .the7-elementor-product-comments .comment_container .meta'                     => 'order: 1',
				'(mobile) .the7-elementor-product-comments .commentlist .comment_container .star-rating' => 'margin-bottom: 10px',
			],
		] );

		$this->add_control( 'hide_comments_if_empty', [
			'label'        => __( 'Comments Section If Empty', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'the7mk2' ),
			'label_off'    => __( 'Hide', 'the7mk2' ),
			'return_value' => 'y',
			'default'      => '',
			'separator'    => 'before',
		] );

		$this->end_controls_section();
    }
	protected function add_form_fields_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => __( 'Form Fields', 'the7mk2' ),
		] );

		$this->add_form_fields_repeater();

		$this->add_control( 'input_size', [
			'label'     => __( 'Input Size', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'xs' => __( 'Extra Small', 'the7mk2' ),
				'sm' => __( 'Small', 'the7mk2' ),
				'md' => __( 'Medium', 'the7mk2' ),
				'lg' => __( 'Large', 'the7mk2' ),
				'xl' => __( 'Extra Large', 'the7mk2' ),
			],
			'default'   => 'sm',
			'separator' => 'before',
		] );

		$this->add_control( 'show_labels', [
			'label'        => __( 'Label', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'the7mk2' ),
			'label_off'    => __( 'Hide', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
		] );
		$this->add_control( 'mark_required', [
			'label'        => __( 'Required Mark', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'the7mk2' ),
			'label_off'    => __( 'Hide', 'the7mk2' ),
			'default'      => 'yes',
			'return_value' => 'yes',
		] );
		$this->end_controls_section();
	}

	protected function add_form_fields_repeater() {
		$repeater = new Repeater();

		$field_types = [
			'author' => __( 'Name', 'the7mk2' ),
			'email'  => __( 'Email', 'the7mk2' ),
			'review' => __( 'Review', 'the7mk2' ),
			'rating' => __( 'Rating', 'the7mk2' ),
		];

		$repeater->add_control( 'field_type', [
			'label'   => __( 'Type', 'the7mk2' ),
			'type'    => Controls_Manager::HIDDEN,
			'options' => $field_types,
			'default' => 'author',
		] );

		$repeater->add_control( 'field_label', [
			'label'   => __( 'Label', 'the7mk2' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );

		$repeater->add_control( 'placeholder', [
			'label'      => __( 'Placeholder', 'the7mk2' ),
			'type'       => Controls_Manager::TEXT,
			'default'    => '',
			'conditions' => [
				'terms' => [
					[
						'name'     => 'field_type',
						'operator' => '!in',
						'value'    => [
							'rating',
						],
					],
				],
			],
		] );

		$repeater->add_responsive_control( 'width', [
			'label'     => __( 'Column Width', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $this->get_field_width_options(),
			'default'   => '100',
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}}%',
			],
		] );

		$repeater->add_control( 'rows', [
			'label'      => __( 'Rows', 'the7mk2' ),
			'type'       => Controls_Manager::NUMBER,
			'default'    => 4,
			'conditions' => [
				'terms' => [
					[
						'name'  => 'field_type',
						'value' => 'review',
					],
				],
			],
		] );

		$this->add_control( 'form_fields', [
			'type'         => Controls_Manager::REPEATER,
			'fields'       => $repeater->get_controls(),
			'default'      => [
				[
					'field_type'  => 'rating',
					'field_label' => __( 'Your rating', 'the7mk2' ),
				],
				[
					'field_type'  => 'author',
					'field_label' => __( 'Name', 'the7mk2' ),
					'placeholder' => __( 'Name', 'the7mk2' ),
					'width'       => '100',
				],
				[
					'field_type'  => 'email',
					'field_label' => __( 'Email', 'the7mk2' ),
					'placeholder' => __( 'Email', 'the7mk2' ),
					'width'       => '100',
				],
				[
					'field_type'  => 'review',
					'field_label' => __( 'Message', 'the7mk2' ),
					'placeholder' => __( 'Message', 'the7mk2' ),
					'width'       => '100',
				],
			],
			'item_actions' => [
				'add'       => false,
				'duplicate' => false,
				'remove'    => false,
			],
			'title_field'  => '{{{ field_label }}}',
		] );
	}

	/**
	 * Get field width options.
	 * Retrieve an array of field width options for the widget.
	 * @return array
	 */
	protected function get_field_width_options() {
		return [
			''    => __( 'Default', 'the7mk2' ),
			'100' => '100%',
			'80'  => '80%',
			'75'  => '75%',
			'70'  => '70%',
			'66'  => '66%',
			'60'  => '60%',
			'50'  => '50%',
			'40'  => '40%',
			'33'  => '33%',
			'30'  => '30%',
			'25'  => '25%',
			'20'  => '20%',
		];
	}

	protected function add_button_controls() {
		$this->start_controls_section( 'section_btn', [
			'label' => __( 'Button', 'the7mk2' ),
		] );
		$this->add_control( 'button_size', [
			'label'   => __( 'Size', 'the7mk2' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'sm',
			'options' => self::get_button_sizes(),
		] );
		$this->add_basic_responsive_control( 'button_width', [
			'label'              => __( 'Column Width', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT,
			'options'            => $this->get_field_width_options(),
			'default'            => '100',
			'frontend_available' => true,
			'selectors'          => [
				'{{WRAPPER}} .elementor-field-type-submit' => 'width: {{VALUE}}%',
			],
		] );
		$this->add_basic_responsive_control( 'button_align', [
			'label'        => __( 'Alignment', 'the7mk2' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => [
				'start'   => [
					'title' => __( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => __( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-text-align-center',
				],
				'end'     => [
					'title' => __( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-text-align-right',
				],
				'stretch' => [
					'title' => __( 'Justified', 'the7mk2' ),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'default'      => 'stretch',
			'prefix_class' => 'elementor%s-button-align-',
		] );
		$this->add_control( 'button_text', [
			'label'       => __( 'Text', 'the7mk2' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Send', 'the7mk2' ),
			'placeholder' => __( 'Send', 'the7mk2' ),
		] );
		$this->end_controls_section();
	}

	/**
	 * Get button sizes.
	 * Retrieve an array of button sizes for the button widget.
	 * @return array An array containing button sizes.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'elementor' ),
			'sm' => __( 'Small', 'elementor' ),
			'md' => __( 'Medium', 'elementor' ),
			'lg' => __( 'Large', 'elementor' ),
			'xl' => __( 'Extra Large', 'elementor' ),
		];
	}

	protected function add_content_style_controls() {
		$this->start_controls_section( 'section_product_reviews_style', [
			'label' => __( 'Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_basic_responsive_control( 'alignment', [
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
			'default'              => 'left',
			'selectors_dictionary' => [
				'left'   => 'left',
				'center' => 'center',
				'right'  => 'right',
			],
			'selectors'            => [
				'{{WRAPPER}} .woocommerce-Reviews-title, {{WRAPPER}} .comment-reply-title, {{WRAPPER}} .comment-notes, {{WRAPPER}} .woocommerce-noreviews' => 'text-align: {{VALUE}}',
			],
		] );

		$this->add_control( 'titles_title', [
			'label'     => __( 'Titles', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$selector = '{{WRAPPER}} #comments .woocommerce-Reviews-title, {{WRAPPER}} #reply-title';
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'titles_typography',
			'label'          => __( 'Typography', 'the7mk2' ),
			'selector'       => $selector,
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
		] );
		$this->add_control( 'titles_color', [
			'label'     => __( 'Font Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'titles_spacing', [
			'label'      => __( 'Bottom Spacing', 'the7mk2' ),
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
				'{{WRAPPER}} .comment-form, {{WRAPPER}} #reviews .commentlist, {{WRAPPER}} .woocommerce-noreviews' => 'margin-top: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'notices_title', [
			'label'     => __( 'Notices', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );


		$selector = '{{WRAPPER}} .logged-in-as, {{WRAPPER}} .comment-notes, {{WRAPPER}} .woocommerce-noreviews, {{WRAPPER}} .comment-form-cookies-consent, {{WRAPPER}}  .comment-form-cookies-consent label';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'notices_typography',
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
			'selector'       => $selector,
		] );

		$this->add_control( 'notices_color', [
			'label'     => __( 'Font Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}


	protected function add_form_style_controls() {
		$this->start_controls_section( 'section_form_style', [
			'label' => __( 'Form', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'column_gap', [
			'label'     => __( 'Columns Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 20,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 60,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-field-group, {{WRAPPER}} .comment-form-cookies-consent, {{WRAPPER}} .comment-notes, {{WRAPPER}} .comment-reply-title' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
				'{{WRAPPER}} .comment-form, {{WRAPPER}} .comment-reply-title'                                                     => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
			],
		] );

		$this->add_control( 'row_gap', [
			'label'     => __( 'Rows Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 20,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 60,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-field-group, {{WRAPPER}} .comment-form-cookies-consent, {{WRAPPER}} .comment-notes'                                           => 'margin-bottom: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomleft, {{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomright, {{WRAPPER}} .comment-form > .elementor-field-type-submit' => 'margin-bottom: 0;',
			],
		] );

		$this->add_control( 'heading_label', [
			'label'     => __( 'Labels & Content', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$selector = '{{WRAPPER}} .comment-form label, {{WRAPPER}} .comment-form-cookies-consent label';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'label_typography',
			'selector' => $selector,
		] );

		$this->add_control( 'label_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'mark_required_color', [
			'label'     => __( 'Asterisk  Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .comment-form .required' => 'color: {{COLOR}};',
			],
			'condition' => [
				'mark_required' => 'yes',
			],
		] );

		$this->add_control( 'label_spacing', [
			'label'     => __( 'Label Bottom Spacing', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 5,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 60,
				],
			],
			'selectors' => [
				'body {{WRAPPER}} .elementor-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'show_labels' => 'yes',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_field_style_controls() {
		$this->start_controls_section( 'section_field_style', [
			'label' => __( 'Field', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $selector = '{{WRAPPER}} p[class*="comment-form-"] input, {{WRAPPER}} p[class*="comment-form-"] textarea, {{WRAPPER}} input::placeholder, {{WRAPPER}} textarea::placeholder';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'field_typography',
			'selector' => $selector,
		] );

		$this->add_control( 'field_text_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );

		$selector = '{{WRAPPER}} p[class*="comment-form-"] input, {{WRAPPER}} p[class*="comment-form-"] textarea';

		$this->add_control( 'field_background_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background-color: {{VALUE}};',
			],
			'separator' => 'before',
		] );

		$this->add_control( 'field_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}};',
			],
			'separator' => 'before',
		] );

		$this->add_control( 'field_border_width', [
			'label'      => __( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'selectors'  => [
				$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'field_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_buttons_style_controls() {
		$this->start_controls_section( 'section_button_style', [
			'label' => __( 'Button', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'button_typography',

			'selector' => '{{WRAPPER}} #reviews .comment-form #submit.elementor-button',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_border',
			'selector' => '{{WRAPPER}} .elementor-button',
			'exclude'  => [
				'color',
			],
		] );

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab( 'tab_button_normal', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );


		$this->add_control( 'button_background_color', [
			'label' => __( 'Background Color', 'the7mk2' ),
			'type'  => Controls_Manager::COLOR,

			'selectors' => [
				'{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]'       => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_text_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]'       => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"] svg *' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .e-form__buttons__wrapper__button-next' => 'border-color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]'       => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'button_border_border!' => '',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_button_hover', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );


		$this->add_control( 'button_background_hover_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [

				'#page {{WRAPPER}} .elementor-button[type="submit"]:hover' => 'background: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_hover_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]:hover'       => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]:hover svg *' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_hover_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => 'border-color: {{VALUE}};',
				'{{WRAPPER}} .elementor-button[type="submit"]:hover'       => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'button_border_border!' => '',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'button_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} #reviews #submit.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator'  => 'before',
		] );

		$this->add_control( 'button_text_padding', [
			'label'      => __( 'Text Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				'{{WRAPPER}} #reviews #submit.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_comments_style_controls() {
		$this->start_controls_section( 'section_comments_style', [
			'label' => __( 'Comment Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'heading_comment_title', [
			'label' => __( 'Title', 'the7mk2' ),
			'type'  => Controls_Manager::HEADING,
		] );

		$selector = '{{WRAPPER}} #reviews ol.commentlist .comment_container .woocommerce-review__author';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'typography_comment_title',
			'selector' => $selector,
		] );
		$this->add_control( 'comments_title_color', [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'heading_comment_date', [
			'label'     => __( 'Date', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$selector = '{{WRAPPER}} #reviews ol.commentlist .comment_container .woocommerce-review__published-date';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'typography_comment_date',
			'selector' => $selector,
		] );
		$this->add_control( 'comments_date_color', [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );
		$this->add_basic_responsive_control( 'comments_date_spacing', [
			'label'      => __( 'Date Top Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 5,
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
				'{{WRAPPER}} #reviews ol.commentlist .comment_container .woocommerce-review__published-date,
				{{WRAPPER}} #reviews ol.commentlist .comment_container .woocommerce-review__awaiting-approval' => 'margin-top: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_control( 'heading_comment_text', [
			'label'     => __( 'Text', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$selector = '{{WRAPPER}} #reviews ol.commentlist .comment_container .description';
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'typography_comment_text',

			'selector' => $selector,
		] );
		$this->add_control( 'comments_text_color', [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );
		$this->add_basic_responsive_control( 'comments_text_spacing', [
			'label'      => __( 'Text Top Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 10,
			],
			'selectors'  => [
				$selector => 'margin-top: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_comments_box', [
			'label' => __( 'Comment Box', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'comments_box_background_color', [
			'label' => __( 'Background Color', 'the7mk2' ),
			'type'  => Controls_Manager::COLOR,

			'selectors' => [
				'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'comments_box_border',
			'selector' => '{{WRAPPER}} #reviews ol.commentlist .comment_container',
			'exclude'  => [
				'color',
			],
		] );

		$this->add_control( 'comments_box_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'comments_box_border_border!' => '',
			],
		] );

		$this->add_basic_responsive_control( 'comments_box_text_padding', [
			'label'      => __( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default'    => [
				'unit' => 'px',
				'size' => 20,
			],
			'selectors'  => [
				'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_control( 'comments_box_bottom_margin', [
			'label'      => __( 'Distance Between Comments', 'the7mk2' ),
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
				'{{WRAPPER}} #reviews ol.commentlist li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
			],
		] );

		$this->end_controls_section();
		$this->start_controls_section( 'section_user_box', [
			'label' => __( "Comment User's Avatar", 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'show_avatar', [
			'label'        => __( 'Avatar', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'the7mk2' ),
			'label_off'    => __( 'Hide', 'the7mk2' ),
			'default'      => 'y',
			'return_value' => 'y',
			'prefix_class' => 'show-avatar-',
		] );

		$this->add_basic_responsive_control( 'comments_avatar_size', [
			'label'      => __( 'Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 60,
			],
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 240,
					'step' => 1,
				],
			],
					'selectors'  => [
				'{{WRAPPER}} .comment_container > img.avatar'            => 'width: 100%',
						'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'grid-template-columns: {{SIZE}}{{UNIT}} auto;',
					],
					'condition' => [
				'show_avatar!' => '',
					],
		] );

		$this->add_control( 'comments_avatar_disabled', [
			'label'        => __( 'disabled', 'the7mk2' ),
			'type'         => Controls_Manager::HIDDEN,
			'selectors'    => [
				'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'grid-template-columns: 0 100%; grid-column-gap: 0;',
				'{{WRAPPER}} #reviews .comment_container > img.avatar'   => 'display:none',
			],
			'default'      => 'y',
			'return_value' => 'y',
			'condition'    => [
				'show_avatar' => '',
			],
		] );

		$this->add_control( 'comments_avatar_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
					'selectors'  => [
				'{{WRAPPER}} #reviews .commentlist li img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
            'condition' => [
				'show_avatar!' => '',
			],
		] );


		$this->add_basic_responsive_control( 'avatar_spacing', [
			'label'     => __( 'Avatar Side Spacing', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'default'    => [
				'size' => 20,
			],
			'selectors' => [
				'{{WRAPPER}} #reviews ol.commentlist .comment_container' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'show_avatar!' => '',
			],
		] );
		$this->end_controls_section();
	}

	protected function add_rating_style_controls() {
		$this->start_controls_section( 'section_stars_style', [
			'label' => __( 'Rating', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'empty_stars_color', [
			'label'     => __( 'Empty Star Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .commentlist .star-rating, {{WRAPPER}} .commentlist .star-rating:before,  {{WRAPPER}} .comment-form .stars a' => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'stars_color', [
			'label'     => __( 'Filled Star Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .commentlist .star-rating span:before, {{WRAPPER}} .comment-form .stars a.active ~ a, {{WRAPPER}} .comment-form .stars a.active, {{WRAPPER}} .comment-form .stars a:hover ~ a, {{WRAPPER}} .comment-form .stars a:hover' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'rating_form_heading', [
			'label'     => __( 'Form', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_basic_responsive_control( 'rating_form_size', [
			'label'      => __( 'Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 20,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .comment-form .stars'=> 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'rating_form_gap', [
			'label'      => __( 'Distance', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 2,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
                '{{WRAPPER}} .comment-form .stars a' => 'padding-left: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'rating_comment_heading', [
			'label'     => __( 'Form', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_basic_responsive_control( 'rating_comment_size', [
			'label'      => __( 'Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 14,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .commentlist .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'rating_comment_gap', [
			'label'      => __( 'Distance', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 2,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .commentlist .star-rating span:before, {{WRAPPER}} .commentlist .star-rating:before' => 'letter-spacing: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->end_controls_section();
	}
}
