	// -----------------------------------------------------------------------------------------------------------
	// Init stbuttons obj
	// -----------------------------------------------------------------------------------------------------------
	var copyNShare_Selected = true;
	var copyNShare_DoNotHash = 'false';
	var copyNShare_DoNotCopy = 'false';
	var copyNShare_HashAddressBar = 'false';
	
	var stbuttons = {
		createGetButtonCallback			: function () {
			return function() {
				switch (stlib.getButtonConfig.dest) {
					case 'blogger':
						if (stbuttons.hasShareThis(stlib_picker.getServices("picker"))) {
							stlib.buttonCodeGenerator.getButtonCodeModal("widgetSelect", stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), false, null, "stbuttons.redirectBlogger");
						} else {
							stbuttons.redirectBlogger(true);
						}
						break;
					case 'typepad':
						if (stbuttons.hasShareThis(stlib_picker.getServices("picker"))) {
							stlib.buttonCodeGenerator.getButtonCodeModal("widgetSelect", stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), false, null, "stbuttons.redirectTypepad");
						} else {
							stbuttons.redirectTypepad(true);
						}
						break;
					case 'tumblr':
						if (stbuttons.hasShareThis(stlib_picker.getServices("picker"))) {
							stlib.buttonCodeGenerator.getButtonCodeModal("widgetSelect", stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), false, null, "stbuttons.redirectTumblr");
						} else {
							stbuttons.redirectTumblr(true);
						}
						break;
					case 'website':
						stbuttons.getJSONButtonList(stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), "website");
						stlib.buttonCodeGenerator.getButtonCodeModal("stbuttons", stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), false, null);
						break;
					default:		// all other services: wordpress, drupal, joomla, newsletter, posterous
						stbuttons.getJSONButtonList(stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), stlib.getButtonConfig.dest);
						break;
				}
			};
		},
		createGetButtonFeaturedCallback			: function (product) {
			return function() {
				stlib.buttonCodeGenerator.getButtonCodeModal(product, stlib_preview.getOptions("preview").icon, stlib_picker.getServices("featuredPicker"), false, null);
			};
		},
		redirectBlogger				: function (widget5x, resp) {
			stlib.buttonCodeGenerator.getPubKey2(function (response) {
				var publisherInfo = "";
				if (typeof(response) != "undefined" && response != null) {
					publisherInfo = ", \"" + response.data.pubkey + "\"";
				} else {
					publisherInfo = ", \"" + stlib.buttonCodeGenerator.generatePublisherKey("blogger") + "\"";
				}
				
				var jsonButtonList = stbuttons.getJSONButtonList(stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), "blogger", resp);
				jQuery('#bloggerInputScript').attr('value', '<span id="st_finder"></span><script type="text/javascript" src="http://w.sharethis.com/widget/stblogger2.js"></script><script type="text/javascript">var switchTo5x=' + widget5x + ';stBlogger2.init("http://w.sharethis.com/button/buttons.js", ' + JSON.stringify(jsonButtonList) + ' ' + publisherInfo + ');var f = document.getElementById("st_finder");var c = f.parentNode.parentNode.childNodes;for (i=0;i<c.length;i++) { try { c[i].style.display = "none"; } catch (err) {}}</script>');
				jQuery('#bloggerSubmit').submit();
			});
		},
		
		redirectTypepad				: function (widget5x, resp) {
			stlib.buttonCodeGenerator.getPubKey2(function (response) {
				var publisherInfo = "";
				if (typeof(response) != "undefined" && response != null) {
					publisherInfo = ", \"" + response.data.pubkey + "\"";
				} else {
					publisherInfo = ", \"" + stlib.buttonCodeGenerator.generatePublisherKey("typepad") + "\"";
				}
				
				var jsonButtonList = stbuttons.getJSONButtonList(stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), "typepad", resp);			
				jQuery('#typepadInputScript').attr('value', '<script type="text/javascript" src="http://w.sharethis.com/widget/sttypepad2.js"></script><script type="text/javascript">var switchTo5x=' + widget5x + ';stTypePad2.init("http://w.sharethis.com/button/buttons.js", ' + JSON.stringify(jsonButtonList) + ' ' + publisherInfo + ');</script>');
				jQuery('#typepadSubmit').submit();
			});
		},
		
		redirectTumblr				: function (widget5x, resp) {
			var jsonButtonList = stbuttons.getJSONButtonList(stlib_preview.getOptions("preview").icon, stlib_picker.getServices("picker"), "tumblr", resp);
			jQuery('#tumblrSubmit').attr('action', '/publishers/get-button-code?platform=' + '1' + '&five=' + widget5x + '&buttons=' + escape(JSON.stringify(jsonButtonList)));
			jQuery('#tumblrSubmit').submit();
//			window.location.href='/publishers/get-button-code?platform=' + '1' + '&five=' + widget5x + '&buttons=' + escape(JSON.stringify(jsonButtonList));
		},
		
		hasShareThis				: function(list) {
			for (var i=0; i<list.length; i++) {
				if (list[i] == "sharethis" || list[i] == "fbsub" || stlib.nativeButtons.checkNativeButtonConfig(list[i])) {
					return true;
				}
			}
			return false;
		},
		
		getJSONButtonList			: function(type, list, dest, response) {
			var jsonButtonList = {};
			for (var i=0; i<list.length; i++) {
				var title = "";
				if (stlib_picker._all_services[list[i]])
					title = stlib_picker._all_services[list[i]].title;
				else if (stlib_picker._all_native_services[list[i]])
					title = stlib_picker._all_native_services[list[i]].title;
				
				if (response && response[list[i]]) {
					jsonButtonList[list[i]] = [type, title, "", response[list[i]]];
				} else {
					jsonButtonList[list[i]] = [type, title, ""];
				}
			}
			stbuttons.gaLog('stbuttonsShowCode', 'finalSettingsChosen', '{platform:\"'+dest+'\",services:'+JSON.stringify(jsonButtonList))+'}';
			return jsonButtonList;
		},
		
		showRegistrationInfo			: function (product)
		{
			if (product == "sharethis") {
				stlib.registrator.createRegistrationPage(stbuttons.createGetButtonCallback(), false, "publisher", "sharethis");
			} else { // featured product
				stlib.registrator.createRegistrationPage(stbuttons.createGetButtonFeaturedCallback(product), false, "publisher", product);
			}
		},
	
		gaLog			: function (category, action, label, value) {
			// The code is removed since we are switching to new GA tracking
		},
		
		getShareThisCode			: function() {
			//ga_log('Get Code Modal - Button ', 'Modal Location', 'Get Sharing Tools');
			//stbuttons.gaLog('GetCodeButtonClick', 'sharethis');
			stbuttons.showRegistrationInfo('sharethis');
			ga_log('Get Sharing Tools', 'ButtonClicked', 'Get the Code');
		},
		getShareThisFeaturedCode	: function() {
			// We can change here, depending on what our feature is
//			stbuttons.gaLog('GetCodeButtonClick', 'sharethis-shareheart');
//			stbuttons.showRegistrationInfo('sharethis-shareheart');
//			stbuttons.gaLog('GetCodeButtonClick', 'sharethis-stpatricks');
//			stbuttons.showRegistrationInfo('sharethis-stpatricks');
			stbuttons.gaLog('GetCodeButtonClick', 'sharethis-'+stlib.getOccasion.theme);
			stbuttons.showRegistrationInfo('sharethis-'+stlib.getOccasion.theme);
		},
		changeSize					: function(inputFieldValue) {
			if (inputFieldValue == '16x16') {
				stlib_preview.updateOpt("preview", {icon:'',layout:'h',label:false});
			} else if (inputFieldValue == '32x32') {
				stlib_preview.updateOpt("preview", {icon:'large',layout:'h'});
			}
			stbuttons.gaLog('GetSharingTools-stbuttons', 'chickletSize', inputFieldValue);
		},
		changeCopyNShare					: function(inputFieldValue, checkedFlag) {
			if (inputFieldValue == 'copyText') {
				//alert('copyText -- '+checkedFlag);
				if(checkedFlag == 'checked') {
					copyNShare_DoNotHash = 'false';
					copyNShare_DoNotCopy = 'false';
					copyNShare_Selected = true;
				}else{
					copyNShare_DoNotHash = 'true';
					copyNShare_DoNotCopy = 'true';
					copyNShare_Selected = false;
				}					
			
			} else if (inputFieldValue == 'copyURL') {
				//alert('copyURL  ' +checkedFlag);
				if(checkedFlag == 'checked') {
					copyNShare_DoNotHash = 'false';
					copyNShare_HashAddressBar = 'true';
					copyNShare_Selected = true;
				}else{
					copyNShare_DoNotHash = 'true';
					copyNShare_HashAddressBar = 'false';
					copyNShare_Selected = false;
				}			
			}
			stlib_preview.updateOpt("stLight", {doNotHash: false, doNotCopy: false, hashAddressBar: true});
			stbuttons.gaLog('GetSharingTools-stbuttons', 'CopyNShareSettings', inputFieldValue);
		},
		updateShareEggChicklets		: function() {
			jQuery('#egg').html("");
//			stlib.shareEgg.createEgg("egg", stlib_picker.getServices("featuredPicker"), {title:"Happy Valentine's Day!",url:"http://www.sharethis.com",theme:"shareheart"});
//			stlib.shareEgg.createEgg("egg", stlib_picker.getServices("featuredPicker"), {title:"Happy St Patrick's Day!",url:"http://www.sharethis.com",theme:"stpatricks"});
			stlib.shareEgg.createEgg("egg", stlib_picker.getServices("featuredPicker"), {title:stlib.getOccasion.title,url:"http://www.sharethis.com",theme:stlib.getOccasion.theme});
			stButtons.locateElements();
		}
	};
	
