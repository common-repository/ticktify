<?php
/**
 * Metabox for new user to admin.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_email_templates = get_option(sanitize_key('ticktify_email_templates'));

$to = isset($ticktify_email_templates['new_user_to_admin']['to']) ? $ticktify_email_templates['new_user_to_admin']['to'] : '';
$subject = isset($ticktify_email_templates['new_user_to_admin']['subject']) ? $ticktify_email_templates['new_user_to_admin']['subject'] : '';
$message = isset($ticktify_email_templates['new_user_to_admin']['message']) ? $ticktify_email_templates['new_user_to_admin']['message'] : '';
?>
<table class="form-table">
  <tbody>
    <tr>
      <th></th>
      <td><strong>[admin_name] [admin_email] [site_title] [first_name] [last_name] [user_email]</strong></td>
    </tr>
    <tr>
      <th><?php esc_html_e("To", "ticktify"); ?></th>
      <td>
        <input class="large-text" type="text" name="new_user_to_admin[to]" id="to_mail_newUsers" value="<?php echo esc_attr($to); ?>" />
      </td>
    </tr>
    <tr>
      <th><?php esc_html_e("Subject", "ticktify"); ?></th>
      <td>
        <input class="large-text" type="text" name="new_user_to_admin[subject]" id="subject_mail_newUsers" value="<?php echo esc_attr($subject); ?>" />
      </td>
    </tr>
    <tr>
      <th><?php esc_html_e("Message", "ticktify"); ?></th>
      <td>
        <?php
        $settings = array('wpautop' => false, 'textarea_name' => 'new_user_to_admin[message]');
        $content = wp_kses_post($message);
        $wp_editor_id = "message_mail_newUsers";
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