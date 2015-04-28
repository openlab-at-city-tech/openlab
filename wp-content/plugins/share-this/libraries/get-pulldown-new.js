	// -----------------------------------------------------------------------------------------------------------
	// Init pulldown obj
	// -----------------------------------------------------------------------------------------------------------
	
	var pulldown = {
		stgOptions 			: {
		    publisher:'6beba854-ee6d-4ae1-a4f3-b69815c8ef63',
		    scrollpx: 50,
		    ad: {visible: false},
		    chicklets:{"items":["sharethis", "facebook", "twitter", "linkedin", "email"]},
		    logo: '//sd.sharethis.com/disc/images/Logo_Area.png'
		},
		
		updateLogo	 		: function () {
			var value = jQuery("#pulldown_optionsTextbox_id").val();
			pulldown.stgOptions.logo = (value == '' ? '//sd.sharethis.com/disc/images/Logo_Area.png' : value);
		},
		
		updateScrollHeight	: function () {
			var value = jQuery("#selectScrollHeight_id").val();
			pulldown.stgOptions.scrollpx = (value == '' || parseInt(value)=="NaN" ? 50 : parseInt(value));
		},
		
		updateWidget 		: function () {
			pulldown.updateLogo();
			pulldown.updateScrollHeight();
			
			jQuery('#stpulldown').remove();
			if(sharethis.utilities.domUtilities.removeListenerCompatible(window, "scroll", stPullDown.onScrollEvent) == false){
				sharethis.utilities.domUtilities.removeListenerCompatible(document, "scroll", stPullDown.onScrollEvent);
			}
			sharethis.globals.pulldownbarOptions = pulldown.stgOptions;
			stPullDown.initWidget();
			stPullDown.showbar(true);
			
			jQuery('#stpulldown .stpulldown-logo').css('background-image', "url('"+pulldown.stgOptions.logo+"')");
		},
		
		updateChicklets		: function () {
			if(typeof(pulldown.stgOptions.chicklets)=='undefined') {
				pulldown.stgOptions.chicklets = {};
			}
			
			pulldown.stgOptions.chicklets.items = stlib_picker.getServices("pulldownPicker");
		},
		
		showCode			: function () {
			if (typeof(pulldown.stgOptions.hostname) != "undefined") {
				delete pulldown.stgOptions.hostname;
			}
			if (typeof(pulldown.stgOptions.location) != "undefined") {
				delete pulldown.stgOptions.location;
			}
			if (typeof(pulldown.stgOptions.st_url) != "undefined") {
				delete pulldown.stgOptions.st_url;
			}
			if (typeof(pulldown.stgOptions.st_title) != "undefined") {
				delete pulldown.stgOptions.st_title;
			}
			
			sharebar.gaLog('pulldownShowCode', 'finalSettingsChosen', JSON.stringify(pulldown.stgOptions));
			return jQuery.extend(true, {}, pulldown.stgOptions);
		},
		
		createGetButtonCallback			: function () {
			var options = pulldown.showCode();
			return function() {
				stlib.buttonCodeGenerator.getButtonCodeModal("pulldown", null, null, false, options);
			};
		},
		
		showRegistrationInfo			: function ()
		{
			stlib.registrator.createRegistrationPage(pulldown.createGetButtonCallback(), false, "publisher", "pulldown");
		},
	
		gaLog			: function (category, action, label, value) {
			// This code is removed since we are switiching to new GA tracking
		},
		
		getPulldownCode					: function() {
			pulldown.gaLog('GetCodeButtonClick', 'pulldown');
//			if (pulldown.validateRequiredFields()){
				getCodeCallback = pulldown.showCode;
				pulldown.showRegistrationInfo();
//			}
		}
	};
	
jQuery(document).ready(function(){
	// -----------------------------------------------------------------------------------------------------------
	// Init
	// -----------------------------------------------------------------------------------------------------------
	
	// -----------------------------------------------------------------------------------------------------------
	// Register events with JQuery
	// -----------------------------------------------------------------------------------------------------------
	
	jQuery('.pulldown_previewButton').click(function(){
		pulldown.updateWidget();
	});
	
	jQuery('ul#pulldown_buttonOptionsList input:checkbox').click(function(){
		var checkedValue = (this.checked) ? true : false;
		
		var inputFieldName = jQuery(this).attr('name');
		if (checkedValue){
			jQuery('#pulldown_' + inputFieldName + '_detail').addClass('sts-db');
		}else{
			jQuery('#pulldown_' + inputFieldName + '_detail').removeClass('sts-db');
			jQuery('#pulldown_' + inputFieldName  + '_detail').addClass('sts-dn');
		}
		
		if (inputFieldName == 'selectButtons') {
			if (!checkedValue){
				if(typeof(pulldown.stgOptions.chicklets)=='undefined') {
					pulldown.stgOptions.chicklets = {};
				}
				pulldown.stgOptions.chicklets.items ={};
			}else{
				pulldown.updateChicklets();
			}
			pulldown.updateWidget();
		}
	});
});