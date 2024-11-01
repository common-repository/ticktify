<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for ZT Organizer taxonomy.
 *
 * @class Ticktify_Organizer
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Organizer')) :
    class Ticktify_Organizer
    {
        /**
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);
            add_action(TICKTIFY_EVENT_ORGANIZER_TAX . '_add_form_fields', [$this, 'ticktify_organizers_add_term_fields']);
            add_action(TICKTIFY_EVENT_ORGANIZER_TAX . '_edit_form_fields', [$this, 'ticktify_organizers_edit_term_fields'], 10, 2);
            add_action('created_' . TICKTIFY_EVENT_ORGANIZER_TAX, [$this, 'ticktify_save_organizer_fields']);
            add_action('edited_' . TICKTIFY_EVENT_ORGANIZER_TAX, [$this, 'ticktify_save_organizer_fields']);
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
                'name'                  => __('Organizers', "ticktify"),
                'singular_name'         => __('Organizer', "ticktify"),
                'search_items'          => __('Search Organizers', "ticktify"),
                'all_items'             => __('All Organizers', "ticktify"),
                'add_new'               => __('Add New', "ticktify"),
                'add_new_item'          => __('Add New Organizer', "ticktify"),
                'parent_item'           => __('Parent Organizer', "ticktify"),
                'parent_item_colon'     => __('Parent Organizers:', "ticktify"),
                'edit_item'             => __('Edit Organizer', "ticktify"),
                'update_item'           => __('Update Organizer', "ticktify"),
                'new_item_name'         => __('New Organizer Name', "ticktify"),
                'menu_name'             => __('Organizers', "ticktify"),
            );
            $args = array(
                'labels'             => $labels,
                'show_ui'            => true,
                'show_admin_column'  => true,
                'capability_type'    => 'organizers',
                'query_var'          => true,
                'rewrite'            => array(
                    "slug" => "organizer",
                    'with_front'          => false
                ),
                'hierarchical'       => false,
                'show_in_rest'       => true,
            );
            register_taxonomy(TICKTIFY_EVENT_ORGANIZER_TAX, [TICKTIFY_EVENT_POST_TYPE], $args);
        }

        /**
         * Responsible for organizers add term fields
         *  
         * @return void
         */
        function ticktify_organizers_add_term_fields($taxonomy)
        {
            ?>
            <h3><?php esc_html_e("Organizer Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Contact Number", "ticktify"); ?></th>
                        <td>
                            <input type="tel" name="contact_number" value="" maxlength="10" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Email", "ticktify"); ?></th>
                        <td>
                            <input type="email" name="email" value="">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Website", "ticktify"); ?></th>
                        <td>
                            <input type="url" name="website" value="">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Responsible for organizers edit term fields
         *  
         * @return void
         */
        function ticktify_organizers_edit_term_fields($term, $taxonomy)
        {
            // get meta data value
            $contact_number = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('contact_number'), true);
            $email = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('email'), true);
            $website = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('website'), true);
            ?>
            <h3><?php esc_html_e("Organizer Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Contact Number", "ticktify"); ?></th>
                        <td>
                            <input type="tel" name="contact_number" value="<?php echo esc_attr($contact_number); ?>">
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Email", "ticktify"); ?></th>
                        <td>
                            <input type="email" name="email" value="<?php echo esc_attr($email); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Website", "ticktify"); ?></th>
                        <td>
                            <input type="url" name="website" value="<?php echo esc_url($website); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Responsible for save organizer fields
         *  
         * @return void
         */
        function ticktify_save_organizer_fields($term_id)
        {
            update_term_meta(sanitize_text_field($term_id), sanitize_key('contact_number'), sanitize_text_field($_POST['contact_number']));
            update_term_meta(sanitize_text_field($term_id), sanitize_key('email'), sanitize_email($_POST['email']));
            update_term_meta(sanitize_text_field($term_id), sanitize_key('website'), sanitize_url($_POST['website']));
        }
    }
    new Ticktify_Organizer();

// EOF
endif;
