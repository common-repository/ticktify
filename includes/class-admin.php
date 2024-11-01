<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for user authentication.
 *
 * @class Ticktify_Admin
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Admin')) :
    class Ticktify_Admin
    {
        public $prefix = "ticktify";
        public $settings_tabs;
        public $current_tab = 'general';
        public $default_tab = 'general';
        /**
         * Constructor for the admin class. Loads options and hooks.
         */
        public function __construct()
        {

            //  Initialize tabs
            $this->settings_tabs = array(
                'general' => __('General', "ticktify"),
                'notifications' => __('Notifications', "ticktify"),
                'payments' => __('Payments', "ticktify"),
                'eventCancellation' =>  __('Cancellation Settings', "ticktify")
            );

            add_action('admin_menu', [$this, 'ticktify_admin_menu_callback']);
            add_action('admin_post_nopriv_ticktify_action_email', [$this, 'ticktify_action_email_callback']);

            //admin_post_ hooks
            add_action('admin_post_save_event_settings', [$this, 'ticktify_on_save_event_settings']);

            //general tab 
            add_action('admin_post_save_pagination_settings', [$this, 'ticktify_on_save_pagination_settings']);
            add_action('admin_post_save_payments_settings', [$this, 'ticktify_on_save_payments_settings']);
            add_action('admin_post_save_cancellation_settings', [$this, 'ticktify_save_cancellation_settings']);

            //add user fields
            add_action('show_user_profile', [$this, 'ticktify_extra_user_profile_fields']);
            add_action('edit_user_profile', [$this, 'ticktify_extra_user_profile_fields']);

            //save user data
            add_action('personal_options_update', [$this, 'save_ticktify_extra_user_profile_fields']);
            add_action('edit_user_profile_update', [$this, 'save_ticktify_extra_user_profile_fields']);
        }

        /**
         *  Extra Profile fields are added
         * 
         * @return html
         */
        public function ticktify_extra_user_profile_fields($user)
        {
            $all_meta_for_user = get_user_meta($user->ID);
            ?>
            <h3><?php esc_html_e("Checkout Info", "ticktify"); ?></h3>

            <table class="form-table">
                <tr>
                    <th><label for="address"><?php esc_html_e("Address", "ticktify"); ?></label></th>
                    <td>
                      
                        <input type="text" name="address" id="address" value="<?php echo isset($all_meta_for_user['address'][0]) ? esc_attr($all_meta_for_user['address'][0]) : ''; ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your address.", "ticktify"); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="city"><?php esc_html_e("City", "ticktify"); ?></label></th>
                    <td>
                        <input type="text" name="city" id="city" value="<?php echo isset($all_meta_for_user['city'][0]) ? esc_attr($all_meta_for_user['city'][0]) : '' ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your city.", "ticktify"); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="state"><?php esc_html_e("State", "ticktify"); ?></label></th>
                    <td>
                        <input type="text" name="state" id="state" value="<?php echo isset($all_meta_for_user['state'][0]) ? esc_attr($all_meta_for_user['state'][0]) : '' ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your state.", "ticktify"); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="zip"><?php esc_html_e("Zip Code", "ticktify"); ?></label></th>
                    <td>
                        <input type="text" name="zip_code" id="zip_code" value="<?php echo  isset($all_meta_for_user['zip_code'][0]) ?   esc_attr($all_meta_for_user['zip_code'][0]) : '' ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your zip code.", "ticktify" ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="phone"><?php esc_html_e("Phone", "ticktify"); ?></label></th>
                    <td>
                        <input type="text" name="phone" id="phone" value="<?php echo  isset($all_meta_for_user['phone'][0]) ? esc_attr($all_meta_for_user['phone'][0]) : '' ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your phone number.", "ticktify"); ?></span>
                    </td>
                </tr>
            </table>
        <?php
        }

        /**
         *  save extra profile field data
         * 
         * @return html
         */
        function save_ticktify_extra_user_profile_fields($user_id)
        {
            if (empty($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'update-user_' . $user_id)) {
                return;
            }
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }
            update_user_meta(sanitize_text_field($user_id), sanitize_key('address'), sanitize_text_field($_POST['address']));
            update_user_meta(sanitize_text_field($user_id), sanitize_key('city'), sanitize_text_field($_POST['city']));
            update_user_meta(sanitize_text_field($user_id), sanitize_key('state'), sanitize_text_field($_POST['state']));
            update_user_meta(sanitize_text_field($user_id), sanitize_key('zip_code'), sanitize_text_field($_POST['zip_code']));
            update_user_meta(sanitize_text_field($user_id), sanitize_key('phone'), sanitize_text_field($_POST['phone']));
        }

        /**
         *  Amin menu callback
         * 
         * @return void
         */
        public function ticktify_admin_menu_callback()
        {
            //this is the main item for the menu
            add_submenu_page(
                'edit.php?post_type=' . TICKTIFY_EVENT_POST_TYPE,
                //$parent_slug
                __('Events', "ticktify"),
                //$page_title
                __('Event Settings', "ticktify"),
                //$menu_title
                'manage_options',
                //$capability
                'event_settings',
                //$menu_slug
                [$this, 'ticktify_event_settings'] //$function
            );
            add_submenu_page(
                'edit.php?post_type=' . TICKTIFY_EVENT_POST_TYPE,
                //$parent_slug
                __('Booking', "ticktify"),
                //$page_title
                __('Bookings', "ticktify"),
                //$menu_title
                'manage_options', //$capability
                'edit.php?post_type=' . TICKTIFY_BOOKING_POST_TYPE,
                //$menu_slug
                '',
                2
            );
        }

        /**
         *  Event setting tabs
         * 
         * @return void
         */
        function ticktify_event_settings()
        {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html_e('You do not have sufficient permissions to access this page.', "ticktify"));
            }
            $this->current_tab = (empty($_GET['action'])) ? sanitize_text_field($this->current_tab) : sanitize_text_field($_GET['action']);
            include TICKTIFY_UI_ADMIN_DIR . "{$this->current_tab}.php";
        }

        /**
         *  Save event cancellation callback
         * 
         * @return void
         */
        public function ticktify_save_cancellation_settings()
        {
            if (isset($_POST['_ticktify_nonce_cancellation']) && wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_cancellation']), 'save_cancellation_settings')) {
                if (isset($_POST['ticktify_event_cancellation_hrs'])) {
                    $ticktify_cancellation_settings = array(
                        'ticktify_bookings_user_cancellation' => sanitize_text_field($_POST['ticktify_bookings_user_cancellation']),
                        'ticktify_event_cancellation_hrs' => sanitize_text_field($_POST['ticktify_event_cancellation_hrs'])
                    );
                }
                update_option(sanitize_key('ticktify_cancellation_settings'), $ticktify_cancellation_settings);
            }
            wp_safe_redirect(wp_get_referer());
        }

        /**
         *  Save pagination setting callback
         * 
         * @return void
         */
        public function ticktify_on_save_pagination_settings()
        {
            if (isset($_POST['_ticktify_nonce_pagination']) && wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_pagination']), 'save_pagination_settings')) {
                if (isset($_POST['event_registration'])) {
                    $ticktify_pagination_settings['event_registration'] = array(
                        'first_text' => sanitize_text_field($_POST['event_registration']['first_text']),
                        'last_text' => sanitize_text_field($_POST['event_registration']['last_text']),
                        'email' => sanitize_text_field($_POST['event_registration']['email']),
                        'password' => sanitize_text_field($_POST['event_registration']['password']),
                        'conpassword' => sanitize_text_field($_POST['event_registration']['conpassword'])
                    );
                }
                if (isset($_POST['event_pagination'])) {
                    $ticktify_pagination_settings['event_pagination'] = array(
                        'event_number' => sanitize_text_field($_POST['event_pagination']['event_number']),
                        'color' => sanitize_text_field($_POST['event_pagination']['color']),
                        'bg_color' => sanitize_text_field($_POST['event_pagination']['bg_color']),
                        'hov_color' => sanitize_text_field($_POST['event_pagination']['hov_color']),
                        'hov_bg' => sanitize_text_field($_POST['event_pagination']['hov_bg']),
                    );
                }
                if (isset($_POST['event_map_api'])) {
                    $ticktify_pagination_settings['event_map_api'] = array(
                        '_google_map_api_key' => sanitize_text_field($_POST['event_map_api']['_google_map_api_key']),
                    );
                }
                update_option(sanitize_key('ticktify_pagination_settings'), $ticktify_pagination_settings);
            }
            wp_safe_redirect(wp_get_referer());
        }

        /**
         *  Save event settings callback
         * 
         * @return void
         */
        public function ticktify_on_save_event_settings()
        {
            global $post;

            if (isset($_POST['_ticktify_nonce_notificaton']) && wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_notificaton']), 'save_event_settings')) {
                if (isset($_POST['new_user_to_admin'])) {
                    $ticktify_email_templates['new_user_to_admin'] = array(
                        'to' => sanitize_text_field($_POST['new_user_to_admin']['to']),
                        'subject' => sanitize_text_field($_POST['new_user_to_admin']['subject']),
                        'message' => wp_kses_post($_POST['new_user_to_admin']['message'])
                    );
                }
                if (isset($_POST['booking_to_user'])) {
                    $ticktify_email_templates['booking_to_user'] = array(
                        'to' => sanitize_text_field($_POST['booking_to_user']['to']),
                        'subject' => sanitize_text_field($_POST['booking_to_user']['subject']),
                        'message' => wp_kses_post($_POST['booking_to_user']['message'])
                    );
                }
                if (isset($_POST['booking_to_admin'])) {
                    $ticktify_email_templates['booking_to_admin'] = array(
                        'to' => sanitize_text_field($_POST['booking_to_admin']['to']),
                        'subject' => sanitize_text_field($_POST['booking_to_admin']['subject']),
                        'message' => wp_kses_post($_POST['booking_to_admin']['message'])
                    );
                }
                if (isset($_POST['cancellation_to_user'])) {
                    $ticktify_email_templates['cancellation_to_user'] = array(
                        'to' => sanitize_text_field($_POST['cancellation_to_user']['to']),
                        'subject' => sanitize_text_field($_POST['cancellation_to_user']['subject']),
                        'message' => wp_kses_post($_POST['cancellation_to_user']['message'])
                    );
                }
                if (isset($_POST['cancellation_to_admin'])) {
                    $ticktify_email_templates['cancellation_to_admin'] = array(
                        'to' => sanitize_text_field($_POST['cancellation_to_admin']['to']),
                        'subject' => sanitize_text_field($_POST['cancellation_to_admin']['subject']),
                        'message' => wp_kses_post($_POST['cancellation_to_admin']['message'])
                    );
                }

                update_post_meta(sanitize_text_field($post->ID), sanitize_key('custom_editor_box'), sanitize_text_field($_POST['custom_editor_box']));
                update_option(sanitize_key('ticktify_email_templates'), $ticktify_email_templates);
            }
            wp_safe_redirect(wp_get_referer());
        }

        /**
         *  Save payment settings callback
         * 
         * @return void
         */
        public function ticktify_on_save_payments_settings()
        {

            if (isset($_POST['_ticktify_nonce_payments']) && wp_verify_nonce(sanitize_text_field($_POST['_ticktify_nonce_payments']), 'save_payments_settings')) {
                if (isset($_POST['event_payments'])) {
                    if (!isset($_POST['event_payments']['test_enabled'])) {
                        $ticktify_payments_settings['stripe_details']['test_enabled'] = 0;
                    } else {
                        $ticktify_payments_settings['stripe_details']['test_enabled'] = sanitize_text_field($_POST['event_payments']['test_enabled']);
                    }
                    $ticktify_payments_settings['stripe_details']['live']['publishable_key'] = sanitize_text_field($_POST['event_payments']['live']['publishable_key']);
                    $ticktify_payments_settings['stripe_details']['live']['secret_key'] = sanitize_text_field($_POST['event_payments']['live']['secret_key']);
                    $ticktify_payments_settings['stripe_details']['live']['webhook_secret'] = sanitize_text_field($_POST['event_payments']['live']['webhook_secret']);

                    $ticktify_payments_settings['stripe_details']['test']['publishable_key'] = sanitize_text_field($_POST['event_payments']['test']['publishable_key']);
                    $ticktify_payments_settings['stripe_details']['test']['secret_key'] = sanitize_text_field($_POST['event_payments']['test']['secret_key']);
                    $ticktify_payments_settings['stripe_details']['test']['webhook_secret'] = sanitize_text_field($_POST['event_payments']['test']['webhook_secret']);
                }
                update_option(sanitize_key('ticktify_payments_settings'), $ticktify_payments_settings);
            }

            wp_safe_redirect(wp_get_referer());
        }

        /**
         *  Tabs render
         * 
         * @return html
         */
        function ticktify_render_tabs()
        {
            global $plugin_page;
            if (count($this->settings_tabs) <= 1) {
                return;
            } //Why bother with one tab
            $this->settings_tabs = apply_filters("{$this->prefix}_setting_tabs", $this->settings_tabs);
            $this->current_tab = empty($_GET['action']) ? $this->current_tab : sanitize_text_field($_GET['action']);
            $this->current_tab = in_array($this->current_tab, array_keys($this->settings_tabs)) ? $this->current_tab : $this->default_tab;
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($this->settings_tabs as $tab => $title) {
                $class = ($tab === $this->current_tab) ? 'nav-tab-active' : '';
                printf(
                    '<a class="nav-tab %s" href="%s" >%s</a>',
                    esc_attr($class),
                    esc_url(admin_url("edit.php?post_type=" . TICKTIFY_EVENT_POST_TYPE . "&page=event_settings&action={$tab}")),
                    esc_attr($title)
                );
            }
            echo '</h2>';
        }
        /**
         *  Responsible for metabox settings pagination
         * 
         * @return void
         */
        function ticktify_metabox_settings_pagination()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/settings_pagination.php';
        }

        /**
         *  Responsible for metabox settings registration
         * 
         * @return void
         */
        function ticktify_metabox_settings_registration()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/settings_registration.php';
        }

        /**
         *  Responsible for metabox settings google api
         * 
         * @return void
         */
        function ticktify_metabox_settings_google_api()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/settings_google_api.php';
        }

        /**
         *  Responsible for metabox settings payments
         * 
         * @return void
         */
        function ticktify_metabox_settings_payments()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/settings_payments.php';
        }

        /**
         *  Responsible for metabox cancellation settings
         * 
         * @return void
         */
        function ticktify_metabox_cancellation_settings()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/settings_cancellation.php';
        }

        /**
         *  Responsible for metabox notify new user to admin
         * 
         * @return void
         */
        function ticktify_metabox_notify_new_user_to_admin()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/notify_new_user_to_admin.php';
        }

        /**
         *  Responsible for metabox notify new booking to admin
         * 
         * @return void
         */
        function ticktify_metabox_notify_new_booking_to_admin()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/notify_new_booking_to_admin.php';
        }

        /**
         *  Responsible for metabox notify new booking to user
         * 
         * @return void
         */
        function ticktify_metabox_notify_new_booking_to_user()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/notify_new_booking_to_user.php';
        }

        /**
         *  Responsible for metabox cancelation notification for admin
         * 
         * @return void
         */
        function ticktify_metabox_cancelation_notification_for_admin()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/notify_cancellation_booking_to_admin.php';
        }

        /**
         *  Responsible for metabox cancelation notification for user
         * 
         * @return void
         */
        function ticktify_metabox_cancelation_notification_for_user()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/notify_cancellation_booking_to_user.php';
        }
    }

    new Ticktify_Admin();
// EOF
endif;
