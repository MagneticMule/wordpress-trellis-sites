<?php
/**
 * The7 remote api.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Remote_API
 */
class The7_Remote_API {

	const THEMEFOREST_THEME_ID = '5556590';

	const THEME_PURCHASE_URL = 'https://themeforest.net/item/the7-responsive-multipurpose-wordpress-theme/5556590?ref=Dream-Theme&license=regular&open_purchase_for_item_id=5556590';

	const THEME_THEMEFOREST_PAGE_URL = 'https://themeforest.net/item/the7-responsive-multipurpose-wordpress-theme/5556590';

	const LICENSE_URL = 'https://themeforest.net/licenses/standard';

	const PURCHASE_CODES_MANAGE_URL = 'https://my.the7.io';

	/**
	 * @var string
	 */
	protected $api_register_url = 'https://repo.the7.io/register.php';

	/**
	 * @var string
	 */
	protected $api_de_register_url = 'https://repo.the7.io/de_register.php';

	/**
	 * @var string
	 */
	protected $api_theme_info_url = 'https://repo.the7.io/theme/info.json';

	/**
	 * @var string
	 */
	protected $api_download_theme_url = 'https://repo.the7.io/theme/download.php';

	/**
	 * @var string
	 */
	protected $api_plugins_list_url = 'https://repo.the7.io/plugins/list.json';

	/**
	 * @var string
	 */
	protected $api_download_plugin_url = 'https://repo.the7.io/plugins/download.php';

	/**
	 * @var string
	 */
	protected $api_verify_purchase_code = 'https://repo.the7.io/verify-code.php';

	/**
	 * @var string
	 */
	protected $api_critical_alert = 'https://repo.the7.io/get-alert/';

	/**
	 * @var string
	 */
	private $api_demo_content_list_url = 'https://repo.the7.io/demo-content/list.json';

	/**
	 * @var string
	 */
	private $api_demo_content_download_url = 'https://repo.the7.io/demo-content/download.php';

	/**
	 * @var array
	 */
	protected $strings = array();

	/**
	 * @var string
	 */
	protected $code = '';

	/**
	 * The7_Remote_API constructor.
	 *
	 * @param $code
	 */
	public function __construct( $code ) {
		$props = array_keys( get_object_vars( $this ) );
		$props = array_filter( $props, function ( $prop ) {
			return strpos( $prop, 'api_' ) === 0;
		} );
		foreach ( $props as $prop ) {
			$constant = strtoupper( "dt_remote_{$prop}" );
			if ( defined( $constant ) && constant( $constant ) ) {
				$this->$prop = constant( $constant );
			}
		}

		$this->code = $code;

		$this->strings['fs_unavailable'] = __(
			'Failed to access the file system. You may try adjusting the permissions for the uploads folder.',
			'the7mk2'
		);
		/* translators: %s: directory name */
		$this->strings['fs_no_folder'] = __( 'Unable to find cache folder (%s).', 'the7mk2' );
		/* translators: %s: the7 server http responce code */
		$this->strings['download_failed'] = __( 'Download failed. The7 server http responce code is %s.', 'the7mk2' );
		$this->strings['bad_request'] = __( 'Bad request.', 'the7mk2' );
		$this->strings['invalid_response'] = __( 'Invalid response.', 'the7mk2' );
	}

	/**
	 * @return array|WP_Error
	 */
	public function register_purchase_code() {
		$args     = array(
			'timeout' => 30,
			'body'    => array(
				'code' => urlencode( $this->code ),
			),
		);
		$response = wp_remote_post( $this->api_register_url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( '200' != $response_code ) {
			return new WP_Error( 'bad_request', $response_code . ': ' . $this->strings['bad_request'] );
		}

		$code_check = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $code_check['errors'] ) ) {
			return new WP_Error( 'remote_api_error', $code_check['errors'] );
		}

		if ( empty( $code_check['success'] ) ) {
			return new WP_Error( 'invalid_response', $this->strings['invalid_response'] );
		}

