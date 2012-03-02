jQuery(document).ready(function() {
	//After the deadline
	$j = jQuery;
	$j('textarea#comment').before("<div id='aec_edit_options'></div>");
	if (aec_frontend.atd == 'true') {
		AtD.rpc_css_lang = aec_frontend.atdlang;
		$j('textarea#comment').addProofreader();
		$j("#submit").click(function() {  
				$j(".AtD_edit_button").trigger("click");
		});
		var spellcheck = $j("#AtD_0").clone(true);
		$j("#AtD_0").remove();
		$j("#aec_edit_options").append(spellcheck);
	}
	if (aec_frontend.expand == 'true') {
		//Don't show this option on a mobile device
		try {
			var uagent = navigator.userAgent.toLowerCase();
			if (uagent.search('iphone') > -1) { return true; }
			if (uagent.search('ipod') > -1) { return true; }
			if (uagent.search('webkit') > -1) { 
				if (uagent.search('series60') > -1) { 
					if (uagent.search('symbian') > -1) { return true; } 
				}
			}
			if (uagent.search('android') > -1) { return true; }
			if (uagent.search('windows ce') > -1) { return true; }
			if (uagent.search('blackberry') > -1) { return true; }
			if (uagent.search('palm') > -1) { return true; }
		} catch(err) { }
		//AEC Expand Comment Option
		$j("#aec_edit_options").append("<span class='aec_expand'></span>");
		$j(".aec_expand").colorbox({title: aec_frontend.title,iframe: true,href: aec_frontend.url, width:"90%", height:"90%", opacity: 0.6, onOpen: function() {$j(".AtD_edit_button").trigger("click");}});
	}
});