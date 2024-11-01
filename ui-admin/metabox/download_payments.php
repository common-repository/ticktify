<?php
/**
 * Metabox for Download payment.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

wp_enqueue_style('jquery_dataTables_css', TICKTIFY_ASSETS_URL . '/css/jquery.dataTables.css');
wp_enqueue_script('jquery_dataTables_js', TICKTIFY_ASSETS_URL . '/js/jquery.dataTables.js');
wp_enqueue_script('dataTables_buttons_min_js', TICKTIFY_ASSETS_URL . '/js/dataTables.buttons.min.js');
wp_enqueue_script('buttons_html5_min_js', TICKTIFY_ASSETS_URL . '/js/buttons.html5.min.js');
wp_enqueue_script('pdfmake_min_js', TICKTIFY_ASSETS_URL . '/js/pdfmake.min.js');
wp_enqueue_script('vfs_fonts_js', TICKTIFY_ASSETS_URL . '/js/vfs_fonts.js');

$resultPayments = TICKTIFY_Download_Payments::ticktify_get_all_transactions();
?>

<div>
    <h1><?php esc_html_e("Download Payments", "ticktify"); ?></h1>
    <table id="table_id" class="display" style="width:100%">
        <thead>
            <tr>
                <th><?php esc_html_e("Booking Id", "ticktify"); ?></th>
                <th><?php esc_html_e("Customer Name", "ticktify"); ?></th>
                <th><?php esc_html_e("Payment Method", "ticktify"); ?></th>
                <th><?php esc_html_e("Payment Status", "ticktify"); ?></th>
                <th><?php esc_html_e("Total Amount", "ticktify"); ?></th>
                <th><?php esc_html_e("Transaction Id", "ticktify"); ?></th>
                <th><?php esc_html_e("Date", "ticktify"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultPayments as $key => $value) {
                $user_info = get_userdata($value->customer_id);
                $user_name = $user_info->display_name;
                ?>
                <tr>
                    <td>#<?php echo esc_html($value->booking_id); ?></td>
                    <td><?php echo esc_html($user_name); ?></td>
                    <td><?php echo esc_html($value->payment_method); ?></td>
                    <td><?php echo esc_html($value->payment_status); ?></td>
                    <td><?php echo esc_html($value->paid_amount); ?></td>
                    <td><?php echo esc_html($value->transaction_id); ?></td>
                    <td><?php echo esc_html($value->created); ?></td>
                </tr>
            <?php } ?>

        </tbody>
        <tfoot>
            <tr>
                <th><?php esc_html_e("Booking Id", "ticktify"); ?></th>
                <th><?php esc_html_e("Customer Name", "ticktify"); ?></th>
                <th><?php esc_html_e("Payment Method", "ticktify"); ?></th>
                <th><?php esc_html_e("Payment Status", "ticktify"); ?></th>
                <th><?php esc_html_e("Total Amount", "ticktify"); ?></th>
                <th><?php esc_html_e("Transaction Id", "ticktify"); ?></th>
                <th><?php esc_html_e("Date", "ticktify"); ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery('#table_id').DataTable({
            dom: 'Bfrtip',
            "pageLength": 25,
            order: [
                [6, 'desc']
            ],
            buttons: [
                'csv', 'pdf'
            ]
        });
    });

</script>