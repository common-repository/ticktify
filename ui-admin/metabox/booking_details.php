<?php
/**
 * Metabox for booking details.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$billingDetails = get_post_meta(sanitize_text_field($post->ID), sanitize_key('_billing_details'), true);
$transactionDetails = Ticktify_Transaction::ticktify_get_transactions(sanitize_text_field($post->ID));
?>
<h1><?php the_title(); ?></h1>
<table class="form-table">
    <thead>
        <tr>
            <!-- <th>General Details</th> -->
            <th><?php esc_html_e("Booking Details", "ticktify"); ?></th>
            <th><?php esc_html_e("Billing Details", "ticktify"); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <!-- <td></td> -->
            <td>
                <p><strong><?php esc_html_e("Booking Id: #", "ticktify"); ?></strong><?php echo esc_html($post->ID); ?></p>
                <p><strong><?php esc_html_e("Transaction Id:", "ticktify"); ?></strong><?php echo esc_html((!empty($transactionDetails->transaction_id)) ? $transactionDetails->transaction_id : ''); ?></p>
                <p><strong><?php esc_html_e("Payment Method:", "ticktify"); ?></strong><?php echo esc_html((!empty($transactionDetails->payment_method)) ? $transactionDetails->payment_method : ''); ?></p>
                <p><strong><?php esc_html_e("Payment Status:", "ticktify"); ?></strong><?php echo esc_html((!empty($transactionDetails->payment_status)) ? $transactionDetails->payment_status : ''); ?></p>
                <p><strong><?php esc_html_e("Paid Amount:", "ticktify"); ?></strong><?php echo esc_html((!empty($transactionDetails->paid_amount)) ? $transactionDetails->paid_amount : '0.00'); ?></p>
            </td>
            <td>
                <p><?php echo esc_html($billingDetails['first_name'] . " " . $billingDetails['last_name']); ?></p>
                <p><?php echo esc_html($billingDetails['phone']) ?></p>
                <p><?php echo esc_html($billingDetails['user_email']) ?></p>
                <p><?php echo esc_html($billingDetails['address']) ?></p>
                <p><?php echo esc_html($billingDetails['city']) ?></p>
                <p><?php echo esc_html($billingDetails['state']) ?></p>
                <p><?php echo esc_html($billingDetails['zip_code']) ?></p>
            </td>
        </tr>
    </tbody>
</table>