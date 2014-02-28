///////////////////////////////		
// iPad and iPod Detection
///////////////////////////////
	
function isiPad(){
    return (navigator.platform.indexOf("iPad") != -1);
}

function isiPhone(){
    return (
        //Detect iPhone
        (navigator.platform.indexOf("iPhone") != -1) || 
        //Detect iPod
        (navigator.platform.indexOf("iPod") != -1)
    );
}


///////////////////////////////		
// Isotope Browser Check
///////////////////////////////

function isotopeAnimationEngine(){
	if(jQuery.browser.mozilla || jQuery.browser.msie){
		return "jquery";
	}else{
		return "css";
	}
}


///////////////////////////////
// Project Filtering 
///////////////////////////////

function projectFilterInit() {
	jQuery('#filterNav a').click(function(){
		var selector = jQuery(this).attr('data-filter');
		var container = jQuery('.thumbs.masonry');	
		var colW = container.width() * .332;
		container.isotope({
			filter: selector,			
			hiddenStyle : {
		    	opacity: 0,
		    	scale : 1
			},
			resizable: false,
			masonry: {
				columnWidth: colW
			}	
		});
	
		if ( !jQuery(this).hasClass('selected') ) {
			jQuery(this).parents('#filterNav').find('.selected').removeClass('selected');
			jQuery(this).addClass('selected');
		}
	
		return false;
	});	
}

///////////////////////////////
// Isotope Grid Resize
///////////////////////////////

function gridResize() {	
	// update columnWidth on window resize
	var container = jQuery('.thumbs.masonry');
	var colW = container.width() * 0.332;	
	container.isotope({
		resizable: false,
		masonry: {
			columnWidth: colW
		}});	
}


///////////////////////////////
// Project thumbs 
///////////////////////////////

function projectThumbInit() {	
	var container = jQuery('.thumbs.masonry');	
	var colW = container.width() * 0.332;	
	// options			
	container.isotope({
		animationEngine: "jquery",	
		resizable: false,
		masonry: {
			columnWidth: colW
		}	
	});	
	
	jQuery(".project.small").css("opacity", "1");
	
}


jQuery.noConflict();
jQuery(window).load(function() {
	
	projectThumbInit();	
	projectFilterInit();
	jQuery(".videoContainer").fitVids();
	
	jQuery(".gallery a").attr('rel', 'gallery').fancybox({
			'overlayColor'	:	'#000',
			'titleShow'	:	false,
			'titlePosition'	:	'inside'
	});
	
	jQuery("a.lightbox").fancybox({
			'overlayColor'	:	'#000',
			'titleShow'	:	false,
			'titlePosition'	:	'inside'
	});
	
	jQuery(window).smartresize(function(){
		gridResize();
	});	

});