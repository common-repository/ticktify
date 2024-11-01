<?php

defined('ABSPATH') or die("you do not have access to this page!");

/**
 * Contains action hooks and functions for TICKTIFY Event post type.
 *
 * @class Ticktify_Email
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Email')) :
    class Ticktify_Email
    {
        /**
         * Ticktify Email contructor funcation
         */
        public function __construct()
        {
            add_action('ticktify_after_booking_event_cancel', [$this, 'ticktify_event_cancellation_email'],10, 2);
            add_action('ticktify_after_booking_success', [$this, 'ticktify_event_booking_confirmation'], 100, 2);
            add_action('ticktify_after_registeration_successfully', [$this, 'ticktify_after_registeration_success']);
        }

        /**
         * responsible for event cancellation email
         *  
         * @return void
         */
        function ticktify_event_cancellation_email($bookingId, $BookingData)
        {
            #Send email to the admin for the bookings
            global $current_user;
            list($admin_email, $admin_name, $site_title) = ticktify_get_admin_data();
            $bookingDetails = ticktify_get_booking_event($BookingData);

            $current_user_email = $current_user->user_email;
            $ticktify_email_notifications = get_option(sanitize_key('ticktify_email_templates'));
            $ticktify_Cancellation_booking_to_admin = $ticktify_email_notifications['cancellation_to_admin'];
            $ticktify_Cancellation_booking_to_user = $ticktify_email_notifications['cancellation_to_user'];

            if (!empty($ticktify_Cancellation_booking_to_user)) {
                $to = esc_attr($ticktify_Cancellation_booking_to_user['to']);
                $to = str_replace("[user_email]", $admin_email, $to);
                $subject = esc_attr($ticktify_Cancellation_booking_to_user['subject']);
                $message = wp_kses_post($ticktify_Cancellation_booking_to_user['message']);

                $replace_array = array(
                    "first_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('first_name'), true)),
                    "last_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('last_name'), true)),
                    "user_email" => esc_attr($current_user->user_email),
                    "site_title" => esc_attr($site_title),
                    "admin_name" => esc_attr($admin_name),
                    "booking_title" => esc_attr(get_the_title(sanitize_text_field($bookingId))),
                    "booking_url_for_user" => '<a href="' . esc_url(site_url() . '/ticktify-profile/?tab=bookings&booking_id=' . esc_attr($bookingId) ). '">View booking</a>',
                    "booking_details" => wp_kses_post($bookingDetails),
                );
                foreach ($replace_array as $key => $value) {
                    $message = str_replace("[$key]", $value, $message);
                }

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }

            if (!empty($ticktify_Cancellation_booking_to_admin)) {
                $to = esc_attr($ticktify_Cancellation_booking_to_admin['to']);
                $to = str_replace("[admin_email]", $admin_email, $to);
                $subject = esc_attr($ticktify_Cancellation_booking_to_admin['subject']);
                $message = wp_kses_post($ticktify_Cancellation_booking_to_admin['message']);

                $replace_array = array(
                    "first_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('first_name'), true)),
                    "last_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('last_name'), true)),
                    "user_email" => esc_attr($current_user->user_email),
                    "site_title" => esc_attr($site_title),
                    "booking_title" => esc_attr(get_the_title(sanitize_text_field($bookingId))),
                    "admin_name" => esc_attr($admin_name),
                    "booking_url_for_admin" => '<a href="' .esc_url( site_url() . '/wp-admin/post.php?post=' . esc_attr($bookingId) ). '&action=edit">View booking</a>',
                    "booking_details" => wp_kses_post($bookingDetails),
                );
                foreach ($replace_array as $key => $value) {
                    $message = str_replace("[$key]", $value, $message);
                }

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }
        }

        /**
         * responsible for event booking confirmation
         *  
         * @return void
         */
        function ticktify_event_booking_confirmation($booking_data, $cartDataNew)
        {
            global $current_user;
            list($admin_email, $admin_name, $site_title) = ticktify_get_admin_data();
            $bookingDetails = ticktify_get_booking_event($cartDataNew);

            $ticktify_email_notifications = get_option(sanitize_key('ticktify_email_templates'));
            $ticktify_notify_booking_to_admin = $ticktify_email_notifications['booking_to_admin'];

            if ($ticktify_notify_booking_to_admin) {
                $to = esc_attr($ticktify_notify_booking_to_admin['to']);
                $to = str_replace("[admin_email]", $admin_email, $to);
                $subject = esc_attr($ticktify_notify_booking_to_admin['subject']);
                $message = wp_kses_post($ticktify_notify_booking_to_admin['message']);

                $replace_array = array(
                    "first_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('first_name'), true)),
                    "last_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('last_name'), true)),
                    "user_email" => esc_attr($current_user->user_email),
                    "site_title" => esc_attr($site_title),
                    "booking_title" => esc_attr($booking_data['post_title']),
                    "admin_name" => esc_attr($admin_name),
                    "booking_url_for_admin" => '<a href="' .esc_url( site_url() . '/wp-admin/post.php?post=' . esc_attr($booking_data['ID']) ) . '&action=edit">View booking</a>',
                    "booking_details" => wp_kses_post($bookingDetails),
                );
                foreach ($replace_array as $key => $value) {
                    $message = str_replace("[$key]", $value, $message);
                }

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }
            #Send email to the user for the bookings
            $ticktify_notify_booking_to_user = $ticktify_email_notifications['booking_to_user'];
            if ($ticktify_notify_booking_to_user) {
                $to = esc_attr($ticktify_notify_booking_to_user['to']);
                $to = str_replace("[user_email]", $current_user->user_email, $to);
                $subject = esc_attr($ticktify_notify_booking_to_user['subject']);
                $message = wp_kses_post($ticktify_notify_booking_to_user['message']);

                $replace_array = array(
                    "first_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('first_name'), true)),
                    "last_name" => esc_attr(get_user_meta(sanitize_text_field($current_user->ID), sanitize_key('last_name'), true)),
                    "user_email" => esc_attr($current_user->user_email),
                    "site_title" => esc_attr($site_title),
                    "booking_title" => esc_attr($booking_data['post_title']),
                    "booking_url_for_user" => '<a href="' . esc_url( site_url() . '/ticktify-profile/?tab=bookings&booking_id=' . esc_attr($booking_data['ID']) ). '">View booking</a>',
                    "booking_details" => wp_kses_post($bookingDetails),
                );
                foreach ($replace_array as $key => $value) {
                    $message = str_replace("[$key]", $value, $message);
                }

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }
        }

        /**
         * responsible for after registeration success
         *  
         * @return void
         */
        function ticktify_after_registeration_success($current_user_data)
        {
            // global $current_user;   
            // $current_user_email = $current_user->user_email;
            $ticktify_registration_notification_setting = get_option(sanitize_key('ticktify_email_templates'));
            $ticktify_notify_new_register = $ticktify_registration_notification_setting['new_user_to_admin'];
            if ($ticktify_notify_new_register) {
                list($admin_email, $admin_name, $site_title) = ticktify_get_admin_data();

                $to = esc_attr($ticktify_notify_new_register['to']);
                $to = str_replace("[admin_email]", $admin_email, $to);

                $subject = esc_attr($ticktify_notify_new_register['subject']);
                $message = wp_kses_post($ticktify_notify_new_register['message']);

                $replace_array = array(
                    "first_name" => esc_attr($current_user_data['first_name']),
                    "last_name" => esc_attr($current_user_data['last_name']),
                    "user_email" => esc_attr($current_user_data['email']),
                    "admin_name" => esc_attr($admin_name),
                    "site_title" => esc_attr($site_title),
                );
                foreach ($replace_array as $key => $value) {
                    $message = str_replace("[$key]", $value, $message);
                }

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($to, $subject, $message, $headers);
            }
        }
    }
    new Ticktify_Email();
// EOF
endif;
