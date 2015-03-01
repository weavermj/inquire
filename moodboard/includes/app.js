$( document ).ready(function() {

	var mainContent = $('.main-content');

	

	$( "#traditional" ).on( "click", function() {
		mainContent.removeClass('arial');
		$('.was-crimson').removeClass('was-crimson').removeClass('opensans').addClass('crimson');
		$('.was-cinzel').removeClass('was-cinzel').removeClass('opensans').addClass('cinzel');
		$('.was-jura').removeClass('was-jura').removeClass('opensans').addClass('jura');
	});

	$( "#traditional-copy" ).on( "click", function() {
		$('.was-crimson').addClass('crimson').removeClass('arial').removeClass('opensans');
	});

	$( "#traditional-headings" ).on( "click", function() {
		$('h2.was-cinzel').addClass('cinzel').removeClass('arial').removeClass('opensans').removeClass('bebas').removeClass('was-cinzel');
		$('h3.was-cinzel').addClass('cinzel').removeClass('arial').removeClass('opensans').removeClass('bebas').removeClass('was-cinzel');
	});

	$( "#traditional-images" ).on( "click", function() {
		$('span.was-cinzel').removeClass('arial').removeClass('opensans').addClass('cinzel').removeClass('bebas');
	});

	$( "#traditional-links" ).on( "click", function() {
		$('.was-jura').removeClass('arial').removeClass('opensans').addClass('jura');
	});

	$( "#arial" ).on( "click", function() {
		mainContent.addClass('arial');
		$('.was-crimson').removeClass('was-crimson').removeClass('opensans').addClass('crimson');
		$('.was-cinzel').removeClass('was-cinzel').removeClass('opensans').addClass('cinzel');
		$('.was-jura').removeClass('was-jura').removeClass('opensans').addClass('jura');
	});

	$( "#arial-copy" ).on( "click", function() {
		$('.crimson').addClass('was-crimson').removeClass('crimson').removeClass('opensans').addClass('arial');
		$('.was-crimson').addClass('was-crimson').removeClass('crimson').removeClass('opensans').addClass('arial');
	});

	$( "#arial-headings" ).on( "click", function() {
		$('h2.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').removeClass('bebas').addClass('arial');
		$('h2.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').removeClass('bebas').addClass('arial');
		$('h3.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').removeClass('bebas').addClass('arial');
		$('h3.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').removeClass('bebas').addClass('arial');
	});

	$( "#arial-images" ).on( "click", function() {
		$('span.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').addClass('arial').removeClass('bebas');
		$('span.was-cinzel').removeClass('opensans').addClass('arial').removeClass('bebas');
	});

	$( "#arial-links" ).on( "click", function() {
		$('.jura').addClass('was-jura').removeClass('jura').removeClass('opensans').addClass('arial');
		$('.was-jura').removeClass('opensans').addClass('arial');
	});

	$( "#opensans" ).on( "click", function() {
		mainContent.removeClass('arial');
		$('.crimson').removeClass('crimson').addClass('opensans').addClass('was-crimson');
		$('.cinzel').removeClass('cinzel').addClass('opensans').addClass('was-cinzel');
		$('.jura').removeClass('jura').addClass('opensans').addClass('was-jura');
	});

	$( "#opensans-copy" ).on( "click", function() {
		$('.was-crimson').addClass('was-crimson').removeClass('crimson').removeClass('arial').addClass('opensans');
		$('.crimson').addClass('was-crimson').removeClass('crimson').removeClass('arial').addClass('opensans');
	});

	$( "#opensans-headings" ).on( "click", function() {
		$('h2.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('bebas').addClass('opensans');
		$('h2.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('bebas').addClass('opensans');
		$('h3.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('bebas').addClass('opensans');
		$('h3.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('bebas').addClass('opensans');
	});

	$( "#opensans-images" ).on( "click", function() {
		$('span.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').addClass('opensans').removeClass('bebas');
		$('span.was-cinzel').removeClass('arial').addClass('opensans').removeClass('bebas');
	});

	$( "#opensans-links" ).on( "click", function() {
		$('.jura').addClass('was-jura').removeClass('jura').removeClass('arial').addClass('opensans');
		$('.was-jura').removeClass('arial').addClass('opensans');
	});

	$( "#bebas-headings" ).on( "click", function() {
		$('h2.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('opensans').addClass('bebas');
		$('h2.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('opensans').addClass('bebas');
		$('h3.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('opensans').addClass('bebas');
		$('h3.was-cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('arial').removeClass('opensans').addClass('bebas');
	});

	$( "#bebas-images" ).on( "click", function() {
		$('span.cinzel').addClass('was-cinzel').removeClass('cinzel').removeClass('opensans').removeClass('arial').addClass('bebas');
		$('span.was-cinzel').removeClass('opensans').removeClass('arial').addClass('bebas');
	});

});
