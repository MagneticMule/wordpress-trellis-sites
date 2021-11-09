<?php
/**
 * Create a schema for a Automotive Business as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaAutomotiveBusiness' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-localbusiness.php';

	/**
	 * Automotive Business schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaAutomotiveBusiness extends bpfwpSchemaLocalBusiness {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'AutomotiveBusiness';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Automotive Business';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			parent::set_fields();
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children( $depth ) {
			$depth--;

			$child_classes = array(
				'autobodyshop' => 'AutoBodyShop',
				'autodealer' => 'AutoDealer',
				'autopartsstore' => 'AutoPartsStore',
				'autorental' => 'AutoRental',
				'autorepair' => 'AutoRepair',
				'autowash' => 'AutoWash',
				'gasstation' => 'GasStation',
				'motorcycledealer' => 'MotorcycleDealer',
				'motorcyclerepair' => 'MotorcycleRepair',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
