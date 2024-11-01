/* 
* change payment option
*/
jQuery('input[type=radio][name=payment_option]').on('change', function() {
    jQuery("#stripePaymentSection").hide();
    jQuery("#paypalPaymentSection").hide();
    jQuery("#cashPaymentSection").hide();
    switch (jQuery(this).val()) {
        case 'cash':
            jQuery("#cashPaymentSection").show();
            break;
        case 'stripe':
            jQuery("#stripePaymentSection").show();
            break;
        case 'paypal':
            jQuery("#paypalPaymentSection").show();
            break;
    }
});

/* 
* Billing form validation
*/
jQuery('#stripePaymentBtn').on('click', function(e) {
    var frmvalid = jQuery("#billingInfoForm").valid();
    if (!frmvalid) {
        return false;
    }
});

jQuery('#freeBookingBtn').on('click', function(e) {
    jQuery('#freeBookingBtn').hide();
    jQuery('#booking_freeEvent_loading').show();
    var frmvalid = jQuery("#billingInfoForm").valid();
    if (!frmvalid) {
        jQuery('#freeBookingBtn').show();
        jQuery('#booking_freeEvent_loading').hide();
        return false;
    }
});


/* 
* Ajjax request for free booking
*/
jQuery('#freeBookingForm').submit(function (event) {
    event.preventDefault();
    var remove_item = jQuery('#remove_item').val();
    var action_url = jQuery('#freeBookingForm').attr('action');
    var billingInfoFormData = jQuery('#billingInfoForm').serialize()+"&action=ticktify_booked_free_event";
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: action_url,
        data: billingInfoFormData,
        success: function(response) {
            console.log(response);
            window.location.href = response.redirectUrl;
            //location.reload();
        }
    });
});