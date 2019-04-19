
(function($){

	

	window.yotu_wp = {

		parse_data : {},

		data : {
			actions : {},
			filters : {}
		},

		init : function (){

			window.addEventListener('message', yotu_wp.sys_msg);
			
			jQuery('.yotu-tabs').each(function (indx){
				var tabs = jQuery(this),
					tabs_content = tabs.next();
					
				tabs.find('a').on('click', function (e){
					e.preventDefault();
					var that 	= jQuery(this),
						tab 	= that.data('tab'),
						yotu 	= that.data('yotu');
						cli = jQuery(this).closest('li');

					tabs_content.find('.yotu-tab-content').css({display: 'none'});

					jQuery('#yotu-tab-' + tab).css({display: 'block'});
					tabs.find('li').removeClass('yotu-active nextactive prevactive');
					cli.addClass('yotu-active');
					cli.next().addClass('nextactive');
					cli.prev().addClass('prevactive');

					jQuery('#yotu-settings-last_tab').val(tab);

					if (yotu === 'insert') {
						jQuery('.yotu_insert_popup').data('type', tab);
						jQuery('.yotu_insert_popup .yotu-info-res').html('');
						jQuery('.yotu_insert_popup .yotu-actions').addClass('yotu-hidden');
					}
				});
			});

			jQuery('.yotu-field [data-func]').on( 'click', function (e) {
				e.preventDefault();

				var action = this.getAttribute('data-func');

				switch( action ){
					case 'preset':
						var field = this.getAttribute('data-preset');
							installed = $(this).data('installed');
						yotu_wp.presets.load(field, installed);
					break;

					case 'delete-cache':
						var obj = {},
							func = function (res) {

							};
						yotu_wp.ajax(
							$(this).closest('.yotu-field'),
							{
								action 	: 'yotu_deletecache'
							}, 
							function (res, wrp, obj) {
								alert(res.msg);
							}
						);
						
					break;
				}
			});

			$('#shortcode_val').click(function () {
			   $(this).select();
			});
			
			
			//active tab with hash
			var hash = window.location.hash,
				last_tab = jQuery('#yotu-settings-last_tab').val(),
				active_tab = '';
			if (
				last_tab !== 'undefined' &&
				last_tab !== ''
			){
				active_tab = last_tab;
				
			}
			if (hash && hash.indexOf('?')<0) {
				active_tab = hash.replace('#','');
			}
			
			if ( active_tab !==''){
				jQuery('.yotu-tabs a[data-tab='+active_tab+']').trigger('click');
			}else{
            	jQuery('.yotu-tabs li:first-child a').trigger('click');
			}
			
			jQuery('.yotu_insert_popup .yotu-tabs li:first-child a').trigger('click');

			jQuery('.yotu_insert_popup .yotu-actions a').on('click', function (){
				var params = yotu_wp.get_params();
				wp.media.editor.insert('[yotuwp type="' + yotu_wp.parse_data.type + '" id="' + yotu_wp.parse_data.id + '" ' +params.join(' ') + ']');
			});

			jQuery('.yotu-insert-popup .yotu-tab-content').each(function(){

				var that = $(this),
					type = that.data('type');
			
			
				that.find('.yotu-search-action').on('click', function (e){
					e.preventDefault();

					var data = {}, val = that.find('.yotu-input-value').val().trim();
					
					if (['channel', 'playlist'].indexOf(type) > -1){
						data = yotu_wp.parse(val);
					}else if (['videos'].indexOf(type) > -1) {
						var lines = val.split('\n'),
							ids = [];
						lines.map(function (url){
							data = yotu_wp.parse(url);
							
							if(data.type == 'video')
								ids.push(data.id);
						});
						
						data.type = 'videos';
						data.id = ids.join(',');
					} else if (['username', 'keyword'].indexOf(type) > -1) {
						data.id = val;
						data.type = type;
						data.valid = true;
					}
					 
					if ( 
						type == 'playlist' && 
						data.type === 'video'
					) {
						if(typeof data.params.list !=='undefined'){
							data.id = data.params.list;
							data.type = 'playlist';
						}else data.valid = false;
					}

					yotu_wp.parse_data = data;

					if (
						data.valid && 
						data.type == type
					) {
						yotu_wp.ajax(
							that,
							{
								type	: type,
								data	: data.id,
								action 	: 'yotu_getinfo'
							}, 
							yotu_wp.actions.search_result 
						);
					}else alert('Please enter correct URL to start getting info.');
				});
				
			});
			
			jQuery('#yotu-settings-handler').on('click', yotu_wp.actions.show_settings);
			

			$('label.yotu-field-radios').on('click', function(e){
				var wrp = $(this).closest('.yotu-radios-img');
				wrp.find('.yotu-field-radios-selected').removeClass('yotu-field-radios-selected');
				$(this).addClass('yotu-field-radios-selected');
			});

			$('label.yotu-field-radios a').on('click', function(e){
				e.preventDefault();
			});
			var style_tag = $('#yotu-styling'),

			render_styling = function (elm, selector, value){

				var style_str = style_tag.html();

				if(style_str.indexOf('/*'+ elm.attr('id') +'*/') < 0){
					style_str += '/*'+ elm.attr('id') +'*/';
					style_str += selector + '{';
					style_str += value;
					style_str += '}';
					style_str += '/*end '+ elm.attr('id') +'*/';
					style_tag.html(style_str);
				}else{

					var patt = new RegExp('\/\\*' + elm.attr('id') + '(.+)'+elm.attr('id')+'\\*\/'),
						rep;

					rep = '/*'+ elm.attr('id') +'*/';
					rep += selector + '{';
					rep += value;
					rep += '}';
					rep += '/*end '+ elm.attr('id') +'*/';
					style_str = style_str.replace(patt, rep);
					style_tag.html(style_str);
				}
			},

			change_color = function (e, ui){
				let elm = $(e.target),
					refer = elm.data('css');
				if(refer !== ''){
					refer = refer.split('|');
					render_styling(elm, refer[0], refer[1] + ':' + ui.color.toString());
				}
				setTimeout(function (){
					elm.trigger('change');
				}, 200);
				
			};

			$('.yotu-colorpicker').wpColorPicker({
				change : change_color,
				clear : function(e){
					$(this).closest('.wp-picker-input-wrap').find('input.yotu-param').trigger('input').trigger('change');
					
				}
			}).on('input', function(e){
				var elm = $(this),
					refer = elm.data('css');

				if(refer !== ''){
					refer = refer.split('|');
					render_styling(elm, refer[0], refer[1] + ':' + elm.val());
				}
			});
			
		},

		add_action : function (name, callback) {
			if (yotu_wp.data.actions[name] === undefined)
			yotu_wp.data.actions[name] = [];

			yotu_wp.data.actions[name].push(callback);
		},

		do_action : function(name) {
			var params = Array.prototype.slice.call(arguments, 1);
			if (yotu_wp.data.actions[name] !== undefined) {
				var res;
				yotu_wp.data.actions[name].map(function(func) {
					if (typeof func == 'function') {
						try { 
							res = func.apply(null, params);
						}catch(e){
							console.warn('action '+name+':' + e.message);
						}
					}
				});
			}
		},

		apply_filter: function (name, val) {
			var params = Array.prototype.slice.call(arguments, 2, arguments.length);
			params.push(val);
			if (yotu_wp.data.filters[name] !== undefined) {
				yotu_wp.data.filters[name].map(function(func) {
					if (typeof func == 'function') {
						try { 
							val = func.apply(null, params);
						}catch(e){
							console.warn('action '+name+':' + e.message);
						}
					}
				});
			}
			return val;
		},

		add_filter: function (name, callback) {
			if (yotu_wp.data.filters[name] === undefined)
				yotu_wp.data.filters[name] = [];

			yotu_wp.data.filters[name].push(callback);
		},

		sys_msg : function (e) {
			
			if ( 
				typeof e.data == 'object' 
				&& typeof e.data['action'] !== 'undefined' 
			) {
				switch( e.data.action ) {
					case 'install_preset' : 
						yotu_wp.presets.install(e.data);
					break;

					case 'error':
						yotu_wp.show_message();
					break;
				}
			}
		},

		msg : {
			elm : null,
			show : function ( msg ) {
				if ( yotu_wp.msg.elm === null ) {
					yotu_wp.msg.elm = jQuery('<div class="yotuwp-message">\
						<a href="yotuwp-msg-close">x</a>\
						<div class="yotuwp-msg-content"></div>\
					</div>');
					yotu_wp.msg.appendTo('body');
				}
				yotu_wp.msg.elm.find('.yotuwp-msg-content').html( msg );
				yotu_wp.msg.elm.addClass('yotuwp-show-msg');
			},

			close : function () {
				if( yotu_wp.msg.elm !== null ) yotu_wp.msg.elm.removeClass('yotuwp-show-msg');
			}
		},

		get_params : function (){
			let type = jQuery('.yotu_insert_popup').data('type'),
				values = jQuery('.yotu-layout .yotu-param').serializeArray(),
				param_keys = yotu_wp.apply_filter('param_keys', ['settings', 'player', 'effects']),
				data = {},
				option, params = [], options={}, player={}, player_params = [];
				
			for (let i in values) {
				let param = values[i];
				param_keys.map(function (key){
					let patt = new RegExp('yotu-'+key+'\\[(.+)\\]');

					if (param.name.indexOf(key) > -1) {
						if (typeof data[key] === 'undefined') data[key] = [];

						data[key][param.name.replace(patt,'$1')] = param.value;
					}
				});

				
			}

			params = yotu_wp.get_options_param(data['settings'], yotujs.options);
			param_keys.map(function (key){
				
				if (key === 'settings') return;

				let params_tab = (['player', 'styling', 'effects'].indexOf(key) > -1)? 
					yotu_wp.get_player_param(data[key], yotujs[key]) : 
					yotu_wp.get_options_param(data[key], yotujs[key]);

				if (params_tab.length > 0)
					params.push(key + '="' + params_tab.join('&') + '"');
			});

			delete yotujs.options['id'];
			delete yotujs.options['type'];
			delete yotujs.options['player'];
			//delete yotujs.options['styling'];
			
			return params;
		},
		
		get_options_param : function (options, def_ops){
			var params = [];

			for (var i in def_ops) {
				if (
					def_ops[i] === 'on' && 
					typeof options[i] === 'undefined'
				) {
					params.push(i + '="off"');
					continue;
				}

				if (
					typeof def_ops[i] !== 'number' && 
					options[i] !== def_ops[i] && 
					typeof options[i] !== 'undefined' 
				) {
					params.push(i + '="' + options[i] + '"');
				}
				
				if (
					typeof def_ops[i] === 'number' && 
					parseInt(options[i]) !== def_ops[i]
				) {
					params.push(i + '="' + options[i] + '"');
				}
			}
			
			return params;
		},
		
		get_player_param : function (options, def_ops) {
			let params = [],
				convert = function (val) {
					switch(options[i]) {
						case 'on':
							return 1;
							break;
						case 'off':
							return 0;
							break;
						default:
							return val;
							break;
					}
				};
			
			for (i in def_ops) {
				if (
					def_ops[i] === 'on' && 
					typeof options[i] === 'undefined'
				) {
					params.push(i + '=0');
					continue;
				}

				if (
					typeof def_ops[i] !== 'number' && 
					options[i] !== def_ops[i] && 
					typeof options[i] !== 'undefined' &&
					options[i] !== ''
				) {
					params.push(i + '=' + convert(options[i]));
				}
				
				if (
					typeof def_ops[i] === 'number' && 
					parseInt(options[i]) !== def_ops[i]
				) {
					params.push(i + '=' + convert(options[i]));
				}
			}
			console.log(params);
			return params;
		},

		ajax : function (wrp, obj, func) {
			var error = function (code) {
				var txt = '';
				switch(code) {
					case 403:
						text = yotujs.lang['01'];
						break;
					case 404:
						text = yotujs.lang['03'];
						break;
				}
				
				if (text !== '') alert(txt);
			};

			jQuery('.yotu_insert_popup .yotu-info-res').addClass('yotu-active');

			$.ajax({

				url: yotujs.ajax_url + '?'+Math.random(),
				type: 'POST',
				dataType: 'json',
				data: obj,
				statusCode : {
					403: function () {
						error(403);
					},
					404: function() {
						error(404);
					},
					200: function (data) {
						func(data, wrp, obj);
					}
				}
			});
		},

		parse : function (url) {
			 
			if ( !/^http(s)?/i.test(url) ) url = "http://" + url;
			 
			var parser = document.createElement("a");
				parser.href = url,
				parsedUrl = {
					valid: false,
					params: {}
				},
				regexYouTubeUrl = /^((?:(?:m|www)\.)?youtu(?:be.com|.be|be.googleapis.com))/i;

			if ( regexYouTubeUrl.test(parser.hostname) ) {

				var regexYouTubeType = /^\/(channel|user|playlist|watch|v|video|embed)/i,
					typeCheck = regexYouTubeType.exec(parser.pathname);
					
				if ( typeCheck ) {
					
					if ( ["watch","v","video","embed"].indexOf(typeCheck[1]) > -1 ) {
						parsedUrl.type = "video";
					}
					else if ( ["channel","user"].indexOf(typeCheck[1]) > -1 ) {
						parsedUrl.type = "channel";
					}else if ( ["playlist"].indexOf(typeCheck[1]) > -1 ) {
						parsedUrl.type = "playlist";
					}
					 
					// If we got a valid type, get the ID
					if ( parsedUrl.type === "channel" ) {

						var regexYouTubeChannelId = /^\/[^\/]*\/([^\/]*)/i,
							channelCheck = regexYouTubeChannelId.exec(parser.pathname);

						parsedUrl.id = channelCheck[1];
					}

					else if ( parsedUrl.type === "video" || parsedUrl.type === "playlist" ) {
						var urlParamsStr = parser.search.substring(1),
							urlParamsPairs = urlParamsStr.split("&");
						 
						urlParamsPairs.forEach(function(pair){
							var pairKeyValue = pair.split("=");
							parsedUrl.params[pairKeyValue[0]] = pairKeyValue[1];
						});
						 
						parsedUrl.id = parsedUrl.params.v;

						if(parsedUrl.type === "playlist")
							parsedUrl.id = parsedUrl.params.list;
					}
					 
					// If we got the ID, then we can mark this as valid
					if ( parsedUrl.id ) {
						parsedUrl.valid = true;
						// Create a normalized YouTube URL
						parsedUrl.normalized = "http://youtube.com/" + parsedUrl.type + "/";

						if ( parsedUrl.type === "video" ) {
							parsedUrl.normalized += "?v=";
						}
						
						parsedUrl.normalized += parsedUrl.id;
					}
				} else if (parser.hostname == 'youtu.be') {
					parsedUrl.type = "video";
					parsedUrl.valid = true;
					parsedUrl.id = parser.pathname.replace('/', '');
				}
			}
			 
			return parsedUrl;
		},

		presets : {

			elm : null,

			load : function ( field, installed = '' ){
				var that = this,
					ifurl = 'https://api.yotuwp.com/presets/?action=load&part='+ field +'&installed=' + installed;
				if ( this.elm === null ) {
					
					this.elm = jQuery('<div class="yotuwp-presets-popup">\
						<div class="yotuwp-popup-wrp-content">\
							<div class="yotuwp-popup-title">Presets</div>\
							<a href="#" class="yotuwp-popup-close">Close</a>\
							<div class="yotuwp-popup-content"><iframe id="ifpresets" src="" width="100%" height="100%"></iframe></div>\
						</div>\
					</div>');
					this.elm.find('.yotuwp-popup-close').on('click', that.close);
					this.elm.appendTo('body');
				}
				
				that.elm.find('iframe').attr({src: ifurl});
				that.elm.addClass('yotuwp-active');
			},

			close : function (e) {
				
				e.preventDefault();
				yotu_wp.presets.elm.removeClass('yotuwp-active');
			},

			install : function (data) {

				var obj = {
					'action' : 'install_presets',
					'data' : data,
					'field' : data.field
				};

				$.ajax({

					url: yotujs.ajax_url + '?'+Math.random(),
					type: 'POST',
					dataType: 'json',
					data: obj,
					statusCode : {
						403: function (){
							error(403);
						},
						404: function(){
							error(404);
						},
						200: function ( res ){

							var new_elm = jQuery('<label class="yotu-field-radios" for="yotu-styling-' + data.field + '-' + data.data.slug + '">\
								<input class="yotu-param" value="' + data.data.slug + '" type="radio" id="yotu-styling-' + data.field + '-' + data.data.slug + '" name="yotu-styling[' + data.field + ']">\
								<img src="' + data.data.thumbnail + '" alt="' + data.data.name + '" title="' + data.data.name + '">\
								<br><span>' + data.data.name + '</span>\
								</label>'),
								installed = $('#yotuwp-field-'+data.field).find('[data-preset]').data('installed')

							installed += ',' + data.data.slug;

							$('#yotuwp-field-'+data.field).find('.yotu-radios-img').append( new_elm );
							

							$('#yotuwp-field-'+data.field).find('[data-preset]').data('installed', installed);
							new_elm.on('click', function(e){
								var wrp = $(this).closest('.yotu-radios-img');
								wrp.find('.yotu-field-radios-selected').removeClass('yotu-field-radios-selected');
								$(this).addClass('yotu-field-radios-selected');
							});

							var msg = {
								'action' : 'installed',
								'field' : data.field,
								'slug' : data.data.slug
							};
							window.ifpresets.contentWindow.postMessage(msg, '*');
						}
					}
				});
			}
		},
		
		actions : {
			
			show_settings : function (e){
				if (jQuery(e.target).is(':checked'))
					jQuery('.yotu-layout').addClass('yotu-hidden');
				else
					jQuery('.yotu-layout').removeClass('yotu-hidden');
			},

			search_result: function ( data, wrp, obj ) {

				var html = '<h4 class="light">' + yotujs.lang[4] + '</h4>',
					is_shortcode_gen = wrp.closest('.shortcode_gen')

				if (data.items.length > 0) {
					data.items.map(function (item) {
						var thumb_url = '';
						if (typeof item.snippet.thumbnails['default'] !== 'undefined')
							thumb_url = item.snippet.thumbnails.default.url;
						else  thumb_url = item.snippet.thumbnails.medium.url;

						html += '<div class="yotu-result">\
									<div class="yotu-item-thumb"><img src="' + thumb_url + '"/></div>\
									<div class="yotu-item-title">' + item.snippet.title + '</div>\
									<div class="yotu-item-desc">' + item.snippet.description + '</div>\
								</div>';
					});

					jQuery('.yotu_insert_popup .yotu-actions').removeClass('yotu-hidden');

					yotu_wp.insert = {
						type: obj.type,
						id: obj.id
					}

				} else if (
					typeof data['error'] !== 'undefined' &&
					data.error === true &&
					data.msg !== ''
				) {
					html = '<p><strong>Error</strong>: ' + data.msg + '</p>';
				} else {
					html = '<p>Items not found, please check your url again.</p>';
				}

				if (is_shortcode_gen.get(0)) {
					var gen = function () {
						params = yotu_wp.get_params();
						jQuery('#shortcode_val').val('[yotuwp type="' + yotu_wp.parse_data.type + '" id="' + yotu_wp.parse_data.id + '" ' + params.join(' ') + ']');
					};
					gen();
					jQuery('.yotu-layout .yotu-param').off().on('change', function () {
						gen();
					})

				}
				jQuery('.yotu_insert_popup .yotu-info-res').html(html).removeClass('yotu-active');
			}
			
		}
	}
})(jQuery);

jQuery( document ).ready(function() {
    yotu_wp.init();
	
});