<?php
/**
 * Metabox for payment setting.
 *
 * @package ticktify-event\ui-admin\metabox
 * @version 1.0.0
 */

$ticktify_payments_settings = get_option(sanitize_key('ticktify_payments_settings'));
?>
<div class="wrap ticktify_events">
	<table class="form-table">
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="ticktify_events_bacs_enabled"><?php esc_html_e("Enable/Disable", "ticktify"); ?> </label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php esc_html_e("Enable/Disable", "ticktify"); ?></span></legend>
					<label>
						<input class="" type="checkbox" name="event_payments[test_enabled]" id="stripe-test-enabled" value="1" <?php echo esc_attr((isset($ticktify_payments_settings['stripe_details']['test_enabled']) && $ticktify_payments_settings['stripe_details']['test_enabled'] == 1) || !isset($ticktify_payments_settings['stripe_details']['test_enabled']) ? esc_attr('checked') : ''); ?> />
						<?php esc_html_e("Enable Test Mode", "ticktify"); ?></label><br />
				</fieldset>
			</td>
		</tr>
		<tr valign="top1" data-type="live">
			<span>
				<th><?php esc_html_e("Live Publishable Key", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe" for="publishable_key"></lable>
					<input class="ticktify_stripe regular-text" type="text" id="publishable_key" value="<?php echo isset($ticktify_payments_settings['stripe_details']['live']['publishable_key']) ? esc_attr($ticktify_payments_settings['stripe_details']['live']['publishable_key']) : ''; ?>" name="event_payments[live][publishable_key]" autocomplete="off">
				</td>
			</span>
		</tr>
		<tr valign="top2" data-type="live">
			<span>
				<th><?php esc_html_e("Live Secret Key", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe" for="secret_key"></lable><input class="ticktify_stripe regular-text" type="text" id="secret_key" value="<?php echo isset($ticktify_payments_settings['stripe_details']['live']['secret_key']) ? esc_attr($ticktify_payments_settings['stripe_details']['live']['secret_key']) : ''; ?>" name="event_payments[live][secret_key]" autocomplete="off">
				</td>
			</span>
		</tr>
		<tr valign="top3" data-type="live">
			<span>
				<th><?php esc_html_e("Webhook Secret", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe"></lable><input class="ticktify_stripe regular-text" type="text" id="webhook_secret" value="<?php echo isset($ticktify_payments_settings['stripe_details']['live']['webhook_secret']) ? esc_attr($ticktify_payments_settings['stripe_details']['live']['webhook_secret']) : ''; ?>" name="event_payments[live][webhook_secret]">
				</td>
			</span>
		</tr>
		<tr valign="top1" data-type="test">
			<span>
				<th><?php esc_html_e("Test Publishable Key", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe" for="publishable_key"></lable>
					<input class="ticktify_stripe regular-text" type="text" id="publishable_key" value="<?php echo isset($ticktify_payments_settings['stripe_details']['test']['publishable_key']) ? esc_attr($ticktify_payments_settings['stripe_details']['test']['publishable_key']) : ''; ?>" name="event_payments[test][publishable_key]" autocomplete="off">
				</td>
			</span>
		</tr>
		<tr valign="top2" data-type="test">
			<span>
				<th><?php esc_html_e("Test Secret Key", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe" for="secret_key"></lable><input class="ticktify_stripe regular-text" type="text" id="secret_key" value="<?php echo isset($ticktify_payments_settings['stripe_details']['test']['secret_key']) ? esc_attr($ticktify_payments_settings['stripe_details']['test']['secret_key']) : ''; ?>" name="event_payments[test][secret_key]" autocomplete="off">
				</td>
			</span>
		</tr>
		<tr valign="top3" data-type="test">
			<span>
				<th><?php esc_html_e("Test Webhook Secret", "ticktify"); ?></th>
				<td>
					<lable class="ticktify_stripe"></lable><input class="ticktify_stripe regular-text" type="text" id="webhook_secret" value="<?php echo isset($ticktify_payments_settings['stripe_details']['test']['webhook_secret']) ? esc_attr($ticktify_payments_settings['stripe_details']['test']['webhook_secret']) : ''; ?>" name="event_payments[test][webhook_secret]">
				</td>
			</span>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" name="submit" class="button button-primary" id="sub_btn" value="<?php _e('Submit', "ticktify") ?>"></td>
		</tr>
	</table>
</div>
<script>
	jQuery(document).ready(function() {
		<?php if ((isset($ticktify_payments_settings['stripe_details']['test_enabled']) && $ticktify_payments_settings['stripe_details']['test_enabled'] == 1) || !isset($ticktify_payments_settings['stripe_details']['test_enabled'])) { ?>
			jQuery('tr[data-type="test"]').show();
			jQuery('tr[data-type="live"]').hide();
		<?php } else { ?>
			jQuery('tr[data-type="test"]').hide();
			jQuery('tr[data-type="live"]').show();
		<?php } ?>
		jQuery('body').on('change', '#stripe-test-enabled', function() {
			if (jQuery(this).is(':checked')) {
				jQuery('tr[data-type="test"]').show();
				jQuery('tr[data-type="live"]').hide();
			} else {
				jQuery('tr[data-type="test"]').hide();
				jQuery('tr[data-type="live"]').show();
			}
		});
	});
</script>