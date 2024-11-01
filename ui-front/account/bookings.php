<?php
/**
 * Booking listing page for user profile.
 *
 * @package ticktify-event\ui-front\account
 * @version 1.0.0
 */

$booking_id = (isset($_REQUEST['booking_id']) ? sanitize_text_field($_REQUEST['booking_id']) : '');
if (!empty($booking_id)) {
    return require_once(TICKTIFY_UI_FRONT_DIR . 'account/view-booking.php');
} else {
?>
    <center>
        <h3 id="orders" style="color: brown;"> <?php esc_html_e("Bookings", "ticktify"); ?></h3>
        <table class="form-table" style="width:70%">
            <tr>
                <th><?php esc_html_e("Booking", "ticktify"); ?></th>
                <th><?php esc_html_e("Booking Date", "ticktify"); ?></th>
                <th><?php esc_html_e("Total", "ticktify"); ?></th>
                <th><?php esc_html_e("Action", "ticktify"); ?></th>
            </tr>
            <?php
            $args = array(
                'post_type'        => TICKTIFY_BOOKING_POST_TYPE,
                'posts_per_page'   => -1,
                'author'      =>  get_current_user_id(),
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $transaction = Ticktify_Transaction::ticktify_get_transactions(sanitize_text_field(get_the_ID()));
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_the_title()); ?></td>
                        <td><?php echo esc_html(get_the_date('d M Y', get_the_ID())) ?></td>
                        <td><?php echo esc_html((!empty($transaction->paid_amount)) ? $transaction->paid_amount : "0.00"); ?></td>
                        <td>
                            <a class="btn btn-primary" href="<?php echo esc_url(site_url('ticktify-profile/?tab=bookings&booking_id=' . get_the_ID())); ?>"><?php esc_html_e("View", "ticktify"); ?></a>
                        </td>
                    </tr>
                    <?php
                } // end while
            } // end if
            wp_reset_query();
            ?>
        </table>
    </center>
<?php } ?>