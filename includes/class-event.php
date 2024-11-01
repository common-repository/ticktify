<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for Ticktify Event post type.
 *
 * @class Ticktify_Event
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Event')) :
    class Ticktify_Event
    {
        /**
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);
            add_action('add_meta_boxes', [$this, 'ticktify_event_register_meta_boxes']);
            add_action('save_post_' . TICKTIFY_EVENT_POST_TYPE, [$this, 'ticktify_script_save_custom_box'], 10, 2);
            add_action('admin_enqueue_scripts', [$this, 'ticktify_event_admin_enqueue_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'ticktify_event_enqueue_scripts']);
            add_shortcode('events-list', [$this, 'ticktify_event_shortcodes']);
            add_filter('template_include', [$this, 'ticktify_event_detail_template'], 99);
            add_filter('post_row_actions', [$this, 'ticktify_remove_row_actions_post'], 10, 2);
            add_action('admin_head', [$this, 'ticktify_restrict_post_deletion']);
            add_action('add_meta_boxes', [$this, 'ticktify_my_add_meta_boxes']);
            add_filter('manage_edit-ticktify_venue_columns', [$this, 'ticktify_modify_tag_columns']);
            add_filter('manage_edit-ticktify_organizer_columns', [$this, 'ticktify_modify_tag_columns']);
            add_filter('manage_edit-ticktify_artist_columns', [$this, 'ticktify_modify_tag_columns']);
            add_filter('manage_edit-ticktify_sponsors_columns', [$this, 'ticktify_modify_tag_columns']);
        }

        /**
         * Loaded callback
         *  
         * @return void
         */
        public function ticktify_loaded_callback()
        {
            $this->ticktify_register_post_types();
        }

        /**
         * Responsible for register post types
         *  
         * @return void
         */
        private function ticktify_register_post_types()
        {
            $labels = array(
                'name' => __('Events', "ticktify"),
                'singular_name' => __('Event', "ticktify"),
                'menu_name' => __('Events', "ticktify"),
                'name_admin_bar' => __(' Event', "ticktify"),
                'add_new' => __('Add New', "ticktify"),
                'add_new_item' => __('Add New Event', "ticktify"),
                'new_item' => __('New Event', "ticktify"),
                'edit_item' => __('Edit Event', "ticktify"),
                'view_item' => __('View Event', "ticktify"),
                'all_items' => __('All Events', "ticktify"),
                'search_items' => __('Search Events', "ticktify"),
                'parent_item_colon' => __('Parent Events:', "ticktify"),
                'not_found' => __('No Events found.', "ticktify"),
                'not_found_in_trash' => __('No Events found in Trash.', "ticktify"),
                'featured_image' => __('Event Cover Image', "ticktify"),
                'set_featured_image' => __('Set cover image', "ticktify"),
                'remove_featured_image' => __('Remove cover image', "ticktify"),
                'use_featured_image' => __('Use as cover image', "ticktify"),
                'archives' => __('Event archives', "ticktify"),
                'insert_into_item' => __('Insert into Event', "ticktify"),
                'uploaded_to_this_item' => __('Uploaded to this Event', "ticktify"),
                'filter_items_list' => __('Filter Events list', "ticktify"),
                'items_list_navigation' => __('Events list navigation', "ticktify"),
                'items_list' => __('Events list', "ticktify"),
            );
            $args = array(
                'labels' => $labels,
                'description' => __('Events', "ticktify"),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => TICKTIFY_EVENT_POST_TYPE),
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => 20,
                //'show_in_rest' => true,
                'menu_icon'  => 'dashicons-calendar-alt',
                'supports' => array('title', 'editor', 'block-editor'),
                'taxonomies' => array('en_event', 'en_categories'),
                'map_meta_cap' => true,
            );
            register_post_type(TICKTIFY_EVENT_POST_TYPE, $args);
        }

        /**
         * Responsible for event register meta boxes
         *  
         * @return void
         */
        public function ticktify_event_register_meta_boxes()
        {
            add_meta_box('ticktify_event_plugin', __('Event', "ticktify"), [$this, 'ticktify_event_metabox_callback'], TICKTIFY_EVENT_POST_TYPE);
            //event gallery metabox
            add_meta_box('ticktify_add_event', __('The Event Gallery', "ticktify"), [$this, 'ticktify_event_metabox_event_callback'], TICKTIFY_EVENT_POST_TYPE);

            //event cover image metabox
            add_meta_box('ticktify_event_image', __('Event Cover Image/Video ', "ticktify"), [$this, 'ticktify_event_metabox_cover_image'], TICKTIFY_EVENT_POST_TYPE);
        }

        /**
         * Responsible for my add meta boxes
         *  
         * @return void
         */
        function ticktify_my_add_meta_boxes()
        {
            remove_meta_box('slugdiv', TICKTIFY_EVENT_POST_TYPE, 'normal');
        }

        /**
         * Responsible for event post redirect location filter
         *  
         * @return void
         */
        function ticktify_event_post_redirect_location_filter($location)
        {
            remove_filter('redirect_post_location', __FUNCTION__, 98);
            $location = add_query_arg('message', 99, $location);
            return $location;
        }

        /**
         * Responsible for event post updated messages filter
         *  
         * @return void
         */
        function ticktify_event_post_updated_messages_filter($messages)
        {
            $wp_error = get_option(sanitize_key('ticktify_event_post_error'), true);
            $messages['post'][99] = $wp_error['title'];
            return $messages;
        }

        /**
         * Responsible for remove row actions post
         *  
         * @return void
         */
        function ticktify_remove_row_actions_post($actions, $post)
        {
            if ($post->post_type === 'ticktify_event') {
                unset($actions['clone']);
                unset($actions['trash']);
            }
            return $actions;
        }

        /**
         * Responsible for restrict post deletion
         *  
         * @return void
         */
        function ticktify_restrict_post_deletion()
        {
            $current_screen = get_current_screen();
            // Hides the "Move to Trash" link on the post edit page.
            if ('post' === $current_screen->base && 'ticktify_event' === $current_screen->post_type) :
                ?>
                <style>
                    #delete-action {
                        display: none;
                    }
                </style>
                <?php
            endif;
        }

        /**
         * Responsible for script save custom box
         *  
         * @return void
         */
        function ticktify_script_save_custom_box($post_id, $post)
        {
            if (isset($_POST['event_gallery_img'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('event_gallery_img'), sanitize_text_field($_POST['event_gallery_img']));
            }
            if (isset($_POST['event_gallery_imgs'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('event_gallery_imgs'), sanitize_text_field($_POST['event_gallery_imgs']));
            }

            if (array_key_exists('search-from-date', $_POST)) {
                $custom_date_meta_value = [sanitize_text_field($_POST['search-from-date'])];
                if (array_key_exists('end-to-date', $_POST)) {
                    $custom_date_meta_value = [sanitize_text_field($_POST['search-from-date']), sanitize_text_field($_POST['end-to-date'])];
                }
                update_post_meta(sanitize_text_field($post_id), sanitize_key('_custom_date_meta_key'), $custom_date_meta_value);
            }
            if (array_key_exists('search-from-time', $_POST)) {
                $custom_time_meta_value = [sanitize_text_field($_POST['search-from-time'])];
                if (array_key_exists('end-to-time', $_POST)) {
                    $custom_time_meta_value = [sanitize_text_field($_POST['search-from-time']), sanitize_text_field($_POST['end-to-time'])];
                }
                update_post_meta(sanitize_text_field($post_id), sanitize_key('_custom_time_meta_key'), $custom_time_meta_value);
            }
            //Paid/free event field
            $event_paid_value = sanitize_text_field((isset($_POST['paid_event']) ? $_POST['paid_event'] : ''));
            update_post_meta(sanitize_text_field($post_id), sanitize_key('event_select'), $event_paid_value);

            //Number of seats field
            if (isset($_POST['seat_number'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('event_seats_numbers'), sanitize_text_field($_POST['seat_number']));
            }
            // Maximum number of seats
            if (isset($_POST['maximum_seat'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('event_max_numbers'), sanitize_text_field($_POST['maximum_seat']));
            }
            //price per page 
            if (isset($_POST['price_seat'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('event_price_seat'), sanitize_text_field($_POST['price_seat']));
            }
            if (!empty($_POST['video_check'])) {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('my_image_for_post'), '');
                update_post_meta(sanitize_text_field($post_id), sanitize_key('video_check'), sanitize_text_field($_POST['video_check']));
                if (isset($_POST['video_URL'])) {
                    update_post_meta(sanitize_text_field($post_id), sanitize_key('video_for_post'), sanitize_url($_POST['video_URL']));
                }
            } else {
                update_post_meta(sanitize_text_field($post_id), sanitize_key('video_check'), '');
                update_post_meta(sanitize_text_field($post_id), sanitize_key('video_for_post'), '');
                if (isset($_POST['upload_image'])) {
                    update_post_meta(sanitize_text_field($post_id), sanitize_key('my_image_for_post'), sanitize_url($_POST['upload_image']));
                }
            }
        }

        /**
         * Responsible for event admin enqueue scripts
         *  
         * @return void
         */
        function ticktify_event_admin_enqueue_scripts($hook)
        {
            if ($hook !== 'post-new.php' && $hook !== 'post.php') {
                return;
            }
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_media();
            // Load the datepicker script (pre-registered in WordPress).
            wp_enqueue_script('jquery-ui-datepicker');
            // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
            wp_register_style('jquery-ui', TICKTIFY_ASSETS_URL . '/build/smoothness/jquery-ui.css');
            wp_enqueue_style('jquery-ui');
            wp_register_script('wp-jquery-times-picker', TICKTIFY_ASSETS_URL . '/build/jquery.timepicker.min.js');
            wp_enqueue_script('wp-jquery-times-picker');
            wp_register_style('jquery-time-picker', TICKTIFY_ASSETS_URL . '/build/jquery.timepicker.min.css');
            wp_enqueue_style('jquery-time-picker');
        }

        /**
         * Responsible for event detail template
         *  
         * @return void
         */
        function ticktify_event_detail_template($template)
        {
            if (is_singular(TICKTIFY_EVENT_POST_TYPE)) {
                $template = ticktify_get_template('single-event.php');
            }
            return $template;
        }

        /**
         * Responsible for event enqueue scripts
         *  
         * @return void
         */
        function ticktify_event_enqueue_scripts()
        {
            wp_register_style('style-css', TICKTIFY_ASSETS_URL . 'css/style.css');
            wp_register_style('cart-css', TICKTIFY_ASSETS_URL . 'css/cart.css');
            wp_register_style('profile-css', TICKTIFY_ASSETS_URL . 'css/profile.css');
            wp_register_style('slick-css', TICKTIFY_ASSETS_URL . 'css/slick.css');
            wp_register_style('slick-theme-css', TICKTIFY_ASSETS_URL . 'css/slick-theme.css');
            wp_register_script('slick-min-js', TICKTIFY_ASSETS_URL . 'js/slick.min.js', array('jquery'), '1.8.0', true);
            wp_register_script('custom-js', TICKTIFY_ASSETS_URL . 'js/custom.js', array('slick-min-js'), '0.0.1', true);
            wp_register_script('cart-js', TICKTIFY_ASSETS_URL . 'js/cart.js', array(), '1.0.0', true);
            wp_register_script('checkout-js', TICKTIFY_ASSETS_URL . 'js/checkout.js', array(), '1.0.0', true);
            wp_register_script('profile-js', TICKTIFY_ASSETS_URL . 'js/profile.js', array(), '0.0.1', true);
            wp_register_script('stripe-js', TICKTIFY_ASSETS_URL . 'js/stripe.js', array(), '1.0.0', true);
            wp_register_script('cpt-js', TICKTIFY_ASSETS_URL . 'js/cpt.js', array(), '1.0.0', true);
            wp_register_script('booking-js', TICKTIFY_ASSETS_URL . 'js/booking.js', array(), '1.0.0', true);
            if (is_singular(TICKTIFY_EVENT_POST_TYPE)) {
                wp_enqueue_style('style-css');
                wp_enqueue_style('profile-css');
                wp_enqueue_style('cart-css');
                wp_enqueue_style('slick-css');
                wp_enqueue_style('slick-theme-css');
                wp_enqueue_style('profile-css');

                wp_enqueue_script('slick-min-js');
                wp_enqueue_script('custom-js');
                wp_enqueue_script('cart-js');
                wp_enqueue_script('checkout-js');
                wp_enqueue_script('profile-js');
                wp_enqueue_script('cpt-js');
            }
        }

        /**
         * Responsible for event shortcodes
         *  
         * @return void
         */
        function ticktify_event_shortcodes($atts)
        {
            ob_start();
            global $wpdb;
            global $paged;
            $ticktify_pagination_settings = get_option(sanitize_key('ticktify_pagination_settings'));
            $event_number = $ticktify_pagination_settings['event_pagination']['event_number'] ? $ticktify_pagination_settings['event_pagination']['event_number'] : '';
            $color = $ticktify_pagination_settings['event_pagination']['color'] ? $ticktify_pagination_settings['event_pagination']['color'] : '';
            $bg_color = $ticktify_pagination_settings['event_pagination']['bg_color'] ? $ticktify_pagination_settings['event_pagination']['bg_color'] : '';
            $hov_color = $ticktify_pagination_settings['event_pagination']['hov_color'] ? $ticktify_pagination_settings['event_pagination']['hov_color'] : '';
            $hov_bg = $ticktify_pagination_settings['event_pagination']['hov_bg'] ? $ticktify_pagination_settings['event_pagination']['hov_bg'] : '';
            $ourCurrentPage = get_query_var('paged');
            $query_count_tatal = new WP_Query(
                array(
                    'post_type' => TICKTIFY_EVENT_POST_TYPE,
                    'posts_per_page' => -1,
                    'order' => 'ASC',
                    'orderby' => 'title',
                )
            );
            $query = new WP_Query(
                array(
                    'post_type' => TICKTIFY_EVENT_POST_TYPE,
                    'posts_per_page' => sanitize_text_field($event_number),
                    'paged' => sanitize_text_field($ourCurrentPage),
                    'order' => 'ASC',
                    'orderby' => 'title',
                )
            );
            if ($query->have_posts()) {
                do_action('ticktify_events-list');
                while ($query->have_posts()) :
                    $query->the_post();
                    include TICKTIFY_UI_FRONT_DIR . 'events-list.php';
                endwhile;
                do_action('ticktify_events_list');
                ?>
                <style>
                    .btn {
                        background-color: brown;
                        border-radius: 5px;
                        font-size: large;
                        color: azure;
                    }

                    .pagination li {
                        padding: 15px;
                        text-decoration: none;
                        border-radius: 2px;
                        border: 2px solid;
                        color: <?php echo esc_attr($color); ?>;
                        background-color: <?php echo esc_attr($bg_color); ?>;
                    }

                    .pagination li:hover {
                        color: <?php echo esc_attr($hov_color); ?>;
                        background-color: <?php echo esc_attr($hov_bg); ?>;
                    }

                    ul.pagination {
                        padding: 0;
                        text-align: center;
                        margin-top: 10%;
                    }

                    ul.pagination li {
                        display: inline-block;
                    }

                    .b-pagination-outer {
                        width: 70%;
                        margin: 10 auto;
                        text-align: center;
                    }

                    .pagination li a {
                        padding: 15px;
                        text-decoration: none;
                        border-radius: 2px;
                        border: 1px solid;


                    }
                </style>
                <div class="b-pagination-outer">
                    <?php if ($query_count_tatal->post_count > $event_number) { ?>
                        <ul class="pagination">
                            <li class="page-item page-link">
                                <?php
                                echo paginate_links(
                                    array(
                                        'total' => sanitize_text_field($query->max_num_pages),
                                    )
                                );
                                do_action('ticktify_events_list');
                                ?>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
                <?php
                wp_reset_postdata();
            } else {
                esc_html_e('Sorry, no posts were found.', "ticktify");
            }
            $event_list_html = ob_get_clean();
            return $event_list_html;
        }
        
        /**
         * Responsible for event metabox callback
         *  
         * @return void
         */
        function ticktify_event_metabox_callback()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/custom_event.php';
        }

        /**
         * Responsible for event metabox event callback
         *  
         * @return void
         */
        function ticktify_event_metabox_event_callback()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/event_gallery.php';
        }

        /**
         * Responsible for event metabox cover image
         *  
         * @return void
         */
        function ticktify_event_metabox_cover_image()
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/event_cover_image.php';
        }

        /**
         * Responsible for modify tag columns
         *  
         * @return void
         */
        function ticktify_modify_tag_columns($columns)
        {
            unset($columns['description']);
            return $columns;
        }
    }
    new Ticktify_Event();
// EOF
endif;
