<?php
/**
 * The template for displaying all single events.
 *
 * @package ticktify-event\ui-front
 * @version 1.0.0
 */

get_header();
$zt_settings = get_option(sanitize_key('zt_settings'));
$var = get_option(sanitize_key('ticktify_booking_ticket'));
$add_tickets = '';
if ($var) {
    $add_tickets = $var['form_add_ticket'] ? $var['form_add_ticket'] : '';
}
$current_user = wp_get_current_user();
$event_details = get_post_meta(get_the_ID());
$total_seats = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('event_seats_numbers'), true);
$allow_max_seats = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('event_max_numbers'), true);
$total_booked_seats = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('_total_seats_booked'), true);

?>
<style type="text/css">
    .attachment-thumbnail {
        padding-right: 8% !important;
    }
</style>
<style>
    div#map {
        height: 100%;
        width: 100%;
    }
</style>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        the_post();
        do_action('ticktify_single_event_before');
        ?>
        <article <?php post_class(); ?> id="event-<?php esc_attr(the_ID()); ?>">
            <section class="row wrapper" style="margin-top: -9%;">
                <div class="column-2">
                    <div class="left">
                        <!-- cover image/ video -->
                        <?php global $post;
                        $url = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('video_for_post'), true);
                        if (!empty($url)) {
                        ?>
                            <video class="left-video" style="height: auto; width: 100%;" controls>
                                <source src="<?php echo esc_url($url); ?>" type="video/mp4" id="vidsrc" class="leftbox">
                            </video>
                        <?php } else {
                            //  <!-- banner image -->
                            $attachment = get_post_meta(sanitize_text_field(get_the_ID()), sanitize_key('my_image_for_post'), true); ?>
                            <img src="<?php echo esc_url($attachment); ?>" class="leftbox" style="width: 90%;">
                        <?php } ?>
                    </div>
                </div>
                <div class="column-3">
                    <h4>
                        <a class="wrapper" href="<?php echo  esc_url(get_the_permalink()); ?>">
                            <?php echo esc_html(the_title()); ?>
                        </a>
                    </h4>
                    <div class="row">
                        <!-- Display the countdown timer in an element -->
                        <div class="count_down center" id="demo"></div>
                    </div>
                    <div class="logo">
                        <?php
                        $image_attr = $event_details['event_gallery_imgs'][0];
                        $image_attr = wp_get_attachment_image_src($image_attr);
                        ?>
                        <?php if(!empty($image_attr)){?>
                        <img src="<?php echo esc_url($image_attr[0]); ?>" width="<?php echo esc_attr($image_attr[1]); ?>" height="<?php echo esc_attr($image_attr[2]); ?>" />
                        <?php }?>
                    </div>
                    <div class="date">
                        <?php $date = $event_details['_custom_date_meta_key'][0];
                        $date = unserialize($date);
                        ?>
                        <p><b><?php esc_html_e("Date:", "ticktify"); ?></b>
                            <span><?php echo esc_html($date[0]); ?></span> <?php esc_html_e("to", "ticktify"); ?>
                            <span>
                                <?php echo esc_html($date[1]); ?>
                            </span>
                        </p>
                    </div>
                    <div class="time">
                        <?php $time = $event_details['_custom_time_meta_key'][0];
                        $time = unserialize($time);
                        $newDate = date("Y-m-d", strtotime($date[0]));
                        ?>
                        <p>
                            <b><?php esc_html_e("Time:", "ticktify"); ?></b><span><?php echo esc_html($time[0]); ?></span> <?php esc_html_e("to", "ticktify"); ?> <span>
                                <?php echo esc_html($time[1]); ?>
                        </p>
                    </div>
                    <div class="taxono">
                        <p class="taxonomy-label">
                            <?php

                            $venue_terms = wp_get_post_terms(get_the_ID(), TICKTIFY_EVENT_VENUE_TAX, array("fields" => "names"));
                            if (!empty($venue_terms)) {
                                ?>
                                <input type="hidden" id="event-venue" value="<?php echo esc_attr($venue_terms[0]); ?>">
                                <span><b><?php esc_html_e("Venue:", "ticktify"); ?></b></span>
                                <?php
                                foreach ($venue_terms as $term) {
                                    echo esc_html($term);
                                }
                            }
                            ?>
                        </p>
                        <p class="taxonomy-label">
                            <?php

                            $org_terms = wp_get_post_terms(get_the_ID(), TICKTIFY_EVENT_ORGANIZER_TAX, array("fields" => "names"));
                            if (!empty($org_terms)) {
                                ?><span><b><?php esc_html_e("Organizer:", "ticktify"); ?></b></span> 
                                <?php
                                    foreach ($org_terms as $term) {
                                        echo esc_html($term);
                                    }
                            }
                            ?>
                        </p>
                        <div class="boxes-2">
                            <p class="event_info"><strong><?php esc_html_e("Total Seats: ", "ticktify"); ?></strong><span><?php echo esc_html($total_seats); ?></span>&nbsp;&nbsp;</p>
                            <p><strong><?php esc_html_e("Allow Max Seats: ", "ticktify"); ?></strong> <span>
                                    <?php echo esc_html($allow_max_seats); ?>
                                </span>&nbsp;&nbsp;
                            </p>
                            <p><strong><?php esc_html_e("Booked Seats: ", "ticktify"); ?></strong> <span>
                                    <?php echo esc_html((!empty($total_booked_seats)) ? $total_booked_seats : "0"); ?>
                                </span>&nbsp;&nbsp;
                            </p>
                        </div>
                        <div class="boxes-2">
                            <?php
                            $price_seats = $event_details['event_price_seat'][0]; ?>
                            <p>
                                <strong><?php esc_html_e("Price:", "ticktify"); ?></strong>
                                <?php if ($price_seats != 0.00) { ?>
                                    <span> <?php echo esc_attr($price_seats); ?> </span>
                                <?php } else { ?>
                                    <span><?php esc_html_e("FREE", "ticktify"); ?></span>
                                <?php } ?>
                            </p>
                        </div>
                        <div>
                            <?php echo '<p id="booked_message"> </p>'; ?>
                        </div>
                        <div class="grid">
                            <form id="booking_cart_from" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                              
                                <?php 
                                if( is_user_logged_in()){

                                $current_id = get_current_user_id(); ?>
                                <input type="hidden" name="customer_id" value="<?php echo esc_attr($current_id); ?>">
                                <input type="hidden" name="event_id" value="<?php esc_attr(the_ID()); ?>">
                                <input type="hidden" name="event_title" value="<?php esc_attr(the_title()); ?>">
                                <input type="hidden" name="price" value="<?php echo esc_attr($price_seats); ?>">
                                <input type="hidden" name="event_date" value="<?php echo esc_attr($newDate); ?>">
                                <input type="hidden" name="event_time" value="<?php echo esc_attr($time[0]); ?>">
                                <?php
                                if (!empty($total_booked_seats)) {
                                    $remaining_seats = ($total_seats - $total_booked_seats);
                                    if ($remaining_seats <= $allow_max_seats) {
                                        $allow_max_seats = $remaining_seats;
                                    }
                                }
                                // check event EXPIRE
                                if (date("Y/m/d", strtotime($date[0])) . " " . date("H:i", strtotime($time[0])) > current_time("Y/m/d H:i")) {
                                    if (!empty($allow_max_seats)) {
                                    ?>
                                        <div class="form-group">
                                            <input type="number" id="amountSlider" max="<?php echo esc_attr($allow_max_seats); ?>" name="quantity" min="1" class="grid-2" value="1">&nbsp;
                                            <span>
                                                <input type="hidden" name="action" value="ticktify_add_to_cart">
                                                <input type="submit" id="cart_submit" class="submit" value="<?php _e('Submit', "ticktify") ?>" name="submit_cart" />
                                                <span id="booking_form_loading" style="display:none" class="loader"><?php esc_html_e("Loading...", "ticktify"); ?></span>
                                            </span>
                                        </div>
                                <?php } else {
                                        echo '<div class="form-group"><span style="color:red;" >' . esc_html__("All tickets are booked!", "ticktify") . '</span></div>';
                                    }
                                }
                             } else{
                                $ticktify_settings = get_option(sanitize_key('ticktify_settings'));
                                echo '<div>' . esc_html__('You Are Not Currently Logged In ', 'ticktify') . '
                                <a class="nav-tab" href="' . esc_url(get_permalink($ticktify_settings['pages']['ticktify_login'])) . '">' . esc_html__('Click Here For Login', 'ticktify') . '</a>
                            </div>';


                             }
                             ?>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <div class="column-4">
                <p><?php echo wp_kses_post(get_the_content()); ?></p>
                <div class="container">
                    <?php
                    $image_attributes = $event_details['event_gallery_img'][0];
                    $image_attributes = explode(',', $image_attributes);
                    if (!empty($image_attributes)) {
                    ?>
                        <div class="fade">
                            <?php foreach ($image_attributes as $custom_keys) { ?>
                                <div>
                                    <?php echo wp_get_attachment_image(sanitize_text_field($custom_keys)); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="column-5" style="text-align: center;">
                <h4><?php esc_html_e("Sponsors", "ticktify"); ?></h4>
                <p class="taxonomy-label" style="margin-top: -2%;">
                <div class="row" style="text-align: center; display: inline-flex;">
                    <?php
                    $sponsors_term = wp_get_post_terms(get_the_ID(), TICKTIFY_EVENT_SPONSORS_TAX, array('fields' => 'all'));
                    foreach ($sponsors_term as $meta_term) {
                        $image_sponsors_id = get_term_meta(sanitize_text_field($meta_term->term_id), sanitize_key('image_sponsors_id'), true);
                        echo "<div style='margin: 0 10px;'>" . wp_get_attachment_image(sanitize_text_field($image_sponsors_id)) . '<br>';
                        echo esc_html($meta_term->name) . '</div>';
                    }
                    ?>
                </div>
                </p>
            </div>
            <div class="column-6" style="text-align: center;">
                <h4><?php esc_html_e("Artist & Speakers", "ticktify"); ?></h4>
                <p class="taxonomy-label" style="margin-top: -2%;">
                <div class="row" style="text-align: center; display: inline-flex;">
                    <?php
                    $artist_term = wp_get_post_terms(get_the_ID(), TICKTIFY_EVENT_ARTIST_TAX, array('fields' => 'all'));
                    foreach ($artist_term as $meta_term) {
                        $image_artist_id = get_term_meta(sanitize_text_field($meta_term->term_id), sanitize_key('image_artist_id'), true);
                        echo "<div style='margin: 0 10px;' >" . wp_get_attachment_image(sanitize_text_field($image_artist_id))  . '<br>';
                        echo esc_html($meta_term->name) . '</div>';
                    }
                    ?>
                </div>
                </p>
            </div>
            <div class="column-7">
                <?php
                $gmap_api_key = get_option(sanitize_key('ticktify_pagination_settings'));
                if (!empty($gmap_api_key['event_map_api']['_google_map_api_key'])) {
                    $ticktify_api_key = $gmap_api_key['event_map_api']['_google_map_api_key'] ? $gmap_api_key['event_map_api']['_google_map_api_key'] : '';
                    ?>
                    <div class="location-gmap" style="width:100%;height:500px;">
                        <input type="hidden" id="google-map-api-key" value="<?php echo esc_attr($ticktify_api_key); ?>">
                        <div id="map"></div>
                    </div>
                <?php } ?>
            </div>
        </article>
        <?php
        do_action('ticktify_single_event_after');
        ?>
    </main>
</div>
<script>
    var countDownDateFormat = '<?php echo date("M d Y", strtotime($date[0])) . " " . date("H:i", strtotime($time[0])); ?>';
    var endDate = new Date(countDownDateFormat).getTime();
    //console.log(countDownDateFormat);
    var x = setInterval(function() {
        var date = new Date().toLocaleString('en-ZA');
        var startDate = new Date().getTime();
        //console.log(startDate);
        var distance = endDate - startDate;
        //console.log(distance);
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("demo").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("demo").innerHTML = '<span style="color:red;"><?php _e('EXPIRED', "ticktify"); ?></span>';
            document.getElementById("booking_cart_from").style.display = "none";
        }
    }, 1000);
</script>
<script>
    var geocoder;
    var map;
    var address = document.getElementById('event-venue').value;

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {
                lat: 28.644800,
                lng: 77.216721
            }
        });
        geocoder = new google.maps.Geocoder();
        codeAddress(geocoder, map);
    }

    function codeAddress(geocoder, map) {
        geocoder.geocode({
            'address': address
        }, function(results, status) {
            if (status === 'OK') {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    title: address
                });
            } else {
                alert('<?php _e('Geocode was not successful for the following reason: ', "ticktify") ?>' + status);
            }
        });
    }
    // load google map script
    var GOOGLE_MAP_KEY = document.getElementById('google-map-api-key').value;

    function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + GOOGLE_MAP_KEY + '&callback=initMap';
        document.body.appendChild(script);
    }
    window.onload = loadScript;

    jQuery("#cart_submit").click(function(event) {
        jQuery('#cart_submit').hide();
        jQuery('#booking_form_loading').show();
    });
</script>
<?php get_footer(); ?>