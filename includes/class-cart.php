<?php

/**
 * Contains action hooks and functions for user authentication.
 *
 * @class Ticktify_Cart
 * @package ticktify-event\classes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Cart')) :
    class Ticktify_Cart
    {
        public function __construct()
        {
            add_action('wp_ajax_nopriv_ticktify_remove_cart', [$this,  'ticktify_remove_cart']);
            add_action('wp_ajax_ticktify_remove_cart', [$this, 'ticktify_remove_cart']);
            add_action('admin_post_nopriv_ticktify_update_Cart', [$this, 'ticktify_update_Cart']);
            add_action('admin_post_ticktify_update_Cart', [$this, 'ticktify_update_Cart']);
            add_action('admin_post_nopriv_ticktify_action_checkout_callback', [$this, 'ticktify_action_checkout_callback']);
            add_action('admin_post_ticktify_action_checkout_callback', [$this, 'ticktify_action_checkout_callback']);
            add_action('admin_post_nopriv_ticktify_add_to_cart', [$this,  'ticktify_add_to_cart']);
            add_action('admin_post_ticktify_add_to_cart', [$this, 'ticktify_add_to_cart']);
            add_action('wp_ajax_nopriv_attendees_model', [$this, 'ticktify_action_attendees_modal_callback']);
            add_action('wp_ajax_attendees_model', [$this, 'ticktify_action_attendees_modal_callback']);
            add_action('wp_ajax_nopriv_attendees_post', [$this, 'ticktify_action_attendees_post_callback']);
            add_action('wp_ajax_attendees_post', [$this, 'ticktify_action_attendees_post_callback']);
        }

        /**
         * Add to cart callback
         * 
         * Reponsible for add to cart feature on an event
         *
         */
        public function ticktify_add_to_cart()
        {
            global $wpdb;
            $current_user = sanitize_text_field(get_current_user_id());
            $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
            $id = sanitize_text_field($_POST['customer_id']);
            $event_id = sanitize_text_field($_POST['event_id']);
            $event_title = sanitize_text_field($_POST['event_title']);
            $price = sanitize_text_field($_POST['price']);
            $quantity = sanitize_text_field($_POST['quantity']);
            $subTotal = floatval($price) * intval($quantity);
            $check_cart = $wpdb->get_row("SELECT * FROM $ticktify_cart where customer_id = $current_user AND event_id = $event_id");
            if ($check_cart) {
                $quantity =  intval($check_cart->quantity) + intval($quantity);
                $subTotal = sanitize_text_field(floatval($price) * intval($quantity));
                $wpdb->query($wpdb->prepare("UPDATE $ticktify_cart SET quantity = $quantity, subtotal = $subTotal, attendees = '' WHERE id = $check_cart->id"));
                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_cart']));
            } else {
                $sql = $wpdb->prepare("INSERT INTO " . $ticktify_cart . " (customer_id, event_id, event_title, price, quantity, subtotal ) VALUES ( %d, %d, %s, %f, %d ,%f )",  $id, $event_id, $event_title, $price, $quantity, $subTotal);
                $wpdb->query($sql);
                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_cart']));
            }
        }

        /**
         * cart result query
         * 
         * Reponsible for cart feature
         *
         */
        public static function ticktify_cart_query()
        {
            global $wpdb;
            $current_user = sanitize_text_field(get_current_user_id());
            $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
            $cart_result = $wpdb->get_results(
                "SELECT * FROM $ticktify_cart where customer_id = $current_user "
            );
            $date = current_time('Y-m-d H:i');
            foreach ($cart_result as $cartResult_values) {
                $event_time = get_post_meta(sanitize_text_field($cartResult_values->event_id), sanitize_key('_custom_time_meta_key'), true);
                $event_date = get_post_meta(sanitize_text_field($cartResult_values->event_id), sanitize_key('_custom_date_meta_key'), true);

                $total_seats = get_post_meta(sanitize_text_field($cartResult_values->event_id), sanitize_key('event_seats_numbers'), true);
                $total_booked_seats = get_post_meta(sanitize_text_field($cartResult_values->event_id), sanitize_key('_total_seats_booked'), true);
                if (empty($total_booked_seats)) {
                    $total_booked_seats = 0;
                }
                $remaining_seats = ($total_seats - $total_booked_seats);
                $quantity = $cartResult_values->quantity;

                $newDate = date("Y-m-d", strtotime($event_date[0])) . " " . date("H:i", strtotime($event_time[0]));

                if ($date >= $newDate || $quantity > $remaining_seats) {
                    $delete_id = $cartResult_values->id;
                    $wpdb->delete($ticktify_cart, array('id' => $delete_id));
                }
            }
            $cart_return = $wpdb->get_results(
                "SELECT * FROM $ticktify_cart where customer_id = $current_user "
            );
            return $cart_return;
        }
        /**
         * cart remove callback
         * 
         * Reponsible for cart feature
         *
         */
        public function ticktify_remove_cart()
        {
            if (isset($_POST['action']) && !empty($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'remove_item')) {
                global $wpdb;
                $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
                $wpdb->delete(
                    $ticktify_cart,
                    array(
                        'id' => sanitize_text_field($_POST['remove_item'])
                    )
                );
                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_cart']));
            }
        }
        /**
         * cart update callback
         * 
         * Reponsible for cart feature
         *
         */
        public function ticktify_update_Cart()
        {
            if (!empty($_POST['update_cart_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['update_cart_wpnonce']), 'update_cart')) {
                global $wpdb;
                $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
                for ($i = 0; $i < count($_REQUEST['customer_id']); $i++) {
                    $c_uid = sanitize_text_field($_REQUEST['customer_id'][$i]);
                    $c_quantity = sanitize_text_field($_REQUEST['quantity'][$i]);

                    $wpdb->query($wpdb->prepare("UPDATE $ticktify_cart SET quantity = $c_quantity, attendees = '' WHERE id = $c_uid"));
                }
                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_cart']));
            }
        }

        /**
         * checkout callback
         * 
         * Reponsible for checkout feature
         *
         */
        public function ticktify_action_checkout_callback()
        {
            global $wpdb;
            $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
            $current_user = sanitize_text_field(get_current_user_id());
            $cart_result = $wpdb->get_results(
                "SELECT * FROM $ticktify_cart where customer_id = $current_user AND attendees = '' "
            );
            $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
            if (empty($cart_result)) {
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_checkout']));
            } else {
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_cart']) . "?error=1");
            }
        }

        /**
         * attendees callback
         * 
         * Reponsible for attendees form model
         *
         */
        function ticktify_action_attendees_modal_callback()
        {
            if (isset($_POST['action'])) {
                $cartID = sanitize_text_field($_POST['cartID']);
                //$quantity = sanitize_text_field($_POST['quantity']);
                global $wpdb, $appendHtml;
                $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
                $cart_result = $wpdb->get_row(
                    "SELECT * FROM $ticktify_cart where id=$cartID"
                );
                $attendeesArr = json_decode($cart_result->attendees);
                $appendHtml .= '<input type="hidden" name="cart_id" value="' . esc_attr($cartID) . '">';
                for ($i = 0; $i < $cart_result->quantity; $i++) {

                    $attendee = isset($attendeesArr[$i]) ? $attendeesArr[$i] : '';
                    $name = isset($attendee->name) ? esc_attr($attendee->name) : '';
                     $age = isset($attendee->age) ? esc_attr($attendee->age) : '';
                    $appendHtml .= '<tr>
                    <td><input type="text" name="attendees_name[]"  value="' . esc_attr($name) . '" required></td>
                    <td><input type="number" name="attendees_age[]" min="1" max="99" value="' . esc_attr($age) . '" required></td>
                </tr>';
                }
                echo json_encode(array('status' => 'success', 'attendees_tr' => $appendHtml));
                wp_die();
            }
        }

        /**
         * attendees callback
         * 
         * Reponsible for post attendees form data
         *
         */
        function ticktify_action_attendees_post_callback()
        {
            if (isset($_POST['action']) && !empty($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'add_attendees')) {
                global $wpdb;
                $ticktify_cart = $wpdb->prefix . 'ticktify_cart';
                $cart_id = sanitize_text_field($_POST['cart_id']);
                $attendeesArr = array();
                for ($i = 0; $i < count($_POST['attendees_name']); $i++) {
                    $attendeesArr[$i]['name'] = sanitize_text_field($_POST['attendees_name'][$i]);
                    $attendeesArr[$i]['age'] = sanitize_text_field($_POST['attendees_age'][$i]);
                }
                $attendeesStr = json_encode($attendeesArr);
                $update = $wpdb->query($wpdb->prepare("UPDATE $ticktify_cart SET attendees='" . $attendeesStr . "' WHERE id=$cart_id"));
                if ($update) {
                    echo json_encode(array('status' => 'success', 'msg' => __('Attendees Added Successfully', "ticktify")));
                } else {
                    echo json_encode(array('status' => 'error', 'msg' => __('Failed', "ticktify")));
                }
                wp_die();
            }
        }
    }
    new Ticktify_Cart();
endif;
