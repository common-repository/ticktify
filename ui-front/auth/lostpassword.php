<?php wp_enqueue_style('style-css'); 
/**
 * Lost password page.
 *
 * @package ticktify-event\ui-front\auth
 * @version 1.0.0
 */
?>
<div class="random">
	<div class="lostpassword-text">
		<h1></h1>
		<p><?php echo __('Lost your password? Please enter your email address. You will receive a link to create a new password via email.', "ticktify"); ?></p>
	</div>
	<form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<?php
		// if $form_error is WordPress Error, loop through the error object
		// and echo the error
		ticktify_get_transient_error('ticktify_lostpassword_errors');
		ticktify_get_transient_messages('ticktify_lostpassword_messages');
		?>
		<div class="form-group">
			<label class="form-label"><?php echo __('Email', "ticktify"); ?></label>
			<input class="form-control" type="text" name="email" id="email">
		</div>
		<div class="form-group">
			<?php wp_nonce_field('ticktify_nonce_lostpassword', '_ticktify_nonce_lostpassword'); ?>
			<input type="hidden" name="action" value="ticktify_action_lostpassword">
			<button class="btn btn-primary" type="submit"><?php echo __('Reset Password', "ticktify"); ?></button>
		</div>
	</form>
</div>