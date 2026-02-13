$(function () {
	$('.btn_filter').tooltip();
  
  
	

	$(".btn_filter").on('click', function(){
		$("#form_filter").animate({
			right: "0",
			
			}, 500, "easeOutBack", function() {
			// Animation complete.
		  });
	});
	$(".btn_close").on('click', function(){
		$("#form_filter").animate({
			right: "-400px",
			}, 500, "easeInBack", function() {
			// Animation complete.
		  });;
	});
  
})