<?php
/**
 * Contains action hooks and functions for user authentication.
 *
 * @class Ticktify_Profile
 * @package ticktify-event\classes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Profile')) :
    class Ticktify_Profile
    {
        public function __construct()
        {
            add_action('ticktify_profile_tab_content', [$this, 'ticktify_profile_tabs_content_callback']);

            add_action('ticktify_profile_tab_content_dashboard_endpoint', [$this, 'ticktify_profile_dashboard_content_callback']);
            add_action('ticktify_profile_tab_content_account_details_endpoint', [$this, 'ticktify_profile_account_details_content_callback']);
            add_action('ticktify_profile_tab_content_bookings_endpoint', [$this, 'ticktify_profile_bookings_content_callback']);

            add_action('admin_post_nopriv_ticktify_save_account_details', [$this, 'ticktify_save_account_details']);
            add_action('admin_post_ticktify_save_account_details', [$this, 'ticktify_save_account_details']);
        }

        /**
         * Create profile tab
         * 
         * @return tab item array
         */
        public static function ticktify_profile_tab()
        {
            $item['dashboard'] = esc_html__('Dashboard', "ticktify");
            $item['account_details'] = esc_html__('Account Details', "ticktify");
            $item['bookings'] = esc_html__('Bookings', "ticktify");
            return apply_filters('ticktify_profile_menu_items', $item);
        }

        /**
         * Profile tab content
         * 
         * Responsible for create action hook for tab content
         *
         */
        function ticktify_profile_tabs_content_callback()
        {
            $tabArray = $this->ticktify_profile_tab();
            $currentTab = (isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'dashboard');
            foreach ($tabArray as $key => $value) {
                if ($key == $currentTab) {
                    if (has_action('ticktify_profile_tab_content_' . $key . '_endpoint')) {
                        do_action('ticktify_profile_tab_content_' . $key . '_endpoint');
                    }
                }
            }
        }

        /**
         * Dashboard tab content
         * 
         * @return Dashboard content
         *
         */
        function ticktify_profile_dashboard_content_callback()
        {
            require_once(TICKTIFY_UI_FRONT_DIR . 'account/dashboard.php');
        }

        /**
         * Account Details tab content
         * 
         * @return Account details content
         *
         */
        function ticktify_profile_account_details_content_callback()
        {
            require_once(TICKTIFY_UI_FRONT_DIR . 'account/account_details.php');
        }

        /**
         * Booking tab content
         * 
         * @return Booking content
         *
         */
        function ticktify_profile_bookings_content_callback()
        {
            require_once(TICKTIFY_UI_FRONT_DIR . 'account/bookings.php');
        }

        /**
         * Responsible for save account details
         *  
         * @return void
         */
        public function ticktify_save_account_details()
        {
            if (!empty($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'account_details')) {
                $current_user = wp_get_current_user();
                update_user_meta(sanitize_text_field($current_user->ID), sanitize_key('first_name'), sanitize_text_field($_POST['account_firstname']));
                update_user_meta(sanitize_text_field($current_user->ID), sanitize_key('last_name'), sanitize_text_field($_POST['account_lastname']));
                update_user_meta(sanitize_text_field($current_user->ID), sanitize_key('phone'), sanitize_text_field($_POST['account_contact']));
                wp_update_user(array(
                    'ID' => sanitize_text_field($current_user->ID),
                    'display_name' => sanitize_text_field($_POST['account_username']),
                    'user_email' => sanitize_email($_POST['account_email']),
                ));

                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                wp_redirect(get_permalink($ticktify_settings['pages']['ticktify_profile']) . "?tab=account_details");
            }
        }
    } //End of class 
    new Ticktify_Profile();
endif;
