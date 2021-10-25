<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var The7_Demo $demo
 */
?>

<?php if ( $demo->id === 'main' ): ?>

	<div class="dt-dummy-controls-block dt-dummy-info-content">
		<?php
		echo wp_kses_post(
			_x(
				'<p><strong>Important!</strong> This demo is huge. Many servers will struggle importing it.<br><strong>Please make a full site backup</strong> before proceeding with the import. In case of emergency, you may have to restore your database (or the whole website) from it.</p>',
				'admin',
				'the7mk2'
			)
		);
		?>
	</div>

<?php endif; ?>

<?php if ( ! $demo->include_attachments ) : ?>

	<div class="dt-dummy-controls-block dt-dummy-info-content">
		<p><strong><?php
				echo esc_html_x(
					'Please note that all copyrighted images were replaced with a placeholder pictures.',
					'admin',
					'the7mk2'
				);
				?></strong></p>
	</div>

<?php endif; ?>

<?php if ( ! $demo->plugins()->is_installed( 'pro-elements' ) ) : ?>

	<div class="dt-dummy-controls-block dt-dummy-info-content">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					_x(
						'<strong>Important!</strong> This demo requires <a href="%1$s" target="_blank" rel="nofollow">Elementor Pro</a> or its free alternative, <a href="%2$s" target="_blank" rel="nofollow">PRO Elements</a> plugin. We cannot install them automatically. Please install one of these plugins to proceed with the demo installation.',
						'admin',
						'the7mk2'
					),
					'https://elementor.com/pro/',
					'https://proelements.github.io/proelements.org/'
				)
			);
			?>
		</p>
	</div>

<?php endif; ?>