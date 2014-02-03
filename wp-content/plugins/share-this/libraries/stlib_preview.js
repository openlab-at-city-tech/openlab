var stlib_preview = {};
stlib_preview.previewList = [];
// Default services
stlib_preview.defaultServices = ["sharethis", "sina", "a1_webmarks"];
stlib_preview.defaultOptions = {
		icon : 'large',
		layout: 'h',
		label: true
};
stlib_preview.currentServices = null;
stlib_preview.currentOptions = null;
// Resets the preview to default
//stlib_preview.reset = function (id) {
//	if (stlib_preview.previewList[id] != null) {
//		var func = stlib_preview.previewList[id]["reset"];
//		return func();
//	}
//}
// Replaces the entire preview
stlib_preview.replace = function (id, newList) {
	if (stlib_preview.previewList[id] != null) {
		var func = stlib_preview.previewList[id]["replace"];
		return func(newList);
	}
}
// Puts item at beginning
//stlib_preview.prepend = function (id, item) {
//	if (stlib_preview.previewList[id] != null) {
//		var func = stlib_preview.previewList[id]["prepend"];
//		return func(item);
//	}
//}
// Puts item at the end
//stlib_preview.append = function (id, item) {
//	if (stlib_preview.previewList[id] != null) {
//		var func = stlib_preview.previewList[id]["append"];
//		return func(item);
//	}
//}
// Returns a JSON of the list
stlib_preview.getList = function (id) {
	if (stlib_preview.previewList[id] != null) {
		var func = stlib_preview.previewList[id]["getList"];
		return func();
	}
}
//Returns a JSON of the options
stlib_preview.getOptions = function (id) {
	if (stlib_preview.previewList[id] != null) {
		var func = stlib_preview.previewList[id]["getOptions"];
		return func();
	}
}
//Removes item if exists
//stlib_preview.remove = function (id, item) {
//	if (stlib_preview.previewList[id] != null) {
//		var func = stlib_preview.previewList[id]["remove"];
//		return func(item);
//	}
//}
//Updates options
stlib_preview.updateOpt = function (id, opt) {
	if (stlib_preview.previewList[id] != null) {
		var func = stlib_preview.previewList[id]["updateOpt"];
		return func(opt);
	}
}

