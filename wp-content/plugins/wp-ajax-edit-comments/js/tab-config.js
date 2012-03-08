jQuery(document).ready(function() {
	//For the tabs
		try {
			jQuery("ul.tabs").tabs("div.panes > div", { effect: "fade",fadeInSpeed: 400}).history();
		} catch(err) { }
});