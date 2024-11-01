<?php
/**
 * Metabox for event gallery.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

global $post;
$banner_img = get_post_meta(sanitize_text_field($post->ID), sanitize_key('event_gallery_img'), true); ?>
<style type="text/css">
    .multi-upload-medias ul li .delete-img {
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

    .multi-upload-medias ul li {
        width: 120px;
        display: inline-block;
        vertical-align: middle;
        margin: 5px;
        position: relative;
    }

    .multi-upload-medias ul li img {
        width: 100%;
        height: 100%;
    }
</style>
<table cellspacing="10" cellpadding="10">
    <tr>
        <!-- <td>event gallery</td> -->
        <td>
            <?php echo ticktify_multi_media_uploader_fields(sanitize_key('event_gallery_img'), $banner_img); ?>
        </td>
    </tr>
</table>
<script>
    jQuery(function($) {

        $('body').on('click', '.wc_multi_upload_image_button', function(e) {
            e.preventDefault();

            var button = $(this),
                custom_uploader = wp.media({
                    title: '<?php _e('Insert Image', "ticktify") ?>',
                    button: {
                        text: '<?php _e('Use this image', "ticktify") ?>'
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
                    $(button).siblings('.wc_multi_remove_image_button').show();
                })
                .open();
        });

        $('body').on('click', '.wc_multi_remove_image_button', function() {
            $(this).hide().prev().val('').prev().addClass('button').html('Add Media');
            $(this).parent().find('ul').empty();
            return false;
        });
    });
    jQuery(document).ready(function() {
        jQuery(document).on('click', '.multi-upload-medias ul li i.delete-img', function() {
            var ids = [];
            var this_c = jQuery(this);
            jQuery(this).parent().remove();
            jQuery('.multi-upload-medias ul li').each(function() {
                ids.push(jQuery(this).attr('data-attechment-id'));
            });
            jQuery('.multi-upload-medias').find('input[type="hidden"]').attr('value', ids);
        });
    })
</script>
<?php
/**
 * Responsible for multi media uploader fields
 *  
 * @return void
 */
function ticktify_multi_media_uploader_fields($name, $value = '')
{
    $image = '">' . __('Add Media', "ticktify");
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
    return '<div class="multi-upload-medias"><ul>' . $image_str . '</ul><a href="#" class="wc_multi_upload_image_button button' . $image . '</a><input type="hidden" class="attechments-ids ' . esc_attr($name) . '" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr(implode(',', $value)) . '" /><a href="#" class="wc_multi_remove_image_buttons button" style="display:inline-block;display:' . esc_attr($display) . '">' . __('Remove media', "ticktify") . '</a></div>';
}
?>