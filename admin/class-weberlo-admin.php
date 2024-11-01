<?php 

defined( 'ABSPATH' ) || exit;

class WEBERLO_Admin {
	public static function init() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'register_scripts_styles' ] );
		add_action( 'admin_menu', [ __CLASS__, 'add_admin_page' ] );
		add_filter( 'plugin_action_links_' . WEBERLO_PLUGIN_BASENAME, [ __CLASS__, 'add_plugin_page_settings_link' ] );
	}

	public static function register_scripts_styles() {
		wp_register_style( WEBERLO_PLUGIN_NAME, WEBERLO_PLUGIN_URL . 'admin/css/weberlo-2.css', [],
			WEBERLO_PLUGIN_VERSION, 'all' );
		wp_register_script( WEBERLO_PLUGIN_NAME, WEBERLO_PLUGIN_URL . 'admin/js/weberlo-2.js', [], WEBERLO_PLUGIN_VERSION,
			true );
	}

	public static function enqueue_scripts_styles() {
		wp_enqueue_style( WEBERLO_PLUGIN_NAME );
		wp_enqueue_script( WEBERLO_PLUGIN_NAME );
	}

	public static function add_admin_page() {
		add_management_page( 'Weberlo', 'Weberlo', 'manage_options', 'weberlo', [ __CLASS__, 'render_admin_page' ] );
	}

	public static function add_plugin_page_settings_link( $links ) {
		return array_merge( [ '<a href="' . admin_url( 'tools.php?page=weberlo' ) . '">' . __( 'Settings' ) . '</a>' ],
			$links );
	}

	public static function render_admin_page() {
		WEBERLO_Admin::enqueue_scripts_styles();

		list( $workspace_id, $workspace_id_error ) = self::check_workspace_id();
		list( $secret_key, $secret_key_error ) = self::check_secret_key();

		if ($workspace_id_error !== true) {
			self::check_embed_url($workspace_id, $secret_key);
		}

		include 'templates/admin-page.php';
	}

	protected static function check_workspace_id() {
		$workspace_id = weberlo_request_input('weberlo_workspace_id');
		$workspace_id_error = true;
	
		if (!$workspace_id) {
			$workspace_id = WEBERLO_Options::get_workspace_id() ?: null;
		}
	
		if (!is_null($workspace_id) && $workspace_id !== '') {
			if (WEBERLO_Options::is_workspace_id_valid($workspace_id)) {
				$workspace_id_error = false;
				WEBERLO_Options::set_workspace_id($workspace_id);
			}
		}
	
		return [$workspace_id, $workspace_id_error];
	}

	protected static function check_secret_key() {
		$secret_key_error = null;
		$secret_key       = weberlo_request_input( 'weberlo_api_key' );

		if ( ! $secret_key ) {
			$secret_key = WEBERLO_Options::get_secret_key() ? : null;
		}

		if ( $secret_key ) {
			$Weberlo_Api = new WEBERLO_API( $secret_key );
			if ( ! $Weberlo_Api->if_secret_key_valid( $secret_key ) ) {
				$secret_key_error = true;
			} else {
				$secret_key_error = false;
			}

			if ( ! empty( $secret_key ) ) {
				WEBERLO_Options::set_secret_key( $secret_key );
			}
		}

		return [ $secret_key, $secret_key_error ];
	}

	protected static function check_embed_url($workspace_id, $secret_key) {

		$Weberlo_Api = new WEBERLO_API( $secret_key );
							
		$embed_url = $Weberlo_Api->embed_url( $workspace_id, $secret_key );

		WEBERLO_Options::set_embed_url( $embed_url );
	}
}