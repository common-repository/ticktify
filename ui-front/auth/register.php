<?php
/**
 * User registration page.
 *
 * @package ticktify-event\ui-front\auth
 * @version 1.0.0
 */

wp_enqueue_style('style-css');
$ticktify_registrations = get_option(sanitize_key('ticktify_pagination_settings'));
$first_label = $ticktify_registrations['event_registration']['first_text'] ? $ticktify_registrations['event_registration']['first_text'] : '';
$last_label = $ticktify_registrations['event_registration']['last_text'] ? $ticktify_registrations['event_registration']['last_text'] : '';
$email_label = $ticktify_registrations['event_registration']['email'] ? $ticktify_registrations['event_registration']['email'] : '';
$password_label = $ticktify_registrations['event_registration']['password'] ? $ticktify_registrations['event_registration']['password'] : '';
$conpassword_label = $ticktify_registrations['event_registration']['conpassword'] ? $ticktify_registrations['event_registration']['conpassword'] : '';
$ticktify_settings = get_option(sanitize_key('ticktify_settings'));

?>
<div class="random">
	<form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<?php
		// if $form_error is WordPress Error, loop through the error object
		// and echo the error
		ticktify_get_transient_error('ticktify_register_errors');
		?>
		<div class="form-group">
			<label class="form-label"><?php echo esc_html($first_label); ?></label><br>
			<input class="form-control" type="text" name="first" id="First_name" required>
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo esc_html($last_label); ?></label><br>
			<input class="form-control" type="text" name="last" id="Last_name" required>
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo esc_html($email_label); ?></label><br>
			<input class="form-control" type="email" name="email" id="email" required>
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo esc_html($password_label); ?></label><br>
			<input class="form-control" type="password" name="password" id="password" required>
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo esc_html($conpassword_label); ?></label><br>
			<input class="form-control" type="password" name="confpass" id="confpass" required>
		</div>
		<div class="form-group">
			<?php wp_nonce_field('ticktify_nonce_register', '_ticktify_nonce_register'); ?>
			<input type="hidden" name="action" value="ticktify_action_register">
			<button class="btn btn-primary" type="submit" name="save"><?php echo __('Register', "ticktify"); ?></button>
		</div>
		<div class="form-group">
			<a href="<?php echo esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])); ?>"><?php echo __('Login', "ticktify"); ?></a>
		</div>
	</form>
</div>