jQuery(document).ready(function($) {
    jQuery('#title').attr('required', true);
    jQuery('[name="paid_event"]').attr("required", true);
 });
 
 
     jQuery(document).ready(function ($) {
         //taxonomy
         var tx = 'ticktify_organizer';
 
         var $scope = $('#' + tx + '-all > ul');
         $('#publish').click(function(){
             if ($scope.find('input:checked').length > 0) {
                 alert('found');
                 return true;
             } else {
                 alert('not found');
                 return false;
             }
         });
     });
 
 