stlib_preview.setupPreview = function(jQElement, newDefaults, newOptions) {
	var optionsArray = [];
	optionsArray["El"] = jQElement;
//	optionsArray["reset"] = function() {
//		updateTags(stlib_preview.defaultServices);
//	};
	optionsArray["replace"] = function(newList) {
//		updateTags(newList);
		updateTags2(newList);
	};
//	optionsArray["prepend"] = function(item) {
//		var obj3 = {};
//		for (attrname in item) { obj3[attrname] = item[attrname]; }
//		for (attrname in stlib_preview.currentServices) { obj3[attrname] = stlib_preview.currentServices[attrname]; }
//		updateTags(obj3);
//	};
//	optionsArray["append"] = function(item) {
//		var obj3 = {};
//		for (attrname in stlib_preview.currentServices) {
//			if (item[attrname] == null)
//				obj3[attrname] = stlib_preview.currentServices[attrname];
//		}
//		for (attrname in item) { obj3[attrname] = item[attrname]; }
//		updateTags(obj3);
//	};
	optionsArray["getList"] = function() {
		if (stlib_preview.currentServices != null)
			return stlib_preview.currentServices;
		else
			return '';
	};
	optionsArray["getOptions"] = function() {
		if (stlib_preview.currentOptions != null)
			return stlib_preview.currentOptions;
		else
			return '';
	};
//	optionsArray["remove"] = function(item) {
//		if (stlib_preview.currentServices[item] != null) {
//			var obj3 = {};
//			for (attrname in stlib_preview.currentServices) {
//				if (attrname != item)
//					obj3[attrname] = stlib_preview.currentServices[attrname];
//			}
//			updateTags(obj3);
//		}
//	};
	optionsArray["updateOpt"] = function(opt) {
		for (attrname in opt) {
			stlib_preview.currentOptions[attrname] = opt[attrname];
		}
//		updateTags(stlib_preview.currentServices);
		updateTags2(stlib_preview.currentServices);
	};
	
	if (newOptions) {
		stlib_preview.currentOptions = newOptions;
	} else {
		stlib_preview.currentOptions = stlib_preview.defaultOptions;
	}
	if (newDefaults) {
//		updateTags(newDefaults);
		updateTags2(newDefaults);
	} else {
//		updateTags(stlib_preview.defaultServices);
		updateTags2(stlib_preview.defaultServices);
	}
	
	// For Object
//	function updateTags(list) {
//		stlib_preview.currentServices = list;
//		
//		type="";
//		if (stlib_preview.currentOptions.icon == 'large')
//			type="_large";
//		else if (stlib_preview.currentOptions.icon == 'hcount')
//			type="_hcount";
//		else if (stlib_preview.currentOptions.icon == 'vcount')
//			type="_vcount";
//		else if (stlib_preview.currentOptions.icon == 'buttons')
//			type="_buttons";
//		
//		tags="";
//		for (attrname in stlib_preview.currentServices) {
//			title="";
//			if (stlib_preview.currentOptions.label && typeof(stlib_preview.currentServices[attrname])!='undefined' && typeof(stlib_preview.currentServices[attrname].title) != 'undefined')
//				title=stlib_preview.currentServices[attrname].title;
//			br="";
//			if (stlib_preview.currentOptions.layout == 'v')
//				br="<br/>";
//			
//			tags+="<span class='st_"+attrname+type+"' displayText='"+ title +"'></span>"+br;
//		}
//		/*
//		jQuery.each(stlib_preview.currentServices, function(key, value) {
//			tags+="<span class='st_"+key+"' displayText='"+ value.title +"'></span>";
//		});
//		*/
//		jQElement.html("");
//		jQElement.append(tags);
//		stButtons.locateElements();
//	}
	
	// For Array
	function updateTags2(list) {
		stlib_preview.currentServices = list;
		
		type="";
		if (stlib_preview.currentOptions.icon == 'large')
			type="_large";
		else if (stlib_preview.currentOptions.icon == 'hcount')
			type="_hcount";
		else if (stlib_preview.currentOptions.icon == 'vcount')
			type="_vcount";
		else if (stlib_preview.currentOptions.icon == 'buttons')
			type="_buttons";
		
		tags="";
		for (var c=0; c<stlib_preview.currentServices.length; c++) {
			br="";
			if (stlib_preview.currentOptions.layout == 'v')
				br="<br/>";
			
			title="";
			if (stlib_preview.currentOptions.label && typeof(stlib_picker._all_services[stlib_preview.currentServices[c]])!='undefined' && typeof(stlib_picker._all_services[stlib_preview.currentServices[c]].title) != 'undefined')
				title=stlib_picker._all_services[stlib_preview.currentServices[c]].title;
			if (stlib_preview.currentOptions.label && typeof(stlib_picker._all_native_services[stlib_preview.currentServices[c]])!='undefined' && typeof(stlib_picker._all_native_services[stlib_preview.currentServices[c]].title) != 'undefined')
				title=stlib_picker._all_native_services[stlib_preview.currentServices[c]].title;
			tags+="<span class='st_"+stlib_preview.currentServices[c]+type+"' displayText='"+ title +"'";
			
			// Add extra info if native services
			// TODO: MUST CHANGE THIS!
			if (stlib_preview.currentServices[c] == "fbsub") {
				tags+=" st_username='tomspano'";
			} else if (stlib_preview.currentServices[c] == "linkedinfollow") {
				tags+=" st_followId='207839'";
			} else if (stlib_preview.currentServices[c] == "foursquarefollow") {
				tags+=" st_username='sharethis' st_followId='21455452'";
			} else if (stlib_preview.currentServices[c] == "pinterest") {
				tags+=" st_img='http://sharethis.com/images/share-icon-128x128.png'";
			} else if (stlib_preview.currentServices[c] == "pinterestfollow") {
				tags+=" st_username='tomspano'";
			} else if (stlib_preview.currentServices[c] == "twitterfollow") {
				tags+=" st_username='sharethis'";
			} else if (stlib_preview.currentServices[c] == "youtube") {
				tags+=" st_username='sharethis'";
			} else if (stlib_preview.currentServices[c] == "googleplusfollow") {
				tags+=" st_followId='110924060789171264851'";
			} else if (stlib_preview.currentServices[c] == "googleplusadd") {
				tags+=" st_followId='113842823840690472625'";
			} else if (stlib_preview.currentServices[c] == "instagram") {
				tags+=" st_username='sharethis'";
			}
			tags+="></span>"+br;
		}
		jQElement.html("");
		jQElement.append(tags);
		stButtons.locateElements();
	}
	
	//Save the options (and the picker) globally
	stlib_preview.previewList[jQElement.attr("id")] = optionsArray;
}