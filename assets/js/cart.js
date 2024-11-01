// remove cart booked
jQuery(document).ready(function(){
    jQuery(document).on('click', '#delete', function() {
        var remove_item = jQuery(this).val();
        var action_url_cancel = jQuery('#cf_action_url_cancel').val();
        var _wpnonce = jQuery("input[name='remove_item_wpnonce']").val();
        jQuery.ajax({
            type: "POST",
            url: action_url_cancel,
            data: {action:'ticktify_remove_cart',_wpnonce: _wpnonce,remove_item: remove_item},
            success: function(response) {
                location.reload();
            }
        });
    });

    /*open mode for attendees*/
    jQuery(".attendeesModalBtn").click(function(){
        var cartId = jQuery(this).attr("data-cartId");
        var quantity = jQuery('.quantity_'+cartId).val();
        var actionurl = jQuery(this).attr("data-actionurl");

        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: actionurl, 
            data: {
                action:'attendees_model',
                cartID: cartId,
                quantity:quantity,
            },
            success: function(result){
                // console.log(result);
                jQuery('#attendeesTbody').html(result.attendees_tr);
                jQuery(".attendeesModal").show();
            }
        });
       
    });

    /*close attendees model*/
    jQuery(".attendeesModal .close").click(function(){
        jQuery(".attendeesModal").hide();
    });

    /* submit attendees */
    jQuery('#attendeesForm').submit(function (event) {
        event.preventDefault();

        var attendees_name = jQuery("input[name='attendees_name[]']")
        .map(function(){return jQuery(this).val();}).get();

        var attendees_age = jQuery("input[name='attendees_age[]']")
        .map(function(){return jQuery(this).val();}).get();

        var cart_id = jQuery("input[name='cart_id']").val();
        var _wpnonce = jQuery("input[name='_wpnonce']").val();

        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: jQuery('#attendeesForm').attr('action'), 
            data: {
                action:'attendees_post',
                _wpnonce: _wpnonce,
                attendees_name: attendees_name,
                attendees_age: attendees_age,
                cart_id: cart_id,
            },
            success: function(result){
                if (result) {
                    jQuery('#attendeesSubmitMsg').text(result.msg);
                    setTimeout(function(){ 
                        jQuery(".attendeesModal").hide(); 
                        jQuery('#attendeesSubmitMsg').text('');
                    }, 2000);
                }
                //console.log(result);
            }
        });
    });

});



