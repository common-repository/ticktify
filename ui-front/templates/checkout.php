<?php
/**
 * Event checkout page template.
 *
 * @package ticktify-event\ui-front\templates
 * @version 1.0.0
 */
?>
<script src="https://js.stripe.com/v3/"></script>
<style>
    table,
    td,
    th {
        border: 1px solid #ddd;
        text-align: left;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 15px;
    }
</style>
<?php
include_once TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-checkout.php';
list($api_key, $api_publishable_key) = get_stripe_keys();
wp_enqueue_script('jquery');
wp_enqueue_script('checkout-js');
wp_enqueue_script('stripe-js');
wp_enqueue_script('validate_min_js', TICKTIFY_ASSETS_URL . '/js/jquery.validate.min.js');
wp_enqueue_script('methods_min_js', TICKTIFY_ASSETS_URL . '/js/additional-methods.min.js');

$current_user = get_current_user_id();
$f_name = get_user_meta(sanitize_text_field($current_user), sanitize_key('first_name'), true);
$l_name = get_user_meta(sanitize_text_field($current_user), sanitize_key('last_name'), true);
$address = get_user_meta(sanitize_text_field($current_user), sanitize_key('address'), true);
$city = get_user_meta(sanitize_text_field($current_user), sanitize_key('city'), true);
$state = get_user_meta(sanitize_text_field($current_user), sanitize_key('state'), true);
$zip_code = get_user_meta(sanitize_text_field($current_user), sanitize_key('zip_code'), true);
$phone = get_user_meta(sanitize_text_field($current_user), sanitize_key('phone'), true);
$user_email = get_user_meta(sanitize_text_field($current_user), sanitize_key('user_email'), true);

