<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for download payment.
 *
 * @class Ticktify_Download_Payments
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Download_Payments')) :
    class Ticktify_Download_Payments
    {
        /**
         * Constructor for the Download Payments. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('admin_menu', [$this, 'ticktify_admin_menu_callback']);
        }
        /**
         * admin Submenu
         *  
         * Responsible for payment menu
         */
        public function ticktify_admin_menu_callback()
        {
            //this is the main item for the menu
            add_submenu_page(
                'edit.php?post_type=' . TICKTIFY_EVENT_POST_TYPE, //$parent_slug
                __('Event', "ticktify"),  //$page_title
                __('Payments', "ticktify"),        //$menu_title
                'manage_options',           //$capability
                'download_payments', //$menu_slug
                [$this, 'ticktify_download_payment_callback'], //$function
                3
            );
        }
        /**
         * Download all transaction
         *  
         * Responsible for transaction table
         */
        function ticktify_download_payment_callback()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.', "ticktify"));
            }
            include_once TICKTIFY_UI_ADMIN_DIR . 'metabox/download_payments.php';
        }
        /**
         * Get transacton
         *  
         * @return Ticktify_Transaction data
         */
        public static function ticktify_get_all_transactions()
        {
            global $wpdb;
            $ticktify_transactions = $wpdb->prefix . 'ticktify_transactions';
            $result_transactions = $wpdb->get_results("SELECT * FROM $ticktify_transactions ORDER BY id DESC  ");
            return $result_transactions;
        }
    }
    new Ticktify_Download_Payments();
// EOF
endif;
