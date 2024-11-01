<?php
/**
 * Metabox for cancellation booking notification to admin.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_email_templates = get_option(sanitize_key('ticktify_email_templates'));
$to = isset($ticktify_email_templates['cancellation_to_admin']['to']) ? $ticktify_email_templates['cancellation_to_admin']['to'] : '';
$subject = isset($ticktify_email_templates['cancellation_to_admin']['subject']) ? $ticktify_email_templates['cancellation_to_admin']['subject'] : '';
$message = isset($ticktify_email_templates['cancellation_to_admin']['message']) ? $ticktify_email_templates['cancellation_to_admin']['message'] : '';
$headers = array('Content-Type: text/html; charset=UTF-8');

?>
<table class="form-table">
    <tbody>
        <tr>
            <th></th>
            <td><strong>[admin_name] [admin_email] [site_title] [first_name] [last_name] [user_email] [booking_title] [booking_details] [booking_url_for_admin]</strong></td>
        </tr>
        <tr>
            <th><?php esc_html_e("To", "ticktify"); ?></th>
            <td>
                <input class="large-text" type="text" name="cancellation_to_admin[to]" id="to_mail_cancellation" value="<?php echo esc_attr($to); ?>" />
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Subject", "ticktify"); ?></th>
            <td>
                <input class="large-text" type="text" name="cancellation_to_admin[subject]" id="subject_mail_cancellation" value="<?php echo esc_attr($subject); ?>" placeholder="" />
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Message", "ticktify"); ?></th>
            <td>
                <?php
                $settings = array('wpautop' => false, 'textarea_name' => 'cancellation_to_admin[message]');
                $content = wp_kses_post($message);
                $wp_editor_id = "booking_cancellation_admin";
                wp_editor($content, $wp_editor_id, $settings);
                ?>
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