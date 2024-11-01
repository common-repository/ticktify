<?php
/**
 * Metabox for booking item.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$eventsArray = get_post_meta(sanitize_text_field($post->ID), sanitize_key('_events'), true);
?>

<table class="form-table">
    <thead>
        <tr>
            <th><?php esc_html_e("Id", "ticktify"); ?></th>
            <th><?php esc_html_e("Event", "ticktify"); ?></th>
            <th><?php esc_html_e("Attendees", "ticktify"); ?></th>
            <th><?php esc_html_e("Status", "ticktify"); ?></th>
            <th><?php esc_html_e("Price", "ticktify"); ?></th>
            <th><?php esc_html_e("Quantity", "ticktify"); ?></th>
            <th><?php esc_html_e("Subtotal", "ticktify"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_sum = 0;
        foreach ($eventsArray as $key => $value) {
            $attendees = json_decode($value['attendees']);
            ?>
            <tr>
                <td>#<?php echo esc_html($value['event_id']); ?></td>
                <td><a href="<?php echo esc_url(get_post_permalink(sanitize_text_field($value['event_id']))); ?>"><?php echo esc_html(get_the_title(sanitize_text_field($value['event_id']))) ?></a></td>
                <td><?php 
              if($attendees != ''){
                foreach ($attendees as $att_key => $att_val) {
                        echo esc_html($att_val->name) . " (" . esc_html($att_val->age) . ") <br>";
                    }   }
                    
                    ?></td>
                <td><?php echo esc_html($value['status']) ?></td>
                <td><?php echo esc_html($value['price']) ?></td>
                <td>&#215;<?php echo esc_html($value['quantity']) ?></td>
                <td><?php echo esc_html($value['price']*$value['quantity']) ?></td>
            </tr>
            <?php $total_sum += ($value['price']*$value['quantity']);
        } ?>
    <tfoot>
        <tr>
            <th colspan="5"></th>
            <th><?php esc_html_e("Total", "ticktify"); ?></th>
            <th><?php echo esc_html($total_sum) ?></th>
        </tr>
    </tfoot>
    </tbody>
</table>