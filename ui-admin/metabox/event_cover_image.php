<?php
/**
 * Metabox for event cover image.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */
?>
<style>
    #image_preview {
        display: none;
    }

    .delete-banner-image {
        margin-left: -24px;
        margin-top: 5px;
        position: relative;
        border: 1px solid #e93737;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #e93737;
        background: #fff;
        cursor: pointer;
    }
</style>
<table cellspacing="10" cellpadding="10">
    <tr>
        <th><?php esc_html_e("Event Banner/Video", "ticktify"); ?> </th>&nbsp;&nbsp;
        <?php $video_check = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('video_check'), true); ?>
        <td> <input id="video_check" type="checkbox" name="video_check" value="1" <?php echo (!empty($video_check)) ? 'checked' : ''; ?> /></td>
        <td data-type="video" <?php echo (!empty($video_check)) ? 'style="display:block;"' : 'style="display:none;"'; ?>>
            <?php $video_url = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('video_for_post'), true);
            ?>
            <div id="banner_video_preview" <?php echo (empty($video_url)) ? 'style="display:none"' : ""; ?>>
                <a href="<?php echo esc_url($video_url); ?>" target="blank"><?php esc_html_e("Video Preview", "ticktify"); ?> </a>
            </div>
            <input id="video_URL" type="hidden" name="video_URL" value="<?php echo esc_url($video_url); ?>" src="<?php echo esc_url($video_url); ?>" />
            <input id="upload_video_button" class="button" type="button" value="<?php _e('Upload Video', "ticktify") ?>" />
        </td>
        <td data-type="image" <?php echo (empty($video_check)) ? 'style="display:block;"' : 'style="display:none;"'; ?>>

            <?php $attachment = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('my_image_for_post'), true);
            
            ?>
            <div id="banner_image_preview" <?php echo (empty(esc_html($attachment))) ? 'style="display:none"' : "" ; ?>>
                <img src="<?php echo esc_url($attachment); ?>" alt="<?php _e('Preview of Banner Image', "ticktify") ?>" width="120" height="80">
                <i class=" dashicons dashicons-no delete-banner-image"></i>
            </div>
            
            <input id="upload_image" type="hidden" name="upload_image" value="<?php echo esc_url($attachment); ?>" src="<?php echo esc_url($attachment); ?>" />
            <input id="upload_image_button" class="button" type="button" value="<?php _e('Upload Image', "ticktify") ?>" />
        </td>
    </tr>
    <tr>
</table>
<!-- checkbox for banner video or image -->
<script>
    jQuery('body').on('change', '#video_check', function() {
        if (jQuery(this).is(':checked')) {
            jQuery('td[data-type="video"]').show();
            jQuery('td[data-type="image"]').hide();
        } else {
            jQuery('td[data-type="video"]').hide();
            jQuery('td[data-type="image"]').show();
        }
    });
</script>
<!--  uploading banner video  -->
<script>
    jQuery(document).ready(function($) {
        $('#video-metabox.postbox').css('margin-top', '30px');
        var custom_uploader;
        $('#upload_video_button').click(function(e) {
            e.preventDefault();
            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }
            //Extend the wp.media object
            custom_uploader = wp.mediafile_frame = wp.media({
                title: '<?php _e('Choose a Video', "ticktify") ?>',
                button: {
                    text: '<?php _e('Choose a Video', "ticktify") ?>'
                },
                library: {
                    type: ['video']
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#video_URL').val(attachment.url);
                $('#banner_video_preview a').attr("href", attachment.url);
                //$('#banner_video_preview a').text(attachment.name);
                $('#banner_video_preview').show();
            });
            //Open the uploader dialog
            custom_uploader.open();
        });
    });
</script>
<!-- uploading  banner image -->
<script>
    jQuery(document).ready(function($) {
        var custom_uploader;
        $('#upload_image_button').click(function(e) {
            e.preventDefault();
            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }
            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: '<?php _e('Choose Image', "ticktify") ?>',
                button: {
                    text: '<?php _e('Choose Image', "ticktify") ?>'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });
            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#upload_image').val(attachment.url);
                $('#upload_image').attr("src", attachment.url);
                $('#banner_image_preview img').attr("src", attachment.url);
                $('#banner_image_preview').show();
            });
            //Open the uploader dialog
            custom_uploader.open();
        });
        $('.delete-banner-image').click(function(e) {
            $('#upload_image').val("");
            $('#upload_image').attr("src", "");
            $('#banner_image_preview img').attr("src", "");
            $('#banner_image_preview').hide();
        });
    });
</script>