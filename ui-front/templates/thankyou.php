<?php
/**
 * Thank you page template.
 *
 * @package ticktify-event\ui-front\templates
 * @version 1.0.0
 */
?>
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
$current_user = get_current_user_id();
$f_name = get_user_meta(sanitize_text_field($current_user), sanitize_key('first_name'), true);
$l_name = get_user_meta(sanitize_text_field($current_user), sanitize_key('last_name'), true);
$address = get_user_meta(sanitize_text_field($current_user), sanitize_key('address'), true);
$city = get_user_meta(sanitize_text_field($current_user), sanitize_key('city'), true);
$state = get_user_meta(sanitize_text_field($current_user), sanitize_key('state'), true);
$zip_code = get_user_meta(sanitize_text_field($current_user), sanitize_key('zip_code'), true);
$phone = get_user_meta(sanitize_text_field($current_user), sanitize_key('phone'), true);
$user_email = get_user_meta(sanitize_text_field($current_user), sanitize_key('user_email'), true);


if (!empty($_REQUEST['pid']) || !empty($_REQUEST['bid'])) {

   if (!empty($_REQUEST['pid'])) {
      $tranResults = Ticktify_Checkout::ticktify_get_transactions(sanitize_text_field($_REQUEST['pid']));
      $booking_id = $tranResults->booking_id;
   } elseif (!empty($_REQUEST['bid'])) {
      $booking_id = base64_decode(sanitize_text_field($_REQUEST['bid']));
   }
   $eventData = get_post_meta(sanitize_text_field($booking_id), sanitize_key('_events'), true);
?>
   <div class="receipt-box">
      <h3><?php esc_html_e('Booking Details', "ticktify"); ?> </h3>
      <div class="input_wrapper">
         <table class="form-table">
            <thead>
               <tr>
                  <th><?php esc_html_e('Booking Number', "ticktify"); ?></th>
                  <th><?php esc_html_e('Booking Date', "ticktify"); ?> </th>
                  <th><?php esc_html_e('Order Email', "ticktify"); ?></th>
                  <th><?php esc_html_e('Order Total', "ticktify"); ?></th>
                  <th><?php esc_html_e('Payment Method', "ticktify"); ?></th>
               </tr>
            </thead>
            <tr>
               <td><?php echo esc_html($booking_id); ?></td>
               <td><?php echo esc_html(get_the_date("d M Y", $booking_id)); ?></td>
               <td><?php echo esc_html($user_email) ?></td>
               <td><?php echo esc_html((!empty($tranResults->paid_amount)) ? $tranResults->paid_amount : '0.00'); ?></td>
               <td><?php echo esc_html((!empty($tranResults->payment_method)) ? $tranResults->payment_method : ""); ?></td>
            </tr>
         </table>
      </div>
      <br><br>
      <div class="input_wrapper">
         <table class="form-table">
            <!-- <thead> -->
            <tr>
               <th><?php esc_html_e('Event Id', "ticktify"); ?></th>
               <th><?php esc_html_e('Event Name', "ticktify"); ?> </th>
               <th><?php esc_html_e('Price', "ticktify"); ?></th>
               <th><?php esc_html_e('Quantity', "ticktify"); ?></th>
               <th><?php esc_html_e('Total', "ticktify"); ?></th>
            </tr>
            <!-- </thead> -->
            <?php foreach ($eventData as $key => $eventVal) :  ?>
               <tr>
                  <td><?php echo esc_html($eventVal['event_id']) ?></td>
                  <td><?php echo esc_html($eventVal['event_title']) ?></td>
                  <td><?php echo esc_html($eventVal['price']) ?></td>
                  <td><?php echo esc_html($eventVal['quantity']) ?></td>
                  <td><?php echo esc_html($eventVal['subtotal']) ?></td>
               </tr>
            <?php endforeach; ?>
         </table>
      </div>
      <div class="billing_details">
         <h4><?php esc_html_e('Billing Details', "ticktify"); ?></h4>
         <p><?php echo esc_html($f_name . " " . $l_name) ?></p>
         <p><?php echo esc_html($address) ?></p>
         <p><?php echo esc_html($city) ?></p>
         <p><?php echo esc_html($state) ?></p>
         <p><?php echo esc_html($zip_code) ?></p>
         <p><?php echo esc_html($user_email) ?></p>
      </div>
   </div>
<?php } ?>