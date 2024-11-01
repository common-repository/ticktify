<?php
/**
 * Event list page template.
 *
 * @package ticktify-event\ui-front
 * @version 1.0.0
 */
?>

<div class="card-group " style="display:inline-flex; width:33%; border-radius: 5px;">
    <div class="card">
        <?php $event_details = get_post_meta(get_the_ID());
        $attachment = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('my_image_for_post'), true);
        $video_check = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('video_check'), true);

        if (!empty($video_check)) {
        ?>
            <video class="left-video" style="height: 18em; width: 18em; margin-bottom: -17%;object-fit: cover;" controls="">
                <source src="<?php echo esc_url(get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('video_for_post'), true)); ?>" type="video/mp4" id="vidsrc" class="leftbox">
            </video>
        <?php } else { ?>
            <img src="<?php echo esc_url($attachment); ?>" style="width:18rem; height:18em;margin-bottom: -17%;" class="card-img" alt="...">
        <?php } ?>
        <div style="padding-left: 20px; line-height:50%; margin-top: 30px;">
            <h4 style="margin-top: 20%;"> <a class="toto" href="<?php echo esc_url(get_the_permalink()); ?>"> <?php echo esc_html(the_title()); ?></a>
            </h4>
            <?php
            $event_date = $event_details['_custom_date_meta_key'][0];
            $event_occuring_date =  unserialize($event_date);
            ?>
            <p class="card-date">
                <?php echo esc_html($event_occuring_date[0]); ?>
            </p>
            <?php
            $event_time = $event_details['_custom_time_meta_key'][0];
            $event_occuring_time = unserialize($event_time);
            ?>
            <p class="card-time">
                <?php echo esc_html($event_occuring_time[0]); ?>
            </p>
            <?php $venue = TICKTIFY_EVENT_VENUE_TAX;
            $terms = wp_get_post_terms(get_the_ID(), $venue, array("fields" => "names"));
            ?> 
            <p> <?php esc_html_e("Venue :", "ticktify"); ?><span><?php echo !empty($terms) ? esc_attr($terms[0]) : ''; ?></span></p>

            <?php $price_seats = $maxm_seats = $event_details['event_price_seat'][0]; ?>
            <?php if ("0.00" == ($price_seats)) { ?>
                <p><?php esc_html_e("FREE", "ticktify"); ?></p>
            <?php } else { ?>
                <?php $price_seats = $maxm_seats = $event_details['event_price_seat'][0]; ?>
                <p><?php esc_html_e("Price:", "ticktify"); ?> &#x20a8; <span><?php echo esc_html($price_seats); ?></span></p>
            <?php } ?>
            <a class="btn" href="<?php echo esc_url(get_the_permalink()); ?>"><?php esc_html_e("Get Ticket", "ticktify"); ?></a>
        </div>
    </div>
</div>