
/* ==============================================
Preloader
=============================================== */

$(window).load(function(){
    $("#preloader").delay(500).fadeOut(1000);
});	

// Sticky Plugin
  $(document).ready(function(){
    $(".navbar").sticky({topSpacing:0});
  });