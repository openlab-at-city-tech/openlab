//This library requires JQuery
//It also requires stcommon.js in your header for an official list of services
//stlib_picker.defaultServices defines the services from stcommon that get loaded as the default services in the picker
//Styling can be found in stlib_picker.css and should be linked in the page.
//To get selected services as an array of strings:  (ie ["twitter", "sharethis", "facebook"] )
//   Call: var answer = stlib_picker.pickerList[uniqueID]["getServices"]();

var stlib_picker = {};
stlib_picker._all_services = stlib.allServices;
stlib_picker._all_native_services = stlib.allNativeServices;

stlib_picker.pickerList = [];
stlib_picker.defaultServices = ["sharethis", "tumblr", "bebo"];
stlib_picker.getServices = function (id) {
	return answer = stlib_picker.pickerList[id]["getServices"]();
}

//Creates the picker - make sure it has a unique ID
// jQElement - id of the picker
// newDefaults - for the left side of picker
// cb - callback when things change
// topServices - the top services for the right picker
// excludedServices - excluding services for the right picker
// options - config options
stlib_picker.setupPicker = function(jQElement, newDefaults, cb, topServices, excludedServices, options) {
	//Make an array to store any needed options
	var optionsArray = [];
	optionsArray["El"] = jQElement;
	optionsArray["isSelect"] = false;
	optionsArray["overLeftPicker"] = false;
	optionsArray["cb"] = function(){};
	optionsArray["options"] = null;
	optionsArray["topServices"] = [];
	optionsArray["excludedServices"] = [];
	optionsArray["topNativeServices"] = [];
	optionsArray["excludedNativeServices"] = [];
	optionsArray["removedServices"] = [];
	optionsArray["getServices"] = function() {
		var answer = [];
		var lis = jQElement.children(".stp_background").children(".stp_pickerLeft").find(".stp_li");
		lis.each(function() {
			answer.push(jQuery(this).attr("id").substring(6));
		});
		return answer;
	};
	if (typeof(cb) != "undefined" && cb != null) {
		optionsArray["cb"] = cb;
	}
	
	//Append the three divs that are needed:
	var html = "<div class='stp_background'>";
	html += "<div class='stp_pickerLeftArrow";
	if (options && !options.showNative) {
	} else {
		html += " stp_pickerLeftArrowNative";
	}
	html += "'><img src='"+PLUGIN_PATH+"images/reorder.png' class='stp_reorder'></div>";
	html += "<div class='stp_pickerLeft";
	if (options && !options.showNative) {
	} else {
		html += " stp_pickerLeftNative";
	}
	html += "'><div style=''margin-bottom:4px><span class='stp_header' style='cursor:default'>Selected Services</span></div><ul class='stp_ulLeft";
	if (options && !options.showNative) {
	} else {
		html += " stp_ulLeftNative";
	}
	html += "'></ul></div>";
	
	html += "<div class='picker_headings'><span id='sharingBtn' class='stp_header' onclick='showSocialButtons()'>Sharing Buttons</span>";
	
	// For HoverBar and PullDown Bar - Disable Social Plugins Tab
	if( (st_btnType == "_none") && ((st_selectedBarStyle == "hoverbarStyle") || (st_selectedBarStyle == "pulldownStyle"))) {
		html += "<span class='stp_header2' style='padding-left:5px'>|</span><span id='socialPlgn' class='stp_header highlightSelection' style='padding-left:5px;color:#CCCCCC;cursor: none !important;'>Social Plugins</span></div>";
	}else{
		html += "<span class='stp_header2' style='padding-left:5px'>|</span><span id='socialPlgn' class='stp_header highlightSelection' style='padding-left:5px;'  onclick='showSocialPlugins()'>Social Plugins</span></div>";
	}
	
	html += "<div class='stp_pickerArrow stp_pickerArrowRtNative'><img src='"+PLUGIN_PATH+"images/drag.png' class='stp_drag'></div>";
	html += "<div class='stp_pickerRight' style='display:block;'><div id='chicklet_search'><input type='text' value='Search services' id='chicklet_search_field' onkeyup='stlib_picker.searchAndDisplay(jQuery(this).parent().parent().parent().parent(), jQuery(this).parent().parent(), this.value);'></div><ul class='stp_ulRight'></ul></div>";
	if (options && !options.showNative) {
		html += "<div class='stp_clear'></div>";
	} else {
		html += "<div class='stp_pickerArrow stp_pickerArrowBtmNative' style='display:none;'><img src='"+PLUGIN_PATH+"images/drag.png' class='stp_drag_bottom'></div>";
		html += "<div class='stp_pickerBottom stp_pickerRightNative' style='display:none;'><div id='chicklet_search'><input type='text' value='Search services' id='chicklet_search_field' onkeyup='stlib_picker.searchAndDisplay(jQuery(this).parent().parent().parent().parent(), jQuery(this).parent().parent(), this.value);'></div><ul class='stp_ulBottom'></ul></div>";
		html += "<div class='stp_clear'></div>";
	}
	html += "</div>";
	jQElement.html(html);
	
	//Add default Services
	var pickerDefaults = [];
	if (newDefaults) {
		pickerDefaults = newDefaults;
	} else {
		pickerDefaults = stlib_picker.defaultServices;
	}
	
	//Add topServices
	if (topServices) {
		optionsArray["topServices"] = topServices;
	} else {
		optionsArray["topServices"] = ["linkedin"];
	}
	
	//Add excludedServices
	if (excludedServices) {
		optionsArray["excludedServices"] = excludedServices;
	} 
	
	// Add options
	if (options) {
		if (options.topNativeServices) {
			optionsArray["topNativeServices"] = options.topNativeServices;
		}
		if (options.excludedNativeServices) {
			optionsArray["excludedNativeServices"] = options.excludedNativeServices;
		}
		optionsArray["options"] = options;
	}
	
	// Add list of services	
	var ul = jQElement.children(".stp_background").children(".stp_pickerLeft").children(".stp_ulLeft");
	for(i=0;i<pickerDefaults.length;i++) {
		stlib_picker.addServiceLink(ul, pickerDefaults[i], pickerDefaults[i]);
	}
	
	// NON-NATIVE SERVICES
	// TOP SERVICES
	ul = jQElement.children(".stp_background").children(".stp_pickerRight").children(".stp_ulRight");
	for(i=0;i<optionsArray["topServices"].length;i++) {
		if (jQuery.inArray(optionsArray["topServices"][i], optionsArray["excludedServices"]) == -1) {
			stlib_picker.addServiceLink(ul, optionsArray["topServices"][i], stlib_picker._all_services[optionsArray["topServices"][i]]);
		}
	}
	// EVERYTHING ELSE
	jQuery.each(stlib_picker._all_services, function(key, value) {
		if(jQuery.inArray(key, pickerDefaults) == -1 && jQuery.inArray(key, optionsArray["topServices"]) == -1 && jQuery.inArray(key, optionsArray["excludedServices"]) == -1) {
			stlib_picker.addServiceLink(ul, key, value);
		}
	});
	
	// NATIVE SERVICES
	if (jQElement.find(".stp_pickerBottom").length > 0) {
		// TOP SERVICES
		ul = jQElement.children(".stp_background").children(".stp_pickerBottom").children(".stp_ulBottom");
		for(i=0;i<optionsArray["topNativeServices"].length;i++) {
			if (jQuery.inArray(optionsArray["topNativeServices"][i], optionsArray["excludedNativeServices"]) == -1) {
				stlib_picker.addServiceLink(ul, optionsArray["topNativeServices"][i], stlib_picker._all_native_services[optionsArray["topNativeServices"][i]]);
			}
		}
		
		// EVERYTHING ELSE
		jQuery.each(stlib_picker._all_native_services, function(key, value) {
			if(jQuery.inArray(key, pickerDefaults) == -1 && jQuery.inArray(key, optionsArray["topNativeServices"]) == -1 && jQuery.inArray(key, optionsArray["excludedNativeServices"]) == -1) {
				stlib_picker.addServiceLink(ul, key, value);
			}
		});
	}
	
	jQElement.find("#chicklet_search_field").blur(function() {
		var element=jQuery(this);
		if(element.val()==""){
			element.val("Search services");
		}
	});
	
	jQElement.find("#chicklet_search_field").focus(function() {
		var element=jQuery(this);
		if(element.val()=="Search services"){
			element.val("");
		}
	});
	
	var deleteElement = false;
	jQElement.find('.stp_ulLeft').sortable({
		revert: 100,
		opacity: 0.8,
		out: function (event, ui) {
			deleteElement = true;
			jQElement.find('.stp_ulLeft').sortable("option", "revert", false);
			jQElement.find('.stp_ulRight li').draggable("option", "revert", true);
			jQElement.find('.stp_ulBottom li').draggable("option", "revert", true);
			stlib_picker.pickerList[jQElement.attr("id")]["overLeftPicker"] = !deleteElement; 
		},
		over: function (event, ui) {
			deleteElement = false;
			jQElement.find('.stp_ulLeft').sortable("option", "revert", true);
			jQElement.find('.stp_ulRight li').draggable("option", "revert", false);
			jQElement.find('.stp_ulBottom li').draggable("option", "revert", false);
			stlib_picker.pickerList[jQElement.attr("id")]["overLeftPicker"] = !deleteElement;
		},
		beforeStop: function (event, ui) {
			if (deleteElement) {
				stlib_picker.removeElement(jQElement, ui.item);
			}
		},
		update: function (event, ui) {
			stlib_picker.makeDraggable(jQElement, "#"+ui.item.attr("id"));
			stlib_picker.enableDraggable(jQElement);
			stlib_picker.pickerList[jQElement.attr("id")]["cb"]({service:ui.item.attr("id").substring(6),action:"move"});
		},
		receive: function(event, ui) {
			if (ui.item.parent().hasClass("stp_ulRight")) {
				var resultName = ".stp_ulRight";
			} else {
				var resultName = ".stp_ulBottom";
			}
			jQElement.find(resultName).find("#"+ui.item.attr("id")).remove();
			stlib_picker.pickerList[jQElement.attr("id")]["cb"]({service:ui.item.attr("id").substring(6),action:"add"});
		}
	});
	stlib_picker.makeDraggable(jQElement);
	
	//Save the options (and the picker) globally
	stlib_picker.pickerList[jQElement.attr("id")] = optionsArray;
}

