<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="dt-dummy-search">
	<label class="screen-reader-text" for="dt-dummy-search-input"><?php esc_html_e( 'Search by demo name or URL (link):', 'the7mk2' ); ?></label>
	<input type="search" id="dt-dummy-search-input" class="widefat" value="" placeholder="<?php esc_attr_e( 'Search by demo name or URL (link)', 'the7mk2' ); ?>" autofocus/>
</div>
<div class="the7-demo-quick-search">
	<?php
	$tags = array_map(
		function ( $tag ) {
			return '<a class="the7-demo-tag" data-tag="' . esc_attr( $tag ) . '">' . esc_html( $tag ) . '</a>';
		},
		the7_demo_get_quick_search_tags_list()
	);

	echo esc_html_x( 'Quick search: ', 'admin', 'the7mk2' ), implode( ', ', $tags ) . '.';
	?>
</div>

<?php foreach ( the7_demo_content()->get_demos() as $demo ) : ?>

	<?php
	/**
	 * @var The7_Demo $demo
	 */

	$tags = wp_json_encode( $demo->tags );
	?>

    <div class="dt-dummy-content" data-dummy-id="<?php echo esc_attr( $demo->id ); ?>" data-tags="<?php echo esc_attr( $tags ) ?>">

		<?php if ( $demo->title ) : ?>
            <h3><?php echo esc_html( $demo->title ), $demo->get_import_status_text() ?></h3>
		<?php endif; ?>

        <div class="dt-dummy-import-item">

			<?php if ( $demo->screenshot ) : ?>

                <div class="dt-dummy-screenshot">

					<?php
					$img    = '<img src="' . esc_url( $demo->screenshot ) . '" alt="' . esc_attr( $demo->title ) . '" ' . image_hwstring( 215, 161 ) . '/>';

					if ( $demo->link ) {
						$img = '<a href="' . esc_url( $demo->link ) . '" target="_blank" rel="nofollow">' . $img . '</a>';
					}

					echo $img;
					?>

					<div class="the7-demo-tags-list">
						<span class="dashicons dashicons-tag"></span>

						<?php
						echo implode(
							', ',
							array_map(
								function ( $tag ) {
									return '<span class="the7-demo-tag">' . esc_html( $tag ) . '</span>';
								},
								$demo->tags
							)
						);
						?>

					</div>

                </div>

			<?php endif; ?>

            <div class="dt-dummy-controls">

				<?php
				$slug = presscore_theme_is_activated() ? null : 'inactive';
				presscore_get_template_part(
					'the7_admin',
					'partials/the7-demo-content/full-import/demo',
					$slug,
					compact( 'demo' )
				);
				?>

            </div>

        </div>

    </div>

<?php endforeach; ?>

