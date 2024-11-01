<?php
/**
 * Metabox for registration setting.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_registration_settings = get_option(sanitize_key('ticktify_pagination_settings'));
$first_text = isset($ticktify_registration_settings['event_registration']['first_text']) ? $ticktify_registration_settings['event_registration']['first_text'] : '';
$last_text = isset($ticktify_registration_settings['event_registration']['last_text']) ? $ticktify_registration_settings['event_registration']['last_text'] : '';
$email = isset($ticktify_registration_settings['event_registration']['email']) ? $ticktify_registration_settings['event_registration']['email'] : '';
$password = isset($ticktify_registration_settings['event_registration']['password']) ? $ticktify_registration_settings['event_registration']['password'] : '';
$conpassword = isset($ticktify_registration_settings['event_registration']['conpassword']) ? $ticktify_registration_settings['event_registration']['conpassword'] : '';
?>
<table class="form-table">
    <tbody>
        <tr>
            <th><label><?php esc_html_e("First Name Label", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_registration[first_text]" id="first_texts" value="<?php echo esc_attr($first_text); ?>" />
            </td>
        </tr>
        <tr>
            <th><label><?php esc_html_e("Last Name Label", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_registration[last_text]" id="last_texts" value="<?php echo esc_attr($last_text); ?>" />
            </td>
        </tr>
        <tr>
            <th><label><?php esc_html_e("Email Label", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_registration[email]" id="emails" value="<?php echo esc_attr($email); ?>" />
            </td>
        </tr>
        <tr>
            <th><label><?php esc_html_e("Password Label", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_registration[password]" id="passwords" value="<?php echo esc_attr($password); ?>" />
            </td>
        </tr>
        <tr>
            <th><label><?php esc_html_e("Re-type Password Label", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_registration[conpassword]" id="conpasswords" value="<?php echo esc_attr($conpassword); ?>" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="save_changes" class="button button-primary" id="buttons" value="<?php _e('Save', "ticktify") ?>">
            </td>
        </tr>
    </tbody>
</table>