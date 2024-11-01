<?php

defined( 'ABSPATH' ) || exit;

class WEBERLO_API
{
    private $api_key;

    public function __construct( $api_key )
    {
        $this->api_key = $api_key;
    }
}
