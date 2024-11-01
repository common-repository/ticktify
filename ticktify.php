<?php
/**
 * Plugin Name: Ticktify
 * Description: Event registration and booking management for WordPress. Events, locations, google maps,booking registration, user login/registration and more!
 * Version: 1.0.3
 * Author: Zehntech Technologies Pvt. Ltd.
 * Author URI: https://www.zehntech.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ticktify
 *
 * @package Ticktify
 */

defined('ABSPATH') or die("you do not have access to this page!");

defined('TICKTIFY_PLUGIN_DIR')    ?: define('TICKTIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
defined('TICKTIFY_TEXT_DOMAIN')   ?: define('TICKTIFY_TEXT_DOMAIN', 'ticktify');
defined('TICKTIFY_DB_VERSION')   ?: define('TICKTIFY_DB_VERSION', '1.0.0');
defined('TICKTIFY_PLUGIN_URL')    ?: define('TICKTIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
defined('TICKTIFY_ASSETS_URL')    ?: define('TICKTIFY_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');
defined('TICKTIFY_PLUGIN_INCLUDES_DIR')   ?: define('TICKTIFY_PLUGIN_INCLUDES_DIR', TICKTIFY_PLUGIN_DIR . 'includes/');
defined('TICKTIFY_UI_FRONT_DIR')   ?: define('TICKTIFY_UI_FRONT_DIR', TICKTIFY_PLUGIN_DIR . 'ui-front/');
defined('TICKTIFY_UI_ADMIN_DIR')   ?: define('TICKTIFY_UI_ADMIN_DIR', TICKTIFY_PLUGIN_DIR . 'ui-admin/');

defined('TICKTIFY_EVENT_POST_TYPE')   ?: define('TICKTIFY_EVENT_POST_TYPE', 'ticktify_event');
defined('TICKTIFY_EVENT_VENUE_TAX')   ?: define('TICKTIFY_EVENT_VENUE_TAX', 'ticktify_venue');
defined('TICKTIFY_EVENT_ORGANIZER_TAX')   ?: define('TICKTIFY_EVENT_ORGANIZER_TAX', 'ticktify_organizer');
defined('TICKTIFY_EVENT_ARTIST_TAX')   ?: define('TICKTIFY_EVENT_ARTIST_TAX', 'ticktify_artist');
defined('TICKTIFY_EVENT_SPONSORS_TAX')   ?: define('TICKTIFY_EVENT_SPONSORS_TAX', 'ticktify_sponsors');

defined('TICKTIFY_BOOKING_POST_TYPE')   ?: define('TICKTIFY_BOOKING_POST_TYPE', 'ticktify_booking');

include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'ec-functions.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-admin.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-event.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-venue.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-organizer.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-artist.php');
require_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-sponsors.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'ec-shortcodes.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-auth.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-cart.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-checkout.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-transaction.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'payment/stripe/class-payment-init.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-booking.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-profile.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-download-payments.php');
include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-email.php');


register_activation_hook(__FILE__, 'ticktify_event_activate');
if (!function_exists('ticktify_event_activate')) {
	function ticktify_event_activate()
	{
		//Create page action hook
		do_action('ticktify_create_pages');
		//Set plugin default settings
		do_action('ticktify_set_default_settings');
		//Create a table for cart page
		do_action('ticktify_cart_activates');
		// Create a table for transactions 
		do_action('ticktify_transactions_activates');
		// Set default value of pagination and registration setting
		do_action('ticktify_pagination_registration_default_setting');
		// Set default value of cancellation setting
		do_action('ticktify_cancellation_default_settings');
		// Set default value of notification email template setting
		do_action('ticktify_notification_default_settings');
	}
}

if (!function_exists('ticktify_cart_activates_callback')) {
	function ticktify_cart_activates_callback()
	{
		global $wpdb;
		global $db_version;
		$ticktify_cart = $wpdb->prefix . 'ticktify_cart';
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE IF NOT EXISTS $ticktify_cart (
		id  bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		customer_id BIGINT(36) NOT NULL ,
		event_id BIGINT(36) NOT NULL ,
		event_title VARCHAR(36) NOT NULL , 
		price DECIMAL(65) NOT NULL ,
		quantity INT(255) NOT NULL , 
		subtotal DECIMAL(65) NOT NULL , 
		attendees LONGTEXT NOT NULL , 
		PRIMARY KEY (id)) $charset_collate;";
		dbDelta($sql);
	}
}

add_action('ticktify_cart_activates', 'ticktify_cart_activates_callback');

/**
 * Callback function to create transactions schema
 * 
 */
if (!function_exists('ticktify_transactions_activates_callback')) {
	function ticktify_transactions_activates_callback()
	{
		global $wpdb;
		global $db_version;
		$ticktify_transactions = $wpdb->prefix . 'ticktify_transactions';
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE IF NOT EXISTS $ticktify_transactions (
		id BIGINT(36) NOT NULL AUTO_INCREMENT,
		customer_id BIGINT(36) NOT NULL,
		booking_id BIGINT(36) NOT NULL,
		payment_method varchar(50)  NOT NULL,
		total_amount float(10,2) NOT NULL,
		paid_amount float(10,2) NOT NULL,
		currency varchar(10) NOT NULL,
		transaction_id varchar(100)  NOT NULL,
		payment_status varchar(25) NOT NULL,
		created datetime NOT NULL,
		modified datetime NOT NULL,
		PRIMARY KEY (id)) $charset_collate;";
		dbDelta($sql);
	}
}
add_action('ticktify_transactions_activates', 'ticktify_transactions_activates_callback');

/**
 * Callback function to create new pages
 * 
 * Hook: ticktify_create_pages
 *
 */
if (!function_exists('ticktify_create_pages_callback')) {
	function ticktify_create_pages_callback()
	{
		if (empty(get_page_by_path(__('Ticktify Login', 'ticktify')))) {
			// Create login page object
			$ticktify_login_object = array(
				'post_title'    => __('Ticktify Login', 'ticktify'),
				'post_content'  => '[ticktify_login]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_login_object);
		}
		if (empty(get_page_by_path(__('Ticktify Lost Password', 'ticktify')))) {
			// Create Lost Password page object
			$ticktify_lost_password_object = array(
				'post_title'    => __('Ticktify Lost Password', 'ticktify'),
				'post_content'  => '[ticktify_lostpassword]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_lost_password_object);
		}
		if (empty(get_page_by_path(__('Ticktify Reset Password', 'ticktify')))) {
			// Create Reset Password page object
			$ticktify_reset_object = array(
				'post_title'    => __('Ticktify Reset Password', 'ticktify'),
				'post_content'  => '[ticktify_resetpassword]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_reset_object);
		}
		if (empty(get_page_by_path(__('Ticktify Register', 'ticktify')))) {
			// Create register page object
			$ticktify_register_object = array(
				'post_title'    => __('Ticktify Register', 'ticktify'),
				'post_content'  => '[ticktify_register]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_register_object);
		}
		if (empty(get_page_by_path(__('Ticktify Profile', 'ticktify')))) {
			// Create profile page object
			$ticktify_register_object = array(
				'post_title'    => __('Ticktify Profile', 'ticktify'),
				'post_content'  => '[ticktify_profile]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_register_object);
		}
		if (empty(get_page_by_path(__('Ticktify Cart', 'ticktify')))) {
			// Create profile page object
			$ticktify_cart_object = array(
				'post_title'    => __('Ticktify Cart', 'ticktify'),
				'post_content'  => '[ticktify_cart]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_cart_object);
		}
		if (empty(get_page_by_path(__('Ticktify Checkout', 'ticktify')))) {
			// Create checkout page object
			$ticktify_checkout_object = array(
				'post_title'    => __('Ticktify Checkout', 'ticktify'),
				'post_content'  => '[ticktify_checkout]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_checkout_object);
		}
		if (empty(get_page_by_path(__('Ticktify Events', 'ticktify')))) {
			// Create checkout page object
			$ticktify_detail_object = array(
				'post_title'    => __('Ticktify Events', 'ticktify'),
				'post_content'  => '[events-list]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_detail_object);
		}
		if (empty(get_page_by_path(__('Ticktify Thank You', 'ticktify')))) {
			// Create checkout page object
			$ticktify_detail_object = array(
				'post_title'    => __('Ticktify Thank You', 'ticktify'),
				'post_content'  => '[ticktify_thankyou]',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'     => 'page',
			);
			// Insert the page into the database
			wp_insert_post($ticktify_detail_object);
		}
	}
}
add_action('ticktify_create_pages', 'ticktify_create_pages_callback');

/**
 * Callback function to set/save default plugin settings
 * 
 * Hook: ticktify_set_default_settings
 *
 */
if (!function_exists('ticktify_set_default_settings_callback')) {
	function ticktify_set_default_settings_callback()
	{
		$ticktify_settings = [
			'pages' => [
				'ticktify_login' => get_page_by_path(__('Ticktify Login', 'ticktify'))->ID,
				'ticktify_register' => get_page_by_path(__('Ticktify Register', 'ticktify'))->ID,
				'ticktify_profile' => get_page_by_path(__('Ticktify Profile', 'ticktify'))->ID,
				'ticktify_lostpassword' => get_page_by_path(__('Ticktify Lost Password', 'ticktify'))->ID,
				'ticktify_resetpassword' => get_page_by_path(__('Ticktify Reset Password', 'ticktify'))->ID,
				'ticktify_cart' => get_page_by_path(__('Ticktify Cart', 'ticktify'))->ID,
				'ticktify_checkout' => get_page_by_path(__('Ticktify Checkout', 'ticktify'))->ID,
				'events-list' => get_page_by_path(__('Ticktify Events', 'ticktify'))->ID,
			]
		];
		update_option(sanitize_key('ticktify_settings'), $ticktify_settings);
	}
}
add_action('ticktify_set_default_settings', 'ticktify_set_default_settings_callback');

/**
 * Callback function to insert default value of pagination and registration setting
 * 
 */
if (!function_exists('ticktify_pagination_registration_callback')) {
	function ticktify_pagination_registration_callback()
	{
		if (empty(get_option(sanitize_key('ticktify_pagination_settings')))) {
			$ticktify_pagination_registration['event_pagination'] = array(
				'event_number' => '9',
				'color' => '#000000',
				'bg_color' => '#ffffff',
				'hov_color' => '#000000',
				'hov_bg' => '#e9ecef'
			);

			$ticktify_pagination_registration['event_registration'] = array(
				'first_text' => __('First Name', "ticktify"),
				'last_text' => __('Last Name', "ticktify"),
				'email' => __('Email', "ticktify"),
				'password' => __('Password', "ticktify"),
				'conpassword' => __('Confirm Password', "ticktify")
			);
			update_option(sanitize_key('ticktify_pagination_settings'), $ticktify_pagination_registration);
		}
	}
}
add_action('ticktify_pagination_registration_default_setting', 'ticktify_pagination_registration_callback');

/**
 * Callback function to insert default value of cancellation setting
 * 
 */
if (!function_exists('ticktify_cancellation_default_settings_callback')) {
	function ticktify_cancellation_default_settings_callback()
	{
		if (empty(get_option(sanitize_key('ticktify_cancellation_settings')))) {
			$ticktify_cancellation_settings = array(
				'ticktify_bookings_user_cancellation' => 0,
				'ticktify_event_cancellation_hrs' => '',
			);
			update_option(sanitize_key('ticktify_cancellation_settings'), $ticktify_cancellation_settings);
		}
	}
}
add_action('ticktify_cancellation_default_settings', 'ticktify_cancellation_default_settings_callback');


/**
 * Callback function to insert default value of notifiaction email template setting
 * 
 */
if (!function_exists('ticktify_notification_default_settings_callback')) {
	function ticktify_notification_default_settings_callback()
	{
		$ticktify_get_email_templates = get_option(sanitize_key('ticktify_email_templates'));
		if (!empty($ticktify_get_email_templates['new_user_to_admin']) || empty($ticktify_get_email_templates)) {
			$ticktify_email_templates['new_user_to_admin'] = array(
				'to' => '[admin_email]',
				'subject' => 'New User Registration',
				'message' => '<p>Dear [admin_name],</p>
							<p>We are pleased to inform you that a new user has registered with our website. The user details are as follows:</p>
							<p>Name: [first_name] [last_name]</p>
							<p>Email: [user_email]</p>
							<p>We would like to welcome the new user to our community and invite you to review their details.</p>
							<p>If you have any questions or concerns, please do not hesitate to contact us.</p>
							<p>Regards,</p>
							<div>
							<div>[site_title]</div>
							</div>',
			);
		}

		if (!empty($ticktify_get_email_templates['booking_to_user']) || empty($ticktify_get_email_templates)) {
			$ticktify_email_templates['booking_to_user'] = array(
				'to' => '[user_email]',
				'subject' => 'New Event Booking',
				'message' => '<p><br />Dear [first_name] [last_name],</p>
				<p>Thank you for your interest in booking an event with us. We are excited to have you join us!</p>
				<p>In order to confirm your booking, please provide the following information:</p>
				<p>Booking Name: [booking_title]<br />Booking Url :[booking_url_for_user]</p>
				<div> [booking_details]</div>
				<p>Once we have received this information, we will confirm your booking and provide you with further details about payment and any other necessary arrangements.</p>
				<p>If you have any questions, please do not hesitate to contact us. We look forward to hearing from you soon.</p>
				<p>Sincerely,<br />[site_title]</p>',
			);
		}

		if (!empty($ticktify_get_email_templates['booking_to_admin']) || empty($ticktify_get_email_templates)) {
			$ticktify_email_templates['booking_to_admin'] = array(
				'to' => '[admin_email]',
				'subject' => 'Event Booking Confirmed',
				'message' => '<p>Dear [admin_name],</p>
				<p>This email is to confirm that an event has been booked.</p>
				<p>Event details are as follows:</p>
				<p>User Name: [first_name] [last_name]<br />User Email: [user_email]<br />Booking Name: [booking_title]<br />Booking Url :[booking_url_for_admin]</p>
				<div> [booking_details]</div>
				<p>If you have any questions, please do not hesitate to contact us.</p>
				<p>Thank you for your time and consideration.</p>
				<p>Sincerely, <br />[site_title]</p>',
			);
		}

		if (!empty($ticktify_get_email_templates['cancellation_to_user']) || empty($ticktify_get_email_templates)) {
			$ticktify_email_templates['cancellation_to_user'] = array(
				'to' => '[user_email]',
				'subject' => 'Event Cancellation Notification',
				'message' => '<p>Dear [first_name] [last_name],</p>
				<p>We regret to inform you that the [booking_title]  has been cancelled.</p>
				<p>Booking Name: [booking_title]<br />Booking Url :[booking_url_for_user]</p>
				<div> [booking_details]</div>
				<p>We apologize for any inconvenience this may have caused. Refunds for tickets purchased for the event will be processed as soon as possible.</p>
				<p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
				<p>Sincerely,<br />[site_title]</p>',
			);
		}
		if (!empty($ticktify_get_email_templates['cancellation_to_admin']) || empty($ticktify_get_email_templates)) {
			$ticktify_email_templates['cancellation_to_admin'] = array(
				'to' => '[admin_email]',
				'subject' => 'Event Cancellation Notification',
				'message' => '<p>Dear [admin_name],</p>
				<p>This email is to notify you that event booking has been cancelled. I understand that this may cause some inconvenience, but I have decided to cancel the event.</p>
				<p>Booking Name: [booking_title]<br />Booking Url :[booking_url_for_admin]</p>
				<div> [booking_details]</div>
				<p>I would appreciate it if you could take the necessary steps to cancel the event. I apologize for any inconvenience this may have caused.</p>
				<p>Thank you for your understanding.</p>
				<p>Sincerely,<br />[site_title]</p>',
			);
		}

		update_option(sanitize_key('ticktify_email_templates'), $ticktify_email_templates);
	}
}
add_action('ticktify_notification_default_settings', 'ticktify_notification_default_settings_callback');


/*Hook Act when user delete the plugin*/
register_uninstall_hook(__FILE__, 'ticktify_event_uninstall');
function ticktify_event_uninstall()
{
}
