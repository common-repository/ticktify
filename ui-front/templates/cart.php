<?php
/**
 * Event cart page template.
 *
 * @package ticktify-event\ui-front\templates
 * @version 1.0.0
 */

wp_enqueue_script('jquery');
wp_enqueue_script('cart-js');
wp_enqueue_style('cart-css');

wp_enqueue_script('validate_min_js', TICKTIFY_ASSETS_URL . '/js/jquery.validate.min.js');
wp_enqueue_script('methods_min_js', TICKTIFY_ASSETS_URL . '/js/additional-methods.min.js');
require_once TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-cart.php';
$current_user = get_current_user_id();
$results = Ticktify_Cart::ticktify_cart_query();
if(is_user_logged_in()){
if (empty($results)) :
    echo "<div>" . esc_html('Your cart is empty go to ticktify Event', "ticktify") . "</div>";
else :

    if (isset($_REQUEST['error']) && $_REQUEST['error'] == 1) {
        echo '<div><span class="error">' . esc_html("Please add attendees details", "ticktify") . '</span></div>';
    }
    ?>
    <style>
        /* The popup form - hidden by default */
        .form-popup {
            display: none;
            bottom: 0;
            transform: translate(-50%, -50%);
            text-align: center;
            background-color: #e8eae6;
            box-sizing: border-box;
            padding: 10px;
            z-index: 100;
            display: none;
        }

        /* Add a red background color to the cancel button */
        .btn-cancel {
            right: 20px;
            top: 15px;
            background-color: black;
            color: white;
            border-radius: 50%;
            padding: 4px;
        }
    </style>

    <div class="cont_cart">
        <h4><?php esc_html_e('Cart Information', "ticktify"); ?></h4>
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="update_cart_wpnonce" value="<?php echo esc_attr(wp_create_nonce('update_cart')); ?>">

            <table class="form-table" class="tb" cellspacing="10" cellpadding="10">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php esc_html_e('Event', "ticktify"); ?></th>
                        <th><?php esc_html_e('Price', "ticktify"); ?></th>
                        <th><?php esc_html_e('Quantity', "ticktify"); ?></th>
                        <th><?php esc_html_e('Subtotal', "ticktify"); ?></th>
                        <th><?php esc_html_e('Remove', "ticktify"); ?></th>
                        <th><?php esc_html_e('Attendees Details', "ticktify"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //$results = Ticktify_Cart::ticktify_cart_query();
                    $attendees_count = count($results);
                    $count = 0;
                    for ($j = 0; $j < $attendees_count; $j++) {
                        $quantity_item = [];
                        $quantity_item = $results[$j]->quantity;
                        //$date_item = $results[$j]->event_date;
                        ?>
                        <tr id="product">
                            <td>
                                <!-- Event ID -->
                                <input type="hidden" name="customer_id[]" class="attendees_id" value="<?php echo esc_attr($results[$j]->id); ?>">
                            </td>
                            <td>
                                <!-- Event Title -->
                                <?php echo esc_html($results[$j]->event_title); ?>
                                <input type="hidden" name="event_title" value="<?php echo esc_attr($results[$j]->event_title); ?>"><br>
                            </td>
                            <td>
                                <!-- price -->
                                <?php echo esc_html($results[$j]->price); ?>
                                <input type="hidden" name="price" value="<?php echo esc_attr($results[$j]->price) ?>">
                            </td>
                            <td>
                                <!-- quantity -->
                                <?php
                                $total_seats = get_post_meta(sanitize_text_field($results[$j]->event_id), sanitize_key('event_seats_numbers'), true);
                                $allow_max_seats = get_post_meta(sanitize_text_field($results[$j]->event_id), sanitize_key('event_max_numbers'), true);
                                $total_booked_seats = get_post_meta(sanitize_text_field($results[$j]->event_id), sanitize_key('_total_seats_booked'), true);
                                if (empty($total_booked_seats)) {
                                    $total_booked_seats = 0;
                                }
                                $remaining_seat = $total_seats - $total_booked_seats;
                                if ($allow_max_seats >= $remaining_seat) {
                                    $allow_max_seats = $remaining_seat;
                                }
                                ?>
                                <input type="number" max="<?php echo esc_attr($allow_max_seats); ?>" name="quantity[]" min="1" class="quantgrid quantity_<?php echo esc_attr($results[$j]->id); ?>" value="<?php echo esc_attr($results[$j]->quantity); ?>">
                            </td>
                            <td>
                                <!-- subtotal -->
                                <?php $subtotal = ((float) $results[$j]->price * (float) $results[$j]->quantity);
                                echo esc_html($subtotal);
                                ?>
                                <input type="hidden" name="subtotal" value="<?php echo esc_attr($subtotal); ?>">
                                <?php $count = $count + $subtotal; ?>
                            </td>
                            <td>
                                <!-- remove Buttton -->
                                <?php $remove_item = $results[$j]->id;
                                ?>
                                <input type="hidden" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" id="cf_action_url_cancel">
                                <input type="hidden" name="action" value="ticktify_remove_cart">
                                <input type="hidden" name="remove_item" id="remove_item" value="<?php echo esc_attr($remove_item); ?>">
                                <input type="hidden" name="remove_item_wpnonce" value="<?php echo esc_attr(wp_create_nonce('remove_item')); ?>">
                                <button name="delete" id="delete" type="button" value="<?php echo esc_attr($remove_item); ?>">
                                    <?php esc_html_e('Remove', "ticktify"); ?>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="attendeesModalBtn" data-cartId="<?php echo esc_attr($results[$j]->id); ?>" data-actionurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"><?php esc_html_e('Add Attendees', "ticktify"); ?></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <td colspan="4"></td>
                    <td>
                        <p><b><?php esc_html_e('Total', "ticktify"); ?>:<?php echo esc_html($count); ?> </b></p>
                    </td>
                    <td>
                        <!-- update Button -->
                        <button name="action" id="update" type="submit" value="ticktify_update_Cart">
                            <?php esc_html_e('Update', "ticktify"); ?>
                        </button>
                    </td>
                    <td>
                        <!-- Continue Button -->
                        <button name="action" type="submit" value="ticktify_action_checkout_callback">
                            <?php esc_html_e('Continue & Checkout', "ticktify"); ?>
                        </button>
                    </td>
                </tfoot>
            </table>
        </form>
    </div>
    <!-- div containing the popup -->

    <!-- attendees model start -->
    <div id="attendeesModal" class="modal attendeesModal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h4><?php esc_html_e('Attendees info', "ticktify"); ?></h4>
            <span id="attendeesSubmitMsg"></span>
            <form id="attendeesForm" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('add_attendees')); ?>">
                <table id="attendeesTable">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Attendees Name', "ticktify"); ?> </th>
                            <th><?php esc_html_e('Attendees Age', "ticktify"); ?> </th>
                        </tr>
                    </thead>
                    <tbody id="attendeesTbody">
                    <tbody>
                    <tfoot>
                        <tr>
                            <td rowspan="2">
                                <button type="submit" class="attendeesSubmitBtn"><?php esc_html_e('Submit', "ticktify"); ?></button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
    <!-- attendess model end -->
<?php endif;  }else{

    $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
    echo '<div>' . esc_html__('You Are Not Currently Logged In ', 'ticktify') . '
    <a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])) . '">' . esc_html__('Click Here For Login', 'ticktify') . '</a>
</div>';


} 