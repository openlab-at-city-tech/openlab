	// -----------------------------------------------------------------------------------------------------------
	// Init hoverbutton obj
	// -----------------------------------------------------------------------------------------------------------
	
	var hoverbuttons = {
		chicklets 			: {},
		adTermsSelected 	: false,
		adBackfillSelected 	: false,
	
		stgOptions 			: {
			publisher:'',
			position:'left',
			
			ad:{
				visible: false,
				demo300x250: true,
				openDelay: 5,
				closeDelay: 0
			}
		},

		updateWidget 		: function () {	
			jQuery('#sthoverbuttons').remove();
			sharethis.globals.hoveringButtonsOptions = hoverbuttons.stgOptions;
			stMini.initWidget();
			
			//switch back to big ad
			hoverbuttons.stgOptions.ad.demo300x250 = true;
			if (typeof(hoverbuttons.stgOptions.ad.adLocation) != 'undefined'){
				delete hoverbuttons.stgOptions.ad.adLocation;
			}
		},
	
		updateAds	: function () {
			//collect ad options
			var fields = jQuery('#hoverbar_selectExpAd input:radio');
			for (i = 0; i < fields.length; i++){
				if (fields[i].checked){
					var value = fields[i].value;
					if (value == "custom"){
						value = jQuery("#hoverbar_selectExp_custom").val();
					}
					hoverbuttons.stgOptions.ad.openDelay = value;
				}
			}
			
			fields = jQuery('#hoverbar_selectCollapseAd input:radio');
			for (i = 0; i < fields.length; i++){
				if (fields[i].checked){
					var value = fields[i].value;
					if (value == "custom"){
						value = jQuery("#hoverbar_selectCollapse_custom").val();
					}
					hoverbuttons.stgOptions.ad.closeDelay = value;
				}
			}
		},
	
		updatePosition			: function () {
			//collect ad options
			var fields = jQuery('#hoverbar_selectDock input:radio');
			for (i = 0; i < fields.length; i++){
				if (fields[i].checked){
					hoverbuttons.stgOptions.position = fields[i].value;
					
					if (fields[i].value == "left") {
						jQuery('.hoverbar_previewDownArrow').removeClass('hoverbar_previewRightArrow');
					} else {
						jQuery('.hoverbar_previewDownArrow').addClass('hoverbar_previewRightArrow');
					}
				}
			}
		},
	
		showCode				: function ()
		{
			hoverbuttons.updatePosition();
			hoverbuttons.updateAds();
			var strFunctionCall = 'var st_hover_widget = new sharethis.widgets.hoverbuttons(options);';
		
			if(!(typeof(hoverbuttons.stgOptions.location)=='undefined' || hoverbuttons.stgOptions.location==''))
			{
				delete hoverbuttons.stgOptions.location;
			}
			
			if(!(typeof(hoverbuttons.stgOptions.hostname)=='undefined' || hoverbuttons.stgOptions.hostname==''))
			{
				delete hoverbuttons.stgOptions.hostname;
			}
			
			if (typeof(hoverbuttons.stgOptions.ad.demo300x250) != 'undefined'){
				delete hoverbuttons.stgOptions.ad.demo300x250;
			}
			
			if (hoverbuttons.stgOptions.ad.visible && hoverbuttons.adTermsSelected){
				//var googleAdClient = jQuery("#hoverbar_selectEnableAds_AdSenseClient").val();
				//var googleAdSlot = jQuery("#hoverbar_selectEnableAds_AdSenseSlot").val();
				var adLocation = jQuery("#hoverbar_selectEnableAds_AdLocation").val();
				if (hoverbuttons.adBackfillSelected && adLocation != ""){
					//hoverbuttons.stgOptions.ad.googleAdClient = googleAdClient;
					//hoverbuttons.stgOptions.ad.googleAdSlot = googleAdSlot;
					hoverbuttons.stgOptions.ad.adLocation = adLocation;
				}else{
					//no backfill
					if (typeof(hoverbuttons.stgOptions.ad.googleAdClient) != 'undefined'){
						delete hoverbuttons.stgOptions.ad.googleAdClient;
					}
					if (typeof(hoverbuttons.stgOptions.ad.googleAdSlot) != 'undefined'){
						delete hoverbuttons.stgOptions.ad.googleAdSlot;
					}
					if (typeof(hoverbuttons.stgOptions.ad.adLocation) != 'undefined'){
						delete hoverbuttons.stgOptions.ad.adLocation;
					}
				}
			}else{
				//no ad, no backfill
				hoverbuttons.stgOptions.ad.visible = false;
				if (typeof(hoverbuttons.stgOptions.ad.googleAdClient) != 'undefined'){
					delete hoverbuttons.stgOptions.ad.googleAdClient;
				}
				if (typeof(hoverbuttons.stgOptions.ad.googleAdSlot) != 'undefined'){
					delete hoverbuttons.stgOptions.ad.googleAdSlot;
				}
				if (typeof(hoverbuttons.stgOptions.ad.adLocation) != 'undefined'){
					delete hoverbuttons.stgOptions.ad.adLocation;
				}
			}		
			var returnOptions = jQuery.extend(true, {}, hoverbuttons.stgOptions);
			//jQuery('#hoverbar_scriptJSArea').val('<script>var options=' + JSON.stringify(hoverbuttons.stgOptions) + '; ' + strFunctionCall + '</script>');
			
			
			//revert hoverbuttons.stgOptions back to demo mode
			hoverbuttons.stgOptions.ad.demo300x250 = true;
			if (typeof(hoverbuttons.stgOptions.ad.googleAdClient) != 'undefined'){
				delete hoverbuttons.stgOptions.ad.googleAdClient;
			}
			if (typeof(hoverbuttons.stgOptions.ad.googleAdSlot) != 'undefined'){
				delete hoverbuttons.stgOptions.ad.googleAdSlot;
			}
			if (typeof(hoverbuttons.stgOptions.ad.adLocation) != 'undefined'){
				delete hoverbuttons.stgOptions.ad.adLocation;
			}
			return returnOptions;
		},
	
		createGetButtonCallback			: function () {
			var options = hoverbuttons.showCode();
			return function() {
				stlib.buttonCodeGenerator.getButtonCodeModal("hoverbuttons", null, null, false, options);
			};
		},
		
		showRegistrationInfo			: function ()
		{
			stlib.registrator.createRegistrationPage(hoverbuttons.createGetButtonCallback(), false, "publisher", "hoverbuttons");
		},
	
	
	
		gaLog			: function (category, action, label, value) {
			// This code is removed since we are switching to new GA tracking
		},
		
		updateChicklets	: function () {
			if(typeof(hoverbuttons.stgOptions.chicklets)=='undefined') {
				hoverbuttons.stgOptions.chicklets = {};
			}
			
			hoverbuttons.stgOptions.chicklets.items = stlib_picker.getServices("hoverbarPicker");
		},
	
		validateRequiredFields		: function (){
			if (hoverbuttons.stgOptions.ad.visible && (!hoverbuttons.adTermsSelected)){
				jQuery('#hoverbar_adErrorMsg1').addClass('hoverbar_sts-db');
				return false;
			}else{
				jQuery('#hoverbar_adErrorMsg1').addClass('hoverbar_sts-dn');
				jQuery('#hoverbar_adErrorMsg1').removeClass('hoverbar_sts-db');
			}
			
			//var googleAdClient = jQuery("#hoverbar_selectEnableAds_AdSenseClient").val();
			//var googleAdSlot = jQuery("#hoverbar_selectEnableAds_AdSenseSlot").val();
			var adLocation = jQuery("#hoverbar_selectEnableAds_AdLocation").val();
			
			//if (adBackfillSelected && ((googleAdClient == "") || (googleAdSlot == ""))){
			if (hoverbuttons.stgOptions.ad.visible && hoverbuttons.adBackfillSelected && adLocation == ""){
				jQuery('#hoverbar_adErrorMsg2').addClass('hoverbar_sts-db');
				return false;
			}else{
				jQuery('#hoverbar_adErrorMsg2').addClass('hoverbar_sts-dn');
				jQuery('#hoverbar_adErrorMsg2').removeClass('hoverbar_sts-db');
			}
			return true;
		},
		
		getHoverbarCode				: function() {
			hoverbuttons.gaLog('GetCodeButtonClick', 'hoveringbuttons');
			if (hoverbuttons.validateRequiredFields()){
				getCodeCallback = hoverbuttons.showCode;
				hoverbuttons.showRegistrationInfo();
			}
		},
	
		htmlEncode 		: function (value){
		  return jQuery('<div/>').text(value).html();
		},
	
		htmlDecode		: function (value){
		  return jQuery('<div/>').html(value).text();
		}
	};

