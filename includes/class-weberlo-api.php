<?php

defined( 'ABSPATH' ) || exit;

class WEBERLO_API
{
    private $api_key;
    private $url;

    public function __construct( $api_key )
    {
        $this->api_key = $api_key;
        
        $root_domain = getenv('ROOT_DOMAIN') ?: 'weberlo.com';
        $this->url = 'https://connect.' . $root_domain . '/event/transaction?workspace_id=' . WEBERLO_Options::get_workspace_id();
    }

    public function post( $body )
    {   
        $res = wp_remote_post( $this->url, [
            'headers'     => [
                'Content-Type' => 'application/json',
                'Authorization'      => 'Bearer ' . $this->api_key
            ],
            'body'  => wp_json_encode($body)
        ]);

        return $res;
    }

    public function if_secret_key_valid()
    {
        $res = $this->post([]);
        $res_code = wp_remote_retrieve_response_code( $res );

        if( $res_code !== 403 )
            return true;
        return false;
    }

    public function error( $errorData )
    {
        $error_url = 'https://connect.' . (getenv('ROOT_DOMAIN') ?: 'weberlo.com') . '/error?workspace_id=' . WEBERLO_Options::get_workspace_id();

        $response = wp_remote_post( $error_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ],
            'body' => wp_json_encode( $errorData )
        ]);
 
        return $response;
    }

    public function embed_url($workspace_id) {
        $site_url = site_url();
        $domain = parse_url($site_url, PHP_URL_HOST);
        $cname = null;

        if (!empty($this->api_key)) {
            $cname_url = 'https://connect.' . (getenv('ROOT_DOMAIN') ?: 'weberlo.com') . '/cname?workspace_id=' . $workspace_id;

            $response = wp_remote_get($cname_url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->api_key,
                ]
            ]);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                $data = json_decode(wp_remote_retrieve_body($response), true);

                foreach ($data['data'] as $domainData) {
                    if (strpos($domainData['domain_name'], str_replace('www.', '', $domain)) !== false && $domainData['status'] === 'active') {
                        $cname = $domainData['domain_name'];
                        break;
                    }
                }
            }
        }

        $embedUrl = $cname ? "https://{$cname}/weberlo.min.js?ws={$workspace_id}" : "https://api." . (getenv('ROOT_DOMAIN') ?: 'weberlo.com') . "/weberlo.min.js?ws={$workspace_id}";
        
        return $embedUrl;
    }
}
