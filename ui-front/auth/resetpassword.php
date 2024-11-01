<?php wp_enqueue_style('style-css'); 
/**
 * User rest password page.
 *
 * @package ticktify-event\ui-front\auth
 * @version 1.0.0
 */


 $ticktify_settings = get_option(sanitize_key('ticktify_settings'));

	if (isset($_GET['key'], $_GET['id'])) {
	$userdata = TICKTIFY_Event_Auth::ticktify_redirect_resetpassword_link();
	
	if(isset($userdata['data']) && $userdata['data'] == 'logininvalid'){
		echo '<div>' . esc_html__("This key is invalid or has already been used. Please reset your password again if needed.", 'ticktify') . '
		<a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_lostpassword'])) . '">' . esc_html__('Click here', 'ticktify') . '</a>
	</div>';

	} else {
?>

<center>
	<div class="resetpassword-text">
		<p><?php echo __('Enter a new password below.'); ?></p>
		<form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="reset_key" value="<?php echo esc_attr($userdata[0]); ?>">
			<input type="hidden" name="reset_login" value="<?php echo esc_attr($userdata[1]); ?>">
			<?php
			// if $form_error is WordPress Error, loop through the error object
			// and echo the error
			ticktify_get_transient_error('ticktify_resetpassword_errors');
			?>
			<div class="form-group">
				<label class="form-label"><?php echo __('New Password', "ticktify"); ?></label>
				<input class="form-control" type="password" name="password" id="password">
			</div><br>
			<div class="form-group">
				<label class="form-label"><?php echo __('Re-Type Password', "ticktify"); ?></label>
				<input class="form-control" type="password" name="re_password" id="re_password">
			</div>
			<div class="form-group">
				<?php wp_nonce_field('ticktify_nonce_resetpassword', '_ticktify_nonce_resetpassword'); ?>
				<input type="hidden" name="action" value="ticktify_action_resetpassword">
				<button class="btn btn-primary" type="submit"><?php echo __('Reset Password', "ticktify"); ?></button>
			</div>
		</form>
	</div>

</center>

<?php 

	}
}
	else{
	echo '<div>' . esc_html__("You Don't Have Access this Page Directly ", 'ticktify') . '
    <a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])) . '"></a>
</div>';

	}
