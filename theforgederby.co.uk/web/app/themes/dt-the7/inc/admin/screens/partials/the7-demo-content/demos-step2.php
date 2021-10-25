<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var array $error
 * @var string $starting_text
 */

if ( ! empty( $error ) ) {
	echo wp_kses_post( $error );

	return;
}
?>

<div class="the7-import-feedback">
	<?php echo wp_kses_post( $starting_text ) ?>
</div>
<div class="the7-go-back-link hide-if-js">
	<p>
		<?php echo esc_html_x( 'All done.', 'admin', 'the7mk2' ) ?>
	</p>
	<p>
		<?php
		echo '<a id="the7-demo-visit-site-link" href="' . esc_url( home_url() ) . '">' . esc_html_x(
				'Visit site',
				'admin',
				'the7mk2'
			) . '</a>';
		echo ' | ';
		echo '<a href="' . esc_url( the7_demo_content()->admin_url() ) . '">' . esc_html_x(
				'Back to Pre-made Websites',
				'admin',
				'the7mk2'
			) . '</a>';
		?>
	</p>
</div>