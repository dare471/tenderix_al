/* ==============================================
	Revolution Slider
=============================================== */
		revapi = jQuery('.tp-banner').revolution(
		{
			delay:9000,
			startwidth:1170,
			startheight:670,
			hideThumbs:10,
			fullWidth:"on",

			thumbWidth:100,
			thumbHeight:50,
			thumbAmount:4,
			onHoverStop:"on",
						
			navigationType:"none",
			navigationArrows:"solo",
			navigationStyle:"preview4",
			
			parallax:"mouse",
			parallaxBgFreeze:"on",
			parallaxLevels:[10,7,4,3,2,5,4,3,2,1],
		});