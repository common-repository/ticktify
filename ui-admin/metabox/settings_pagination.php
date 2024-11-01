<?php
/**
 * Metabox for pagination setting.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_pagination_settings = get_option(sanitize_key('ticktify_pagination_settings'));

$event_number = isset($ticktify_pagination_settings['event_pagination']['event_number']) ? $ticktify_pagination_settings['event_pagination']['event_number'] : '';
$color = isset($ticktify_pagination_settings['event_pagination']['color']) ? $ticktify_pagination_settings['event_pagination']['color'] : '';
$bg_color = isset($ticktify_pagination_settings['event_pagination']['bg_color']) ? $ticktify_pagination_settings['event_pagination']['bg_color'] : '';
$hov_color = isset($ticktify_pagination_settings['event_pagination']['hov_color']) ? $ticktify_pagination_settings['event_pagination']['hov_color'] : '';
$hov_bg = isset($ticktify_pagination_settings['event_pagination']['hov_bg']) ? $ticktify_pagination_settings['event_pagination']['hov_bg'] : '';
?>

<table class="form-table">
    <tbody>
        <tr>
            <th><?php esc_html_e("Number of events to show per page", "ticktify"); ?></th>
            <td>
                <input type="number" name="event_pagination[event_number]" id="event_numbers" min="1" value="<?php echo esc_attr($event_number); ?>" />
                <p class="tooltip description"><?php esc_html_e("The number of events per page on the List, Photo, and Map Views. Does not affect other views.", "ticktify"); ?></p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Color", "ticktify"); ?></th>
            <td>
                <input class="my-color-field" name="event_pagination[color]" id="favcolor" type="color" value="<?php echo esc_attr($color); ?>" /><br>

            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Background Color", "ticktify"); ?></th>
            <td>
                <input class="my-color-field" name="event_pagination[bg_color]" type="color" value="<?php echo esc_attr($bg_color); ?>" /><br>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Hover color", "ticktify"); ?></th>
            <td>
                <input class="my-color-field" type="color" name="event_pagination[hov_color]" value="<?php echo esc_attr($hov_color); ?>" /><br>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Hover Background", "ticktify"); ?></th>
            <td>
                <input class="my-color-field" type="color" name="event_pagination[hov_bg]" value="<?php echo esc_attr($hov_bg); ?>" /><br>
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

