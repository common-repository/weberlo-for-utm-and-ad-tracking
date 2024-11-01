<?php

defined( 'ABSPATH' ) || exit;

class WEBERLO_Public 
{
    public static function init()
    {
        add_action( 'wp_head', [ __CLASS__, 'add_script_code' ]);
    }

    public static function add_script_code()
    {
        $embed_url = WEBERLO_Options::get_embed_url();
        include 'embed-code.php';
    }
}