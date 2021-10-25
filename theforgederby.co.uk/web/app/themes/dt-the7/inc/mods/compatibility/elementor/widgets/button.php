<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button as Button_Template;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor button widget.
 *
 * Elementor widget that displays a button with the ability to control every
 * aspect of the button design.
 */
class Button extends The7_Elementor_Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'the7_button_widget';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	protected function the7_title() {
		return __( 'Button', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function the7_icon() {
		return 'eicon-button';
	}

	/**
	 * Register button widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Button', 'the7mk2' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Click here', 'the7mk2' ),
				'placeholder' => __( 'Click here', 'the7mk2' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_basic_responsive_control(
			'align',
			[
				'label'        => __( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label'       => __( 'Button ID', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => '',
				'title'       => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'the7mk2' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor' ),
				'separator'   => 'before',

			]
		);

		$this->end_controls_section();

		$this->template( Button_Template::class )->add_style_controls(
			Button_Template::ICON_MANAGER,
			[],
			[
				'gap_above_button' => null,
			]
		);
	}

	/**
	 * Render button widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_link_attributes( 'box-button', $settings['link'] );
		if ( $settings['button_css_id'] ) {
			$this->add_render_attribute( 'box-button', 'id', $settings['button_css_id'] );
		}
		echo '<div class="elementor-button-wrapper">';
			$this->template( Button_Template::class )->render_button( 'box-button', esc_html( $settings['button_text'] ) );
		echo '</div>';
	}
}
