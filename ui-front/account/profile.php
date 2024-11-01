<?php
/**
 * User profile.
 *
 * @package ticktify-event\ui-front\account
 * @version 1.0.0
 */

wp_enqueue_script('jquery');
wp_enqueue_style('profile-css');
wp_enqueue_script('profile-js');
wp_enqueue_script('booking-js');
$tabResult = Ticktify_Profile::ticktify_profile_tab();
$currentTab = sanitize_text_field(isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'dashboard');
?>
<style>
    table,
    th,
    td {
        border: 1px solid black;
    }

    .detail-sidemenu li a.nav-tab-active {
        list-style: none;
        background-color: #5f9ea0;
        color: #000000;
    }
</style>
<?php
if ( is_user_logged_in() ) {  ?>
<div class="sidebar">
    <ul class="list-unstyled detail-sidemenu mrb15">
    <?php
        foreach ($tabResult as $tab => $title) {
            $class = ($tab === $currentTab) ? 'nav-tab-active' : '';
            printf(
                '<li><a class="nav-tab %s" href="%s" >%s</a></li>',
                esc_attr($class),
                esc_url(site_url('ticktify-profile/?tab=' . $tab)),
                esc_html($title)
            );
        }
        ?>
        <li>
            <a class="nav-tab" href="<?php echo esc_url(wp_logout_url(home_url())); ?>"><?php esc_html_e("Logout", "ticktify"); ?></a>
        </li>
  </ul>
</div>

<div id="profile-content">
    <?php do_action('ticktify_profile_tab_content'); ?>
</div>

<?php }
else{
    $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
    echo '<div>' . esc_html__('You do not have permission to access this page, ', 'ticktify') . '
    <a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])) . '">' . esc_html__('Click here for Login', 'ticktify') . '</a>
</div>';

}
