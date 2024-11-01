<?php

/**
 * Contains action hooks and functions for booking transaction.
 *
 * @class Ticktify_Transaction
 * @package ticktify-event\classes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Transaction')) :
    class Ticktify_Transaction
    {
        /**
         * Ticktify Transaction contractor
         */
        public function __construct()
        {
        }

        /**
         * insert booking trnasaction
         *  
         * @return integer transactions_id
         */
        public static function ticktify_insert_transaction($transactionArray)
        {
            global $wpdb;
            $ticktify_transactions = $wpdb->prefix . 'ticktify_transactions';
            $insert = $wpdb->insert($ticktify_transactions, $transactionArray);
            $transactions_id = $wpdb->insert_id;
            if ($insert) {
                $status = "success";
            } else {
                $status = "error";
            }
            do_action('ticktify_after_payment_success', $status);
            return $transactions_id;
        }
        /**
         * Get transacton by booking id
         *  
         * @return Ticktify_Transaction data
         */
        public static function ticktify_get_transactions($booking_id)
        {
            global $wpdb;
            $ticktify_transactions = $wpdb->prefix . 'ticktify_transactions';
            $result = $wpdb->get_row("SELECT * FROM $ticktify_transactions WHERE booking_id = '".sanitize_text_field($booking_id)."'  ");
            return $result;
        }
    }
    new Ticktify_Transaction();
endif;
