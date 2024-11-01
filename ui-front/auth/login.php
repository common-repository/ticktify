<?php
/**
 * User login page.
 *
 * @package ticktify-event\ui-front\auth
 * @version 1.0.0
 */

wp_enqueue_style('style-css');
$ticktify_settings = get_option(sanitize_key('ticktify_settings'));

if (is_user_logged_in()) :

    echo '<div>' . esc_html__('You Are Already Logged In , ', 'ticktify') . '
    <a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_profile'])) . '">' . esc_html__('Click Here For Logout', 'ticktify') . '</a>
</div>';
else :
?>

<div class="random">
	<form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<?php
		// if $form_error is WordPress Error, loop through the error object
		// and echo the error
		ticktify_get_transient_error('ticktify_login_errors');
		ticktify_get_transient_messages('ticktify_register_messages');
		?>
		<div class="form-group">
			<label class="form-label"><?php echo __('Username', "ticktify"); ?></label><br>
			<input class="form-control" type="text" name="username" id="username">
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo __('Password', "ticktify"); ?></label><br>
			<input class="form-control" type="password" name="password" id="password">
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo __('Remember Me', "ticktify"); ?><input type="checkbox" name="rememberme" id="rememberme" value="1"></label>
		</div>
		<div class="form-group">
			<?php wp_nonce_field('ticktify_nonce_login', '_ticktify_nonce_login'); ?>
			<input type="hidden" name="action" value="ticktify_action_login">
			<button class="btn btn-primary" type="submit"><?php echo __('Login', "ticktify"); ?></button>
		</div>
		<div class="form-group">
			<a href="<?php echo esc_url(get_permalink($ticktify_settings['pages']['ticktify_lostpassword'])); ?>"><?php echo __('Lost your password?', "ticktify"); ?></a> | <a href="<?php echo esc_url(get_permalink($ticktify_settings['pages']['ticktify_register'])); ?>"><?php echo __('Register', "ticktify"); ?></a>
		</div>
	</form>
</div>
<?php endif; 