/*
|--------------------------------------------------------------------------
| POPUP data-width="600" data-height="800" rel="1" id="pop_1" class="newWindow"
|--------------------------------------------------------------------------
*/
jQuery(document).ready(function($){
var scrollBArray = [ "scrollbars=no",  /* rel="0" */
                     "scrollbars=yes" /* rel="1" */
                   ];
$('.newWindow').click(function (event){
var url = $(this).attr("href");
var w1 = $(this).attr("data-width"), h1 = $(this).attr("data-height");
var left  = ($(window).width()/2)-(w1/2),
    top   = ($(window).height()/2)-(h1/2);
var windowName = $(this).attr("id");
var scrollB = scrollBArray[$(this).attr("rel")];
window.open(url, windowName,"width="+w1+", height="+h1+", top="+top+", left="+left+", "+scrollB);
event.preventDefault();
      });
});

/* alert
-------------------------------------------------------------- */
jQuery(document).ready(function($) {  
$("#cl").click(function(){
alert("fabrix@fabrix.net");
});
});

/* Select
-------------------------------------------------------------- */
jQuery( document ).ready( function() {
	jQuery( '#url_shortener' ).live( 'change', function() {
		var currentValue = jQuery( this ).val();
 		jQuery( '#select1, #select2' ).hide();
		if ( currentValue == 'yourls' ) {
			jQuery( '#select1' ).slideDown();
		  		} else if ( currentValue == 'bitly' ) {
		   	jQuery( '#select2' ).slideDown();
		}
	}).change();

 });

/* Tabs
-------------------------------------------------------------- */
jQuery(document).ready(function($){
	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});

});

