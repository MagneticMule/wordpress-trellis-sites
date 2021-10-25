<?php
/**
 * @since   2.0.0
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var The7_Demo $demo
 */
?>

<?php presscore_get_template_part(
	'the7_admin',
	'partials/the7-demo-content/full-import/demo/info',
	null,
	compact(
		'demo'
	)
); ?>

<form action="<?php echo add_query_arg( 'step', '2', the7_demo_content()->admin_url() ) ?>" method="post">

	<?php
	$need_to_install_or_activate_plugins = ! $demo->plugins()->is_plugins_active();
	?>
	<?php if ( $need_to_install_or_activate_plugins || ! $demo->plugins()->is_active( 'pro-elements' ) ): ?>

		<?php presscore_get_template_part(
			'the7_admin',
			'partials/the7-demo-content/full-import/demo/plugins',
			null,
			compact(
				'demo'
			)
		); ?>

	<?php endif; ?>

	<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo->id ) ?>">

	<div class="dt-dummy-controls-block">

		<div class="dt-dummy-field">
			<label>
				<input type="checkbox" name="import_theme_options" checked="checked" value="1"/><?php echo esc_html_x(
					'Import Theme Options',
					'admin',
					'the7mk2'
				); ?>
			</label><span class="dt-dummy-checkbox-desc"><?php
				printf(
					strip_tags(
						esc_html_x(
							'(Attention! That this will overwrite your current Theme Options and widget areas. You may want to %1$sexport%2$s them before proceeding.)',
							'admin',
							'the7mk2'
						)
					),
					'<a href="' . admin_url( 'admin.php?page=of-importexport-menu' ) . '" target="_blank">',
					'</a>'
				);
				?></span>
		</div>

    <?php if ( $need_to_install_or_activate_plugins ): ?>

		<div class="dt-dummy-field">
			<label><input type="checkbox" name="install_plugins" value="1" checked="checked"/><?php echo esc_html_x(
					'Install and activate required plugins',
					'admin',
					'the7mk2'
				); ?></label>
		</div>

    <?php endif; ?>

		<div class="dt-dummy-import-settings">
			<div class="dt-dummy-field <?php echo $demo->post_types_imported ? 'disabled' : '' ?>">
				<label>
					<input type="checkbox" name="import_post_types" checked="checked" value="1" <?php the7_prop_disabled( $demo->post_types_imported ) ?>><?php echo esc_html_e(
						'Import the entire content',
						'admin',
						'the7mk2'
					); ?>
				</label><span class="dt-dummy-checkbox-desc"><?php echo esc_html_x(
						'(Note that this will automatically switch your active Menu and Homepage.)',
						'admin',
						'the7mk2'
					); ?></span>
			</div>
			<div class="dt-dummy-field <?php echo $demo->attachments_imported ? 'disabled' : '' ?>">
				<label>
					<input type="checkbox" name="import_attachments" checked="checked" value="1" <?php the7_prop_disabled( $demo->attachments_imported ) ?>/><?php echo esc_html_x(
						'Download and import file attachments',
						'admin',
						'the7mk2'
					); ?>
				</label>
			</div>

			<?php if ( $demo->plugins()->is_required( 'revslider' ) ): ?>

				<div class="dt-dummy-field <?php echo $demo->sliders_imported ? 'disabled' : '' ?>">
					<label>
						<input type="checkbox" name="import_rev_sliders" checked="checked" value="1" <?php the7_prop_disabled( $demo->sliders_imported ) ?>/><?php echo esc_html_x(
							'Import slider(s)',
							'admin',
							'the7mk2'
						); ?>
					</label>
				</div>

			<?php endif ?>

			<div class="dt-dummy-field">
				<?php
				echo esc_html_x( 'Assign posts to an existing user: ', 'admin', 'the7mk2' );
				wp_dropdown_users(
					[
						'class'    => 'dt-dummy-content-user',
						'selected' => get_current_user_id(),
						'id'       => "users-{$demo->id}",
					]
				);
				?>
			</div>
		</div>

    </div>
	<div class="the7-demo-notifications"></div>
	<div class="dt-dummy-controls-block dt-dummy-control-buttons">
		<?php $disable_import = ! $demo->import_allowed() ?>
		<button type="submit" class="button button-primary<?php echo $disable_import ? '' : ' dt-dummy-button-import' ?>" name="import_type" value="full_import" <?php the7_prop_disabled( $disable_import ) ?>><?php echo esc_html_x( 'Import content', 'admin', 'the7mk2' ) ?></button>

		<?php if ( $demo->partially_imported() ): ?>
			<button type="submit" class="dt-keep-content-button button button-secondary" name="import_type" value="keep"><?php echo esc_html_x( 'Keep content', 'admin', 'the7mk2' ); ?></button>
			<button type="submit" class="dt-remove-content-button button button-secondary" name="import_type" value="remove"><?php echo esc_html_x( 'Remove content', 'admin', 'the7mk2' ); ?></button>
		<?php endif ?>
	</div>

</form>

<?php presscore_get_template_part(
	'the7_admin',
	'partials/the7-demo-content/full-import/demo/post-import-form',
	null,
	compact(
		'demo'
	)
) ?>
