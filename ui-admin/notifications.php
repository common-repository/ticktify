<?php
/**
 * Metabox for notifications tab.
 *
 * @package ticktify-event\ui-admin
 * @version 1.0.0
 */

wp_enqueue_script('postbox');
$currentScreen = get_current_screen();
add_meta_box(
    'ticktify_metabox_notify_new_user_to_admin',
    __('New User Registration Email Notification For Admin', "ticktify"),
    [$this, 'ticktify_metabox_notify_new_user_to_admin'],
    esc_attr($currentScreen->id),
    'advanced',
    'high'
);
add_meta_box(
    'ticktify_metabox_notify_new_booking_to_admin',
    __('Event Booking Notification To Admin', "ticktify"),
    [$this, 'ticktify_metabox_notify_new_booking_to_admin'],
    esc_attr($currentScreen->id),
    'general',
    'high'
);
add_meta_box(
    'ticktify_metabox_notify_new_booking_to_user',
    __('Event Booking Notification For User', "ticktify"),
    [$this, 'ticktify_metabox_notify_new_booking_to_user'],
    esc_attr($currentScreen->id),
    'advanced',
    'high'
);
add_meta_box(
    'ticktify_metabox_cancelation_notification_for_user',
    __('Event Cancellation Notification For User', "ticktify"),
    [$this, 'ticktify_metabox_cancelation_notification_for_user'],
    esc_attr($currentScreen->id),
    'advanced',
    'high'
);
add_meta_box(
    'ticktify_metabox_cancelation_notification_for_admin',
    __('Event Cancellation Notification For Admin', "ticktify"),
    [$this, 'ticktify_metabox_cancelation_notification_for_admin'],
    esc_attr($currentScreen->id),
    'advanced',
    'high'
);
?>
<style>
    .hndle {
        padding-left: 1%;
    }
</style>
<form id="form-id" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <div class="wrap">
        <h1><?php esc_html_e("Event Settings", "ticktify"); ?></h1>
        <?php $this->ticktify_render_tabs(sanitize_text_field($this->current_tab)); ?>
    </div>
    <br>
    <div class="wrap">
        <div class="main" style="width: 100%;">
            <?php
            do_meta_boxes('', 'advanced', '');
            ?>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            postboxes.add_postbox_toggles(pagenow);
        });
    </script>
    <br>
    <div class="wrap">
        <div class="" style="width: 100%;">
            <?php
            do_meta_boxes('', 'general', '');
            ?>
        </div>
    </div>

    <div class="wrap">
        <div class="" style="width: 100%;">
            <?php
            do_meta_boxes('', 'email', '');
            ?>
        </div>
    </div>
    <?php wp_nonce_field('save_event_settings', '_ticktify_nonce_notificaton'); ?>
    <input type="hidden" name="action" value="save_event_settings">
</form>