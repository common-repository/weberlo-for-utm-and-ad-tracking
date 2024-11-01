<?php

/*
Plugin Name: Weberlo for UTM and Ad Tracking
Plugin URI: https://www.weberlo.com/
Description: Track, Analyze & Optimize all your traffic sources (paid & organic)
Version: 0.0.18
Author: Weberlo
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 6.6.2
Requires at least: 5.5
*/

defined( 'ABSPATH' ) || exit;

define( 'WEBERLO_PLUGIN_NAME', 'cci-instructor');
define( 'WEBERLO_PLUGIN_VERSION', '0.0.18');
define( 'WEBERLO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define( 'WEBERLO_PLUGIN_URL', plugin_dir_url(__FILE__));
define( 'WEBERLO_PLUGIN_BASENAME', plugin_basename(__FILE__));

include WEBERLO_PLUGIN_PATH.'includes/autoload.php';
include WEBERLO_PLUGIN_PATH.'includes/functions.php';

WEBERLO_Admin::init();

if( WEBERLO_Options::is_weberlo_active() ) {
    WEBERLO_Public::init();
    WEBERLO_Webhooks::init();
}
