<?php
/**
 * Contains shortcode functions for Ticktify Event.
 *
 * @class Ticktify_Event
 * @package ticktify-event\includes
 * @version 1.0.0
 */

/**
 * Login template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_login_callback')) {
	function ticktify_login_callback()
	{
		$ticktify_settings = get_option(sanitize_key('ticktify_settings'));
		ob_start();
		ticktify_get_template('auth/login.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_login', 'ticktify_login_callback');

/**
 * Lost Password template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_lostpassword_callback')) {
	function ticktify_lostpassword_callback()
	{
		$ticktify_settings = get_option(sanitize_key('ticktify_settings'));
		ob_start();
		ticktify_get_template('auth/lostpassword.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_lostpassword', 'ticktify_lostpassword_callback');

/**
 * Reset Password template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_resetpassword_callback')) {
	function ticktify_resetpassword_callback()
	{
		$ticktify_settings = get_option(sanitize_key('ticktify_settings'));
		ob_start();
		ticktify_get_template('auth/resetpassword.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_resetpassword', 'ticktify_resetpassword_callback');

/**
 * Register template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_register_callback')) {
	function ticktify_register_callback()
	{
		ob_start();
		ticktify_get_template('auth/register.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_register', 'ticktify_register_callback');

/**
 * Profile template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_profile_callback')) {
	function ticktify_profile_callback()
	{
		$ticktify_settings = get_option(sanitize_key('ticktify_settings'));
		ob_start();
		ticktify_get_template('account/profile.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_profile', 'ticktify_profile_callback');

/**
 * add to cart template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_cart_callback')) {
	function ticktify_cart_callback()
	{
		ob_start();
		ticktify_get_template('templates/cart.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_cart', 'ticktify_cart_callback');

/**
 * Checkout template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_checkout_callback')) {
	function ticktify_checkout_callback()
	{
		ob_start();
		ticktify_get_template('templates/checkout.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_checkout', 'ticktify_checkout_callback');

/**
 * thankyou template shortcode
 *
 * @return string
 */
if (!function_exists('ticktify_thankyou_callback')) {
	function ticktify_thankyou_callback()
	{
		ob_start();
		ticktify_get_template('templates/thankyou.php');
		return ob_get_clean();
	}
}
add_shortcode('ticktify_thankyou', 'ticktify_thankyou_callback');

if (!function_exists('ticktify_addoneweek')) {
	function ticktify_addoneweek()
	{
		$now = new DateTime();
		return $now->add(new DateInterval('P1M'))->format('m-d-Y');
	}
}
add_shortcode('arttime', 'ticktify_addoneweek');