stlib_picker.addServiceLink = function(ul, key, value, before) {
	var title = "", imgProto = '';
	if (value.title) {
		title = value.title; 
	} else {
		if (stlib_picker._all_services[value])
			title = stlib_picker._all_services[value].title;
		else if (stlib_picker._all_native_services[value])
			title = stlib_picker._all_native_services[value].title;
	}
	
	imgProto = (document.location.protocol === "https:") ? "https://ws" : "http://w";
		
	if (before && before == true)
		ul.prepend("<li id='st_li_" + key + "' class='stp_li'><img src="+imgProto+"'.sharethis.com/images/"+key+"_32.png'></img><span class='stp_liText'>" + title + "</span><img src='"+PLUGIN_PATH+"images/close.png' class='stp_remove'></img></li>");
	else
		ul.append("<li id='st_li_" + key + "' class='stp_li'><img src='"+imgProto+".sharethis.com/images/"+key+"_32.png'></img><span class='stp_liText'>" + title + "</span><img src='"+PLUGIN_PATH+"images/close.png' class='stp_remove'></img></li>");
}

stlib_picker.searchAndDisplay = function(jQElement, pickerClass, searchTerm) {
	if (pickerClass.hasClass("stp_pickerRight")) {
		var pickerName = ".stp_pickerRight";
		var resultName = ".stp_ulRight";
		var searchServices = stlib_picker._all_services;
		var topServices = stlib_picker.pickerList[jQElement.attr("id")]["topServices"];
		var excludedServices = stlib_picker.pickerList[jQElement.attr("id")]["excludedServices"];
	} else {
		var pickerName = ".stp_pickerBottom";
		var resultName = ".stp_ulBottom";
		var searchServices = stlib_picker._all_native_services;
		var topServices = stlib_picker.pickerList[jQElement.attr("id")]["topNativeServices"];
		var excludedServices = stlib_picker.pickerList[jQElement.attr("id")]["excludedNativeServices"];
	}
	
	var ul = jQElement.children(".stp_background").children(pickerName).children(resultName);
	var leftServices = stlib_picker.getServices(jQElement.attr("id"));
	ul.html("");
	if(searchTerm == "") {
		// Add top services
		for(i=0;i<topServices.length;i++) {
			if(jQuery.inArray(topServices[i], leftServices) == -1 && jQuery.inArray(topServices[i], excludedServices) == -1) {
				stlib_picker.addServiceLink(ul, topServices[i], searchServices[topServices[i]]);
			}
		}
		// Add remaining services
		jQuery.each(searchServices, function(key, value) {
			if(jQuery.inArray(key, leftServices) == -1 && jQuery.inArray(key, topServices) == -1 && jQuery.inArray(key, excludedServices) == -1) {
				stlib_picker.addServiceLink(ul, key, value);
			}
		});
	} else {
		try{var reg = new RegExp(searchTerm, "i");}catch(err){return false;}
		var matches = [];
		for(var i in searchServices){
			var text=searchServices[i].title;
			if(reg.test(text)==true && i!="sharebox"){
				matches.push(i);
			}
		}
		
		//Add top services
		for(var c=0; c<matches.length; c++){
			if(jQuery.inArray(matches[c], leftServices) == -1 && jQuery.inArray(matches[c], topServices) != -1 && jQuery.inArray(matches[c], excludedServices) == -1) {
				stlib_picker.addServiceLink(ul, matches[c], searchServices[matches[c]]);
			}
		}
		//Add remaining services
		for(var c=0; c<matches.length; c++){
			if(jQuery.inArray(matches[c], leftServices) == -1 && jQuery.inArray(matches[c], topServices) == -1 && jQuery.inArray(matches[c], excludedServices) == -1) {
				stlib_picker.addServiceLink(ul, matches[c], searchServices[matches[c]]);
			}
		}
	}
	stlib_picker.makeDraggable(jQElement);
	return true;
}