jQuery(document).ready(function(){
	// -----------------------------------------------------------------------------------------------------------
	// Init
	// -----------------------------------------------------------------------------------------------------------
	jQuery('#selectSizeType input:radio').click(function(){
		var inputFieldValue = jQuery(this).attr('value');
		
		stbuttons.changeSize(inputFieldValue);
	});
	
	
	
	// Clear browser history by setting inputcheckboxes to default 
	if(jQuery('.selectDock_type').length > 0){
		jQuery('.selectDock_type').removeAttr('checked');
		jQuery(".selectDock_type").first().attr('checked', 'checked');
	}
	
	if(jQuery('.copyNShareOption_hoverBar').length > 0){
		jQuery('.copyNShareOption_hoverBar').removeAttr('checked');
		jQuery('.copyNShareOption_hoverBar').first().attr('checked','checked');
	}
	
	if(jQuery('.copyNShareOption_sharebar').length > 0){
		jQuery('.copyNShareOption_sharebar').removeAttr('checked');
		jQuery('.copyNShareOption_sharebar').first().attr('checked','checked');
	}	
	
	if(jQuery('.copyNShareOption_pulldownBar').length > 0){
		jQuery('.copyNShareOption_pulldownBar').removeAttr('checked');
		jQuery('.copyNShareOption_pulldownBar').first().attr('checked','checked');
	}
	
	if(jQuery('.copyNShareOption_shareEgg').length > 0){
		jQuery('.copyNShareOption_shareEgg').removeAttr('checked');
		jQuery('.copyNShareOption_shareEgg').first().attr('checked','checked');
	}	
	
	if(jQuery('.copyNShareOption_fb').length > 0){
		jQuery('.copyNShareOption_fb').removeAttr('checked');
		jQuery('.copyNShareOption_fb').first().attr('checked','checked');
	}	
	
	if(jQuery('.copyNShareOption').length > 0){
		jQuery('.copyNShareOption').removeAttr('checked');
		jQuery('.copyNShareOption').first().attr('checked','checked');
	}	
	// end of clearing browser cache code
	
	jQuery('#copyNShare_Button input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	jQuery('#copyNShare_hoverBar input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	jQuery('#copyNShare_sharebar input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	jQuery('#copyNShare_pulldownBar input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	jQuery('#copyNShare_shareEgg input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	jQuery('#copyNShare_fb input:checkbox').click(function(){		
		var inputFieldValue = jQuery(this).attr('value');		
		if(jQuery(this).attr('checked') == 'checked'){
			stbuttons.changeCopyNShare(inputFieldValue, "checked");
		}else{
			stbuttons.changeCopyNShare(inputFieldValue, "unchecked");
		}		
	});
	
	// -----------------------------------------------------------------------------------------------------------
	// Register events with JQuery
	// -----------------------------------------------------------------------------------------------------------
});