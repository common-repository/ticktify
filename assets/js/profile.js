
jQuery(document).ready(function(){
    jQuery('.nav-tab').on('click', function() {
      jQuery('li').removeClass('active');
      jQuery(this).addClass("active");
      });
  });