jQuery(document).ready(function(){
	// -----------------------------------------------------------------------------------------------------------
	// Init
	// -----------------------------------------------------------------------------------------------------------
	
	//clear values
	var fields = jQuery('#hoverbar_selectEnableAds_detail input:checkbox');
	for (i = 0; i < fields.length; i++){
		fields[i].checked = false;
	}
	
	fields = jQuery('ul#hoverbar_buttonOptionsList input:checkbox');
	for (i = 0; i < fields.length; i++){
		fields[i].checked = true;
	}
	
	// -----------------------------------------------------------------------------------------------------------
	// Register events with JQuery
	// -----------------------------------------------------------------------------------------------------------
	
	jQuery('ul#hoverbar_buttonOptionsList input:checkbox').click(function(){
		var checkedValue = (this.checked) ? true : false;
		
		var inputFieldName = jQuery(this).attr('name');
		if (checkedValue){
			jQuery('#hoverbar_' + inputFieldName + '_detail').addClass('sts-db');
		}else{
			jQuery('#hoverbar_' + inputFieldName + '_detail').removeClass('sts-db');
			jQuery('#hoverbar_' + inputFieldName  + '_detail').addClass('sts-dn');
		}
		
		if (inputFieldName == 'selectButtons') {
			if (!checkedValue){
				if(typeof(hoverbuttons.stgOptions.chicklets)=='undefined') {
					hoverbuttons.stgOptions.chicklets = {};
				}
				hoverbuttons.stgOptions.chicklets.items ={};
			}else{
				hoverbuttons.updateChicklets();
			}
			hoverbuttons.updateWidget();
		}
	});
	
	jQuery('ul#hoverbar_adOptionsList input:checkbox').click(function(){
		var checkedValue = (this.checked) ? true : false;
		
		var inputFieldName = jQuery(this).attr('name');
		if (checkedValue){
			jQuery('#hoverbar_' + inputFieldName + '_detail').addClass('sts-db');
		}else{
			jQuery('#hoverbar_' + inputFieldName + '_detail').removeClass('sts-db');
			jQuery('#hoverbar_' + inputFieldName  + '_detail').addClass('sts-dn');
		}
		
		if(inputFieldName == 'selectEnableAds'){
			hoverbuttons.stgOptions.ad.visible = checkedValue;
			hoverbuttons.updateWidget();
		}else if (inputFieldName == 'selectEnableAds_terms'){
			hoverbuttons.adTermsSelected = checkedValue;
		}else if (inputFieldName == 'selectEnableAds_backfill'){
			hoverbuttons.adBackfillSelected = checkedValue;
			hoverbuttons.updateWidget();
		}
		
		hoverbuttons.gaLog('HoveringButtonsAdOptions', inputFieldName, checkedValue);
	});
	
	jQuery('#hoverbar_selectExpAd input:radio').click(function(){
		var selectedValue = this.value;
		var inputFieldValue = jQuery(this).attr('value');
		
		if (inputFieldValue == 'custom'){
			jQuery("#hoverbar_selectExp_custom").focus();
		}else{
			jQuery("#hoverbar_selectExp_custom").val("");
		}
		//hoverbuttons.stgOptions.ad.openDelay = inputFieldValue;
	});
	
	jQuery('#hoverbar_selectCollapseAd input:radio').click(function(){
		var selectedValue = this.value;
		var inputFieldValue = jQuery(this).attr('value');
		
		if (inputFieldValue == 'custom'){
			jQuery("#hoverbar_selectCollapse_custom").focus();
		}else{
			jQuery("#hoverbar_selectCollapse_custom").val("");
		}
		//hoverbuttons.stgOptions.ad.closeDelay = inputFieldValue;
	});
	
	jQuery('#hoverbar_selectDock input:radio').click(function(){
		hoverbuttons.updatePosition();
		hoverbuttons.updateWidget();
		
		hoverbuttons.gaLog('ChooseDocking', 'hoveringbuttons');
	});
	
	jQuery('.hoverbar_previewAd').click(function(){
		hoverbuttons.updateAds();

		hoverbuttons.stgOptions.ad.demo300x250 = false;
		var adLocation = jQuery("#hoverbar_selectEnableAds_AdLocation").val();
		hoverbuttons.stgOptions.ad.adLocation = adLocation;
		hoverbuttons.updateWidget();
		
		hoverbuttons.gaLog('PreviewAd', 'hoveringbuttons');
	});
	
	jQuery('#hoverbar_adCodeExample').click(function(){
		var html = "<br />" + hoverbuttons.htmlEncode('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">') + "<br />";
		html += hoverbuttons.htmlEncode('<html lang="en-US">') + "<br />";
		html += hoverbuttons.htmlEncode('<body style="margin:0px;padding:0px;overflow:hidden;">') + "<br /><br />";
		html += hoverbuttons.htmlEncode('<!-- Put your ad tags/iframe here. -->') + "<br /><br />";
		html += hoverbuttons.htmlEncode('</body></html>');
		jQuery('#hoverbar_adExampleStepArea').html(html);
		jQuery('#hoverbar_adExampleStep').removeClass('hoverbar_sts-dn');
	});
});
