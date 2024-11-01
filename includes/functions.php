<?php
defined( 'ABSPATH' ) || exit;

function weberlo_request_input( $key = '' ) {

	if ( isset( $_POST[ $key ] ) ) {
		return sanitize_text_field( $_POST[ $key ] );
	} elseif ( isset( $_GET[ $key ] ) ) {
		return sanitize_text_field( $_GET[ $key ] );
	}

	return null;
}