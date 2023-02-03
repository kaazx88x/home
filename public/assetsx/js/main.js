$(document).ready(function() {

	function fix_header() {
		var window_top = $(window).scrollTop();
		var div_top = $('#sticky-anchor').offset().top;
		if (window_top > div_top) {
			$('.nav-bar').addClass('fixed');
			$('#sticky-anchor').height($('.nav-bar').outerHeight());
		} else {
			$('.nav-bar').removeClass('fixed');
			$('#sticky-anchor').height(0);
		}
	}

	$(function() {
		$(window).scroll(fix_header);
		fix_header();
	});

	$(window).scroll(function() {
		var scroll = $(window).scrollTop();

		 //>=, not <=
		if (scroll >= 350) {
			//clearHeader, not clearheader - caps H
			$(".side-nav .details").addClass("fixed");
		} else {
			$(".side-nav .details").removeClass("fixed");
		}
	});

	$('#nav_up').on("click", function () {
		var percentageToScroll = 100;
		var percentage = percentageToScroll / 100;
		var height = $(document).scrollTop();
		var scrollAmount = height * (1 - percentage);

		console.log('scrollAmount: ' + scrollAmount);
		$('html,body').animate({
			scrollTop: 0
		});

	});

	$('#nav_down').on("click", function () {
		var percentageToScroll = 100;
		var percentage = percentageToScroll / 100;
		var height = $(document).height() - $(window).height();
		var scrollAmount = height * percentage;
		console.log('scrollAmount: ' + scrollAmount);
		jQuery("html, body").animate({
			scrollTop: scrollAmount
		}, 900);
	});

	$(".expand a.expand-toggle").click(function(e){
		$(".expand>.expandable-menu").toggleClass("true");
		e.stopPropagation();
	});
	$(document).on("click", function(e) {
		if ($(e.target).is(".expandable-menu,.expand-toggle") === false) {
		  $(".expand>.expandable-menu").removeClass("true");
		}
	});

	$(".country a.country-toggle").click(function(e){
		$(".country>.expandable-menu").toggleClass("true");
		e.stopPropagation();
	});
	$(document).on("click", function(e) {
		if ($(e.target).is(".expandable-menu,.country-toggle") === false) {
		  $(".country>.expandable-menu").removeClass("true");
		}
	});

	$(".top-link-toggle").click(function(e){
		$(".top-link > ul").toggleClass("true");
		e.stopPropagation();
	});
	$(document).on("click", function(e) {
		if ($(e.target).is(".top-link-toggle,.top-link > ul") === false) {
		  $(".top-link > ul").removeClass("true");
		}
	});

	$(".securecode").keyup(function () {
		if (this.value.length == this.maxLength) {
		  $(this).next('.securecode').focus();
		}
	});

	;( function ( document, window, index )
	{
		var inputs = document.querySelectorAll( '.inputfile' );
		Array.prototype.forEach.call( inputs, function( input )
		{
			var label	 = input.nextElementSibling,
				labelVal = label.innerHTML;

			input.addEventListener( 'change', function( e )
			{
				var fileName = '';
				if( this.files && this.files.length > 1 )
					fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
				else
					fileName = e.target.value.split( '\\' ).pop();

				if( fileName )
					label.querySelector( 'span' ).innerHTML = fileName;
				else
					label.innerHTML = labelVal;
			});

			// Firefox bug fix
			input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
			input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
		});
	}( document, window, 0 ));

});