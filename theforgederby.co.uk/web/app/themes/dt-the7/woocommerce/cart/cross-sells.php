<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>

	<div class="cross-sells">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'the7mk2' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<ul class="related-product cart-btn-below-img">

			<?php presscore_config()->set( 'product.preview.icons.show_cart', true ) ?>

			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<li>
					<?php
                    global $product;

					$product = wc_get_product( $cross_sell->get_id() );
					if ( $product->is_on_sale() ) :
						?>
                        <span class="onsale"><i class="dt-icon-the7-magn-004-12" aria-hidden="true"></i></span>
					<?php
					endif;
					?>
                    <a class="product-thumbnail" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo $product->get_image(); ?>
                    </a>
                    <div class="product-content">
                        <a class="product-title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo $product->get_name(); ?>
                        </a>

                        <span class="price"><?php echo $product->get_price_html(); ?></span>

						<?php
						if ( wc_review_ratings_enabled() ) {
							echo wc_get_rating_html( $product->get_average_rating() );
						}

						if ( presscore_config()->get( 'product.related.show_cart_btn' ) ) {
							echo '<div class="woo-buttons">' . dt_woocommerce_get_product_add_to_cart_icon() . '</div>';
						}
						?>
                    </div>
				</li>

		<?php endforeach; ?>

        </ul>

	</div>
<?php
endif;

wp_reset_postdata();