		return $code_check;
	}

	/**
	 * @return array|bool|WP_Error
	 */
	public function de_register_purchase_code() {
		$args     = array(
			'timeout' => 30,
			'body'    => array(
				'code' => urlencode( $this->code ),
			),
		);
		$response = wp_remote_post( $this->api_de_register_url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( '200' != wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );
		}

		$code_check = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $code_check['errors'] ) ) {
			return new WP_Error( 'remote_api_error', $code_check['errors'] );
		}

		if ( empty( $code_check['success'] ) ) {
			return new WP_Error( 'invalid_response', $this->strings['invalid_response'] );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function verify_code() {
		$url      = add_query_arg( 'code', $this->code, $this->api_verify_purchase_code );
		$response = $this->remote_get_json( $url );
		if ( ! is_wp_error( $response ) && array_key_exists(
				'code',
				$response
			) && $response['code'] === 'deregistered' ) {
			return false;
		}

		return true;
	}

	public function is_api_url( $url ) {
		$host = @parse_url( $url, PHP_URL_HOST );

		return strpos( $this->api_download_plugin_url, $host ) !== false;
	}

	/**
	 * Check theme update info.
	 *
	 * @return array|WP_Error
	 */
	public function check_theme_update() {
		return $this->remote_get_json( $this->api_theme_info_url );
	}

	/**
	 * Return theme download url.
	 *
	 * @param string $version Required theme version.
	 *
	 * @return string
	 */
	public function get_theme_download_url( $version = '' ) {
		$query_args = array(
			'code' => $this->code,
		);

		if ( $version ) {
			$query_args['version'] = $version;
		}

		return add_query_arg( $query_args, $this->api_download_theme_url );
	}

	/**
	 * Get plugins list.
	 *
	 * @return array|WP_Error
	 */
	public function check_plugins_list() {
		return $this->remote_get_json( $this->api_plugins_list_url );
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	public function get_plugin_download_url( $slug ) {
		return add_query_arg( array( 'code' => $this->code, 'item' => $slug ), $this->api_download_plugin_url );
	}

	/**
	 * Return critical alert body as array or WP_Error on error.
	 *
	 * @return array|WP_Error
	 */
	public function get_critical_alert() {
		return $this->remote_get_json( add_query_arg( array( 'code' => $this->code ), $this->api_critical_alert ) );
	}

	/**
	 * @param string $url
	 *
	 * @return array|WP_Error
	 */
	protected function remote_get_json( $url ) {
		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( '200' != wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );
		}

		$json = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $json ) || ! is_array( $json ) ) {
			return new WP_Error( 'invalid_response', $this->strings['invalid_response'] );
		}

		return $json;
	}

	/**
	 * @return array|bool
	 */
	public function get_demos_list() {
		$response = wp_remote_get(
			$this->api_demo_content_list_url,
			array(
				'timeout'    => 30,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . network_site_url(),
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code || is_wp_error( $response ) ) {
			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );

		return json_decode( $response_body, true );
	}

	/**
	 * This method try to download demo content with $id and put it to $target_path.
	 * Creates $target_path dir if it is not exists. Dummy content zip archive will be unzipped.
	 *
	 * @param string $id
	 * @param string $target_dir
	 * @param string $req_url
	 *
	 * @return string|WP_Error Path where dummy content files is located on success or WP_Error on failure.
	 */
	public function download_demo( $id, $target_dir, $req_url = '' ) {
		/**
		 * @var $wp_filesystem WP_Filesystem_Base
		 */ global $wp_filesystem;

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return new WP_Error( 'fs_unavailable', $this->strings['fs_unavailable'] );
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
			return $wp_filesystem->errors;
		}

		$request_url_args = array(
			'item'    => $id,
			'code'    => $this->code,
			'req_url' => rawurlencode( $req_url ),
		);
		$request_url      = add_query_arg( $request_url_args, $this->api_demo_content_download_url );
		$remote_response  = wp_safe_remote_get(
			$request_url,
			array(
				'timeout'    => 300,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . network_site_url(),
			)
		);

		if ( is_wp_error( $remote_response ) ) {
			return $remote_response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $remote_response );

		if ( ! is_array( $remote_response ) || 200 !== $response_code ) {
			return new WP_Error(
				'download_failed', sprintf( $this->strings['download_failed'], $response_code )
			);
		}

		wp_mkdir_p( $target_dir );

		$file_content  = wp_remote_retrieve_body( $remote_response );
		$zip_file_name = trailingslashit( $target_dir ) . "{$id}.zip";
		$wp_filesystem->put_contents( $zip_file_name, $file_content );

		$unzip_result = unzip_file( $zip_file_name, $target_dir );
		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result;
		}

		$dummy_dir = trailingslashit( $target_dir ) . $id;

		if ( ! is_dir( $dummy_dir ) ) {
			return new WP_Error( 'fs_no_folder', sprintf( $this->strings['fs_no_folder'], $dummy_dir ) );
		}

		return $dummy_dir;
	}
}