$results = Ticktify_Cart::ticktify_cart_query();
if(is_user_logged_in()){
if (empty($results)) :
    echo "<div>" . esc_html('Your cart is empty go to Ticktify Event', "ticktify") . "</div>";
else :

?>
    <style>
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
            position: fixed;
            left: 50%;
            top: 50%;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .hidden {
            display: none;
        }

        .box_spinner {
            opacity: 0;
        }

        body {
            margin: 0;
            background-color: hsl(0, 0%, 98%);
            color: #333;
            font: 100% / normal sans-serif;
        }

        .receipt-box {
            margin: 0 auto;
            padding: 4rem 0;
            width: 90%;
            max-width: 60rem;
        }

        #billingInfoForm {
            box-sizing: border-box;
            padding: 2rem;
            border-radius: 1rem;
            background-color: hsl(0, 0%, 100%);
            border: 4px solid hsl(0, 0%, 90%);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    </style>
    <div id="spinner" class="loader hidden"></div>
    <div class="receipt-box">
        <h3><?php esc_html_e('Billing Info', "ticktify"); ?></h3>
        <hr>
        <div class="input_wrapper">
            <form method="POST" id="billingInfoForm">
                <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('billing_info')); ?>">
                <div>
                    <label for="f-name"><?php esc_html_e('First Name', "ticktify"); ?> </label>
                    <input type="text" id="first_name" name="first_name" required value="<?php echo esc_attr($f_name); ?>">
                </div>
                <div>
                    <label for="l-name"><?php esc_html_e('Last Name', "ticktify"); ?> </label>
                    <input type="text" id="last_name" name="last_name" required value="<?php echo esc_attr($l_name); ?>">
                </div>
                <div>
                    <label for="street"><?php esc_html_e('Address', "ticktify"); ?> </label>
                    <input type="text" id="address" name="address" required value="<?php echo esc_attr($address); ?>">
                </div>
                <div>
                    <label for="city"><?php esc_html_e('City', "ticktify"); ?> </label>
                    <input type="text" id="city" name="city" required value="<?php echo esc_attr($city); ?>">
                </div>
                <div>
                    <label for="state"><?php esc_html_e('State', "ticktify"); ?> </label>
                    <input type="text" id="state" name="state" required value="<?php echo esc_attr($state); ?>">
                </div>
                <div>
                    <label for="zip"><?php esc_html_e('Zip Code', "ticktify"); ?></label>
                    <input type="text" id="zip_code" name="zip_code" required value="<?php echo esc_attr($zip_code); ?>">
                </div>
                <div>
                    <label for="phone"><?php esc_html_e('Phone', "ticktify"); ?></label>
                    <input type="text" id="phone" name="phone" required value="<?php echo esc_attr($phone); ?>">
                </div>
                <div>
                    <label for="email"><?php esc_html_e('Email', "ticktify"); ?></label>
                    <input type="email" id="user_email" name="user_email" required value="<?php echo esc_attr($user_email); ?>">
                </div>
            </form>
            <table class="form-table">
                <h3><?php esc_html_e('Reciept Summary', "ticktify"); ?> </h3>
                <hr>
                <thead>
                    <tr>
                        <th><?php esc_html_e('Event Name', "ticktify"); ?> </th>
                        <th><?php esc_html_e('Price', "ticktify"); ?> </th>
                        <th><?php esc_html_e('Quantity', "ticktify"); ?> </th>
                        <th><?php esc_html_e('Attendees', "ticktify"); ?> </th>
                        <th><?php esc_html_e('Subtotal', "ticktify"); ?> </th>
                    </tr>
                </thead>
                <?php
                $count = 0;
               $check_empty_attendees = false;
                foreach ($results as $cart_result) {
                    $user_subtotal = ((float)$cart_result->price * (float)$cart_result->quantity);
                    $attendeesArr = json_decode($cart_result->attendees);
                    if($attendeesArr == ''){
                        $check_empty_attendees = true;
                    }
                    ?>
                    <tr>
                        <td><?php echo esc_html($cart_result->event_title); ?></td>
                        <td><?php echo esc_html($cart_result->price) ?></td>
                        <td><?php echo esc_html($cart_result->quantity) ?></td>
                        
                            <?php
                         
                            if($attendeesArr != ''){
                                echo '<td>';
                            foreach ($attendeesArr as $key => $value) {
                                
                                echo esc_html($value->name . " (" . $value->age . ")");
                                echo '</br>';
                            }
                            echo '</td>';
                        }else{
                            echo '<td style="color:red;">';
                            echo esc_html("*Attendees Name Should Not Be Empty*");
                            echo '</td>';
                        }
                            ?>
                        
                        <td><?php echo esc_html($user_subtotal) ?></td>
                    </tr>
                    <?php $count = $count + $user_subtotal; ?>
                    <?php } ?>
                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td><strong><?php esc_html_e('Total', "ticktify"); ?></strong></td>
                        <td><strong><?php echo esc_html($count) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="payment-body">
            <?php if (!empty($api_key) && !empty($api_publishable_key) && !empty($count) &&  $check_empty_attendees == false )  :  ?>
                <input type="radio" id="stripe" name="payment_option" value="stripe" checked>
                <label for="stripe"><?php esc_html_e('Stripe', "ticktify"); ?></label><br>
                <!-- stripe paymnt section -->
                <div id="stripePaymentSection">
                    <div id="stripePaymentResponse" class="hidden"></div>
                    <!-- Display a payment form -->
                    <form id="stripePaymentForm" class="hidden" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                        <input type="hidden" id="stripeAction" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=stripe_payment">
                        <input type="hidden" id="STRIPE_PUBLISHABLE_KEY" value="<?php echo esc_attr($api_publishable_key); ?>">
                        <input type="hidden" id="cartTotalAmount" value="<?php echo esc_attr($count) ?>">
                        <input type="hidden" id="stripeRedirectUrl" value="<?php echo esc_url(get_home_url() . '/ticktify-thank-you'); ?>">
                        <div id="stripePaymentElement"> </div>
                        <button id="stripePaymentBtn" class="btn btn-primary">
                            <span id="buttonText"><?php esc_html_e('Book Now', "ticktify"); ?></span>
                        </button>
                    </form>
                </div><br>
            <?php elseif (empty($count) && $check_empty_attendees == false ) : 
               
                ?>
                <form id="freeBookingForm" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                    <button id="freeBookingBtn" class="btn btn-primary">
                        <span id="buttonText"><?php esc_html_e('Book Now', "ticktify"); ?></span>
                    </button>
                    <span id="booking_freeEvent_loading" style="display:none" class="loader"></span>
                </form>

                <?php elseif ($check_empty_attendees == true) : 
                 $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
              
               ?> <span id="buttonText" ><?php esc_html_e('Please Enter The Attendees Name ', "ticktify"); ?></span>
               <a href="<?php echo get_permalink($ticktify_settings['pages']['ticktify_cart']) ?>"   style="color:blue;">Click here</a>
            <?php else : ?>
                <span style="color:red;"><?php esc_html_e('*Currently Payment Option is Not Available*', "ticktify"); ?></span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; }else{

$ticktify_settings = get_option(sanitize_key('ticktify_settings'));
echo '<div>' . esc_html__('You Are Not Currently Logged In ', 'ticktify') . '
<a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])) . '">' . esc_html__('Click Here For Login', 'ticktify') . '</a>
</div>';

}