jQuery(document).ready(function($) { 
	
	$('#nav ul.superfish, #subnav ul.superfish, #header ul.nav, #header ul.menu').superfish({ 
		delay:       100,								// 0.1 second delay on mouseout 
		animation:   {opacity:'show',height:'show'},	// fade-in and slide-down animation 
		dropShadows: false								// disable drop shadows 
	});
	
});