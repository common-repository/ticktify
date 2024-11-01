<?php
/**
 * Handle the meta box saving.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

global $post, $wpdb;
wp_enqueue_script('cpt-js');
$meta = get_post_meta(sanitize_text_field($post->ID));
$post_meta = get_post_meta(sanitize_text_field(get_the_id()));
$date_type = 'datetime-local';
if (isset($meta['allday']) && $meta['allday'][0] == 'true') {
    $date_type = 'date';
}
?>
<style>
    .ui-widget-header .ui-icon {
        background-image: url(<?php echo esc_url(TICKTIFY_ASSETS_URL); ?>/ui-icons_444444_256x240.png)
    }
</style>
<table class="form-table">
    <thead></thead>
    <tfoot></tfoot>
    <tbody>
        <tr>
            <th><?php esc_html_e("Start/End: Date", "ticktify"); ?></th>
            <td>
                <?php $custom_date = get_post_meta(sanitize_text_field($post->ID), sanitize_key('_custom_date_meta_key'), true); ?>
                <label for="search-from-date"><?php esc_html_e("From", "ticktify"); ?></label>
                <input type="text" name="search-from-date" id="search-from-date" autocomplete="off" readonly value="<?php if (isset($custom_date[0])) { echo esc_attr($custom_date[0]); } ?>" required />
                <span>
                    <label for="search-to-date"> <?php esc_html_e("To", "ticktify"); ?></label>
                </span>
                <input type="text" name="end-to-date" id="search-to-date" autocomplete="off" readonly value="<?php if (isset($custom_date[1])) {  echo esc_attr($custom_date[1]);   } ?>" />
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Start/End: Time", "ticktify"); ?></th>
            <td>
                <?php $custom_time = get_post_meta(sanitize_text_field($post->ID), sanitize_key('_custom_time_meta_key'), true); ?>
                <label for="search-from-time"><?php esc_html_e("From", "ticktify"); ?></label>
                <input type="text" name="search-from-time" id="search-from-time" autocomplete="off" readonly value="<?php if (isset($custom_time[0])) { echo esc_attr($custom_time[0]);   } ?>" />
                <span>
                    <label for="search-to-time"> <?php esc_html_e("To", "ticktify"); ?></label>
                </span>
                <input type="text" name="end-to-time" id="search-to-time" autocomplete="off" readonly value="<?php if (isset($custom_time[1])) {  echo esc_attr($custom_time[1]);   } ?>" />
            </td>

        </tr>

        <tr>
            <th><?php esc_html_e("Event Logo", "ticktify"); ?></th>
            <td>
                <?php $banner_imgs = get_post_meta(sanitize_text_field($post->ID), sanitize_key('event_gallery_imgs'), true); ?>
                <style type="text/css">
                    .multi-upload-media ul li .delete-img {
                        position: absolute;
                        right: 3px;
                        top: 2px;
                        background: aliceblue;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 14px;
                        line-height: 20px;
                        color: red;
                    }

                    .multi-upload-media ul li {
                        width: 120px;
                        display: inline-block;
                        vertical-align: middle;
                        margin: 5px;
                        position: relative;
                    }

                    .multi-upload-media ul li img {
                        width: 100%;
                    }
                </style>
                <table cellspacing="10" cellpadding="10">
                    <tr>
                        <td>
                            <?php echo ticktify_multi_media_uploader_field(sanitize_key('event_gallery_imgs'), $banner_imgs); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Paid/Free Event", "ticktify"); ?></th>
            <td>
                <?php $value = get_post_meta(sanitize_text_field($post->ID), sanitize_key('event_select'), true);
                $event_paid_value = esc_html((isset($meta['paid_event'][0]) && $meta['paid_event'][0] != '') ? $meta['paid_event'][0] : ''); ?>
                <label><input type="radio" id="paid_event" name="paid_event" value="Paid" <?php checked(sanitize_text_field($value), 'Paid'); ?> checked><?php esc_html_e("Paid Event", "ticktify"); ?> </label>
                <label><input type="radio" id="free_event" name="paid_event" value="Free" <?php checked(sanitize_text_field($value), 'Free'); ?>><?php esc_html_e("Free Event", "ticktify"); ?> </label>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Number of seats", "ticktify"); ?></th>
            <td>
                <?php $event_seats = get_post_meta(sanitize_text_field($post->ID), sanitize_key('event_seats_numbers'), true); ?>
                <input type="number" id="seat_number" name="seat_number" min="1" value="<?php echo esc_attr($event_seats); ?>" required>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e("Maximum number of seats one can reserve", "ticktify"); ?></th>
            <td>
                <?php $event_max = get_post_meta(sanitize_text_field($post->ID), sanitize_key('event_max_numbers'), true); ?>

                <input type="number" id="maximum_seat" name="maximum_seat" max="<?php echo esc_attr($event_seats); ?>" min="1" value="<?php echo esc_attr($event_max); ?>" required>

                <span id="errorMsg" style="display:none;"><?php esc_html_e("Please enter the value less than Number of seats", "ticktify"); ?></span>
            </td>
        </tr>
        <tr <?php echo ($value == "Free") ? 'style="display:none;"' : ''; ?> class="event_price" data-type="price_check">
            <th><?php esc_html_e("Price per seat", "ticktify"); ?></th>
            <td>
                <?php $price_per_seat = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('event_price_seat'), true) ?>
                <input type="text" id="event_price" name="price_seat" pattern="^\d+(\.\d+)$" value="<?php echo esc_attr($price_per_seat); ?>" required>
                <p><?php esc_html_e("Please enter the decimal format value", "ticktify"); ?></p>
                <!-- ^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$ -->
            </td>
        </tr>
    </tbody>
</table>
<script>
    jQuery("#seat_number").bind('keyup mouseup', function() {
        jQuery("#maximum_seat").attr("max", jQuery(this).val());
    });
    //      set price for free event in price input.
    jQuery('body').on('change', '#free_event', function() {
        jQuery('tr[data-type="price_check"]').hide();
        jQuery('#event_price').val('0.00');
    });
    jQuery('body').on('change', '#paid_event', function() {
        jQuery('tr[data-type="price_check"]').show();
    });
</script>
<script>
    jQuery(document).ready(function() {
        var select = function() {
            var d1 = jQuery('#search-from-date').datepicker('getDate');
            var d2 = jQuery('#search-to-date').datepicker('getDate');
            var diff = 0;
            if (d1 && d2) {
                diff = Math.floor((d2.getTime() - d1.getTime()) / 86400000); // ms per day
            }
            //jQuery('#calculated').val(diff);
        }
        jQuery("#search-from-date").datepicker({
            // showMonthAfterYear: true,
            dateFormat: "dd-mm-yy",
            numberOfMonths: 1,
            minDate: 0,
            onSelect: function(selected) {
                jQuery("#search-to-date").datepicker("option", "minDate", selected);
                select();
            }
        });
        jQuery("#search-to-date").datepicker({
            // showMonthAfterYear: true,
            dateFormat: "dd-mm-yy",
            numberOfMonths: 1,
            onSelect: function(selected) {
                jQuery("#search-from-date").datepicker("option", "maxDate", selected)
                select();
            }
        });
    });
    //Event time js
    jQuery(document).ready(function() {
        jQuery('#search-from-time').timepicker({
            timeFormat: 'hh:mm p',
            interval: 30,
            //minTime: '10',            
            //maxTime: '6:00pm',            
            //defaultTime: '10',            
            startTime: '01:00 AM',
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function(selected) {
                const newdate = new Date(selected.getTime() + 30 * 60 * 1000);
                jQuery("#search-to-time").timepicker("option", "minTime", newdate);
                jQuery("#search-to-time").timepicker("option", "startTime", newdate);
            }
        });
        jQuery('#search-to-time').timepicker({
            timeFormat: 'hh:mm p',
            interval: 30,
            //minTime: '10',            
            //maxTime: '6:00pm',            
            //defaultTime: '10',            
            startTime: '01:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function(selected) {
                const newdate = new Date(selected.getTime() - 30 * 60 * 1000);
                jQuery("#search-from-time").timepicker("option", "maxTime", newdate);
            }
        });
    });
</script>
<script>
    jQuery(function($) {
        $('body').on('click', '.wc_multi_upload_image_buttons', function(e) {
            e.preventDefault();
            var button = $(this),
                custom_uploader = wp.media({
                    title: 'Insert image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: true
                }).on('select', function() {
                    var attech_ids = '';
                    attachments
                    var attachments = custom_uploader.state().get('selection'),
                        attachment_ids = new Array(),
                        i = 0;
                    attachments.each(function(attachment) {
                        attachment_ids[i] = attachment['id'];
                        attech_ids += ',' + attachment['id'];
                        if (attachment.attributes.type == 'image') {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.url + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        } else {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.icon + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        }
                        i++;
                    });

                    var ids = $(button).siblings('.attechments-ids').attr('value');
                    if (ids) {
                        var ids = ids + attech_ids;
                        $(button).siblings('.attechments-ids').attr('value', ids);
                    } else {
                        $(button).siblings('.attechments-ids').attr('value', attachment_ids);
                    }
                    $(button).siblings('.wc_multi_remove_image_buttons').show();
                })
                .open();
        });
        $('body').on('click', '.wc_multi_remove_image_buttons', function() {
            $(this).hide().prev().val('').prev().addClass('button').html('Add Media');
            $(this).parent().find('ul').empty();
            return false;
        });
    });
    jQuery(document).ready(function() {
        jQuery(document).on('click', '.multi-upload-media ul li i.delete-img', function() {
            var ids = [];
            var this_c = jQuery(this);
            jQuery(this).parent().remove();
            jQuery('.multi-upload-media ul li').each(function() {
                ids.push(jQuery(this).attr('data-attechment-id'));
            });
            jQuery('.multi-upload-media').find('input[type="hidden"]').attr('value', ids);
        });
    })
</script>
<?php
/**
 * Responsible for multi media uploader field
 *  
 * @return void
 */
function ticktify_multi_media_uploader_field($name, $value = '')
{
    $image = '">' . __('Add Logo', "ticktify");
    $image_str = '';
    $image_size = 'full';
    $display = 'none';
    $value = explode(',', $value);

    if (!empty($value)) {
        foreach ($value as $values) {
            if ($image_attributes = wp_get_attachment_image_src($values, $image_size)) {
                $image_str .= '<li data-attechment-id=' . esc_attr($values) . '><a href="' . esc_url($image_attributes[0]) . '" target="_blank"><img src="' . esc_url($image_attributes[0]) . '" /></a><i class="dashicons dashicons-no delete-img"></i></li>';
            }
        }
    }
    if ($image_str) {
        $display = 'inline-block';
    }
    return '<div class="multi-upload-media"><ul>' . $image_str . '</ul><a href="#" class="wc_multi_upload_image_buttons button' . $image . '</a><input type="hidden" class="attechments-ids ' . esc_attr($name) . '" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr(implode(',', $value)) . '" /><a href="#" class="wc_multi_remove_image_button button" style="display:inline-block;display:' . esc_attr($display) . '">' . __('Remove logo', "ticktify") . '</a></div>';
}
