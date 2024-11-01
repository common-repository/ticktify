<?php
/**
 * Metabox for event cancellation tab.
 *
 * @package ticktify-event\ui-admin
 * @version 1.0.0
 */

wp_enqueue_script('postbox');

$currentScreen = get_current_screen();
add_meta_box(
    'ticktify_metabox_cancellation_settings',
    __('Cancellation Settings', "ticktify"),
    [$this, 'ticktify_metabox_cancellation_settings'],
    esc_attr($currentScreen->id),
    'advanced',
    'high'
);

$footer_Screen = (isset($_GET['action']) && 'footer' == $_GET['action']) ? true : false; ?>
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
    <?php wp_nonce_field('save_cancellation_settings', '_ticktify_nonce_cancellation'); ?>
    <input type="hidden" name="action" value="save_cancellation_settings">
</form>