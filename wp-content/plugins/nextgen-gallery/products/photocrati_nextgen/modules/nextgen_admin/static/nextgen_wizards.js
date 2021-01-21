
if (typeof(NextGEN_Wizard_Manager) === 'undefined') {
	function NextGEN_Wizard(id, manager) {
		this.id = id;
		this.manager = manager;
		this.data = {};
		this.steps = [];
		this.status = null;
		this.tour = null;
		this.created = Math.floor(Date.now() / 1000);
	};
	
	NextGEN_Wizard.prototype.start = function () {
		if (this.tour != null)
			this.tour.start();
	};
	
	NextGEN_Wizard.prototype.getNextStep = function (stepId) {
		var index = 0;
		for (; index < this.steps.length; index++) {
			var step = this.steps[index];
			
			if (step['id'] == stepId)
				break;
		}
		
		index++;
		
		if (index < this.steps.length)
			return this.steps[index];
		
		return null;
	};
	
	NextGEN_Wizard.prototype.setState = function (state) {
	};
	
	var manager = {
		state : [],
		wizards : [],
		runningWizard : null,
		views : [],
		starter : null,
		anchor : null,
		events : {},
		targetWatchers : {},
		refreshTimer : null,
		nggEvents : [],
		
		init : function () {
			if (typeof(NextGEN_Wizard_Manager_State) !== 'undefined') {
				this.state = NextGEN_Wizard_Manager_State;
			}
			
			var manager = this;
			jQuery(window.top.document).find('body').on('nextgen_event', function (e, type) {
				manager.nggEvents.push({ type: type });
			});
			
			function BaseView(el) {
				var self = this;
				this.itemSelector = '.view-item';
				this.currentWizard = null;
				this.currentStep = null;
				this.doneFlag = false;
				
				this.init = function () {
				};
				this.disable = function() {
				  el.addClass('ngg-wizard-disabled');
				};
				this.enable = function() {
				  el.removeClass('ngg-wizard-disabled');
				};
				this.setup = function() {
				};
				this.reset = function() {
				  el.removeClass('ngg-wizard-has-selection');
				  el.find(this.itemSelector).removeClass('ngg-wizard-selected');
				};
				this.done = function(child) {
					if (!this.doneFlag) {
						this.doneFlag = true;
						this.trigger('done', this, child);
					}
				};
			}
			_.extend(BaseView.prototype, Backbone.Events);
			
			function GenericView(el) {
				BaseView.call(this, el);
				
				var self = this;
				
				jQuery(el.get(0).ownerDocument).on('click', el.selector, function (e) {
					self.done(el);
				});
			}
			_.extend(GenericView.prototype, BaseView.prototype);
			
			function ButtonView(el) {
				BaseView.call(this, el);
				
				var self = this;
				
				el.on('click', function (e) {
					// here we only trigger done() if anchor is page-local (i.e. JS button) because for normal anchors this step is only "done" when reaching the new page
					if (!el.is('a,button.ngg_save_settings_button,input.ngg_save_gallery_changes') || (el.attr("href").startsWith("#") || el.attr("href").startsWith("javascript:") || el.hasClass("thickbox"))) {
						self.done(el);
					}
				});
				
				this.setup = function() {
					
					if (!el.is('a,button.ngg_save_settings_button,button.ngg_save_pricelist_button,input.ngg_save_gallery_changes')) {
						return;
					}
					
					if (el.is('a')) {
						var href = el.attr("href");
						this.originalHref = href;
					}

					if (el.is('button.ngg_save_settings_button,button.ngg_save_pricelist_button,input.ngg_save_gallery_changes')) {
						var href = el.parents('form').attr('action');
						if (href.indexOf("&ngg_wizard") > -1) {
							hrefSplit = href.split("&ngg_wizard");
							href = hrefSplit[0];
						} 
					}
		
					if (!href.startsWith("#")) {
						if (href.indexOf("?") == -1)
							href = href + "?";
						else
							href = href + "&";
					
						href = href + "ngg_wizard=" + this.currentWizard + "&ngg_wizard_step=" + this.currentStep;
				
						if ( el.is('a') ) 
							el.attr("href", href);

						if ( el.is('button.ngg_save_settings_button,button.ngg_save_pricelist_button,input.ngg_save_gallery_changes') ) 
							el.parents('form').attr('action', href);
					}
				};
				
				this.reset = function() {
					if (this.originalHref) {
						if (el.is('a')) {
							el.attr('href', this.originalHref);
						}
					}
				};
			}
			_.extend(ButtonView.prototype, BaseView.prototype);
			
			function TextView(el) {
				BaseView.call(this, el);
				
				var self = this;
				
				el.on('input', function (e) {
					if (jQuery(this).val().length >= 3)
						self.done(el);
				});
				
				this.setup = function() {
					// in here we can support wizards steps after a post is submitted by editing the form's URL
				};
			}
			_.extend(TextView.prototype, BaseView.prototype);
			
			function RadioView(el) {
				BaseView.call(this, el);
				
				var self = this;
				
				jQuery(el.get(0).ownerDocument).on('change', el.selector, function (e) {
					if (jQuery(this).is(':checked'))
						self.done(el);
				});
				
				this.setup = function() {
					// in here we can support wizards steps after a post is submitted by editing the form's URL
				};
			}
			_.extend(RadioView.prototype, BaseView.prototype);
			
			function SelectView(el) {
				BaseView.call(this, el);
				
				var self = this;
				
				el.on('change', function (e) {
					self.done(el);
				});
			}
			_.extend(SelectView.prototype, BaseView.prototype);
			
			var view = null;
			
			view = {
				handler : SelectView,
				name : "SelectView",
				selector : "select"
			};
			this.views.push(view);
			
			view = {
				handler : RadioView,
				name : "RadioView",
				selector : "input[type='radio']"
			};
			this.views.push(view);
			
			view = {
				handler : TextView,
				name : "TextView",
				selector : "input[type='text'], input[type='search'], input[type='email'], input[type='tel'], input[type='number'], input[type='username'], input[type='password'], textarea"
			};
			this.views.push(view);
			
			view = {
				handler : ButtonView,
				name : "ButtonView",
				selector : "a, input[type='button'], input[type='submit'], button"
			};
			this.views.push(view);
			
			view = {
				handler : GenericView,
				name : "GenericView",
				selector : "*"
			};
			this.views.push(view);
			
			// override close button template
    	Tourist.Tip.Base.prototype.nextButtonTemplate = '<a class="button-primary pull-right tour-next">Next step →</a>';
    	Tourist.Tip.Base.prototype.finalButtonTemplate = '<button class="button-primary pull-right tour-next">Finish up</button>';
			// override close button template
			Tourist.Tip.Base.prototype.closeButtonTemplate = '<a class="btn btn-close tour-close" href="#"><i class="icon icon-remove far fa-window-close"></i></a>';
			
			// override Tourist's BootstrapTip logic to retrieve target bounds
		  Tourist.Tip.BootstrapTip.prototype._getTargetBounds = function(target) {
		    var el, size;
		    el = target[0];
		    if (typeof el.getBoundingClientRect === 'function') {
		      size = el.getBoundingClientRect();
		    } else {
		      size = {
		        width: el.offsetWidth,
		        height: el.offsetHeight
		      };
		    }
		    
		    var offset = target.offset();
		    if (target.ownerDocument != document) {
		    	var findFrameHierarchy = function (root, targetDoc) {
				  	var iframes = root.find("iframe");
				  	var ret = [];
				  	for (var i = 0; i < iframes.length; i++) {
				  		var iframe = jQuery(iframes.get(i));
				  		try {
								if (iframe.prop("contentWindow").document == targetDoc)
									return [ iframe ];
				  		}
				  		catch (ex) {
				  			continue;
				  		}
				  		
				  		var iframeNested = findFrameHierarchy(iframe.contents(), targetDoc);
				  		if (iframeNested.length > 0) {
				  			ret.push(iframe);
				  			for (var l = 0; l < iframeNested.length; l++) {
				  				ret.push(iframeNested[l]);
				  			}
				  			break;
				  		}
				  	}
				  	return ret;
		    	};
		    	
		    	var iframes = findFrameHierarchy(jQuery(document), target.get(0).ownerDocument);
		    	for (var i = 0; i < iframes.length; i++) {
		    		var iframe = iframes[i];
		    		var iframeOff = iframe.offset();
		    		offset.left += iframeOff.left;
		    		offset.top += iframeOff.top;
		    	}
		    }
		    
		    return jQuery.extend({}, size, offset);
		  };
		  
		  jQuery('.ngg-wizard-invoker').on('click', function (e) {
		  	e.preventDefault();
		  	
		  	var manager = NextGEN_Wizard_Manager;
		  	var wizardId = jQuery(this).data('ngg-wizard');
		  	var wizardFound = null;
		  	
		  	for (var i = 0; i < manager.wizards.length; i++) {
					var wizard = manager.wizards[i];
					
					if (wizard.id == wizardId) {
						wizardFound = wizard;
						break;
					}
		  	}
		  	
		  	if (wizardFound != null && this.runningWizard == null)
		  		wizardFound.start();
		  	
		  	return false;
		  });
		},
		
		bind: function (eventName, handler) {
			if (!(eventName in this.events))
				this.events[eventName] = [];
				
			var evtOb = { 'handler' : handler };
			
			this.events[eventName].push(evtOb);
		},
		
		trigger: function (eventName, params) {
			if (typeof(params) === "undefined")
				params = {};
			
			if (eventName in this.events) {
				var evtList = this.events[eventName];
				
				for (var i = 0; i < evtList.length; i++) {
					var evt = evtList[i];
					
					if (evt.handler)
						evt.handler(this, params);
				}
			}
		},
		
		didNextgenEventFire: function (type) {
			for (var i = 0; i < this.nggEvents.length; i++)
			{
				var evt = this.nggEvents[i];
				
				if (evt.type == type)
					return true;
			}
			
			return false;
		},
		
		getViewForSelector: function (jquerySet, init) {
			var handler = null;
			
			if (typeof(init) === "undefined")
				init = true;
			
			for (var i = 0; i < this.views.length; i++) {
				var view = this.views[i];
				
				if (jquerySet.is(view.selector)) {
					handler = view.handler;
					break;
				}
			}
			
			if (handler != null) {
				var view = new handler(jquerySet);
				
				if (init)
					view.init();
				
				return view;
			}
			
			return null;
		},
		
		getViewFor: function (jquerySet, wizardId, stepId) {
			var view = this.getViewForSelector(jquerySet, false);
			
			if (view != null) {
				view.currentWizard = wizardId;
				view.currentStep = stepId;
				
				view.init();
			}
			
			return view;
		},
		
		generateQueue : function ($) {
			var self = this;
			var state = this.state;
			var wizard_count = state.wizard_list.length;
			var starter = state.starter;
			var runningWizardId = state.running_wizard;
		
			// create a starter for the wizards, a call to attention
			if (wizard_count > 0) {
				var $starter = $('<div id="ngg-wizard-starter" class="ngg-wizard-starter"></div>');
				$starter.append('<div class="starter-wrap-top"><div class="starter-wrap-icon"><img class="starter-icon" src="' + starter['image'] + '" /></div></div>');
				$starter.append('<div class="starter-wrap-bottom"><div class="starter-wrap-text">' + starter['text'] + '</div></div>');
			
				$starter.on("click", ".starter-wrap-icon, starter-wrap-bottom", function () {
					self.startQueue();	
				});
				
				$starter.hide();
				$starter.appendTo('body');
				this.starter = $starter;
				
				var $anchor = $('<div id="ngg-wizard-anchor" class="ngg-wizard-anchor"></div>');
				$anchor.appendTo('body');
				this.anchor = $anchor;
				
				var waitCount = 0;
	
				for (var i = 0; i < wizard_count; i++) {
					var wizardData = state.wizard_list[i];
					var wizard = new NextGEN_Wizard(wizardData['id'], this);
					wizard.data = wizardData;
					wizard.steps = wizardData['steps'];
					
					var result = this.generateTour(wizard);
					
					if (result.result == "ok") {
						wizard.status = "ready";
						wizard.tour = result.tour;
					}
					else if (result.result == "wait_element") {
						wizard.status = "wait";
						waitCount++;
					}
					
					if (wizard.id == runningWizardId) {
						this.wizards.unshift(wizard);
						this.runningWizard = wizard;
					}
					else
						this.wizards.push(wizard);
				}
				
				if (waitCount > 0)
					this.enqueueRefreshOperation();
				else
					this.trigger('ready');
			}
		},
		
		getContextObject : function (context) {
			var jContext = null;
			
			if (context != null) {
				if (typeof(context) !== "object")
					context = [ context ];
				
				for (var i = 0; i < context.length; i++) {
					var contextIt = context[i];
					
					if (jContext == null)
						jContext = jQuery(contextIt);
					else
						jContext = jContext.find(contextIt);
						
					if (jContext.is("iframe")) {
						//jContext = jContext.contents().find("body");
						var iframeWin = jContext.prop("contentWindow");
						if (iframeWin.document.readyState == 'interactive' || iframeWin.document.readyState == 'complete') {
							if (iframeWin.jQuery)
								jContext = iframeWin.jQuery("body");
							else
								jContext = jContext.contents().find("body");
						}
						else {
							jContext = jQuery([]);
							
							break;
						}
					}
				}
				
				if (jContext.length == 0)
					jContext = jQuery([]);
			}
			else
				jContext = jQuery(document);
				
			return jContext;
		},
		
		computeStepTarget : function (wizard, step, stepOb) {
			var targetWait = step['target_wait'];
			var target = step['target'];
			var view = step['view'];
			var jTarget = null;
			var jView = null;
			var viewOb = null;
			var jContext = this.getContextObject(step['context']);
			var isTargeted = false;
		
			if (target != null) {
				jTarget = jContext.find(target);
				
				if (jTarget.length > 0)
					isTargeted = true;
				else
					jTarget = this.anchor;
				
				// if we couldn't locate the target but the step is specified to wait for it, then return
				if (!isTargeted && targetWait != null) {
					if ((Math.floor(Date.now() / 1000) - wizard.created) <= targetWait)
						return false;
				}
			}
			else
				jTarget = this.anchor;
			
			if (view != null) {
				jView = jContext.find(view);
				
				if (jView.length > 0)
					viewOb = this.getViewFor(jView, wizard.id, step['id']);
			}
			
			stepOb.highlightTarget = isTargeted;
			stepOb.nextButton = !isTargeted || step['optional'];
			stepOb.target = jTarget;
			stepOb.ngg_view = viewOb;
				
			return true;
		},
		
		scrollIntoView : function (target) {
			
		},
		
		generateTour : function (wizard) {
			// return if we already generated it
			if (wizard.tour != null)
				return;
		
			var wizardData = wizard.data;
			var steps = wizardData['steps'];
			var currentStep = wizardData['current_step'];
			var skipSteps = 0;
			var tourSteps = [];
			var tourView = null;
			
			if (wizardData['view'] != null) {
				tourView = this.getViewFor(jQuery(wizardData['view']), wizard.id);
			
				if (tourView == null)
					return { result: "wait_element", element: wizardData['view'] };
			}
			
			for (var l = 0; l < steps.length; l++) {
				var step = steps[l];
				
				if (currentStep != null) {
					if (currentStep == step['id'])
						skipSteps = l;
				}
				
				var tourStep = {
					ngg_step_id: step['id'],
					ngg_step_data: step,
					viewport: jQuery(window),
					closeButton: true,
					content: '<div class="ngg-wizard-text">' + step['text'] + '</div>',
					my: step['popup_anchor'],
					at: step['target_anchor'],
				  // a function name in bind allows you to reference
				  // it with `this` in setup and teardown
				  bind: ['onDone'],
				  onDone: function(tour, options, view, el) {
						var wizard = tour.ngg_wizard;
						var step = this.ngg_step_data;
						var canNext = true;
						
						if (step['condition'] != null) {
							if (!this.condition_met) {
								canNext = false;
								
								if (!this.condition_setup)
									this.setupCondition(tour, options);
							}
						}
						
						if (canNext && wizard != null) {
							var stepNext = wizard.getNextStep(this.ngg_step_id);
							
							if (stepNext != null && stepNext['lazy']) {
								canNext = false;
								var tourStep = null;
								
								for (var i = 0; i < tour.options.steps.length; i++) {
									var step2 = tour.options.steps[i];
									if (step2.ngg_step_id == stepNext['id']) {
										tourStep = step2;
										break;
									}
								}
								
								if (tourStep != null) {
									if (tourStep.target == null || tourStep.target.length == 0 || tourStep.target.attr('id') == 'ngg-wizard-anchor') {
										canNext = wizard.manager.computeStepTarget(wizard, stepNext, tourStep);
										
										if (canNext && (tourStep.target == null || tourStep.target.length == 0 || tourStep.target.attr('id') == 'ngg-wizard-anchor'))
											canNext = false;
									}
									else
										canNext = true;
										
//									if (canNext)
//										tourStep.target[0].scrollIntoView();
								}
							}
						}
						
						if (canNext) {
				    	tour.next();
						}
						else {
							if (this.lazyTimeout != null) {
								clearTimeout(this.lazyTimeout);
								this.lazyTimeout = null;
							}
							
							var stepOb = this;
							this.lazyTimeout = setTimeout(function () { stepOb.lazyTimeout = null; stepOb.onDone(tour, options, view, el); }, 500);
						}
				  }
				};
				
				var ready = this.computeStepTarget(wizard, step, tourStep);
				
				if (!ready)
					return { result: "wait_element", element: step['target'] };
					
				tourStep.setupCondition = function (tour, options) {
					var wizard = tour.ngg_wizard;
					var step = this.ngg_step_data;
					
					if (!this.condition_setup && step['condition'] != null) {
						var condition = step['condition'];
						var condType = condition['type'];
						var condValue = condition['value'];
						var condCtx = condition['context'];
						var condTimeout = condition['timeout'];
						this.condition_met = false;
						this.condition_setup = false;

						if (condCtx && typeof(condCtx) !== "object")
							condCtx = [ condCtx ];
						
						var $ob = jQuery([]);
						var obWin = null;
						if (condCtx) {
							// clone array
							condCtx = condCtx.slice(0);
							var condOb = condCtx.pop();
							$ob = wizard.manager.getContextObject(condCtx).find(condOb);
						}
						
						if ($ob.length > 0) {
							var doc = $ob.get(0).ownerDocument;
							obWin = doc.defaultView || doc.parentWindow;
						}
						
						switch (condType) {
							case 'frame_event': {
								if (window.Frame_Event_Publisher) {
									this.condition_setup = true;
									var tourStep = this;
									Frame_Event_Publisher.listen_for(condValue, function() {
										tourStep.condition_met = true;
									});
								}
								
								break;
							}
							case 'event_bind':
							case 'uppy_bind': {
								var doBind = $ob.length > 0;

								if (condType === 'uppy_bind' && 'undefined' !== typeof window.ngg_uppy) {
									$ob = window.ngg_uppy;
								}

								if (doBind) {
									this.condition_setup = true;
									var tourStep = this;
									$ob.on(condValue, function () {
										tourStep.condition_met = true;
									});
								}
								
								break;
							}
							case 'nextgen_event': {
								this.condition_setup = true;
								if (wizard.manager.didNextgenEventFire(condValue))
									this.condition_met = true;
								else {
									var tourStep = this;
									jQuery(window.top.document).find('body').on('nextgen_event', function (e, type) {
										if (type == condValue)
											tourStep.condition_met = true;
									});
								}
								
								break;
							}
							case 'wait': {
								this.condition_setup = true;
								var tourStep = this;
								setTimeout(function () {
									tourStep.condition_met = true;
								}, condValue);
								
								break;
							}
						}
					}
					
					var $content = jQuery('.tourist-popover .ngg-wizard-text');
					var $mark = $content.find('.ngg-wizard-loading');
					$mark.hide();
					
					if (this.condition_setup && !this.condition_met && condTimeout > 0) {
						this.condition_timeout = 0;
						if ($mark.length == 0) {
							$mark = jQuery('<div class="ngg-wizard-loading"></div>');
							$content.append($mark);
						}
						$mark.html('Loading... (' + Math.ceil(condTimeout / 1000).toString() + ')');
						$mark.show();
						
						var smallTimeout = 1000;
						var tourStep = this;
						tourStep.condition_timer = setInterval(function () {
							tourStep.condition_timeout += smallTimeout;
							if (tourStep.condition_timeout >= condTimeout)
								tourStep.condition_met = true;
							var secsIn = Math.floor(tourStep.condition_timeout / 1000);
							var secsTot = Math.ceil(condTimeout / 1000);
							var secsDiff = Math.floor((condTimeout - tourStep.condition_timeout) / 1000);
							//$mark.html('Loading... (' + secsIn.toString() + '/' + secsTot.toString());
							$mark.html('Loading... (' + secsDiff.toString() + ')');
							if (tourStep.condition_met) {
								$mark.hide();
								clearInterval(tourStep.condition_timer);
								tourStep.condition_timer = null;
							}
						}, smallTimeout);
					}
					
					return this.condition_setup;
				};
				
				tourStep.setup = function(tour, options) {
					var view = options.view;
					
					if (this.ngg_view != null)
						view = this.ngg_view;
					
					if (view != null) {
					  view.currentWizard = tour.ngg_wizard_id;
					  view.currentStep = this.ngg_step_id;
					  view.setup();
					  view.on('done', this.onDone);
					  view.enable();
					}
			  };
			  
				tourStep.teardown = function(tour, options) {
					var view = options.view;
					
					if (this.ngg_view != null)
						view = this.ngg_view;
						
					if (view != null) {
					  view.disable();
					  view.off('done', this.onDone);
					  view.reset();
					}
			  };
				
				tourSteps.push(tourStep);
			}
			
			var tourOpts = {
				steps: tourSteps,
				tipClass: 'Bootstrap',
				tipOptions: { showEffect: 'slidein' },
				stepOptions: { }
			};
			
			if (tourView != null)
				tourOpts.stepOptions.view = tourView;

			var tour = new Tourist.Tour(tourOpts);
			tour.ngg_wizard_id = wizard.id;
			tour.ngg_wizard = wizard;
			
			while (skipSteps > 0) {
				tour.next();
				skipSteps--;
			}
			
			return { result: "ok", tour: tour };
		},
		
		enqueueRefreshOperation : function () {
			if (this.refreshTimer != null) {
				clearTimeout(this.refreshTimer);
				this.refreshTimer = null;
			}
			
			this.refreshTimer = setTimeout(function (manager) { NextGEN_Wizard_Manager.refreshQueue(); }, 500);
		},
		
		refreshQueue : function () {
			this.refreshTimer = null;
		
			var waitCount = 0;

			for (var i = 0; i < this.wizards.length; i++) {
				var wizard = this.wizards[i];
				
				if (wizard.status == "wait") {
					var result = this.generateTour(wizard);
					
					if (result.result == "ok") {
						wizard.status = "ready";
						wizard.tour = result.tour;
					}
					else if (result.result == "wait_element") {
						wizard.status = "wait";
						waitCount++;
					}
				}
			}
			
			if (waitCount > 0)
				this.enqueueRefreshOperation();
			else
				this.trigger('ready');
		},
		
		start : function () {
			if (this.runningWizard != null) {
				this.runningWizard.start();
			}
			else
				this.showStarter();
		},
		
		showStarter : function () {
			// XXX starter disabled for now
			return;
			this.starter.fadeIn();
		},
		
		startQueue : function () {
			if (this.starter != null) {
				var self = this;
				this.starter.fadeOut(function () {
					self.nextTour();
				});
			}
			else
				this.nextTour();
		},
		
		nextTour : function () {
			var index = 0;
			
			for (var i = 0; i < this.wizards.length; i++) {
				var wizard = this.wizards[i];
				
				if (wizard.status == "ready" && wizard.tour != null) {
					index = i;
					break;
				}
			}
			
			if (index < this.wizards.length) {
				var wizard = this.wizards[index];
				wizard.tour.start();
			}
		},
		
		updateWizardState : function (wizard) {
			
		}
	};

	manager.init();
	
	window.NextGEN_Wizard_Manager = manager;
}


jQuery(function($){
	if (typeof(NextGEN_Wizard_Manager) !== 'undefined') {
		NextGEN_Wizard_Manager.bind('ready', function () {
			NextGEN_Wizard_Manager.start();
		});
		
		NextGEN_Wizard_Manager.generateQueue($);
	}
});
