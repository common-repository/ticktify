<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for ZT Artist taxonomy.
 *
 * @class Ticktify_Artist
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Artist')) :
    class Ticktify_Artist
    {
        /**
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);

            add_action(TICKTIFY_EVENT_ARTIST_TAX . '_add_form_fields', [$this, 'ticktify_artists_add_term_fields']);
            add_action(TICKTIFY_EVENT_ARTIST_TAX . '_edit_form_fields', [$this, 'ticktify_artists_edit_term_fields'], 10, 2);
            add_action('created_' . TICKTIFY_EVENT_ARTIST_TAX, [$this, 'ticktify_save_artist_fields']);
            add_action('edited_' . TICKTIFY_EVENT_ARTIST_TAX, [$this, 'ticktify_updated_artist_fields']);
            add_action('admin_enqueue_scripts', [$this, 'ticktify_load_media']);
            add_action('admin_footer', array($this, 'ticktify_artist_admin_enqueue_scripts'));
        }

        /**
         *  Responsible for loaded callback
         * 
         * @return void
         */
        public function ticktify_loaded_callback()
        {
            $this->ticktify_register_taxonomies();
        }

        /**
         *  Responsible for register taxonomies
         * 
         * @return void
         */
        function ticktify_register_taxonomies()
        {

            $labels = array(
                'name' => __('Artist & Speakers', "ticktify"),
                'singular_name' => __('Artist & Speakers ', "ticktify"),
                'search_items' => __('Search Artists', "ticktify"),
                'all_items' => __('All Artists', "ticktify"),
                'add_new' => __('Add New', "ticktify"),
                'add_new_item' => __('Add New Artist', "ticktify"),
                'parent_item' => __('Parent Artist', "ticktify"),
                'parent_item_colon' => __('Parent Artists:', "ticktify"),
                'edit_item' => __('Edit Artist', "ticktify"),
                'update_item' => __('Update Artist', "ticktify"),
                'new_item_name' => __('New Artist Name', "ticktify"),
                'menu_name' => __('Artist & Speakers', "ticktify"),
            );
            $args = array(
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'capability_type' => 'artists',
                'query_var' => true,
                'rewrite' => array("slug" => "artist"),
                'hierarchical' => false,
                'show_in_rest' => true,
            );
            register_taxonomy(TICKTIFY_EVENT_ARTIST_TAX, [TICKTIFY_EVENT_POST_TYPE], $args);
        }

        /**
         *  Responsible for Load media
         * 
         * @return void
         */
        function ticktify_load_media()
        {
            wp_enqueue_media();
        }

        /**
         *  Responsible for artists add term fields
         * 
         * @return void
         */
        function ticktify_artists_add_term_fields($taxonomy)
        {
            ?>
            <div class="form-field">
                <label for="rudr_text"><?php esc_html_e("Website", "ticktify"); ?></label>
                <input type="url" name="website" id="website" value=" " />
            </div>
            <div class="form-field">
                <label for="rudr_text"><?php esc_html_e("Bio", "ticktify"); ?></label>
                <input type="text" name="bio" id="bio" value="" />
            </div>
            <div class="form-field term-group">

                <div id="image_wrapper">
                </div>

                <label for="image_artist_id">
                    <input type="hidden" id="image_artist_id" name="image_artist_id" class="custom_media_url" value="">
                    <?php esc_html_e('Image', "ticktify"); ?>
                </label>
                <p>
                    <input type="button" class="button button-secondary taxonomy_media_button" id="taxonomy_media_button" name="taxonomy_media_button" value="<?php echo esc_attr('Add Image'); ?>">
                    <input type="button" class="button button-secondary taxonomy_media_remove" id="taxonomy_media_remove" name="taxonomy_media_remove" value="<?php echo esc_attr('Remove Image'); ?>">
                </p>
            </div>
        <?php
        }

        /**
         *  Responsible for save artist fields
         * 
         * @return void
         */
        function ticktify_save_artist_fields($term_id)
        {
            update_term_meta(sanitize_text_field($term_id), sanitize_key('website'), sanitize_url($_POST['website']));
            update_term_meta(sanitize_text_field($term_id), sanitize_key('bio'), sanitize_text_field($_POST['bio']));


            if (isset($_POST['image_artist_id']) && '' !== $_POST['image_artist_id']) {
                $image = sanitize_text_field($_POST['image_artist_id']);
                add_term_meta(sanitize_text_field($term_id), sanitize_key('image_artist_id'), $image, true);
            }
        }

        /**
         *  Responsible for artists edit term fields
         * 
         * @return void
         */
        function ticktify_artists_edit_term_fields($term, $taxonomy)
        {
            //get meta data value
            $website = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('website'), true);
            $bio = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('bio'), true);
            $image_artist_id = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('image_artist_id'), true);
            ?>
            <h3><?php esc_html_e("Artist Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Website", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="url" name="website" value="<?php echo esc_url($website); ?>">
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Bio", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="text" name="bio" value="<?php echo esc_attr($bio); ?>">
                        </td>
                    <tr class="form-field term-group-wrap">
                        <th scope="row">
                            <label for="image_artist_id">
                                <?php esc_html_e('Image', "ticktify"); ?>
                            </label>
                        </th>
                        <td>

                            <?php $image_artist_id = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('image_artist_id'), true); ?>
                            <input type="hidden" id="image_artist_id" name="image_artist_id" value="<?php echo esc_attr($image_artist_id); ?>">

                            <div id="image_wrapper">
                                <?php if ($image_artist_id) { ?>
                                    <?php echo wp_get_attachment_image(sanitize_text_field($image_artist_id), 'thumbnail'); ?>
                                <?php } ?>
                            </div>

                            <p>
                                <input type="button" class="button button-secondary taxonomy_media_button" id="taxonomy_media_button" name="taxonomy_media_button" value="<?php echo esc_attr(__('Add Image', "ticktify")); ?>">
                                <input type="button" class="button button-secondary taxonomy_media_remove" id="taxonomy_media_remove" name="taxonomy_media_remove" value="<?php echo esc_attr(__('Remove Image', "ticktify")); ?>">
                            </p>

                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        function ticktify_updated_artist_fields($term_id)
        {
            update_term_meta(sanitize_text_field($term_id), sanitize_key('website'), sanitize_url($_POST['website']));
            update_term_meta(sanitize_text_field($term_id), sanitize_key('bio'), sanitize_text_field($_POST['bio']));

            if (isset($_POST['image_artist_id']) && '' !== $_POST['image_artist_id']) {
                $image = sanitize_text_field($_POST['image_artist_id']);
                update_term_meta(sanitize_text_field($term_id), 'image_artist_id', $image);
            } else {
                update_term_meta(sanitize_text_field($term_id), 'image_artist_id', '');
            }
        }

        public function ticktify_artist_admin_enqueue_scripts()
        { ?>
            <script>
                jQuery(document).ready(function($) {
                    function taxonomy_media_upload(button_class) {
                        var custom_media = true,
                            original_attachment = wp.media.editor.send.attachment;
                        $('body').on('click', button_class, function(e) {
                            var button_id = '#' + $(this).attr('id');
                            var send_attachment = wp.media.editor.send.attachment;
                            var button = $(button_id);
                            custom_media = true;
                            wp.media.editor.send.attachment = function(props, attachment) {
                                if (custom_media) {
                                    $('#image_artist_id').val(attachment.id);
                                    $('#image_wrapper').html(
                                        '<img decoding="async" class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
                                    );
                                    $('#image_wrapper .custom_media_image').attr('src', attachment.url).css(
                                        'display', 'block');
                                } else {
                                    return original_attachment.apply(button_id, [props, attachment]);
                                }
                            }
                            wp.media.editor.open(button);
                            return false;
                        });
                    }
                    taxonomy_media_upload('.taxonomy_media_button.button');
                    $('body').on('click', '.taxonomy_media_remove', function() {
                        $('#image_artist_id').val('');
                        $('#image_wrapper').html(
                            '<img decoding="async" class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
                        );
                    });

                    $(document).ajaxComplete(function(event, xhr, settings) {
                        var queryStringArr = settings.data.split('&');
                        if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                            var xml = xhr.responseXML;
                            $response = $(xml).find('term_id').text();
                            if ($response != "") {
                                $('#image_wrapper').html('');
                            }
                        }
                    });
                });
            </script>
            <?php
        }
    }

    new Ticktify_Artist();
// EOF
endif;
