<?php
/**
 * Create a schema for a Employer Aggregate Rating as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaEmployerAggregateRating' ) ) :

	/**
	 * Employer Aggregate Rating schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaEmployerAggregateRating extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'EmployerAggregateRating';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Employer Aggregate Rating';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-field.php';

			$fields = array(
				new bpfwpSchemaField( array( 
					'slug' 				=> 'itemReviewed', 
					'name' 				=> 'Item Reviewed', 
					'type'				=> 'Organization',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'sameAs', 
							'name' 				=> 'Corresponding URL (sameAs)', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'sameAs', $this->slug, 'itemReviewed' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'ratingValue', 
					'name' 				=> 'Rating', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'ratingCount', 
					'name' 				=> 'Rating Count', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingCount', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'bestRating', 
					'name' 				=> 'Best Rating', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bestRating', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'worstRating', 
					'name' 				=> 'Worst Rating', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'worstRating', $this->slug )
				) )
			);

			$this->fields = apply_filters( 'bpfwp_schema_fields', $fields, $this->slug );
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children(  $depth ) {
			$depth--;

			$child_classes = array ();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;