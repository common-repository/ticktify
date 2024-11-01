<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for ZT Venue taxonomy.
 *
 * @class Ticktify_Venue
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Venue')) :
    class Ticktify_Venue
    {
        /**
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);
            add_action(TICKTIFY_EVENT_VENUE_TAX . '_add_form_fields', [$this, 'ticktify_venue_add_term_fields']);
            add_action(TICKTIFY_EVENT_VENUE_TAX . '_edit_form_fields', [$this, 'ticktify_venue_edit_term_fields'], 10, 2);
            add_action('created_' . TICKTIFY_EVENT_VENUE_TAX, [$this, 'ticktify_save_venue_fields']);
            add_action('edited_' . TICKTIFY_EVENT_VENUE_TAX, [$this, 'ticktify_save_venue_fields']);
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
                'name'                  => __('Venue', "ticktify"),
                'singular_name'         => __('Venue', "ticktify"),
                'search_items'          => __('Search Venue', "ticktify"),
                'all_items'             => __('All Venue', "ticktify"),
                'add_new'               => __('Add New', "ticktify"),
                'add_new_item'          => __('Add New Venue', "ticktify"),
                'parent_item'           => __('Parent Venue', "ticktify"),
                'parent_item_colon'     => __('Parent Venue:', "ticktify"),
                'edit_item'             => __('Edit Venue', "ticktify"),
                'update_item'           => __('Update Venue', "ticktify"),
                'new_item_name'         => __('New Venue Name', "ticktify"),
                'menu_name'             => __('Venue', "ticktify"),
            );
            $args = array(
                'labels'             => $labels,
                'show_ui'            => true,
                'show_admin_column'  => true,
                'capability_type'    => 'venues',
                'query_var'          => true,
                'rewrite'            => array("slug" => "venue"),
                'hierarchical'       => false,
                'show_in_rest'       => true,
            );
            register_taxonomy(TICKTIFY_EVENT_VENUE_TAX, [TICKTIFY_EVENT_POST_TYPE], $args);
        }

        /**
         * Responsible for venue add term fields
         *  
         * @return void
         */
        function ticktify_venue_add_term_fields($taxonomy)
        {
            ?>
            <h3><?php esc_html_e("Venue Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Address", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="text" name="address" value="">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Contact Number", "ticktify"); ?></th>
                        <td>
                        <input class="large-text" type="text" name="contact_number" value="" maxlength="10" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Email", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="email" name="email" value="" >
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        /**
         * Responsible for venue edit term fields
         *  
         * @return void
         */
        function ticktify_venue_edit_term_fields($term, $taxonomy)
        {
            // get meta data value
            $address = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('address'), true);
            $contact_number = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('contact_number'), true);
            $email = get_term_meta(sanitize_text_field($term->term_id), sanitize_key('email'), true);
            ?>
            <h3><?php esc_html_e("Venue Details", "ticktify"); ?></h3>
            <hr>
            <table class="form-table">
                <thead></thead>
                <tfoot></tfoot>
                <tbody>
                    <tr>
                        <th><?php esc_html_e("Address", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="text" name="address" value="<?php echo esc_attr($address); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Contact Number", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="text" name="contact_number" maxlength="10" value="<?php echo esc_attr($contact_number); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Email", "ticktify"); ?></th>
                        <td>
                            <input class="large-text" type="email" name="email" value="<?php echo esc_attr($email); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Responsible for save venue fields
         *  
         * @return void
         */
        function ticktify_save_venue_fields($term_id)
        {
            update_term_meta(sanitize_text_field($term_id), sanitize_key('address'), isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '');
            update_term_meta(sanitize_text_field($term_id), sanitize_key('contact_number'), isset($_POST['contact_number']) ? sanitize_text_field(absint($_POST['contact_number'])) : '');
            update_term_meta(sanitize_text_field($term_id), sanitize_key('email'), isset($_POST['email']) ? sanitize_email($_POST['email']) : '');
            
        }
    }
    new Ticktify_Venue();
// EOF
endif;
