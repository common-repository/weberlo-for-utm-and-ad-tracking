<?php

defined( 'ABSPATH' ) || exit;

class WEBERLO_Webhooks
{
    public static function init()
    {
        add_action( 'woocommerce_thankyou', [ __CLASS__, 'on_order_processed' ], 99, 1 );
        add_action( 'woocommerce_order_refunded', [ __CLASS__, 'refund'], 10, 2 );
        add_action( 'woocommerce_admin_order_data_after_billing_address',  [ __CLASS__, 'display_wbr_id' ], 10, 1 );
        add_action( 'woocommerce_new_order', [ __CLASS__, 'capture_wbr_id' ], 10, 2);
    }

    public static function capture_wbr_id( $order_id, $order ) {

        if ( ! empty( $_COOKIE['__wbr_uid'] ) ) {
            update_post_meta( $order_id, '_wbr_id', sanitize_text_field( $_COOKIE['__wbr_uid'] ) );
        }
    } 

    public static function display_wbr_id( $order )
    {
        // compatibility with WC +3
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        echo '<p><strong>'.__('Weberlo ID', 'woocommerce').':</strong> ' . get_post_meta( $order_id, '_wbr_id', true ) . '</p>';
    }

    public static function on_order_processed( $order_id )
    {
        $API = new WEBERLO_API( WEBERLO_Options::get_secret_key() );

        $data = [];

        try {
            if( ! $order_id )
                return;

            if( get_post_meta( $order_id, 'weberlo_order_processed', true ) )
                return;
            
            $order = wc_get_order( $order_id );

            $order_date_created = $order->get_date_created();
            $transaction_time_ms = $order_date_created ? $order_date_created->getTimestamp() * 1000 : 0;

            $site_url = get_site_url(); // Retrieves the site URL
            $domain_name = parse_url($site_url, PHP_URL_HOST); // Extracts the domain name

            $order_items = $order->get_items();
            $transaction_description = array();
            foreach ( $order_items as $item_id => $item ) {
                $transaction_description[] = $item->get_name();
            }
            $transaction_description = implode(', ', $transaction_description);

            $data = [
                'time'                      => $transaction_time_ms,
                'transaction_description'   => $transaction_description,
                'transaction_id'            => 'woocommerce_' . $order_id,
                'transaction_amount'        => round($order->get_total() * 100),
                'transaction_currency'      => $order->get_currency(),
                'first_name'                => $order->get_billing_first_name(),
                'last_name'                 => $order->get_billing_last_name(),
                'email'                     => $order->get_billing_email(),
                'phone'                     => $order->get_billing_phone(),
                'country'                   => $order->get_billing_country(),
                'ip_address'                => $order->get_customer_ip_address(),
                'platform'                  => 'woocommerce',
                'platform_id'               => $domain_name,
                'session_id'                => get_post_meta( $order_id, '_wbr_id', true )
            ];

            $parent_id = self::get_parent_order_id($order_id);
            $response = null;
            if( ! $parent_id ) {
                $data['transaction_type'] = 'order-success';
                $response = $API->post( $data );
            }  
            else {
                $data['transaction_type'] = 'order-recurring';
                $data['parent_id'] = 'woocommerce_' . $parent_id;
                $response = $API->post( $data );
            }

            if ( is_wp_error( $response ) || $response['response']['code'] >= 400 ) {
                throw new Exception('API request failed with response: ' . wp_remote_retrieve_body( $response ));
            }

            update_post_meta( $order_id, 'weberlo_order_processed', 1 );

        } catch (Exception $e) {
            error_log('Error processing order: ' . $e->getMessage());

            $errorData = [
                'source' => 'woocommerce',
                'message' => $e->getMessage(),
                'data' => wp_json_encode( $data )
            ];

            $API->error($errorData);
        }
    }
    
    public static function refund( $order_id, $refund_id )
    {
        try {
            if( ! $order_id )
                return;
    
            $API = new WEBERLO_API( WEBERLO_Options::get_secret_key() );
            
            $order = wc_get_order( $order_id );
    
            // Getting the refund date (assuming the refund date is current date)
            $refund_date = current_time( 'timestamp' );

            // Get domain name from site URL
            $site_url = get_site_url(); // Retrieves the site URL
            $domain_name = parse_url($site_url, PHP_URL_HOST); // Extracts the domain name
    
            // Get the refunded items description
            $refund = new WC_Order_Refund( $refund_id );
            $items = $refund->get_items();
            $refund_items_description = array();
            foreach ( $items as $item_id => $item ) {
                $refund_items_description[] = $item->get_name();
            }
            $refund_description = implode(', ', $refund_items_description);
    
            // Prepare data for API
            $data = [
                'time'                      => $refund_date * 1000,
                'transaction_description'   => $refund_description,
                'transaction_type'          => 'order-refund',
                'transaction_id'            => 'woocommerce_' . $refund_id,
                'parent_id'                 => 'woocommerce_' . $order_id,
                'transaction_amount'        => -round($order->get_total_refunded() * 100),
                'transaction_currency'      => $order->get_currency(),
                'first_name'                => $order->get_billing_first_name(),
                'last_name'                 => $order->get_billing_last_name(),
                'email'                     => $order->get_billing_email(),
                'country'                   => $order->get_billing_country(),
                'ip_address'                => $order->get_customer_ip_address(),
                'platform'                  => 'woocommerce',
                'platform_id'               => $domain_name,
                'session_id'                => get_post_meta( $order_id, '_wbr_id', true )
            ];
    
            $response = $API->post( $data );
    
            if ( is_wp_error( $response ) || $response['response']['code'] >= 400 ) {
                throw new Exception('API request failed with response: ' . wp_remote_retrieve_body( $response ));
            }
    
        } catch (Exception $e) {
            error_log('Error processing refund: ' . $e->getMessage());
    
            $errorData = [
                'source' => 'woocommerce',
                'message' => $e->getMessage(),
                'data' => wp_json_encode( $data )
            ];
    
            $API->error($errorData);
        }
    }

    private static function get_parent_order_id( $order_id )
    {
        //Checks for WC Subscription related order
        if( function_exists('wcs_get_subscriptions_for_renewal_order') ) {
            if( wcs_order_contains_renewal( $order_id ) ) {
                $subscriptions = wcs_get_subscriptions_for_renewal_order($order_id);
                $subscription_obj = array_values($subscriptions)[0];
                return $subscription_obj->get_parent_id();
            }
        }
        return false;

    }

}