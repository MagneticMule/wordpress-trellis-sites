<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class bpfwpInstallationWalkthrough {

	// The scheduler control so that a business can sets its opening hours
	public $scheduler;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'register_install_screen' ));
		add_action( 'admin_head', array($this, 'hide_install_screen_menu_item' ));
		add_action( 'admin_init', array($this, 'redirect'), 9999);

		add_action('admin_head', array($this, 'admin_enqueue'));

		require_once BPFWP_PLUGIN_DIR . '/lib/simple-admin-pages/classes/AdminPageSetting.class.php';
		require_once BPFWP_PLUGIN_DIR . '/lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php';

		$args = array(
			'id'          => 'opening-hours',
			'page'		  => 'walkthrough',
			'title'       => __( 'Opening Hours', 'business-profile' ),
			'description' => __( 'Define your weekly opening hours by adding scheduling rules.', 'business-profile' ),
			'weekdays'    => array(
				'monday'    => _x( 'Mo', 'Monday abbreviation', 'business-profile' ),
				'tuesday'   => _x( 'Tu', 'Tuesday abbreviation', 'business-profile' ),
				'wednesday' => _x( 'We', 'Wednesday abbreviation', 'business-profile' ),
				'thursday'  => _x( 'Th', 'Thursday abbreviation', 'business-profile' ),
				'friday'    => _x( 'Fr', 'Friday abbreviation', 'business-profile' ),
				'saturday'  => _x( 'Sa', 'Saturday abbreviation', 'business-profile' ),
				'sunday'    => _x( 'Su', 'Sunday abbreviation', 'business-profile' ),
			),
			'time_format'   => _x( 'h:i A', 'Time format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/time.htm#formats', 'business-profile' ),
			'date_format'   => _x( 'mmmm d, yyyy', 'Date format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/date.htm#formatting-rules', 'business-profile' ),
			'disable_weeks' => true,
			'disable_date'  => true,
			'strings'       => array(
				'add_rule'         => __( 'Add another opening time', 'business-profile' ),
				'weekly'           => _x( 'Weekly', 'Format of a scheduling rule', 'business-profile' ),
				'monthly'          => _x( 'Monthly', 'Format of a scheduling rule', 'business-profile' ),
				'date'             => _x( 'Date', 'Format of a scheduling rule', 'business-profile' ),
				'weekdays'         => _x( 'Days of the week', 'Label for selecting days of the week in a scheduling rule', 'business-profile' ),
				'month_weeks'      => _x( 'Weeks of the month', 'Label for selecting weeks of the month in a scheduling rule', 'business-profile' ),
				'date_label'       => _x( 'Date', 'Label to select a date for a scheduling rule', 'business-profile' ),
				'time_label'       => _x( 'Time', 'Label to select a time slot for a scheduling rule', 'business-profile' ),
				'allday'           => _x( 'All day', 'Label to set a scheduling rule to last all day', 'business-profile' ),
				'start'            => _x( 'Start', 'Label for the starting time of a scheduling rule', 'business-profile' ),
				'end'              => _x( 'End', 'Label for the ending time of a scheduling rule', 'business-profile' ),
				'set_time_prompt'  => _x( 'All day long. Want to %sset a time slot%s?', 'Prompt displayed when a scheduling rule is set without any time restrictions', 'business-profile' ),
				'toggle'           => _x( 'Open and close this rule', 'Toggle a scheduling rule open and closed', 'business-profile' ),
				'delete'           => _x( 'Delete rule', 'Delete a scheduling rule', 'business-profile' ),
				'delete_schedule'  => __( 'Delete scheduling rule', 'business-profile' ),
				'never'            => _x( 'Never', 'Brief default description of a scheduling rule when no weekdays or weeks are included in the rule', 'business-profile' ),
				'weekly_always'    => _x( 'Every day', 'Brief default description of a scheduling rule when all the weekdays/weeks are included in the rule', 'business-profile' ),
				'monthly_weekdays' => _x( '%s on the %s week of the month', 'Brief default description of a scheduling rule when some weekdays are included on only some weeks of the month. %s should be left alone and will be replaced by a comma-separated list of days and weeks in the following format: M, T, W on the first, second week of the month', 'business-profile' ),
				'monthly_weeks'    => _x( '%s week of the month', 'Brief default description of a scheduling rule when some weeks of the month are included but all or no weekdays are selected. %s should be left alone and will be replaced by a comma-separated list of weeks in the following format: First, second week of the month', 'business-profile' ),
				'all_day'          => _x( 'All day', 'Brief default description of a scheduling rule when no times are set', 'business-profile' ),
				'before'           => _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'business-profile' ),
				'after'            => _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'business-profile' ),
				'separator'        => _x( '&mdash;', 'Separator between times of a scheduling rule', 'business-profile' ),
			),
			'args'			=> array(
				'class' 	=> 'bpfwp-opening-hours'
			)
		);
			

		// This is required otherwise SAP_VERSION will throw error.
		require_once BPFWP_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php';
		$sap = sap_initialize_library(
			array(
				'version' => '2.5.5',
				'lib_url' => BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/',
			)
		);

		$this->scheduler = new sapAdminPageSettingScheduler_2_5_5( $args );

		add_action('wp_ajax_bpfwp_welcome_add_contact_page', array($this, 'add_contact_page'));
		add_action('wp_ajax_bpfwp_welcome_set_contact_information', array($this, 'set_contact_information'));
		add_action('wp_ajax_bpfwp_welcome_set_opening_hours', array($this, 'set_opening_hours'));
	}

	public function redirect() {
		global $bpfwp_controller;

		if ( ! get_transient( 'bpfwp-getting-started' ) ) 
			return;

		delete_transient( 'bpfwp-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$plugin_items = get_posts(array('post_type' => array( $bpfwp_controller->cpts->schema_cpt_slug, $bpfwp_controller->cpts->location_cpt_slug)));
		if (!empty($plugin_items)) {
			set_transient('bpfwp-admin-install-notice', true, 5);
			return;
		}
		
		wp_safe_redirect( admin_url( 'index.php?page=bpfwp-getting-started' ) ); 
		exit;
	}

	public function register_install_screen() {
		add_dashboard_page(
			esc_html__( 'Five Star Business Profile and Schema - Welcome!', 'business-profile' ),
			esc_html__( 'Five Star Business Profile and Schema - Welcome!', 'business-profile' ),
			'manage_options',
			'bpfwp-getting-started',
			array($this, 'display_install_screen')
		);
	}

	public function hide_install_screen_menu_item() {
		remove_submenu_page( 'index.php', 'bpfwp-getting-started' );
	}

	public function add_contact_page() {
		global $bpfwp_controller;

		$contact_page = wp_insert_post(array(
			'post_title' => (isset($_POST['contact_page_title']) ? stripslashes_deep($_POST['contact_page_title']) : ''),
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'page'
		));

		$bpfwp_controller->settings->set_setting( 'contact-page', $contact_page );

		$bpfwp_controller->settings->save_settings();
	
		exit();
	}

	public function set_contact_information() {
		global $bpfwp_controller;

		$bpfwp_controller->settings->set_setting( 'schema_type', sanitize_text_field( $_POST['schema_type'] ) );
		$bpfwp_controller->settings->set_setting( 'name', sanitize_text_field( $_POST['name'] ) );
		$bpfwp_controller->settings->set_setting( 'address', array( 'text' => sanitize_textarea_field( $_POST['address'] ) ) );
		$bpfwp_controller->settings->set_setting( 'phone', sanitize_text_field( $_POST['phone'] ) );
		$bpfwp_controller->settings->set_setting( 'contact-email', sanitize_text_field( $_POST['email'] ) );

		$bpfwp_controller->settings->save_settings();
	
	    exit();
	}

	public function set_opening_hours() {
		global $bpfwp_controller; 

		$sanitized_data = $this->scheduler->sanitize_callback_wrapper( $_POST['walkthrough']['opening-hours'] );

		$bpfwp_controller->settings->set_setting( 'opening-hours', $sanitized_data );

		$bpfwp_controller->settings->save_settings();
	
	    exit();
	}

	public function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'bpfwp-getting-started' ) { return; }

		wp_enqueue_style( 'bpfwp-welcome-screen', BPFWP_PLUGIN_URL . '/assets/css/admin-bpfwp-welcome-screen.css', array(), BPFWP_VERSION );

		foreach ( $this->scheduler->styles as $slug => $style ) {
			wp_enqueue_style( $slug , BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/' . $style['path'], $style['dependencies'], $style['version'], $style['media'] );
		}
		
		wp_enqueue_script( 'bpfwp-getting-started', BPFWP_PLUGIN_URL . '/assets/js/admin-bpfwp-welcome-screen.js', array('jquery'), BPFWP_VERSION );

		foreach ( $this->scheduler->scripts as $slug => $script ) {
			wp_enqueue_script( $slug , BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/' . $script['path'], $script['dependencies'], $script['version'], $script['footer'] );
		}
	}

	public function display_install_screen() { ?>
		<?php global $bpfwp_controller; ?>
		<?php $schema_types = $bpfwp_controller->settings->get_schema_types(); ?>

		<div class='bpfwp-welcome-screen'>
			<form class='bpfwp-welcome-screen-form'>
				<?php  if (!isset($_GET['exclude'])) { ?>
				<div class='bpfwp-welcome-screen-header'>
					<h1><?php _e('Welcome to the Five Star Business Profile and Schema', 'business-profile'); ?></h1>
					<p><?php _e('Thanks for choosing the Five Star Business Profile and Schema! The following will help you get started with the setup of the plugin by creating a contact page, menu items and menu page, as well as configuring a few key options.', 'business-profile'); ?></p>
				</div>
				<?php } ?>

				<div class='bpfwp-welcome-screen-box bpfwp-welcome-screen-create_contact_page bpfwp-welcome-screen-open' data-screen='create_contact_page'>
					<h2><?php _e('1. Add a Contact Page', 'business-profile'); ?></h2>
					<div class='bpfwp-welcome-screen-box-content'>
						<p><?php _e('You can create a dedicated contact page below, or skip this step and add your contact schema to a page you\'ve already created manually.', 'business-profile'); ?></p>
						<div class='bpfwp-welcome-screen-menu-page'>
							<div class='bpfwp-welcome-screen-add-contact-page-name bpfwp-welcome-screen-box-content-divs'><label><?php _e('Page Title:', 'business-profile'); ?></label><input type='text' value='Contact' /></div>
							<div class='bpfwp-welcome-screen-add-contact-page-button'><?php _e('Create Page', 'business-profile'); ?></div>
						</div>
						<div class="bpfwp-welcome-clear"></div>
						<div class='bpfwp-welcome-screen-next-button' data-nextaction='set_contact_info'><?php _e('Next Step', 'business-profile'); ?></div>
						<div class='clear'></div>
					</div>
				</div>
		
				<div class='bpfwp-welcome-screen-box bpfwp-welcome-screen-set_contact_info' data-screen='set_contact_info'>
					<h2><?php _e('2. Set Contact Information', 'business-profile'); ?></h2>
					<div class='bpfwp-welcome-screen-box-content'>
						<p><?php _e('Set the information that will be displayed on your contact page and in the contact schema for your business', 'business-profile'); ?></p>
						<div class='bpfwp-welcome-screen-key-options'>
							<div class='rtb-welcome-screen-option'>
								<label class='bpfwp-option-name' for='bpfwp-contact-name'>Schema Type:</label>
								<select name='bpfwp-schema-type'>
									<?php foreach ( $schema_types as $schema_type => $schema_name ) { ?>
										<option value='<?php echo $schema_type ?>'><?php echo $schema_name; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class='rtb-welcome-screen-option'>
								<label class='bpfwp-option-name' for='bpfwp-contact-name'>Name:</label>
								<input type='text' name='bpfwp-contact-name' />
							</div>
							<div class='rtb-welcome-screen-option'>
								<label class='bpfwp-option-name' for='bpfwp-contact-address'>Address:</label>
								<textarea name='bpfwp-contact-address'></textarea>
							</div>
							<div class='rtb-welcome-screen-option'>
								<label class='bpfwp-option-name' for='bpfwp-contact-name'>Phone:</label>
								<input type='text' name='bpfwp-contact-phone' />
							</div>
							<div class='rtb-welcome-screen-option'>
								<label class='bpfwp-option-name' for='bpfwp-contact-name'>Email:</label>
								<input type='text' name='bpfwp-contact-email' value='<?php echo get_option('admin_email'); ?>' />
							</div>
							<div class='bpfwp-welcome-screen-set-contact-information-button'><?php _e('Set Contact Information', 'business-profile'); ?></div>
						</div>
						<div class='clear'></div>
						<div class='bpfwp-welcome-screen-next-button bpfwp-welcome-screen-next-button-not-top-margin' data-nextaction='set_hours'><?php _e('Next Step', 'business-profile'); ?></div>
						<div class='bpfwp-welcome-screen-previous-button' data-previousaction='create_contact_page'><?php _e('Previous Step', 'business-profile'); ?></div>
						<div class='clear'></div>
					</div>
				</div>
			
				<div class='bpfwp-welcome-screen-box bpfwp-welcome-screen-set_hours' data-screen='set_hours'>
					<h2><?php _e('3. Set Opening Hours', 'business-profile'); ?></h2>
					<div class='bpfwp-welcome-screen-box-content'>
						<div class='bpfwp-welcome-screen-set-hours-div'>
						
							<?php $this->scheduler->display_setting(); ?>

							<div class='bpfwp-welcome-screen-set-hours-button'><?php _e('Set Hours', 'business-profile'); ?></div>
						</div>
						<div class="bpfwp-welcome-clear"></div>
						<div class='bpfwp-welcome-screen-next-button' data-nextaction='create_schema'><?php _e('Next Step', 'business-profile'); ?></div>
						<div class='bpfwp-welcome-screen-previous-button' data-previousaction='set_contact_info'><?php _e('Previous Step', 'business-profile'); ?></div>
						<div class='clear'></div>
					</div>
				</div>

				<div class='bpfwp-welcome-screen-box bpfwp-welcome-screen-create_schema' data-screen='create_schema'>
					<h2><?php _e('4. Create a Schema', 'business-profile'); ?></h2>
					<div class='bpfwp-welcome-screen-box-content'>
						<p><?php _e('You can create a schema for items such as blog posts, products, FAQs, etc., or you can create multiple schema later.', 'business-profile'); ?></p>
						<div class='bpfwp-welcome-screen-create-schema-link-div'>
							<a href='post-new.php?post_type=<?php echo $bpfwp_controller->cpts->schema_cpt_slug; ?>'><?php _e('Create a schema now', 'business-profile'); ?></a>
						</div>
						<div class="bpfwp-welcome-clear"></div>
						<div class='bpfwp-welcome-screen-previous-button' data-previousaction='set_hours'><?php _e('Previous Step', 'business-profile'); ?></div>
						<div class='bpfwp-welcome-screen-finish-button'><a href='admin.php?page=bpfwp-dashboard'><?php _e('Finish', 'business-profile'); ?></a></div>
						<div class='clear'></div>
					</div>
				</div>
		
				<div class='bpfwp-welcome-screen-skip-container'>
					<a href='edit.php?post_type=bpfwp-menu'><div class='bpfwp-welcome-screen-skip-button'><?php _e('Skip Setup', 'business-profile'); ?></div></a>
				</div>
			</form>
		</div>

	<?php }
}


?>