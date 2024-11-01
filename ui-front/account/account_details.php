<?php
/**
 * Account details page for user profile.
 *
 * @package ticktify-event\ui-front\account
 * @version 1.0.0
 */

global $current_user;
$first_name = '';
 $last_name = '';
wp_enqueue_style('profile-css');
$user_info = get_user_meta(get_current_user_id());
// include_once(TICKTIFY_PLUGIN_INCLUDES_DIR . 'class-profile.php'); 
?>
<center>
    <div>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('account_details')); ?>">
            <div>
                <h3 id="account" style="color: brown;"><?php esc_html_e("Account details", "ticktify"); ?></h3>
                <?php
                $contact_number = '';
                if(!empty($user_info)){
                    $first_name = $user_info['first_name'][0];
                    $last_name = $user_info['last_name'][0];
                    if(metadata_exists('user', get_current_user_id(), 'phone')) {
                        $contact_number = $user_info['phone'][0];
                    }
                    
                }
                
                ?>
                <div class="form_group">
                    <label for="name" class="form_label"><?php esc_html_e("First Name ", "ticktify"); ?> </label>
                    <input type="text" name="account_firstname" class="form_input" value="<?php echo esc_attr($first_name); ?>" />
                    <label for="name" class="form_label"><?php esc_html_e("Last Name ", "ticktify"); ?> </label>
                    <input type="text" name="account_lastname" class="form_input" value="<?php echo esc_attr($last_name); ?>" />
                </div>
                <div class="form_group">
                    <label for="name" class="form_label"><?php esc_html_e("User Name *", "ticktify"); ?></label>
                    <input type="text" class="form_input" name="account_username" value="<?php echo esc_attr($current_user->display_name); ?>" required />
                </div>
                <div class="form_group">
                    <label for="name" class="form_label"><?php esc_html_e("Email Address *", "ticktify"); ?></label>
                    <input type="text" class="form_input" name="account_email" value="<?php echo esc_attr($current_user->user_email); ?>" required />
                </div>
                <div class="form_group">
                    <label for="name" class="form_label"><?php esc_html_e("Contact Number", "ticktify"); ?></label>
                    <input type="tel" class="form_input" name="account_contact" value="<?php echo esc_attr($contact_number); ?>" pattern="[789][0-9]{9}" />
                    <!-- <small>Format: 123-456-7890</small> -->
                </div>
                <p>
                    <button name="action" type="submit" value="ticktify_save_account_details"><?php esc_html_e('Save Changes', "ticktify"); ?></button>
                </p>
            </div>
        </form>
    </div>
</center>
