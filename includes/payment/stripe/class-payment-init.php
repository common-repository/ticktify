<?php

/**
 * Contains action hooks and functions for stripe payment.
 *
 * @class Ticktify_Stripe
 * @package zt-event-calender\classes
 * @version 1.0.0
 */
if (!class_exists('Ticktify_Stripe')) :
    class Ticktify_Stripe
    {

        private $api_key = null;
        private $api_publishable_key = null;

        public function __construct()
        {
            list($this->api_key, $this->api_publishable_key) = get_stripe_keys();

            add_action('wp_ajax_nopriv_stripe_payment', [$this, 'ticktify_stripe_payment_callback']);
            add_action('wp_ajax_stripe_payment', [$this, 'ticktify_stripe_payment_callback']);
        }

        /**
         * stripe insert trnasaction
         *  
         */

        public function ticktify_stripe_payment_callback()
        {

            // Include the Stripe PHP library 
            require_once 'stripe-php/init.php';

            // Set API key 
            \Stripe\Stripe::setApiKey($this->api_key);

            // Retrieve JSON from POST body 
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            if (!empty($jsonObj->request_type) && $jsonObj->request_type == 'create_payment_intent') {

                // Define item price and convert to cents 
                $itemPriceCents = round($jsonObj->cartTotalAmount * 100);

                // Set content type to JSON 
                header('Content-Type: application/json');

                try {
                    // Create PaymentIntent with amount and currency 
                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'amount' => $itemPriceCents,
                        'currency' => "USD",
                        'description' => __("Booking Event", "ticktify"),
                        'payment_method_types' => [
                            'card'
                        ],

                    ]);

                    $output = [
                        'id' => esc_attr($paymentIntent->id),
                        'clientSecret' => esc_attr($paymentIntent->client_secret)
                    ];

                    echo json_encode($output);
                } catch (Error $e) {
                    http_response_code(500);
                    echo json_encode(['error' => esc_html($e->getMessage())]);
                }
            } elseif (!empty($jsonObj->request_type) && $jsonObj->request_type == 'create_customer') {
                $payment_intent_id = !empty($jsonObj->payment_intent_id) ? $jsonObj->payment_intent_id : '';
                $name = $jsonObj->first_name . ' ' . $jsonObj->last_name;
                $email = !empty($jsonObj->email) ? $jsonObj->email : '';

                // Add customer to stripe 
                try {
                    $customer = \Stripe\Customer::create(array(
                        'name' => $name,
                        'email' => $email,
                        'description' => __('Ticktify WP Plugin Customer', "ticktify"),
                    ));

                    $customerMetaArray = array(
                        'first_name' => sanitize_text_field($jsonObj->first_name),
                        'last_name' => sanitize_text_field($jsonObj->last_name),
                        'address' => sanitize_text_field($jsonObj->address),
                        'city' => sanitize_text_field($jsonObj->city),
                        'state' => sanitize_text_field($jsonObj->state),
                        'zip_code' => sanitize_text_field($jsonObj->zip_code),
                        'phone' => sanitize_text_field($jsonObj->phone),
                        'user_email' => sanitize_email($jsonObj->email),
                    );

                    Ticktify_Checkout::ticktify_insert_billing_details($customerMetaArray);
                } catch (Exception $e) {
                    $api_error = $e->getMessage();
                }

                if (empty($api_error) && $customer) {
                    try {
                        // Update PaymentIntent with the customer ID 
                        $paymentIntent = \Stripe\PaymentIntent::update($payment_intent_id, [
                            'customer' => $customer->id,
                            'shipping' => [
                                'name' => $name,
                                'address' => [
                                    'line1' => $jsonObj->address,
                                    'postal_code' => $jsonObj->zip_code,
                                    'city' => $jsonObj->city,
                                    'state' => $jsonObj->state,
                                    'country' => "US",
                                ],
                            ],
                        ]);
                    } catch (Exception $e) {
                        // log or do what you want 
                    }

                    $output = [
                        'id' => esc_attr($payment_intent_id),
                        'customer_id' => esc_attr($customer->id)
                    ];
                    echo json_encode($output);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => esc_html($api_error)]);
                }
            } elseif (!empty($jsonObj->request_type) && $jsonObj->request_type == 'payment_insert') {
                $payment_intent = !empty($jsonObj->payment_intent) ? $jsonObj->payment_intent : '';
                $customer_id = !empty($jsonObj->customer_id) ? $jsonObj->customer_id : '';

                // Retrieve customer info 
                try {
                    $customer = \Stripe\Customer::retrieve($customer_id);
                } catch (Exception $e) {
                    $api_error = $e->getMessage();
                }

                // Check whether the charge was successful 
                if (!empty($payment_intent) && $payment_intent->status == 'succeeded') {
                    // Transaction details  
                    $transactionID = $payment_intent->id;
                    $paidAmount = $payment_intent->amount;
                    $paidAmount = ($paidAmount / 100);
                    $paidCurrency = $payment_intent->currency;
                    $payment_status = $payment_intent->status;

                    $name = $email = '';
                    if (!empty($customer)) {
                        $name = !empty($customer->name) ? $customer->name : '';
                        $email = !empty($customer->email) ? $customer->email : '';
                    }

                    $payment_id = 0;

                    $bookingArray = array('payment_status' => sanitize_text_field($payment_intent->status));

                    $customerMetaArray = array(
                        'first_name' => sanitize_text_field($jsonObj->first_name),
                        'last_name' => sanitize_text_field($jsonObj->last_name),
                        'address' => sanitize_text_field($jsonObj->address),
                        'city' => sanitize_text_field($jsonObj->city),
                        'state' => sanitize_text_field($jsonObj->state),
                        'zip_code' => sanitize_text_field($jsonObj->zip_code),
                        'phone' => sanitize_text_field($jsonObj->phone),
                        'user_email' => sanitize_email($jsonObj->email),
                    );

                    $bookingResults = Ticktify_Checkout::ticktify_insert_booking_details($bookingArray, $customerMetaArray);

                    $transactionArray = array(
                        'customer_id' => sanitize_text_field(get_current_user_id()),
                        'booking_id' => sanitize_text_field($bookingResults),
                        'payment_method' => sanitize_text_field('stripe'),
                        'total_amount' => sanitize_text_field($paidAmount),
                        'paid_amount' => sanitize_text_field($paidAmount),
                        'currency' => sanitize_text_field($paidCurrency),
                        'transaction_id' => sanitize_text_field($transactionID),
                        'payment_status' => sanitize_text_field($payment_status),
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                    );

                    $transactionResults = Ticktify_Transaction::ticktify_insert_transaction($transactionArray);

                    if ($transactionResults) {
                        Ticktify_Checkout::ticktify_empty_cart(get_current_user_id());
                        // $payment_id = $stmt->insert_id;
                        $payment_id = $transactionResults;
                    }


                    $output = [
                        'payment_id' => esc_attr(base64_encode($payment_id))
                    ];
                    echo json_encode($output);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => __('Transaction has been failed!', "ticktify")]);
                }
            }

            wp_die();
        }
    }

    new Ticktify_Stripe();
endif;
