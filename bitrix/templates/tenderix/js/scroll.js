$(document).ready(function() {
	var $backToTop = $('.back-to-top');
	var showAfter = 300;

	function toggleBackToTop() {
		if ($(window).scrollTop() > showAfter) {
			$backToTop.addClass('is-visible');
		} else {
			$backToTop.removeClass('is-visible');
		}
	}

	$(window).on('scroll', toggleBackToTop);
	toggleBackToTop();

	$backToTop.on('click', function(e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: 0 }, 400);
	});
});
