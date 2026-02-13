$(document).ready(function() {
	
	$(window).scroll(function(){

		// Show button after 100px
		var showAfter = 100;
		if ( $(this).scrollTop() > showAfter ) { 
			$('.back-to-top').fadeIn();
		} else { 
			$('.back-to-top').fadeOut();
		}
	});

	//Click event to scroll to top
	$('.back-to-top').click(function(){
		$('html, body').animate({scrollTop : 0},800);
			return false;
	});
	
});
