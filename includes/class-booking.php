<?php
defined('ABSPATH') or die("you do not have access to this page!");
/**
 * Contains action hooks and functions for ticktify Event post type.
 *
 * @class Ticktify_Booking
 * @package ticktify-event\includes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Booking')) :
    class Ticktify_Booking
    {
        /**.TICKTIFY_EVENT_POST_TYPE
         * Constructor for the event class. Loads options and hooks.
         */
        public function __construct()
        {
            add_action('init', [$this, 'ticktify_loaded_callback']);
            add_action('add_meta_boxes', [$this, 'ticktify_event_booking_register_meta_boxes']);
            add_filter('post_row_actions', [$this, 'ticktify_remove_post_action'], 10, 2);
            add_filter('manage_' . TICKTIFY_BOOKING_POST_TYPE . '_posts_columns', [$this, 'ticktify_booking_table_columns_field']);
            add_action('manage_' . TICKTIFY_BOOKING_POST_TYPE . '_posts_custom_column', [$this, 'ticktify_booking_table_columns_data'], 10, 2);
            add_filter('manage_edit-' . TICKTIFY_BOOKING_POST_TYPE . '_sortable_columns', [$this, 'ticktify_booking_table_columns_sortable']);
            add_action('wp_ajax_nopriv_ticktify_update_booking_status', [$this, 'ticktify_update_booking_status_callback']);
            add_action('wp_ajax_ticktify_update_booking_status', [$this, 'ticktify_update_booking_status_callback']);
        }

        public function ticktify_loaded_callback()
        {
            $this->ticktify_register_post_types();
        }

        private function ticktify_register_post_types()
        {
            $labels = array(
                'name'                  => __('Event Bookings', "ticktify"),
                'singular_name'         => __('Event Booking', "ticktify"),
                'menu_name'             => __('Event Booking', "ticktify"),
                'add_new'               => __('Add New', "ticktify"),
                'add_new_item'          => __('Add New Booking', "ticktify"),
                'new_item'              => __('New Booking', "ticktify"),
                'edit_item'             => __('Edit Booking', "ticktify"),
                'view_item'             => __('View Booking', "ticktify"),
                'all_items'             => __('All Booking', "ticktify"),
                'search_items'          => __('Search Booking', "ticktify"),
                'parent_item_colon'     => __('Parent Booking:', "ticktify"),
                'not_found'             => __('No Booking found.', "ticktify"),
                'not_found_in_trash'    => __('No Booking found in Trash.', "ticktify"),
                'featured_image'        => __('Booking Cover Image', "ticktify"),
                'set_featured_image'    => __('Set cover image', "ticktify"),
                'remove_featured_image' => __('Remove cover image', "ticktify"),
                'use_featured_image'    => __('Use as cover image', "ticktify"),
                'archives'              => __('Booking archives', "ticktify"),
                'insert_into_item'      => __('Insert into Booking', "ticktify"),
                'uploaded_to_this_item' => __('Uploaded to this Booking', "ticktify"),
                'filter_items_list'     => __('Filter Booking list', "ticktify"),
                'items_list_navigation' => __('Booking list navigation', "ticktify"),
                'items_list'            => __('Booking list', "ticktify"),
            );
            $args = array(
                'labels'             => $labels,
                'description'        => __('Booking', "ticktify"),
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => true,
                'rewrite'            => array('slug' => TICKTIFY_BOOKING_POST_TYPE),
                'capability_type'    => 'post',
                'capabilities'       => array(
                    'create_posts'   => false,
                ),
                'map_meta_cap'       => true,
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => 20,
                'supports'           => array(''),
                'taxonomies'         => array('en_event', 'en_categories'),
                'show_in_rest'       => true
            );

            register_post_type(TICKTIFY_BOOKING_POST_TYPE, $args);
        }

        // Booking metabox
        public function ticktify_event_booking_register_meta_boxes()
        {
            add_meta_box('ticktify_event_bookings', __('Booking Details', "ticktify"), [$this, 'ticktify_booking_details_metabox_callback'], TICKTIFY_BOOKING_POST_TYPE);
            add_meta_box('ticktify_event_bookings_event', __('Booking Items', "ticktify"), [$this, 'ticktify_booking_items_metabox_event_callback'], TICKTIFY_BOOKING_POST_TYPE);
        }

        //metabox callback
        public function ticktify_booking_details_metabox_callback($post, $post_id)
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/booking_details.php';
        }

        function ticktify_booking_items_metabox_event_callback($post, $post_id)
        {
            require_once TICKTIFY_UI_ADMIN_DIR . 'metabox/booking_items.php';
        }

        function ticktify_remove_post_action($actions, $post)
        {
            if ($post->post_type == TICKTIFY_BOOKING_POST_TYPE) {
                unset($actions['inline hide-if-no-js']);
                unset($actions['edit']);
                unset($actions['trash']);
                unset($actions['view']);
            }
            return $actions;
        }

        /**
         * Add and unset booking post type columns
         *  
         * @return column fields
         */
        function ticktify_booking_table_columns_field($columns)
        {
            // unset( $columns['date'] ); 
            // unset( $columns['title'] ); 
            $columns['title'] = __('Booking', "ticktify");
            $columns['customer'] = __('Customer', "ticktify");
            $columns['total'] = __('Total Amount', "ticktify");
            $columns['booking_date'] = __('Booking Date', "ticktify");

            return $columns;
        }

        /**
         * display custom column field data
         *  
         * @return field data value
         */
        function ticktify_booking_table_columns_data($column, $post_id)
        {
            if ($column == 'customer') {
                $customer_id = get_post_meta(sanitize_text_field($post_id), sanitize_key('_customer_id'), true);
                echo esc_html(get_user_meta(sanitize_text_field($customer_id), sanitize_key('first_name'), true)) . ' ' . esc_html(get_user_meta(sanitize_text_field($customer_id), sanitize_key('last_name'), true));
            }
            if ($column == 'total') {
                $transactionDetails =  Ticktify_Transaction::ticktify_get_transactions($post_id);
                echo esc_attr((!empty($transactionDetails->paid_amount)) ? $transactionDetails->paid_amount : '0.00');
            }

            if ($column == 'booking_date') {
                echo esc_attr(get_the_date('Y/m/d  H:i A', $post_id));
            }
        }

        /**
         * short by custom field
         *  
         * @return short column field
         */
        function ticktify_booking_table_columns_sortable($columns)
        {
            $columns['customer'] = 'customer';
            //$columns['total'] = 'total';
            $columns['booking_date'] = 'booking_date';
            return $columns;
        }
        /**
         * Update booking status
         *  
         * @return status true/false
         */
        function ticktify_update_booking_status_callback()
        {
            if (isset($_POST['action']) && !empty($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'event_cancel')) {
                $eventsArray = get_post_meta(sanitize_text_field($_POST['bookingId']), sanitize_key('_events'), true);

                foreach ($eventsArray as $key => $event_val) {
                    foreach ($event_val as $skey => $val) {
                        if (($event_val['event_id'] == sanitize_text_field($_POST['eventId'])) && $skey == "status") {
                            $subArr[$skey] = sanitize_text_field($_POST['bookingEventStatus']);
                            $booked_seats = get_post_meta(sanitize_text_field($event_val['event_id']), sanitize_key("_total_seats_booked"), true);
                            $total_seats = intval($booked_seats) - intval($event_val['quantity']);
                            update_post_meta(sanitize_text_field($event_val['event_id']), sanitize_key("_total_seats_booked"), sanitize_text_field($total_seats));
                        } else {
                            $subArr[$skey] = $val;
                        }
                    }
                    $eventsArrayNew[] = $subArr;
                }

                if (!empty($eventsArrayNew)) {
                    $statusUpdate = update_post_meta(sanitize_text_field($_POST['bookingId']), sanitize_key('_events'), $eventsArrayNew);
                    if ($statusUpdate) {
                        do_action('ticktify_after_booking_event_cancel', sanitize_text_field($_POST['bookingId']), $eventsArrayNew);
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'bookingStatus' => ucfirst(
                                    sanitize_text_field($_POST['bookingEventStatus'])
                                )
                            )
                        );
                    } else {
                        echo json_encode(array('status' => 'error'));
                    }
                }
            }
            wp_die();
        }
    }

    new Ticktify_Booking();
// EOF
endif;
