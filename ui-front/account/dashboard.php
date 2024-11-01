<?php
/**
 * Dashboard page for user profile.
 *
 * @package ticktify-event\ui-front\account
 * @version 1.0.0
 */

global $current_user;
$user_info = get_user_meta(get_current_user_id());
?>
<center>
    <div>
        <h3 id="dashboard" style="color: brown;">
            <?php esc_html_e("Dashboard", "ticktify"); ?>
        </h3>
        <p>
            <?php esc_html_e("Hello", "ticktify"); ?>
            <?php echo esc_html($user_info['first_name'][0]) . ' ' . esc_html($user_info['last_name'][0]); ?> <a
                class="nav-tab" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">
                <?php esc_html_e("Logout", "ticktify"); ?>
            </a>
        </p>
    </div>
</center>