$(document).ready( function() {
	
	var Overlay = document.createElement('div');
	Overlay.className = 'BackDrop';
	document.getElementsByTagName('body')[0].appendChild(Overlay);
			
	$("#MoreToggle").click(function(more){
		$(".MoreMenu").toggleClass("active");
		more.stopPropagation()
	});

	$(document).mouseup(function (more) {
		 var popup = $(".MoreMenu");
		 if (!$('#MoreToggle').is(more.target) && !popup.is(more.target) && popup.has(more.target).length == 0) {
			$(".MoreMenu").removeClass("active");
		 }
	});
	
	$(window).scroll(function() {    
		var scroll = $(window).scrollTop();
		if (scroll >= 800) {
			$(".ProductHeader").addClass("Scrolled");
		} else {
			$(".ProductHeader").removeClass("Scrolled");
		};
		if (scroll >= 150) {
			$(".ListingTopbar").addClass("fixtop");
		} else {
			$(".ListingTopbar").removeClass("fixtop");
		};
	});

	
	$(".ProductSelection").click(function(){
		$(".PanelSelection").addClass("visible");
		$(".BackDrop").addClass("visible");
	});
	$(".ProductAction .btn-buy").click(function(){
		$(".PanelSelection").addClass("visible");
		$(".BackDrop").addClass("visible");
	});
	$(".BackDrop").click(function(){
		if($(".BackDrop").hasClass('visible')){
			$(".PanelSelection").removeClass("visible");
			$(".CategoryPanel").removeClass("visible");
			$(".FilterPanel").removeClass("visible");
			$(".BackDrop").removeClass("visible");
		}
	});
	
	$(".CloseToggle").click(function(){
		if($(".BackDrop").hasClass('visible')){
			$(".CategoryPanel").removeClass("visible");
			$(".FilterPanel").removeClass("visible");
			$(".BackDrop").removeClass("visible");
		}
	});
	
	$(".CategoryToggle").click(function(){
		$(".CategoryPanel").addClass("visible");
		$(".BackDrop").addClass("visible");
	});
	$(".FilterToggle").click(function(){
		$(".FilterPanel").addClass("visible");
		$(".BackDrop").addClass("visible");
	});
	
	$(".WalletToggle").click(function(){
		$(".Wallet").addClass("active");
	});
	$(".Wallet .wallet-backdrop").click(function(){
		$(".Wallet").removeClass("active");
	});
	
	
	/* ## QUANTITY PLUGIN ## */
	$('.add').click(function () {
		$(this).prev().val(+$(this).prev().val() + 1);
	});
	$('.sub').click(function () {
		if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() - 1);
	});
	
	
	$("body").css("display", "none");
	$("body").fadeIn(500);
	$("a.transition").click(function(event){
		event.preventDefault();
		linkLocation = this.href;
		$("body").fadeOut(250, redirectPage);      
	});
	 
	function redirectPage() {
		window.location = linkLocation;
	}
	
	/* ## IMG LAZY LOAD ## */	
	$('.lazyload').lazy({
		effect: 'fadeIn',
		effectTime: 2000,
		threshold: 0
	});
	
	if ($(window).width() < 960) {
	   $('.Search img').addClass('SearchToggle');
	}
	else {
	   $('.Search img').removeClass('SearchToggle');
	}
	$(".SearchToggle").click(function(){
		$(".Search").addClass("input-visible");
	});
	$(".CloseSearch").click(function(){
		$(".Search").removeClass("input-visible");
	});

	
});