stlib_picker.makeDraggable = function (jQElement, elem) {
	jQElement.find('.stp_ulRight li').draggable({
		cursor: 'pointer',
		containment: 'document',
		helper: 'clone',
		opacity: 0.8,
		revert: 'invalid',
		revertDuration: 100,
		connectToSortable: jQElement.find('.stp_ulLeft'),
		stop: function (event, ui) {
			if (stlib_picker.pickerList[jQElement.attr("id")]["overLeftPicker"]) {
				stlib_picker.disableDraggable(jQElement);
			}
		}
	});
	jQElement.find('.stp_ulBottom li').draggable({
		cursor: 'pointer',
		containment: 'document',
		helper: 'clone',
		opacity: 0.8,
		revert: 'invalid',
		revertDuration: 100,
		connectToSortable: jQElement.find('.stp_ulLeft'),
		stop: function (event, ui) {
			if (stlib_picker.pickerList[jQElement.attr("id")]["overLeftPicker"]) {
				stlib_picker.disableDraggable(jQElement);
			}
		}
	});
	//Add the various Event handlers 
	//Need to make sure that we don't get confused when there are multiple pickers
	var targetElem = ".stp_li";
	if (typeof(elem) != "undefined") {
		targetElem = elem;
	}
	jQElement.find(targetElem).find(".stp_remove").mousedown(function() {
		var element=jQuery(this).parent();
		stlib_picker.removeElement(jQElement, element);
	});
	jQElement.find(targetElem).mouseover(function() {
		if (jQElement.find(this).parent().hasClass("stp_ulLeft")) {
			if (!jQElement.find(".stp_reorder").hasClass("stp_show")) {
				jQElement.find(".stp_reorder").addClass("stp_show");
			} 
			if (!jQElement.find(this).find(".stp_remove").hasClass("stp_show")) {
				jQElement.find(this).find(".stp_remove").addClass("stp_show");
			} 
		} else if (jQElement.find(this).parent().hasClass("stp_ulRight")) {
			if (!jQElement.find(".stp_drag").hasClass("stp_show")) {
				jQElement.find(".stp_drag").addClass("stp_show");
			} 
		} else if (jQElement.find(this).parent().hasClass("stp_ulBottom")) {
			if (!jQElement.find(".stp_drag_bottom").hasClass("stp_show")) {
				jQElement.find(".stp_drag_bottom").addClass("stp_show");
			} 
		}
		
	});
//	jQElement.find(targetElem).mousedown(function() {
//		jQElement.find(".stp_drag").removeClass("stp_show");
//		jQElement.find(".stp_reorder").removeClass("stp_show");
//		jQElement.find(this).find(".stp_remove").removeClass("stp_show");
//	});
	jQElement.find(targetElem).mouseout(function() {
		jQElement.find(".stp_drag").removeClass("stp_show");
		jQElement.find(".stp_drag_bottom").removeClass("stp_show");
		jQElement.find(".stp_reorder").removeClass("stp_show");
		jQElement.find(this).find(".stp_remove").removeClass("stp_show");
	});
}

