<?php
/**
 * Single Booking page for user profile.
 *
 * @package ticktify-event\ui-front\account
 * @version 1.0.0
 */
$booking_id = (isset($_REQUEST['booking_id']) ? sanitize_text_field($_REQUEST['booking_id']) : '');
$eventsArray = get_post_meta(sanitize_text_field($booking_id), sanitize_key('_events'), true);
$ticktify_cancellation_settings = get_option(sanitize_key('ticktify_cancellation_settings'));
$ticktify_cancellation_bookings = $ticktify_cancellation_settings['ticktify_bookings_user_cancellation'];
$ticktify_cancellation_time = $ticktify_cancellation_settings['ticktify_event_cancellation_hrs'];
$ticktify_cancellation_user_settings = $ticktify_cancellation_settings['ticktify_bookings_user_cancellation'];
?>

<center>
    <?php if (!empty($booking_id) && !empty($eventsArray)) { ?>
        <h3 id="orders" style="color: brown;"> <?php echo esc_html(get_the_title($booking_id)); ?></h3>
        <?php
        if ($ticktify_cancellation_bookings == 1) {
        ?>
            <h5><?php esc_html_e("You are able to cancel the Ticket only", "ticktify"); ?> <?php echo esc_html($ticktify_cancellation_time);  esc_html_e(" HRS before an event starts", "ticktify"); ?></h5>
        <?php
        }
        ?>
        <table class="form-table" style="width:70%">
            <thead>
                <tr>
                    <th><?php esc_html_e("Id", "ticktify"); ?></th>
                    <th><?php esc_html_e("Event", "ticktify"); ?></th>
                    <th><?php esc_html_e("Attendees", "ticktify"); ?></th>
                    <th><?php esc_html_e("Price", "ticktify"); ?></th>
                    <th><?php esc_html_e("Quantity", "ticktify"); ?></th>
                    <th><?php esc_html_e("Subtotal", "ticktify"); ?></th>
                    <th><?php esc_html_e("Status", "ticktify"); ?></th>
                    <th><?php esc_html_e("Action", "ticktify"); ?></th>
                </tr>
                <thead>
                <tbody>
                    <?php
                    $total_sum = 0;
                    foreach ($eventsArray as $key => $value) {
                        $attendees = json_decode($value['attendees']);
                    ?>
                        <tr>
                            <td>#<?php echo esc_html($value['event_id']); ?></td>
                            <td><a href="<?php echo esc_url(get_post_permalink($value['event_id'])); ?>"><?php echo esc_html(get_the_title($value['event_id'])); ?></a></td>
                            <td>
                                <?php foreach ($attendees as $att_key => $att_val) {
                                    echo esc_html($att_val->name . " (" . $att_val->age . ") ");
                                } ?>
                            </td>
                            <td><?php echo esc_html($value['price']); ?></td>
                            <td>&#215;<?php echo esc_html($value['quantity']); ?></td>
                            <td><?php echo esc_html($value['price']*$value['quantity']); ?></td>
                            <td id="event_status_<?php echo esc_attr($value['event_id']); ?>"><?php echo esc_html(ucfirst($value['status'])); ?></td>
                            <td>
                                <?php
                                $dateArr = get_post_meta(sanitize_text_field($value['event_id']), sanitize_key('_custom_date_meta_key'), true);
                                $timeArr = get_post_meta(sanitize_text_field($value['event_id']), sanitize_key('_custom_time_meta_key'), true);
                                if (!empty($ticktify_cancellation_bookings)) {
                                    $event_start_date = date("Y/m/d", strtotime($dateArr[0])) . " " . date("H:i", strtotime($timeArr[0]));
                                    $current_date_time =  current_time("Y/m/d H:i");
                                    if (!empty($ticktify_cancellation_time)) {
                                        $time_arr = explode(":", $ticktify_cancellation_time);
                                        $total_minutes = ($time_arr[0] * 60) + ($time_arr[1]);

                                        $newtimestamp = strtotime($event_start_date . ' - ' . $total_minutes . ' minute');
                                        $event_start_date = date('Y/m/d H:i', $newtimestamp);
                                    }
                                    if (strtotime($event_start_date) >= strtotime($current_date_time)) {
                                        ?>
                                        <?php if ($value['status'] != 'cancelled') { ?>
                                            <button class="btn btn-primary booking_event_cancel" data-bookingId="<?php echo esc_attr($booking_id); ?>" data-eventId="<?php echo esc_attr($value['event_id']); ?>" data-actionUrl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-wpnonce="<?php echo esc_attr(wp_create_nonce('event_cancel')); ?>"><?php esc_html_e("Cancel", "ticktify"); ?> </button>
                                        <?php }
                                    }
                                } ?>
                            </td>
                        </tr>
                    <?php
                        $total_sum += ($value['price']*$value['quantity']);
                    } ?>
            <tbody>
            <tfoot>
                <tr>
                    <th colspan="6"></th>
                    <th><?php esc_html_e("Total", "ticktify"); ?></th>
                    <th><?php echo esc_html($total_sum); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php } else { ?>
        <div>
            <p><?php esc_html_e("Result Not Found", "ticktify"); ?>
            <p>
        </div>
    <?php } ?>
</center>