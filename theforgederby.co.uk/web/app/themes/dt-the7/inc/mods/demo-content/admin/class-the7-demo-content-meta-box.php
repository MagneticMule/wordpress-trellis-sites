<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Content_Meta_Box {

	public static function add() {
		if ( get_post_meta( get_the_ID(), '_the7_imported_item', true ) ) {
			$supported_post_types = presscore_get_pages_with_basic_meta_boxes();
			$supported_post_types[] = 'attachment';

			add_meta_box(
				'the7-demo-content-box',
				__( 'The7 Demo Content', 'the7mk2' ),
				[
					__CLASS__,
					'render',
				],
				$supported_post_types,
				'advanced',
				'low'
			);
		}
	}

	/**
	 * Render the meta box.
	 */
	public static function render( $post ) {
		$nonce = wp_create_nonce( 'the7_demo_keep_the_post' );
		?>
		<p class="description elementor-panel-alert elementor-panel-alert-warning"><?php esc_html_e(
				'This post is a part of The7 demo content and will be deleted with the "Remove content" action. You can keep this post by clicking the button below.',
				'the7mk2'
			) ?></p>
		<br>
		<button type="button" id="the7-keep-the-post-button" class="button button-primary" data-nonce="<?php echo esc_attr( $nonce ) ?>" data-post-id="<?php echo esc_attr( get_the_ID() ) ?>"><?php esc_html_e( 'Keep this post', 'the7mk2' ); ?></button>
		<script>
            jQuery(function ($) {
                $("#the7-keep-the-post-button").on("click", function () {
                    var $this = $(this);

                    $.post(
                        ajaxurl,
                        {
                            "action": "the7_demo_keep_the_post",
                            "post_id": $this.attr("data-post-id"),
                            "_wpnonce": $this.attr("data-nonce")
                        }
                    ).done(function () {
                        $("#the7-demo-content-box,.the7-demo-content-box").slideUp(300, function() {
                            window.location.reload();
						});
                    });
                });
            });
		</script>
		<?php
	}

	public static function save() {
		check_ajax_referer( 'the7_demo_keep_the_post' );

		if ( ! isset( $_POST['post_id'] ) || ! current_user_can( 'edit_post', (int) $_POST['post_id'] ) ) {
			wp_die( 'You have no sufficient rights to edit this post.' );
		}

		delete_post_meta( (int) $_POST['post_id'], '_the7_imported_item' );

		wp_send_json_success();
	}
}