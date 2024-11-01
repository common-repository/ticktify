<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for ZT Sponsor taxonomy.
 *
 * @class Ticktify_Sponsor
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Sponsor')) :
    class Ticktify_Sponsor
    {
        /**
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);

            add_action(TICKTIFY_EVENT_SPONSORS_TAX . '_add_form_fields', [$this, 'ticktify_sponsors_add_term_fields']);
            add_action(TICKTIFY_EVENT_SPONSORS_TAX . '_edit_form_fields', [$this, 'ticktify_sponsors_edit_term_fields'], 10, 2);
            add_action('created_' . TICKTIFY_EVENT_SPONSORS_TAX, [$this, 'ticktify_save_sponsor_fields']);
            add_action('edited_' . TICKTIFY_EVENT_SPONSORS_TAX, [$this, 'ticktify_save_sponsor_fields']);
            add_action('admin_footer', array($this, 'ticktify_artist_admin_enqueue_scripts'));
            add_action('admin_enqueue_scripts', [$this, 'ticktify_load_media']);
        }

        /**
         * Responsible for loaded callback
         *  
         * @return void
         */
        public function ticktify_loaded_callback()
        {
            $this->ticktify_register_taxonomies();
        }

        /**
         * Responsible for register taxonomies
         *  
         * @return void
         */
        function ticktify_register_taxonomies()
        {
            $labels = array(
                'name' => __('Sponsors', "ticktify"),
                'singular_name' => __('Sponsor', "ticktify"),
                'search_items' => __('Search Sponsors', "ticktify"),
                'all_items' => __('All Sponsors', "ticktify"),
                'add_new' => __('Add New', "ticktify"),
                'add_new_item' => __('Add New Sponsor', "ticktify"),
                'parent_item' => __('Parent Sponsor', "ticktify"),
                'parent_item_colon' => __('Parent Sponsors:', "ticktify"),
                'edit_item' => __('Edit Sponsor', "ticktify"),
                'update_item' => __('Update Sponsor', "ticktify"),
                'new_item_name' => __('New Sponsor Name', "ticktify"),
                'menu_name' => __('Sponsors', "ticktify"),
            );
            $args = array(
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'capability_type' => 'sponsors',
                'query_var' => true,
                'rewrite' => array("slug" => "sponsort"),
                'hierarchical' => false,
                'show_in_rest' => true,
            );
            register_taxonomy(TICKTIFY_EVENT_SPONSORS_TAX, [TICKTIFY_EVENT_POST_TYPE], $args);
        }

        /**
         * Responsible for load media
         *  
         * @return void
         */
        function ticktify_load_media()
        {
            wp_enqueue_media();
        }

        /**
         * Responsible for sponsors add term fields
         *  
         * @return void
         */
        function ticktify_sponsors_add_term_fields($taxonomy)
        {
            ?>
            <div class="form-field">
                <label for="rudr_text"><?php esc_html_e("Website Link", "ticktify"); ?></label>
                <input type="url" name="website" id="website" />
            </div>
            <div class="form-field">
                <label for="image_sponsors_id"><?php esc_html_e('Event Sponsors Logo', "ticktify"); ?></label>
                <p>
                    <input type="hidden" id="image_sponsors_id" name="image_sponsors_id" value="">
                <div id="image_sponsors_id-wrapper"> </div>
                <input type="button" class="button button-secondary add_img_button" id="add_img_button" name="add_img_button" value="<?php echo esc_attr('Add Logo', "ticktify"); ?>" />
                <input type="button" class="button button-secondary remove_img" id="remove_img" name="remove_img" value="<?php echo esc_attr('Remove Logo', "ticktify"); ?>" />
                </p>
            </div>
        <?php
        }

        /**
         * Responsible for sponsors edit term fields
         *  
         * @return void
         */
        function ticktify_sponsors_edit_term_fields($term, $taxonomy)
        {
            //get meta data value
            $website = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('website'), true); ?>

            <h3><?php esc_html_e("Event Sponsors Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Website Link", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="url" name="website" value="<?php echo esc_url($website); ?>">
                    </tr>
                    <tr class="form-field">
                        <th scope="row">
                            <label for="image_sponsors_id">
                                <?php esc_html_e('Event Sponsors Logo', "ticktify"); ?>
                            </label>
                        </th>
                        <td>
                            <?php $image_id = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('image_sponsors_id'), true); ?>

                            <input type="hidden" id="image_sponsors_id" name="image_sponsors_id" value="<?php echo esc_attr($image_id); ?>">
                            <div id="image_sponsors_id-wrapper">
                                <?php if ($image_id) { ?>
                                    <?php echo wp_get_attachment_image(sanitize_text_field($image_id), 'thumbnail'); ?>
                                <?php } ?>
                            </div>
                            <p>
                                <input type="button" class="button button-secondary add_img_button" id="add_img_button" name="add_img_button" value="<?php echo esc_attr('Add Logo'); ?>" />
                                <input type="button" class="button button-secondary remove_img" id="remove_img" name="remove_img" value="<?php echo esc_attr('Remove Logo'); ?>" />
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Responsible for save sponsor fields
         *  
         * @return void
         */
        function ticktify_save_sponsor_fields($term_id)
        {
            update_term_meta(sanitize_text_field($term_id), sanitize_key('website'), sanitize_url($_POST['website']));
            if (isset($_POST['image_sponsors_id']) && '' !== sanitize_text_field($_POST['image_sponsors_id'])) {
                update_term_meta(sanitize_text_field($term_id), sanitize_key('image_sponsors_id'), absint(sanitize_text_field($_POST['image_sponsors_id'])));
            } else {
                update_term_meta(sanitize_text_field($term_id), sanitize_key('image_sponsors_id'), '');
            }
        }

        /**
         * Responsible for artist admin enqueue scripts
         *  
         * @return void
         */
        public function ticktify_artist_admin_enqueue_scripts()
        {
            ?>
            <script>
                jQuery(document).ready(function($) {
                    _wpMediaViewsL10n.insertIntoPost = '<?php echo esc_js("Insert"); ?>';

                    function ct_media_upload(button_class) {
                        var _custom_media = true,
                            _orig_send_attachment = wp.media.editor.send.attachment;
                        $('body').on('click', button_class, function(e) {
                            var button_id = '#' + $(this).attr('id');
                            var send_attachment_bkp = wp.media.editor.send.attachment;
                            var button = $(button_id);
                            _custom_media = true;
                            wp.media.editor.send.attachment = function(props, attachment) {
                                if (_custom_media) {
                                    $('#image_sponsors_id').val(attachment.id);
                                    $('#image_sponsors_id-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                    $('#image_sponsors_id-wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
                                } else {
                                    return _orig_send_attachment.apply(button_id, [props, attachment]);
                                }
                            }
                            wp.media.editor.open(button);
                            return false;
                        });
                    }
                    ct_media_upload('.add_img_button.button');
                    $('body').on('click', '.remove_img', function() {
                        $('#image_sponsors_id').val('');
                        $('#image_sponsors_id-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                    });
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        var queryStringArr = settings.data.split('&');
                        if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                            var xml = xhr.responseXML;
                            $response = $(xml).find('term_id').text();
                            if ($response != "") {
                                // Clear the thumb image
                                $('#image_sponsors_id-wrapper').html('');
                            }
                        }
                    });
                });
            </script>
            <?php
        }
    }

    new Ticktify_Sponsor();

// EOF
endif;
