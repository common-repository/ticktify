<?php
/**
 * Metabox for google api setting.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_google_map_api = get_option(sanitize_key('ticktify_pagination_settings'));

$ticktify_api = isset($ticktify_google_map_api['event_map_api']['_google_map_api_key']) ? $ticktify_google_map_api['event_map_api']['_google_map_api_key'] : '';

?>
<table class="form-table">
    <tbody>
        <tr>
            <th><label><?php esc_html_e("Google Map API Key", "ticktify"); ?></label></th>
            <td>
                <input class="regular-text" type="text" name="event_map_api[_google_map_api_key]" id="_google_map_api_key" value="<?php echo esc_attr($ticktify_api); ?>" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="submit" class="button button-primary" id="sub_btn" value="<?php _e("Save", "ticktify") ?>">
            </td>
        </tr>

    </tbody>

</table>