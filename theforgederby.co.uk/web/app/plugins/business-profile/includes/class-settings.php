<?php
/**
 * Define settings used throughout the plugin.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSettings' ) ) :

	/**
	 * Class to handle configurable settings for Business Profile
	 *
	 * @since 0.0.1
	 */
	class bpfwpSettings {

		/**
		 * Default values for settings
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    array
		 */
		public $defaults = array();

		/**
		 * Default values for display settings
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    array
		 */
		public $default_display_settings = array();

		/**
		 * Stored values for settings
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    array
		 */
		public $settings = array();

		/**
		 * Initialize the class and register hooks.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'set_defaults' ) );

			add_action( 'init', array( $this, 'load_settings_panel' ) );

			// Order schedule exceptions and remove past exceptions
			add_filter( 'sanitize_option_bpfwp-settings', array( $this, 'clean_schedule_exceptions' ), 100 );
		}

		/**
		 * Load the plugin's default settings
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function set_defaults() {

			$this->defaults = array(
				'schema_type' => 'Organization',
				'name'        => get_bloginfo( 'name' ),
			);

			$this->defaults = apply_filters( 'bpfwp_defaults', $this->defaults );
		}

		/**
		 * Get default display settings
		 *
		 * Controls default visibility of elements in the contact card as well
		 * as when template functions, like bpfwp_print_name, are called
		 * directly.
		 *
		 * @since  1.1
		 * @access public
		 * @return array $default_display_settings The display settings defaults.
		 */
		public function get_default_display_settings() {

			if ( ! empty( $this->default_display_settings ) ) {
				return $this->default_display_settings;
			}

			$this->default_display_settings = apply_filters(
				'bpfwp_default_display_settings',
				array(
					'location'                  => false,
					'show_name'                 => true,
					'show_address'              => true,
					'show_get_directions'       => true,
					'show_phone'                => true,
					'show_ordering_link'        => true,
					'show_contact'              => true,
					'show_opening_hours'        => true,
					'show_opening_hours_brief'  => false,
					'show_map'                  => true,
					'show_image'                => false
				)
			);

			return $this->default_display_settings;
		}

		/**
		 * Get a setting's value or fallback to a default if one exists
		 *
		 * @since  0.0.1
		 * @access public
		 * @param  string $setting The setting to retrieve.
		 * @param  string $location The location where the setting is used.
		 * @return mixed A setting based on the key provided.
		 */
		public function get_setting( $setting, $location = false ) {

			$result_value = null;

			// Most settings are named with hyphens, but the schema_type uses
			// an underscore. This just provides a small convenience by allowing
			// users to look up the setting by `schema-type`.
			if ( 'schema-type' === $setting ) {
				$setting = 'schema_type';
			}

			if ( ! empty( $location ) ) {
				// Map setting slugs to post data.

				switch ( $setting ) {

					case 'image' :
						$result_value = has_post_thumbnail( $location ) ? get_post_thumbnail_id( $location ) : $this->get_setting( $setting );
						break;

					case 'name' :
						$result_value = get_the_title( $location );
						break;

					case 'description' :
						$result_value = get_the_content( $location );
						break;

					case 'address' :
						$result_value = '';

						$text = get_post_meta( $location, 'geo_address', true );
						$lat  = get_post_meta( $location, 'geo_latitude', true );
						$lon  = get_post_meta( $location, 'geo_longitude', true );

						if(! empty( $text ) || ( $lat && $lon ) ) {
							$result_value = array(
								'text' => $text,
								'lat'  => $lat,
								'lon'  => $lon
							);
						}
						break;

					case 'contact-page' :
						$result_value = get_post_meta( $location, 'contact_post', true );
						break;

					case 'contact-email' :
						$result_value = get_post_meta( $location, 'contact_email', true );
						break;

					case 'opening-hours' :
						$result_value = get_post_meta( $location, 'opening_hours', true );
						break;

					case 'clickphone' :
						$result_value = get_post_meta( $location, $setting, true );
						if( empty($result_value) ) {
							$result_value = get_post_meta( $location, 'phone', true );
						}
						break;

					case 'exceptions' :
					case 'schema_type' :
					case 'phone' :
					default :
						$result_value = get_post_meta( $location, $setting, true );
						break;
				}
			}

			// fallback
			if( ! $result_value ) {
				if ( empty( $this->settings ) ) {
					$this->settings = get_option( 'bpfwp-settings' );
				}

				if ( ! $result_value && ! empty( $this->settings[ $setting ] ) ) {
					$result_value = $this->settings[ $setting ];
				}

				if ( ! $result_value && ! empty( $this->defaults[ $setting ] ) ) {
					$result_value = $this->defaults[ $setting ];
				}
			}

			return $result_value;
		}

		/**
		 * Set the value for a particular setting
		 *
		 *
		 * @since  2.0.4
		 * @access public
		 */
		public function set_setting( $setting_name, $setting_value ) {

			if ( empty( $this->settings ) ) {
				$this->settings = get_option( 'bpfwp-settings' );
			}

			if ( $setting_name ) {
				$this->settings[ $setting_name ] = $setting_value;
			}
		}

		/**
		 * Save all settings, to be used with set_setting
		 * @since 2.0.8
		 */
		public function save_settings() {
		
			update_option( 'bpfwp-settings', $this->settings );
		}

		/**
		 * Load the admin settings page.
		 *
		 * @since 0.0.1
		 * @access public
		 * @link  https://github.com/NateWr/simple-admin-pages
		 */
		public function load_settings_panel() {

			global $bpfwp_controller;

			require_once BPFWP_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php';
			$sap = sap_initialize_library(
				$args = array(
					'version' => '2.5.5',
					'lib_url' => BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/',
				)
			);

			$sap->add_page(
				'submenu',
				array(
					'id'            => 'bpfwp-settings',
					'parent_menu'	=> 'bpfwp-business-profile',
					'title'         => __( 'Settings', 'business-profile' ),
					'menu_title'    => __( 'Settings', 'business-profile' ),
					'capability'    => 'manage_options',
					'default_tab'   => 'bpfwp-basic',
				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'            => 'bpfwp-basic',
					'title'         => __( 'Basic', 'business-profile' ),
					'is_tab'		=> true,
				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'    => 'bpfwp-seo',
					'title' => __( 'Search Engine Optimization', 'business-profile' ),
					'tab'	=> 'bpfwp-basic'
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-seo',
				'select',
				array(
					'id'           => 'schema_type',
					'title'        => __( 'Schema Type', 'business-profile' ),
					'description'  => __( 'Select the option that best describes your business to improve how search engines understand your website.', 'business-profile' ) . ' <a href="http://schema.org/" target="_blank">Schema.org</a>',
					'blank_option' => false,
					'options'      => $this->get_schema_types(),
					'args'			=> array(
						'label_for' => 'schema_type',
						'class' 	=> 'bpfwp-schema_type'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-seo',
				'image',
				array(
					'id'           => 'image',
					'title'        => __( 'Image', 'business-profile' ),
					'description'  => __( 'Google requires you provide an image to display with your local business search profile.', 'business-profile' ),
					'strings'      => array(
						'add_image'    => __( 'Add Image', 'business-profile' ),
						'change_image' => __( 'Change Image', 'business-profile' ),
						'remove_image' => __( 'Remove Image', 'business-profile' ),
					),
					'args'         => array(
						'label_for' => 'image',
						'class'     => 'bpfwp-image'
					),
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-seo',
				'text',
				array(
					'id'          => 'ordering-link',
					'title'       => __( 'Ordering Link', 'business-profile' ),
					'description' => __( 'If you offer online ordering, enter the URL of your order page here (can be on this site or external) and a link to it will display in your contact card.', 'business-profile' ),
					'placeholder' => 'https://example.com',
					'args'        => array(
						'label_for' => 'bpfwp-settings[ordering-link]',
						'class'     => 'bpfwp-ordering-link'
					)
				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'    => 'bpfwp-contact',
					'title' => __( 'Contact Information', 'business-profile' ),
					'tab'	=> 'bpfwp-basic'
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'text',
				array(
					'id'          => 'name',
					'title'       => __( 'Name', 'business-profile' ),
					'description' => __( 'Enter the name of your business if it is different than the website name.', 'business-profile' ),
					'placeholder' => $this->defaults['name'],
					'args'			=> array(
						'label_for' => 'bpfwp-settings[name]',
						'class' 	=> 'bpfwp-name'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'address',
				array(
					'id'      => 'address',
					'title'   => __( 'Address', 'business-profile' ),
					'api_key_selector' => '.bpfwp-google-maps-api-key input',
					'strings' => array(
						'sep-action-links' => _x( ' | ', 'separator between admin action links in address component', 'business-profile' ),
						'sep-lat-lon'      => _x( ', ', 'separates latitude and longitude', 'business-profile' ),
						'no-setting'       => __( 'No map coordinates set.', 'business-profile' ),
						'retrieving'       => __( 'Requesting new coordinates', 'business-profile' ),
						'select'           => __( 'Select a match below', 'business-profile' ),
						'view'             => __( 'View', 'business-profile' ),
						'retrieve'         => __( 'Retrieve map coordinates', 'business-profile' ),
						'remove'           => __( 'Remove map coordinates', 'business-profile' ),
						'try_again'        => __( 'Try again?', 'business-profile' ),
						'result_error'     => __( 'Error', 'business-profile' ),
						'result_invalid'   => __( 'Invalid request. Be sure to fill out the address field before retrieving coordinates.', 'business-profile' ),
						'result_denied'    => __( 'Request denied.', 'business-profile' ),
						'result_limit'     => __( 'Request denied because you are over your request quota.', 'business-profile' ),
						'result_empty'     => __( 'Nothing was found at that address', 'business-profile' ),
					),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[address]',
						'class' 	=> 'bpfwp-address'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'text',
				array(
					'id'          => 'google-maps-api-key',
					'title'       => __( 'Google Maps API Key', 'business-profile' ),
					'description' => sprintf(
						__( 'Google requires an API key to use their maps. %sGet an API key%s. A full walk-through is available in the %sdocumentation%s.', 'business-profile' ),
						'<a href="https://developers.google.com/maps/documentation/javascript/get-api-key">',
						'</a>',
						'<a href="http://doc.fivestarplugins.com/plugins/business-profile/user/faq#google-maps-api-key">',
						'</a>'
					),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[google-maps-api-key]',
						'class' 	=> 'bpfwp-google-maps-api-key'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'text',
				array(
					'id'    => 'phone',
					'title' => __( 'Phone', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[phone]',
						'class' 	=> 'bpfwp-phone'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'text',
				array(
					'id'    => 'clickphone',
					'title' => __( 'Click-to-Call Phone', 'business-profile' ),
					'description' => __( 'Use this field to set a different number for when the phone number is clicked in the contact card (e.g. if you\'d like it to be just an unformatted string of numbers). If left blank, the click-to-call will use the phone number provided in the option above.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[clickphone]',
						'class' 	=> 'bpfwp-clickphone'
					)
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'post',
				array(
					'id'           => 'contact-page',
					'title'        => __( 'Contact Page', 'business-profile' ),
					'description'  => __( 'Select a page on your site where users can reach you, such as a contact form.', 'business-profile' ),
					'blank_option' => true,
					'args'         => array(
						'post_type'      => 'page',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'label_for' => 'bpfwp-settings[contact-page]',
						'class' 	=> 'bpfwp-contact-page'
					),
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'toggle',
				array(
					'id'			=> 'disable-contact-page-card',
					'title'			=> __( 'Disable Contact Page Card', 'business-profile' ),
					'label'			=> __( 'This disables the contact card from being automatically included on the page you set as the contact page in the option above this one.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[disable-contact-page-card]',
						'class' 	=> 'bpfwp-disable-contact-page-card'
					)

				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-contact',
				'text',
				array(
					'id'          => 'contact-email',
					'title'       => __( 'Email Address (optional)', 'business-profile' ),
					'description' => __( 'Enter an email address only if you want to display this publicly. Showing your email address on your site may cause you to receive excessive spam.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[contact-email]',
						'class' 	=> 'bpfwp-contact-email'
					)
				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'    => 'bpfwp-schedule',
					'title' => __( 'Schedule', 'business-profile' ),
					'tab'	=> 'bpfwp-basic'
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-schedule',
				'scheduler',
				array(
					'id'          => 'opening-hours',
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
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-schedule',
				'scheduler',
				array(
					'id'				=> 'exceptions',
					'title'				=> __( 'Exceptions', 'business-profile' ),
					'description'		=> __( '<strong>This feature requires at least version 5.3 of WordPress.</strong> Define special opening hours for holidays, events or other needs. Leave the time empty if you\'re closed all day.', 'business-profile' ),
					'time_format'   	=> _x( 'h:i A', 'Time format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/time.htm#formats', 'business-profile' ),
					'date_format'   	=> _x( 'mmmm d, yyyy', 'Date format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/date.htm#formatting-rules', 'business-profile' ),
					'disable_weekdays'	=> true,
					'disable_weeks'		=> true,
					'strings'       	=> array(
						'add_rule'         => __( 'Add another exception', 'business-profile' ),
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
						'all_day'          => _x( 'Closed all day', 'Brief default description of a scheduling exception when no times are set', 'business-profile' ),
						'before'           => _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'business-profile' ),
						'after'            => _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'business-profile' ),
						'separator'        => _x( '&mdash;', 'Separator between times of a scheduling rule', 'business-profile' ),
					),
				)
			);
	
			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'            => 'bpfwp-locations',
					'title'         => __( 'Multiple Locations', 'business-profile' ),
					'tab'	          => 'bpfwp-basic'
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-locations',
				'toggle',
				array(
					'id'			=> 'multiple-locations',
					'title'			=> __( 'Multiple Locations', 'business-profile' ),
					'label'			=> __( 'Enable support for multiple business locations.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[multiple-locations]',
						'class' 	=> 'bpfwp-multiple-locations'
					)

				)
			);

			// $sap->add_section(
			// 	'bpfwp-settings',
			// 	array(
			// 		'id'            => 'bpfwp-about-page',
			// 		'title'         => __( 'About Us Page', 'business-profile' ),
			// 		'tab'	=> 'bpfwp-basic'
			// 	)
			// );

			// $sap->add_setting(
			// 	'bpfwp-settings',
			// 	'bpfwp-about-page',
			// 	'post',
			// 	array(
			// 		'id'           => 'about-page',
			// 		'title'        => __( 'About Us Page', 'business-profile' ),
			// 		'description'  => __( 'Select your about page to have the appropriate schema automatically added to it.', 'business-profile' ),
			// 		'blank_option' => true,
			// 		'args'         => array(
			// 			'post_type'      => 'page',
			// 			'posts_per_page' => -1,
			// 			'post_status'    => 'publish',
			// 			'label_for' => 'bpfwp-settings[about-page]',
			// 			'class' 	=> 'bpfwp-about-page'
			// 		),
			// 	)
			// );

			// $sap->add_section(
			// 	'bpfwp-settings',
			// 	array(
			// 		'id'            => 'bpfwp-site-navigation',
			// 		'title'         => __( 'Site Navigation', 'business-profile' ),
			// 		'tab'	=> 'bpfwp-basic'
			// 	)
			// );

			// $sap->add_setting(
			// 	'bpfwp-settings',
			// 	'bpfwp-site-navigation',
			// 	'menu',
			// 	array(
			// 		'id'           => 'main-menu',
			// 		'title'        => __( 'Main Menu', 'business-profile' ),
			// 		'description'  => __( 'Select your main menu to have the appropriate schema automatically added to it.', 'business-profile' ),
			// 		'blank_option' => true,
			// 		'args'         => array(
			// 			'label_for' => 'bpfwp-settings[main-menu]',
			// 			'class' 	=> 'bpfwp-main-menu'
			// 		),
			// 	)
			// );

			if ( ! is_object($bpfwp_controller) or ! $bpfwp_controller->permissions->check_permission('premium') ) {
				$premium_permissions = array(
					'disabled'		=> true,
					'disabled_image'=> 'https://www.etoilewebdesign.com/wp-content/uploads/2018/06/Logo-White-Filled40-px.png',
					'purchase_link'	=> 'https://www.fivestarplugins.com/plugins/business-profile/'
				);
			}
			else { $premium_permissions = array(); }

			if ( ! is_object($bpfwp_controller) or ! $bpfwp_controller->permissions->check_permission('integrations') ) {
				$integrations_permissions = array(
					'disabled'		=> true,
					'disabled_image'=> 'https://www.etoilewebdesign.com/wp-content/uploads/2018/06/Logo-White-Filled40-px.png',
					'purchase_link'	=> 'https://www.fivestarplugins.com/plugins/business-profile/'
				);
			}
			else { $integrations_permissions = array(); }

			if ( ! is_object($bpfwp_controller) or ! $bpfwp_controller->permissions->check_permission('locations') ) {
				$locations_permissions = array(
					'disabled'		=> true,
					'disabled_image'=> 'https://www.etoilewebdesign.com/wp-content/uploads/2018/06/Logo-White-Filled40-px.png',
					'purchase_link'	=> 'https://www.fivestarplugins.com/plugins/business-profile/'
				);
			}
			else { $locations_permissions = array(); }

			$sap->add_section(
				'bpfwp-settings',
				array(
					'id'            => 'bpfwp-premium',
					'title'         => __( 'Premium', 'business-profile' ),
					'is_tab'		=> true,
				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array_merge(
					array(
						'id'            => 'bpfwp-premium-general',
						'title'         => __( 'General', 'business-profile' ),
						'tab'	          => 'bpfwp-premium'
					),
					$premium_permissions
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-premium-general',
				'toggle',
				array(
					'id'			=> 'article-rich-snippets',
					'title'			=> __( 'Post Rich Snippets', 'business-profile' ),
					'description'	=> __( 'Automatically enable article \'Rich Snippets\' for Google for all regular posts on the site.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[article-rich-snippets]',
						'class' 	=> 'bpfwp-article-rich-snippets'
					)

				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-premium-general',
				'toggle',
				array(
					'id'			=> 'schema-default-helpers',
					'title'			=> __( 'Schema Default Helpers', 'business-profile' ),
					'description'	=> __( 'Adds a helper pop-up that can be accessed on click on the Schema edit screen that list the available default options, functions and metas', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[schema-default-helpers]',
						'class' 	=> 'bpfwp-schema-default-helpers'
					)

				)
			);

			$sap->add_section(
				'bpfwp-settings',
				array_merge(
					array(
						'id'            => 'bpfwp-integrations',
						'title'         => __( 'Plugin Integrations', 'business-profile' ),
						'tab'	         => 'bpfwp-premium'
					),
					$integrations_permissions
				)
			);

			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-integrations',
				'toggle',
				array(
					'id'			=> 'woocommerce-integration',
					'title'			=> __( 'WooCommerce Integration', 'business-profile' ),
					'description'	=> __( 'Automatically enable product \'Rich Snippets\' for Google.', 'business-profile' ),
					'args'			=> array(
						'label_for' => 'bpfwp-settings[woocommerce-integration]',
						'class' 	=> 'bpfwp-woocommerce-integration'
					)

				)
			);

			$sap = apply_filters( 'bpfwp_settings_page', $sap );

			$sap->add_admin_menus();

		}

		/**
		 * Sort the schedule exceptions and remove past exceptions before saving
		 *
		 * @since 2.1.5
		 */
		public function clean_schedule_exceptions( $val ) {
	
			if ( empty( $val['exceptions'] ) ) {
				return $val;
			}
	
			// Sort by date
			$exceptions = $val['exceptions'];
			usort( $exceptions, array( $this, 'sort_by_date' ) );
	
			// Remove exceptions more than a day old
			$week_ago = time() - 24*3600;
			for( $i = 0; $i < count( $exceptions ); $i++ ) {
				if ( strtotime( $exceptions[$i]['date'] ) > $week_ago ) {
					break;
				}
			}
			if ( $i ) {
				$exceptions = array_slice( $exceptions, $i );
			}
	
			$val['exceptions'] = $exceptions;
	
			return $val;
		}
	
		/**
		 * Sort an associative array by the value's date parameter
		 *
		 * @usedby self::clean_schedule_exceptions()
		 * @since 2.1.5
		 */
		public function sort_by_date( $a, $b ) {
	
			$ad = empty( $a['date'] ) ? 0 : strtotime( $a['date'] );
			$bd = empty( $b['date'] ) ? 0 : strtotime( $b['date'] );
	
			return $ad - $bd;
		}

		/**
		 * Array of schema type options
		 *
		 * @since  1.1
		 * @access public
		 * @return array A filtered list of schema types.
		 */
		public function get_schema_types() {
			return apply_filters(
				'bp_schema_types',
				array(
					'Organization'                	=> 'Organization',
					'Airline'					  	=> 'Airline',
					'Consortium'                  	=> 'Consortium',
					'Corporation'                 	=> 'Corporation',
					'EducationalOrganization'     	=> 'Educational Organization',
					'CollegeOrUniversity'         	=> '- College or University',
					'ElementarySchool'	         	=> '- Elementary School',
					'HighSchool'         			=> '- High School',
					'MiddleSchool'         			=> '- Middle School',
					'Prechool'         				=> '- Prechool',
					'School'        			 	=> '- School',
					'FundingScheme'				  	=> 'Funding Scheme',
					'GovernmentOrganization'      	=> 'Government Organization',
					'LibrarySystem'               	=> 'Library System',
					'LocalBusiness'               	=> 'Local Business',
					'AnimalShelter'               	=> '- Animal Shelter',
					'AutomotiveBusiness'          	=> '- Automotive Business',
					'AutoBodyShop'          		=> '--- AutoBody Shop',
					'AutoDealer'          			=> '--- Auto Dealer',
					'AutoPartsStore'          		=> '--- Auto Parts Store',
					'AutoRental'          			=> '--- Auto Rental',
					'AutoRepair'          			=> '--- Auto Repair',
					'AutoWash'          			=> '--- Auto Wash',
					'GasStation'          			=> '--- Gas Station',
					'MotorcycleDealer'          	=> '--- Motorcycle Dealer',
					'MotorcycleRepair'          	=> '--- Motorcycle Repair',
					'ChildCare'                   	=> '- Child Care',
					'Dentist'                   	=> '- Dentist',
					'DryCleaningOrLaundry'        	=> '- Dry Cleaning or Laundry',
					'EmergencyService'            	=> '- Emergency Service',
					'FirsStation'          			=> '--- Fire Station',
					'Hospital'          			=> '--- Hospital',
					'PoliceStation'          		=> '--- Police Station',
					'EmploymentAgency'            	=> '- Employment Agency',
					'EntertainmentBusiness'       	=> '- Entertainment Business',
					'AdultEntertainment'          	=> '--- Adult Entertainment',
					'AmusementPark'          		=> '--- Amusement Park',
					'ArtGallery'          			=> '--- Art Gallery',
					'Casino'          				=> '--- Casino',
					'ComedyClub'          			=> '--- Comedy Club',
					'MovieTheater'          		=> '--- Movie Theater',
					'NightClub'          			=> '--- Night Club',
					'FinancialService'            	=> '- Financial Service',
					'AccountingService'          	=> '--- Accounting Service',
					'AutomatedTeller'          		=> '--- Automated Teller',
					'BankOrCreditUnion'          	=> '--- Bank or Credit Union',
					'InsuranceAgency'          		=> '--- Insurance Agency',
					'FoodEstablishment'           	=> '- Food Establishment',
					'Bakery'          				=> '--- Bakery',
					'BarOrPub'          			=> '--- Bar or Pub',
					'Brewery'          				=> '--- Brewery',
					'CafeOrCoffeeShop'          	=> '--- Cafe or Coffee Shop',
					'Distillery'          			=> '--- Distillery',
					'FastFoodRestaurant'        	=> '--- Fast Food Restaurant',
					'IceCreamShop'          		=> '--- Ice Cream Shop',
					'Restaurant'          			=> '--- Restaurant',
					'Winery'          				=> '--- Winery',
					'GovernmentOffice'            	=> '- Government Office',
					'PostOffice'          			=> '--- Post Office',
					'HealthAndBeautyBusiness'     	=> '- Health and Beauty Business',
					'BeautySalon'          			=> '--- Beauty Salon',
					'DaySpa'          				=> '--- Day Spa',
					'HairSalon'          			=> '--- Hair Salon',
					'HealthClub'          			=> '--- Health Club',
					'NailSalon'          			=> '--- Nail Salon',
					'TattooParlor'          		=> '--- Tattoo Parlor',
					'HomeAndConstructionBusiness' 	=> '- Home and Construction Business',
					'Electrician'          			=> '--- Electrician',
					'GeneralContractor'          	=> '--- General Contractor',
					'HVACBusiness'          		=> '--- HVAC Business',
					'HousePainter'          		=> '--- House Painter',
					'Locksmith'          			=> '--- Locksmith',
					'MovingCompany'          		=> '--- Moving Company',
					'Plumber'          				=> '--- Plumber',
					'RoofingContractor'          	=> '--- Roofing Contractor',
					'InternetCafe'                	=> '- Internet Cafe',
					'LegalService'                	=> '- Legal Service',
					'Attorney'          			=> '--- Attorney',
					'Notary'          				=> '--- Notary',
					'Library'                     	=> '- Library',
					'LodgingBusiness'             	=> '- Lodging Business',
					'BedAndBreakfast'          		=> '--- Bed and Breakfast',
					'Campground'          			=> '--- Campground',
					'Hostel'          				=> '--- Hostel',
					'Hotel'          				=> '--- Hotel',
					'Motel'          				=> '--- Motel',
					'Resort'          				=> '--- Resort',
					'MedicalBusiness'         		=> '- Medical Business',
					'CommunityHealth'          		=> '--- Community Health',
					'Dentist'          				=> '--- Dentist',
					'Dermatology'          			=> '--- Dermatology',
					'DietNutrition'          		=> '--- Diet Nutrition',
					'Emergency'          			=> '--- Emergency',
					'Geriatric'          			=> '--- Geriatric',
					'Gynecologic'          			=> '--- Gynecologic',
					'MedicalClinic'          		=> '--- Medical Clinic',
					'Midwifery'          			=> '--- Midwifery',
					'Nursing'          				=> '--- Nursing',
					'Obstetric'          			=> '--- Obstetric',
					'Oncologic'          			=> '--- Oncologic',
					'Optician'          			=> '--- Optician',
					'Optometric'          			=> '--- Optometric',
					'Otolaryngologic'          		=> '--- Otolaryngologic',
					'Pediatric'          			=> '--- Pediatric',
					'Pharmacy'          			=> '--- Pharmacy',
					'Physiotherapy'          		=> '--- Physiotherapy',
					'PlasticSurgery'          		=> '--- Plastic Surgery',
					'Podiatric'          			=> '--- Podiatric',
					'PrimaryCare'          			=> '--- Primary Care',
					'Psychiatric'          			=> '--- Psychiatric',
					'PublicHealth'          		=> '--- Public Health',
					'ProfessionalService'          	=> '- Professional Service',
					'RadioStation'                	=> '- Radio Station',
					'RealEstateAgent'             	=> '- Real Estate Agent',
					'RecyclingCenter'             	=> '- Recycling Center',
					'SelfStorage'                 	=> '- Self Storage',
					'ShoppingCenter'                => '- Shopping Center',
					'SportsActivityLocation'      	=> '- Sports Activity Location',
					'BowlingAlley'          		=> '--- Bowling Alley',
					'ExerciseGym'          			=> '--- Exercise Gym',
					'GolfCourse'          			=> '--- Golf Course',
					'HealthClub'          			=> '--- Health Club',
					'PublicSwimmingPool'          	=> '--- Public Swimming Pool',
					'SkiResort'          			=> '--- Ski Resort',
					'SportsClub'          			=> '--- Sports Club',
					'StadiumOrArena'          		=> '--- Stadium or Arena',
					'TennisComplex'          		=> '--- Tennis Complex',
					'Store'                       	=> '- Store',
					'AutoPartsStore'          		=> '--- Auto Parts Store',
					'BikeStore'          			=> '--- Bike Store',
					'BookStore'          			=> '--- Book Store',
					'ClothingStore'          		=> '--- Clothing Store',
					'ComputerStore'          		=> '--- Computer Store',
					'ConvenienceStore'          	=> '--- Convenience Store',
					'DepartmentStore'          		=> '--- Department Store',
					'ElectronicsStore'          	=> '--- Electronics Store',
					'Florist'          				=> '--- Florist',
					'FurnitureSotre'          		=> '--- Furniture Store',
					'GardenStore'          			=> '--- Garden Store',
					'GroceryStore'          		=> '--- Grocery Store',
					'HardwareStore'          		=> '--- Hardware Store',
					'HobbyShop'          			=> '--- Hobby Shop',
					'HomeGoodsStore'          		=> '--- Home Goods Store',
					'JewelryStore'          		=> '--- Jewelry Store',
					'LiquorStore'          			=> '--- Liquor Store',
					'MensClothingStore'          	=> '--- Mens Clothing Store',
					'MobilePhoneStore'          	=> '--- Mobile Phone Store',
					'MovieRentalStore'          	=> '--- Movie Rental Store',
					'MusicStore'          			=> '--- Music Store',
					'OfficeEquipmentStore'          => '--- Office Equipment Store',
					'OutletStore'          			=> '--- Outlet Store',
					'PawnShop'          			=> '--- Pawn Shop',
					'PetStore'          			=> '--- Pet Store',
					'ShoeStore'          			=> '--- Shoe Store',
					'SportingGoodsStore'          	=> '--- Sporting Goods Store',
					'TireShop'          			=> '--- Tire Shop',
					'ToyStore'          			=> '--- Toy Store',
					'WholesaleStore'          		=> '--- Wholesale Store',
					'TelevisionStation'    			=> '- Television Station',
					'TouristInformationCenter'    	=> '- Tourist Information Center',
					'TravelAgency'                	=> '- Travel Agency',
					'MedicalOrganization'          	=> 'Medical Organization',
					'Dentist'                		=> '- Dentist',
					'DiagnosticLab'                	=> '- Diagnostic Lab',
					'Hospital'                		=> '- Hospital',
					'MedicalClinic'                	=> '- Medical Clinic',
					'Pharmacy'                		=> '- Pharmacy',
					'Physician'                		=> '- Physician',
					'VeterinaryCare'                => '- Veterinary Care',
					'NGO'                         	=> 'NGO',
					'NewsMediaOrganization'        	=> 'News Media Organization',
					'PerformingGroup'             	=> 'PerformingGroup',
					'DanceGroup'                	=> '- Dance Group',
					'MusicGroup'                	=> '- Music Group',
					'TheaterGroup'                	=> '- Theater Group',
					'Project'             			=> 'Project',
					'FundingAgency'                	=> '- Funding Agency',
					'ResearchProject'             	=> '- Research Project',
					'SportsOrganization'           	=> 'Sports Organization',
					'SportsTeam'                	=> '- Sports Team',
					'WorkersUnion'             		=> 'Workers Union',
				)
			);
		}

	}
endif;