/* 
* Ajax request for free booking
*/
jQuery('.booking_event_cancel').click(function(event){
    event.preventDefault();
    var action_url = jQuery(this).attr('data-actionUrl');
    var bookingId = jQuery(this).attr('data-bookingId');
    var eventId = jQuery(this).attr('data-eventId');
    var _wpnonce = jQuery(this).attr('data-wpnonce');

    var $this = jQuery(this);
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: action_url,
        data: {
            action:'ticktify_update_booking_status',
            _wpnonce:_wpnonce,
            bookingId: bookingId,
            eventId:eventId,
            bookingEventStatus:'cancelled',
        },
        beforeSend:function(){
            return confirm("Are you sure you want to cancel booking?");
         },
        success: function(response) {
            if(response.status =="success"){
                jQuery('#event_status_'+eventId).text(response.bookingStatus);
                $this.hide();
            }
        }
    });
});