<?php
/*
 * The7 elements product Related widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

class Product_Related extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-related';
	}

	protected function the7_title() {
		return __( 'Product Related', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-related';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'related', 'similar', 'product'];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function render_plain_content() {
	}

	public function set_heading( $heading ) {
		$settings = $this->get_settings_for_display();
		if ( $settings['heading_title'] && ! empty( $settings['heading_title'] ) ) {
			$heading = $settings['heading_title'];
		}

		return $heading;
	}

	protected function _register_controls() {
		$this->start_controls_section( 'section_product_tabs_style', [
			'label' => __( 'Styles', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'wc_style_warning', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => __( 'The style of this widget is often can be affected by thirdparty plugins. If you experience any such issue, try to deactivate related plugins.', 'the7mk2' ),
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		] );

		$this->add_control( 'heading_title', [
			'label'   => __( 'Title', 'the7mk2' ),
			'type'    => Controls_Manager::TEXT,
			'description' => __( 'Leave empty to use default text', 'the7mk2' ),
			'default' => '',
		] );
		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}
        ?>
        <div class="the7-elementor-widget the7-elementor-product-<?php echo esc_attr( wc_get_product()->get_type() ); ?>">
            <?php
	            add_filter( 'woocommerce_product_related_products_heading', [ $this, 'set_heading' ] );
				woocommerce_output_related_products();
				remove_filter( 'woocommerce_product_related_products_heading', [ $this, 'set_heading' ] );
            ?>
        </div>

		<?php
	}
}
