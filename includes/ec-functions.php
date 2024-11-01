<?php
/**
 * Contains functions for Ticktify Event.
 *
 * @package ticktify-event\includes
 * @version 1.0.0
 */

/**
 * Set transient in options before redirect.
 *
 * @param string $transient_key Transient Key.
 * @param string $data_to_set Mixed.
 */
if (!function_exists('ticktify_set_transient')) {
	function ticktify_set_transient($transient_key, $data_to_set)
	{
		set_transient($transient_key, $data_to_set, DAY_IN_SECONDS);
	}
}

/**
 * Get transient errors saved in options before redirect.
 *
 * @param string $transient_key Transient Key.
 */
if (!function_exists('ticktify_get_transient_error')) {
	function ticktify_get_transient_error($transient_key)
	{
		$form_error = get_transient($transient_key);
		if (!empty($form_error)) {
			foreach ($form_error as $error) {
				echo '<ul class="ticktify-notice ticktify-errors">';
				echo '<li>' . esc_html($error) . '</li>';
				echo '</ul>';
			}
		}

		delete_transient($transient_key);
	}
}

/**
 * Get transient messages saved in options before redirect.
 *
 * @param string $transient_key Transient Key.
 */
if (!function_exists('ticktify_get_transient_messages')) {
	function ticktify_get_transient_messages($transient_key)
	{
		$form_messages = get_transient($transient_key);
		if (!empty($form_messages)) {
			foreach ($form_messages as $message) {
				echo '<ul class="ticktify-notice ticktify-messages">';
				echo '<li>' . esc_html($message) . '</li>';
				echo '</ul>';
			}
		}

		delete_transient($transient_key);
	}
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name Template name.
 */
if (!function_exists('ticktify_get_template')) {
	function ticktify_get_template($template_name)
	{

		$template = ticktify_locate_template($template_name);

		$filter_template = apply_filters('ticktify_get_template', $template, $template_name);

		if ($filter_template !== $template) {
			if (!file_exists($filter_template)) {
				wp_die(__FUNCTION__ . ': ' . sprintf(__('%s does not exist.', 'ticktify_events'), '<code>' . esc_html($filter_template) . '</code>'));
				return;
			}
			$template = $filter_template;
		}

		include $template;
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @param string $template_name Template name.
 * @return string
 */
if (!function_exists('ticktify_locate_template')) {
	function ticktify_locate_template($template_name)
	{
		$template_path = apply_filters('ticktify_template_path', 'user-account/');
		$default_path = TICKTIFY_UI_FRONT_DIR;

		$cs_template = str_replace('_', '-', $template_name);
		$template = locate_template(
			array(
				$template_path . $cs_template,
				$cs_template,
			)
		);

		if (empty($template)) {
			$template = locate_template(
				array(
					$template_path . $template_name,
					$template_name,
				)
			);
		}

		// Get default template/.
		if (!$template) {
			if (empty($cs_template)) {
				$template = $default_path . $template_name;
			} else {
				$template = $default_path . $cs_template;
			}
		}

		// Return what we found.
		return apply_filters('ticktify_locate_template', $template, $template_name, $template_path);
	}
}

/**
 * Get stripe keys
 *
 */
if (!function_exists('get_stripe_keys')) {
	function get_stripe_keys()
	{
		$api_key = $api_publishable_key = NULL;
		$ticktify_payments_settings = get_option(sanitize_key('ticktify_payments_settings'));

		if (!empty($ticktify_payments_settings['stripe_details']['test_enabled'])) {
			$api_key = $ticktify_payments_settings['stripe_details']['test']['secret_key'];
			$api_publishable_key = $ticktify_payments_settings['stripe_details']['test']['publishable_key'];
		} else {
			if (isset($ticktify_payments_settings['stripe_details']['live'])) {
				$api_key = $ticktify_payments_settings['stripe_details']['live']['secret_key'];
				$api_publishable_key = $ticktify_payments_settings['stripe_details']['live']['publishable_key'];
			}
		}

		return [$api_key, $api_publishable_key];
	}
}

/**
 * Get stripe keys
 *
 * @return admin_email
 * @return display_name
 * @return bloginfo
 */
if (!function_exists('ticktify_get_admin_data')) {
	function ticktify_get_admin_data()
	{
		$admin_email = get_option(sanitize_key('admin_email'));
		$user = get_user_by('email', $admin_email);
		return [$admin_email, $user->display_name, get_bloginfo('name')];
	}
}

/**
 * Get stripe keys
 *
 */
if (!function_exists('ticktify_get_booking_event')) {
	function ticktify_get_booking_event($eventsArray)
	{
		ob_start();
		?>
		<table class="form-table" style="width:70%">
			<thead>
				<tr>
					<th><?php esc_html_e("Id", "ticktify"); ?></th>
					<th><?php esc_html_e("Event", "ticktify"); ?></th>
					<th><?php esc_html_e("Attendees", "ticktify"); ?></th>
					<th><?php esc_html_e("Price", "ticktify"); ?></th>
					<th><?php esc_html_e("Quantity", "ticktify"); ?></th>
					<th><?php esc_html_e("Subtotal", "ticktify"); ?></th>
					<th><?php esc_html_e("Status", "ticktify"); ?></th>
				</tr>
				<thead>
				<tbody>
					<?php
					$total_sum = 0;
					foreach ($eventsArray as $key => $value) {
						$attendees = json_decode($value['attendees']);
						?>
						<tr>
							<td>#<?php echo esc_html($value['event_id']); ?></td>
							<td><a href="<?php echo esc_url(get_post_permalink($value['event_id'])); ?>"><?php echo esc_html(get_the_title($value['event_id'])); ?></a></td>
							<td>
								<?php foreach ($attendees as $att_key => $att_val) {
									echo esc_html($att_val->name . " (" . $att_val->age . ") ");
								} ?>
							</td>
							<td><?php echo esc_html($value['price']); ?></td>
							<td>&#215;<?php echo esc_html($value['quantity']); ?></td>
							<td><?php echo esc_html($value['subtotal']); ?></td>
							<td id="event_status_<?php echo esc_attr($value['event_id']); ?>"><?php echo esc_html(ucfirst($value['status'])); ?></td>
						</tr>
					<?php
						$total_sum += $value['subtotal'];
					} ?>
			<tbody>
			<tfoot>
				<tr>
					<th colspan="5"></th>
					<th><?php esc_html_e("Total", "ticktify"); ?></th>
					<th><?php echo esc_html($total_sum); ?></th>
				</tr>
			</tfoot>
		</table>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
