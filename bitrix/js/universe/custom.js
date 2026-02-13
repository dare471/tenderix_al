( function($) {
	
	"use strict";
	
	
	/* ==============================================
		Owl Carousel 1
	=============================================== */		 
	$("#owl1").owlCarousel({
		items : 3,
		lazyLoad : true,
		navigation : false,
		pagination : true
	}); 
	
	/* ==============================================
		Owl Carousel 2
	=============================================== */		 
	$("#owl2").owlCarousel({
		items : 1,
		itemsCustom : false,
		itemsDesktop : [1199,1],
		itemsDesktopSmall : [980,1],
		itemsTablet: [768,1],
		itemsMobile : [479,1],
		lazyLoad : true,
		navigation : false,
		pagination : true
	}); 	
	
	/* ==============================================
		Owl Carousel 3
	=============================================== */		 
	$("#owl3").owlCarousel({
		items : 4,
		lazyLoad : true,
		navigation : false,
		pagination : false
	});
	
	
	
	/* ==============================================
		Pie Charts
	=============================================== */		
	
	jQuery('#pie-charts').waypoint(function(direction) {			
		jQuery('.chart').easyPieChart({
			barColor: "#4bcdf8",
			onStep: function(from, to, percent) {
				jQuery(this.el).find('.percent').text(Math.round(percent));
			}
		});
		}, {
		offset: function() {
			return jQuery.waypoints('viewportHeight') - jQuery(this).height() + 200;
		}
	});
	
	
	
	/* ==============================================
		TO TOP
	=============================================== */
	jQuery().UItoTop({ easingType: 'easeOutQuart' });
	
	
	//hiding horizontal bar
	javascript:void(document.body.style.setProperty('overflow-x','hidden',''));	
	
	
	
})(jQuery);