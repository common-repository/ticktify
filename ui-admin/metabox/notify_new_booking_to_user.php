<?php
/**
 * Metabox for new booking notification to user.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_email_templates = get_option(sanitize_key('ticktify_email_templates'));

$to = isset($ticktify_email_templates['booking_to_user']['to']) ? $ticktify_email_templates['booking_to_user']['to'] : '';
$subject = isset($ticktify_email_templates['booking_to_user']['subject']) ? $ticktify_email_templates['booking_to_user']['subject'] : '';
$message = isset($ticktify_email_templates['booking_to_user']['message']) ? $ticktify_email_templates['booking_to_user']['message'] : '';
?>
<table class="form-table">
  <tbody>
    <tr>
      <th></th>
      <td><strong>[site_title] [first_name] [last_name] [user_email] [booking_title] [booking_details] [booking_url_for_user]</strong></td>
    </tr>
    <tr>
      <th><?php esc_html_e("To", "ticktify"); ?></th>
      <td>
        <input class="large-text" type="text" name="booking_to_user[to]" id="to_mail_bookings_users" value="<?php echo esc_attr($to); ?>" />
      </td>
    </tr>
    <tr>
      <th><?php esc_html_e("Subject", "ticktify"); ?></th>
      <td>
        <input class="large-text" type="text" name="booking_to_user[subject]" id="subject_mail_bookings_users" value="<?php echo esc_attr($subject); ?>" />
      </td>
    </tr>
    <tr>
      <th><?php esc_html_e("Message", "ticktify"); ?></th>
      <td>
        <?php
        $settings = array('wpautop' => false, 'textarea_name' => 'booking_to_user[message]');
        $content = wp_kses_post($message);
        $wpText_editor_id = "message_mail_bookings_users";
        wp_editor($content, $wpText_editor_id, $settings);
        ?>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input type="submit" name="submit_btn" class="button button-primary" id="sub_btn" value="<?php _e('Save', "ticktify") ?>">
      </td>
    </tr>
  </tbody>
</table>