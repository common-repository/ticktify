<?php

/**
 * Contains action hooks and functions for user authentication.
 *
 * @class Ticktify_Checkout
 * @package ticktify-event\classes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Checkout')) :
    class Ticktify_Checkout
    {
        public function __construct()
        {
            add_action('wp_ajax_nopriv_ticktify_booked_free_event', [$this, 'ticktify_booked_free_event_callback']);
            add_action('wp_ajax_ticktify_booked_free_event', [$this, 'ticktify_booked_free_event_callback']);
        }
        public function ticktify_checkout_query_callback()
        {
            global $wpdb;
            $ticktify_booking = $wpdb->prefix . 'ticktify_bookings';
            $result_check = $wpdb->get_results(
                "SELECT * FROM $ticktify_booking"
            );
            return $result_check;
        }

        /**
         * update user billing meta
         * 
         * @return update status 
         */
        public static function ticktify_insert_billing_details($customerMetaArray)
        {
            foreach ($customerMetaArray as $key => $value) {
                update_user_meta(sanitize_text_field(get_current_user_id()), sanitize_key($key), sanitize_text_field($value));
            }
            return true;
        }

        /**
         * save booking data
         *
         * @return Ticktify_Booking id  
         */
        public static function ticktify_insert_booking_details($bookingArray, $customerMetaArray)
        {
            if ((isset($bookingArray['payment_status']) && $bookingArray['payment_status'] == 'succeeded') || $bookingArray['event_type'] == 'free') {

                $cartData = Ticktify_Cart::ticktify_cart_query();
                foreach ($cartData as $key => $cart_val) {
                    $subArr = array();
                    $subArr["status"] = 'confirmed';
                    foreach ($cart_val as $skey => $val) {
                        $subArr[$skey] = sanitize_text_field($val);
                    }
                    $cartDataNew[] = $subArr;
                    $total_seats = 0;
                    $booked_seats = get_post_meta(sanitize_text_field($cart_val->event_id), sanitize_key("_total_seats_booked"), true);
                    if (!empty($booked_seats)) {
                        $total_seats = intval($booked_seats) + intval($cart_val->quantity);
                    } else {
                        $total_seats = $cart_val->quantity;
                    }
                    update_post_meta(sanitize_text_field($cart_val->event_id), sanitize_key("_total_seats_booked"), sanitize_text_field($total_seats));
                }

                $post_id = wp_insert_post(array(
                    'post_type' => TICKTIFY_BOOKING_POST_TYPE,
                    'post_title' => '',
                    'post_content' => "",
                    'post_status' => 'publish',
                ));

                if ($post_id) {
                    //update query for post title  Boking #$post_id

                    $updateBookingArray = array(
                        'ID'           => sanitize_text_field($post_id),
                        'post_title'   => sanitize_text_field(__('Booking #', "ticktify") . $post_id),
                    );

                    wp_update_post($updateBookingArray);
                    // insert post meta
                    add_post_meta(sanitize_text_field($post_id), sanitize_key('_events'), $cartDataNew);
                    add_post_meta(sanitize_text_field($post_id), sanitize_key('_billing_details'), $customerMetaArray);
                    add_post_meta(sanitize_text_field($post_id), sanitize_key('_customer_id'), sanitize_text_field(get_current_user_id()));
                    //pending,confirmed,cancelled,rejected
                    add_post_meta(sanitize_text_field($post_id), sanitize_key('_booking_status'), sanitize_text_field('confirmed'));
                    $booking_id = $post_id;
                    do_action('ticktify_after_booking_success', $updateBookingArray, $cartDataNew);
                } else {
                    $booking_id = '';
                }
            }
            return $booking_id;
        }

        /**
         * empty customer cart after checkout
         *  
         * @return Ticktify_Cart empty status
         */
        public static function ticktify_empty_cart($customerId)
        {
            global $wpdb;
            $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
            $cartEmpty = $wpdb->delete(
                $ticktify_cart,
                array(
                    'customer_id' => sanitize_text_field($customerId)
                )
            );
            return $cartEmpty;
        }

        /**
         * Get transacton
         *  
         * @return Ticktify_Transaction data
         */
        public static function ticktify_get_transactions($transactions_id)
        {
            global $wpdb;
            $transactions_id = sanitize_text_field(base64_decode($transactions_id));
            $ticktify_transactions = $wpdb->prefix . 'ticktify_transactions';
            $result_check = $wpdb->get_row("SELECT * FROM $ticktify_transactions WHERE id = $transactions_id  ");
            return $result_check;
        }

        /**
         * Booked free event
         *  
         * @return Ticktify_Transaction data
         */
        function ticktify_booked_free_event_callback()
        {
            if (isset($_POST['action']) && !empty($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'billing_info')) {
                $bookingArray = array('event_type' => 'free');
                $customerMetaArray = array(
                    'first_name' => sanitize_text_field($_POST['first_name']),
                    'last_name' => sanitize_text_field($_POST['last_name']),
                    'address' => sanitize_text_field($_POST['address']),
                    'city' => sanitize_text_field($_POST['city']),
                    'state' => sanitize_text_field($_POST['state']),
                    'zip_code' => sanitize_text_field($_POST['zip_code']),
                    'phone' => sanitize_text_field($_POST['phone']),
                    'user_email' => sanitize_email($_POST['user_email']),
                );
                Ticktify_Checkout::ticktify_insert_billing_details($customerMetaArray);
                $bookingResults = Ticktify_Checkout::ticktify_insert_booking_details($bookingArray, $customerMetaArray);
                if ($bookingResults) {
                    Ticktify_Checkout::ticktify_empty_cart(get_current_user_id());
                    $redirectUrl = site_url() . '/ticktify-thank-you?bid=' . base64_encode(esc_attr($bookingResults));
                    echo json_encode(array('status' => 'success', 'redirectUrl' => esc_url($redirectUrl)));
                } else {
                    echo json_encode(array('status' => 'error'));
                }
                wp_die();
            }
        }
    }
    new Ticktify_Checkout();
endif;
