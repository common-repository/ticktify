<?php
/**
 * Metabox for event cancellation setting.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_cancellation_settings = get_option(sanitize_key('ticktify_cancellation_settings'));

$ticktify_cancellation_bookings = isset($ticktify_cancellation_settings['ticktify_bookings_user_cancellation']) ? $ticktify_cancellation_settings['ticktify_bookings_user_cancellation'] : 0;
$ticktify_cancellation_time = isset($ticktify_cancellation_settings['ticktify_event_cancellation_hrs']) ? $ticktify_cancellation_settings['ticktify_event_cancellation_hrs'] : '';
?>
<table class="form-table">
    <tbody>
        <tr>
            <th><label><?php esc_html_e("Can users cancel their booking? ", "ticktify"); ?></label></th>
            <td>
                <input type="radio" name="ticktify_bookings_user_cancellation" id="ticktify_bookings_cancellation" value="1" <?php if ($ticktify_cancellation_bookings == 1) {  echo esc_attr('checked="checked"');  } ?> /><?php esc_html_e("Yes", "ticktify"); ?> &nbsp;&nbsp;
                <input type="radio" name="ticktify_bookings_user_cancellation" id="ticktify_bookings_not_cancellation" value="0" <?php if ($ticktify_cancellation_bookings == 0) {  echo esc_attr('checked="checked"'); } ?> /><?php esc_html_e("No", "ticktify"); ?><br />
            </td>
        </tr>
        <tr>
            <th><label><?php esc_html_e("How long before an event can users cancel events?", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="ticktify_event_cancellation_hrs" id="ticktify_event_cancellation_hrs" placeholder="HH:MM" value="<?php echo esc_attr($ticktify_cancellation_time); ?>" pattern="^([0-9]+):([0-5]?[0-9])$" /><br>
                <em>
                    <?php esc_html_e("Enter the number of hours before an event starts for when users can cancel a booking. Leave blank for the start time of the event. PHP date intevals are also accepted, for example HH:MM.", "ticktify"); ?>
                </em>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="submit" class="button button-primary" id="sub_btn" value="<?php _e('Save', "ticktify") ?>">
            </td>
        </tr>
    </tbody>
</table>