stlib_picker.enableDraggable = function (jQElement) {
	jQElement.find('.stp_ulRight li').draggable("option", "delay", 0);
	jQElement.find('.stp_ulBottom li').draggable("option", "delay", 0);
}

stlib_picker.disableDraggable = function (jQElement) {
//	jQElement.find('.stp_ulRight li').draggable("option", "connectToSortable", false);
//	jQElement.find('.stp_ulBottom li').draggable("option", "connectToSortable", false);
//	jQElement.find('.stp_ulRight li').draggable("disable");
//	jQElement.find('.stp_ulBottom li').draggable("disable");
	jQElement.find('.stp_ulRight li').draggable("option", "delay", 300);
	jQElement.find('.stp_ulBottom li').draggable("option", "delay", 300);
}

stlib_picker.removeElement = function (jQElement, element) {
	var temp = element.attr("id").substring(6);
	if (typeof(stlib_picker._all_native_services[temp]) == "undefined") {
		var pickerName = ".stp_pickerRight";
		var resultName = ".stp_ulRight";
		var searchServices = stlib_picker._all_services;
	} else {
		var pickerName = ".stp_pickerBottom";
		var resultName = ".stp_ulBottom";
		var searchServices = stlib_picker._all_native_services;
	}
	jQElement.find(element).effect("fade", {}, 500, function(){
		stlib_picker.addServiceLink(jQElement.children(".stp_background").children(pickerName).children(resultName), temp, searchServices[temp], true);
		jQElement.find(element).remove();
		jQElement.find(".stp_drag").removeClass("stp_show");
		jQElement.find(".stp_reorder").removeClass("stp_show");
		stlib_picker.makeDraggable(jQElement, "#"+element.attr("id"));
        stlib_picker.pickerList[jQElement.attr("id")]["cb"]({service:temp,action:"remove"});
    });
}

function showSocialButtons(){
	jQuery(".stp_pickerRight").show();
	jQuery(".stp_pickerBottom").hide();
	jQuery(".stp_pickerArrowBtmNative").hide();
	jQuery("#sharingBtn").removeClass("highlightSelection");
	jQuery("#socialPlgn").addClass("highlightSelection");
}

function showSocialPlugins(){
	jQuery(".stp_pickerRight").hide();
	jQuery(".stp_pickerBottom").show();
	jQuery(".stp_pickerArrowBtmNative").show();
	jQuery("#socialPlgn").removeClass("highlightSelection");
	jQuery("#sharingBtn").addClass("highlightSelection");
}