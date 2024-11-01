<?php

defined( 'ABSPATH' ) || exit;

class WEBERLO_Options {

	public static function is_woocommerce_active() {
		if ( class_exists( 'woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_weberlo_active() {
		$workspace_id = self::get_workspace_id();
		if ( $workspace_id && self::is_workspace_id_valid( $workspace_id ) ) {
			return true;
		}

		return false;
	}

	public static function set_embed_url( $embed_url ) {
		update_option( 'weberlo_embed_url', $embed_url );
	}

	public static function get_embed_url() {
		return get_option( 'weberlo_embed_url' );
	}

	public static function set_workspace_id( $workspace_id ) {
		update_option( 'weberlo_workspace_id', $workspace_id );
	}

	public static function get_workspace_id() {
		return get_option( 'weberlo_workspace_id' );
	}

	public static function is_workspace_id_valid( $workspace_id ) {

		$root_domain = getenv('ROOT_DOMAIN') ?: 'weberlo.com';
		return true;
	}

	public static function set_enable_for_wc_status( $status ) {
		update_option( 'weberlo_for_wc', $status );
	}

	public static function get_enable_for_wc_status() {
		return get_option( 'weberlo_for_wc' );
	}

	public static function set_secret_key( $secret_key ) {
		update_option( 'weberlo_api_key', $secret_key );
	}

	public static function get_secret_key() {
		return get_option( 'weberlo_api_key' );
	}

	public static function get_visitor_id() {
		return isset( $_COOKIE['__wbr_uid'] ) ? sanitize_text_field( $_COOKIE['__wbr_uid'] ) : false;
	}
}
