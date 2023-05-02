$(document).ready(function(){
	const knstSliderArrows = [
		'<svg class="nav-icon" viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M48.75 43.3333L48.75 5.41667C48.75 3.98008 48.1793 2.60233 47.1635 1.58651C46.1477 0.570686 44.7699 -3.47949e-07 43.3333 -4.7354e-07L5.41667 -3.78832e-06C3.98008 -3.91391e-06 2.60233 0.570682 1.58651 1.5865C0.57069 2.60233 3.91391e-06 3.98008 3.78832e-06 5.41666L4.7354e-07 43.3333C3.47949e-07 44.7699 0.570687 46.1477 1.58651 47.1635C2.60233 48.1793 3.98008 48.75 5.41667 48.75L43.3333 48.75C44.7699 48.75 46.1477 48.1793 47.1635 47.1635C48.1793 46.1477 48.75 44.7699 48.75 43.3333ZM37.9167 27.0833L24.375 27.0833L24.375 37.9167L10.8333 24.375L24.375 10.8333L24.375 21.6667L37.9167 21.6667L37.9167 27.0833Z" fill="currentColor"/></svg>',
		'<svg class="nav-icon" viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5.41667V43.3333C0 44.7699 0.570683 46.1477 1.5865 47.1635C2.60233 48.1793 3.98008 48.75 5.41667 48.75H43.3333C44.7699 48.75 46.1477 48.1793 47.1635 47.1635C48.1793 46.1477 48.75 44.7699 48.75 43.3333V5.41667C48.75 3.98008 48.1793 2.60233 47.1635 1.5865C46.1477 0.570683 44.7699 0 43.3333 0H5.41667C3.98008 0 2.60233 0.570683 1.5865 1.5865C0.570683 2.60233 0 3.98008 0 5.41667ZM10.8333 21.6667H24.375V10.8333L37.9167 24.375L24.375 37.9167V27.0833H10.8333V21.6667Z" fill="currentColor"/></svg>'
	];

	let isMobileDevice = false;

	if( /Android|webOS|iPhone|iPad|Mac|Macintosh|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		isMobileDevice = true;
	}

	$('.category-slider').owlCarousel({
		items: 1,
		nav: true,
		loop: false,
		navText: knstSliderArrows,
		dots: false,
		smartSpeed: 600
	});

	$(".product-slider").owlCarousel({
		items: 1,
		nav: true,
		loop: true,
		center: true,
		autoWidth: true,
		dots: false,
		navText: knstSliderArrows,
		responsive : {
			0: {
				items: 1
			},
			768 : {
				items: 2,
			},
			992: {
				items: 7,
			}
		}
	});

	$(".news-slider").owlCarousel({
		items: 1,
		margin: 30,
		nav: true,
		navText: knstSliderArrows,
		dots: false,
		responsive : {
			0: {
				items: 1
			},
			576 : {
				items: 2,
			},
			992: {
				items: 3,
			},
			1200: {
				items: 4,
			}
		}
	});

	/*ripple effect for buttons*/
	$.ripple(".knst-btn", {
		debug: false,
		on: 'mouseenter',
		opacity: 0.3,
		color: "auto",
		multi: true,
		duration: 0.4,
		easing: 'linear'
	});

	/* quantity */
	$('.js-quantity-plus').click(function () {
		$(this).prev().val(+$(this).prev().val() + 1);
	});
	$('.js-quantity-minus').click(function () {
		if ($(this).next().val() > 1) {
			if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() - 1);
		}
	});

	$(document).on('click', '.product-item__favorite', function(e){
		e.preventDefault();
		$(this).toggleClass('active');
	});

	if ( isMobileDevice == true ) {
		$('body').addClass('mobile-device-styles');
	} else {
		$('.product-slider .slide').each(function(){
			$(this).css('height', $(this).outerHeight()+74);
		});
	}
	
});