var sharenow = {
	
	selectedDomain: '',
	selectedDefault: 'all',
	ieFallback: (/MSIE [678]/).test(navigator.userAgent),
	
	stgOptions 			: {
		"service":"facebook", 
		 timer: {
			countdown: 30,
			interval: 10,
			enable: false
		},
		frictionlessShare: false,
		style: "3",
		disableAll: false,
	},
	
	copyBarProperties 		: function (master, a) {	
	    for (var b in a){
	    	if(master.hasOwnProperty(b) && a[b]!==null){
	    		if (typeof (master[b]) == "object") {
	    			sharenow.copyBarProperties(master[b], a[b]);
	    		}else{
	    			master[b] = a[b];
	    		}
	    	}
	    }
	},
	
	updateWidget 		: function () {	
	
		stServiceWidget.destroyWidget();
		jQuery('#stservicewidget').remove();		
		sharethis.globals.serviceWidgetOptions = sharenow.stgOptions;
		stServiceWidget.initWidget();		
		sharenow.updateCSS();
	},
	
	updateCSS 		: function () {		
		var links = document.getElementsByTagName('link');
		for (var i=0; i< links.length; i++){
			if (links[i].getAttribute("type") && links[i].getAttribute("type")=="text/css" && links[i].getAttribute("href")) {
				if (links[i].getAttribute("href").match(/http:\/\/sd.sharethis.com\/disc\/css\/serviceWidget-facebook-style.*\.css/)) {
					if (sharenow.ieFallback && i>31) {break;}
					links[i].setAttribute("href", "http://sd.sharethis.com/disc/css/serviceWidget-facebook-style"+sharenow.stgOptions.style+".css");
				}
			}
	    }
	},
	
	
	showCode				: function ()	{
	
		var copyOptions = sharenow.stgOptions;
		sharenow.copyBarProperties(copyOptions, sharenow.stgOptions);
		
		if(!(typeof(copyOptions.disableAll)=='undefined'))
		{
			delete copyOptions.disableAll;
		}
		
		if(!(typeof(copyOptions.location)=='undefined' || copyOptions.location==''))
		{
			delete copyOptions.location;
		}
		
		if(!(typeof(copyOptions.hostname)=='undefined' || copyOptions.hostname==''))
		{
			delete copyOptions.hostname;
		}
		
		if(!(typeof(copyOptions.frictionlessShare)=='undefined'))
		{
			copyOptions.frictionlessShare = true;
		}
		
		var returnOptions = jQuery.extend(true, {}, copyOptions);

		gaLog('sharenow', 'finalSettingsChosen', JSON.stringify(returnOptions));
		return returnOptions;
	},
	
	createGetButtonCallback			: function () {
		var options = sharenow.showCode();
		return function() {
			stlib.buttonCodeGenerator.getButtonCodeModal("facebookwidget", null, null, false, options);
		};
	},
	
	showRegistrationInfo:	function()	{
		stlib.registrator.createRegistrationPage(sharenow.createGetButtonCallback(), false, "publisher", "facebookwidget");
	},
	
	getShareNowCode				: function() {
		gaLog('sharenow', 'GetCodeButtonClick-link');
		getCodeCallback = sharenow.showCode;
		sharenow.showRegistrationInfo();
	},	
	
	htmlEncode 		: function (value){
		  return jQuery('<div/>').text(value).html();
	},

	htmlDecode		: function (value){
	  return jQuery('<div/>').html(value).text();
	}	
}

jQuery(document).ready(function(){
			
    jQuery('#duration_id').keyup(function(){
		if (!isNaN(jQuery('#duration_id').attr("value"))) {
			stgOptions.timer.countdown = parseInt(jQuery('#duration_id').attr("value"));
		} else {
			stgOptions.timer.countdown = 30;
		}
	});
	
	jQuery('ul#themeList li').click(function(){
		jQuery('ul#themeList').find('li.selected').removeClass('selected');
		jQuery(this).addClass('selected');

		themeid = jQuery(this).attr('data-value');
		sharenow.stgOptions.style = themeid;
		sharenow.updateWidget();
	});
	
	jQuery('.previewButton').click(function(){
		sharenow.updateWidget();
	});
	
	jQuery('ul#buttonOptionsList input:checkbox').click(function(){
		var checkedValue = (this.checked) ? true : false;
		
		var inputFieldName = jQuery(this).attr('name');
		if (checkedValue){
			jQuery('#' + inputFieldName + '_detail').addClass('sts-db');
		}else{
			jQuery('#' + inputFieldName + '_detail').removeClass('sts-db');
			jQuery('#' + inputFieldName  + '_detail').addClass('sts-dn');
		}
		
		if (inputFieldName == 'duration') {
			if (!checkedValue){
				jQuery('#duration_id').attr("disabled", "disabled")
				jQuery('#duration_id').addClass('disable');
			} else {
				jQuery("#duration_id").removeAttr("disabled");
				jQuery('#duration_id').removeClass('disable');
			}
		}

		if (jQuery('#enableFS').attr('checked') == true) {
			stgOptions.frictionlessShare = true;
		} else {
			stgOptions.frictionlessShare = false;
		}
		
		if (jQuery('#duration').attr('checked') == true) {
			stgOptions.timer.enable = true;
		} else {
			stgOptions.timer.enable = false;
		}

		if (!isNaN(jQuery('#duration_id').attr("value"))) {
			stgOptions.timer.countdown = parseInt(jQuery('#duration_id').attr("value"));
		} else {
			stgOptions.timer.countdown = 30;
		}
		
	});

});