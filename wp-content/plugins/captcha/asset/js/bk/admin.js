(function($) {
	if (!window['wordfenceAdmin']) { //To compile for checking: java -jar /usr/local/bin/closure.jar --js=admin.js --js_output_file=test.js
		window['wordfenceAdmin'] = {
			isSmallScreen: false,
			loading16: '<div class="wfLoading16"></div>',
			loadingCount: 0,
			dbCheckTables: [],
			dbCheckCount_ok: 0,
			dbCheckCount_skipped: 0,
			dbCheckCount_errors: 0,
			issues: [],
			ignoreData: false,
			iconErrorMsgs: [],
			scanIDLoaded: 0,
			colorboxQueue: [],
			mode: '',
			visibleIssuesPanel: 'new',
			preFirstScanMsgsLoaded: false,
			newestActivityTime: 0, //must be 0 to force loading of all initially
			elementGeneratorIter: 1,
			reloadConfigPage: false,
			nonce: false,
			tickerUpdatePending: false,
			activityLogUpdatePending: false,
			lastALogCtime: 0,
			activityQueue: [],
			totalActAdded: 0,
			maxActivityLogItems: 1000,
			scanReqAnimation: false,
			debugOn: false,
			blockedCountriesPending: [],
			ownCountry: "",
			schedStartHour: false,
			currentPointer: false,
			countryMap: false,
			countryCodesToSave: "",
			performanceScale: 3,
			performanceMinWidth: 20,
			tourClosed: false,
			welcomeClosed: false,
			passwdAuditUpdateInt: false,
			_windowHasFocus: true,
			serverTimestampOffset: 0,
			serverMicrotime: 0,
			wfLiveTraffic: null,
			loadingBlockedIPs: false,
			basePageName: '',

			init: function() {
				this.isSmallScreen = window.matchMedia("only screen and (max-width: 500px)").matches;
				
				this.nonce = WordfenceAdminVars.firstNonce;
				this.debugOn = WordfenceAdminVars.debugOn == '1' ? true : false;
				this.tourClosed = WordfenceAdminVars.tourClosed == '1' ? true : false;
				this.welcomeClosed = WordfenceAdminVars.welcomeClosed == '1' ? true : false;
				this.basePageName = document.title;
				var startTicker = false;
				var self = this;

				$(window).on('blur', function() {
					self._windowHasFocus = false;
				}).on('focus', function() {
					self._windowHasFocus = true;
				}).focus();

				$('.do-show').click(function() {
					var $this = $(this);
					$this.hide();
					$($this.data('selector')).show();
					return false;
				});
				
				$('.downloadLogFile').each(function() {
					$(this).attr('href', WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadLogFile&nonce=' + WFAD.nonce + '&logfile=' + encodeURIComponent($(this).data('logfile')));
				});

				$('#doSendEmail').click(function() {
					var ticket = $('#_ticketnumber').val();
					if (ticket === null || typeof ticket === "undefined" || ticket.length == 0) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error", "Please include your support ticket number or forum username.");
						return;
					}
					WFAD.ajax('wordfence_sendDiagnostic', {email: $('#_email').val(), ticket: ticket}, function(res) {
						if (res.result) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Email Diagnostic Report", "Diagnostic report has been sent successfully.");
						} else {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error", "There was an error while sending the email.");
						}
					});
				});

				$('#sendByEmail').click(function() {
					$('#sendByEmailForm').removeClass('hidden');
					$(this).hide();
				});

				$(window).bind("scroll", function() {
					$(this).scrollTop() > 200 ? $(".wf-scrollTop").fadeIn() : $(".wf-scrollTop").fadeOut()
				});
				$(".wf-scrollTop").click(function(e) {
					return e.stopPropagation(), $("body,html").animate({
						scrollTop: 0
					}, 800), !1;
				});

				var tabs = jQuery('#wordfenceTopTabs').find('a');
				if (tabs.length > 0) {
					tabs.click(function() {
						jQuery('#wordfenceTopTabs').find('a').removeClass('nav-tab-active');
						jQuery('.wordfenceTopTab').removeClass('active');
						jQuery(this).addClass('nav-tab-active');
						
						var tab = jQuery('#' + jQuery(this).attr('id').replace('-tab', ''));
						tab.addClass('active');
						jQuery('#wfHeading').html(tab.data('title'));
						jQuery('#wordfenceTopTabsMobileTitle').text(jQuery(this).text());
						document.title = tab.data('title') + " \u2039 " + self.basePageName;
						self.sectionInit();
					});
					if (window.location.hash) {
						var hashes = window.location.hash.split('#');
						var hash = hashes[hashes.length - 1];
						for (var i = 0; i < tabs.length; i++) {
							if (hash == jQuery(tabs[i]).attr('id').replace('-tab', '')) {
								jQuery(tabs[i]).trigger('click');
							}
						}
					}
					else {
						jQuery(tabs[0]).trigger('click');
					}
					jQuery(window).on('hashchange', function () {
						var hashes = window.location.hash.split('#');
						var hash = hashes[hashes.length - 1];
						for (var i = 0; i < tabs.length; i++) {
							if (hash == jQuery(tabs[i]).attr('id').replace('-tab', '')) {
								jQuery(tabs[i]).trigger('click');
							}
						}
					});
				}
				else {
					this.sectionInit();
				}
				
				if (this.mode) {
					jQuery(document).bind('cbox_closed', function() {
						self.colorboxIsOpen = false;
						self.colorboxServiceQueue();
					});
				}

				$(document).focus();

				// (docs|support).wordfence.com GA links
				$(document).on('click', 'a', function() {
					if (this.href && this.href.indexOf('utm_source') > -1) {
						return;
					}
					var utm = '';
					if (this.host == 'docs.wordfence.com') {
						utm = 'utm_source=plugin&utm_medium=pluginUI&utm_campaign=docsIcon';
					}
					if (utm) {
						utm = (this.search ? '&' : '?') + utm;
						this.href = this.protocol + '//' + this.host + this.pathname + this.search + utm + this.hash;
					}

					if (this.href == 'http://support.wordfence.com/') {
						this.href = 'https://support.wordfence.com/support/home?utm_source=plugin&utm_medium=pluginUI&utm_campaign=supportLink';
					}
				});
			},
			sectionInit: function() {
				var self = this;
				var startTicker = false;
				this.mode = false;
				if (jQuery('#wordfenceMode_dashboard:visible').length > 0) {
					this.mode = 'dashboard';
					if (this.needTour()) {
						this.scanTourStart();
					}
				} else if (jQuery('#wordfenceMode_scan:visible').length > 0) {
					this.mode = 'scan';
					jQuery('#wfALogViewLink').prop('href', WordfenceAdminVars.siteBaseURL + '?_wfsf=viewActivityLog&nonce=' + this.nonce);
					jQuery('#consoleActivity').scrollTop(jQuery('#consoleActivity').prop('scrollHeight'));
					jQuery('#consoleSummary').scrollTop(jQuery('#consoleSummary').prop('scrollHeight'));
					this.noScanHTML = jQuery('#wfNoScanYetTmpl').tmpl().html();


					var loadingIssues = true;

					this.loadIssues(function() {
						loadingIssues = false;
					});
					this.startActivityLogUpdates();

					if (this.needTour()) {
						self.tour('wfTourScan', 'wfHeading', 'top', 'left', "Learn about the Firewall", function() {
							self.tourRedir('WordfenceWAF');
						});
					}

					var issuesWrapper = $('#wfScanIssuesWrapper');
					var hasScrolled = false;
					$(window).on('scroll', function() {
						var win = $(this);
						// console.log(win.scrollTop() + window.innerHeight, liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top);
						var currentScrollBottom = win.scrollTop() + window.innerHeight;
						var scrollThreshold = issuesWrapper.outerHeight() + issuesWrapper.offset().top;
						if (hasScrolled && !loadingIssues && currentScrollBottom >= scrollThreshold) {
							// console.log('infinite scroll');

							loadingIssues = true;
							hasScrolled = false;
							var offset = $('div.wfIssue').length;
							WFAD.loadMoreIssues(function() {
								loadingIssues = false;
							}, offset);
						} else if (currentScrollBottom < scrollThreshold) {
							hasScrolled = true;
							// console.log('no infinite scroll');
						}
					});
				} else if (jQuery('#wordfenceMode_waf:visible').length > 0) {
					this.mode = 'waf';
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWAFTour', 'wfHeading', 'top', 'left', "Learn about Live Traffic", function() {
							self.tourRedir('WordfenceActivity');
						});
					}
				} else if (jQuery('#wordfenceMode_activity:visible').length > 0) {
					this.mode = 'activity';
					this.setupSwitches('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
					});
					jQuery('#wfLiveTrafficOnOff').change(function() {
						self.updateSwitch('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
							window.location.reload(true);
						});
					});

					if (WordfenceAdminVars.liveTrafficEnabled) {
						this.activityMode = 'hit';
					} else {
						this.activityMode = 'loginLogout';
						this.switchTab(jQuery('#wfLoginLogoutTab'), 'wfTab1', 'wfDataPanel', 'wfActivity_loginLogout', function() {
							WFAD.activityTabChanged();
						});
					}
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContent3', 'wfHeading', 'top', 'left', "Learn about IP Blocking", function() {
							self.tourRedir('WordfenceBlocking#top#blockedips');
						});
					}
				} else if (jQuery('#wordfenceMode_options:visible').length > 0) {
					this.mode = 'options';
					this.updateTicker(true);
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfContentBasicOptions', 'wfMarkerBasicOptions', 'top', 'left', "Learn about Live Traffic Options", function() {
							self.tour('wfContentLiveTrafficOptions', 'wfMarkerLiveTrafficOptions', 'bottom', 'left', "Learn about Scanning Options", function() {
								self.tour('wfContentScansToInclude', 'wfMarkerScansToInclude', 'bottom', 'left', "Learn about Rate Limiting Rules", function() {
									self.tour('wfContentFirewallRules', 'wfMarkerFirewallRules', 'bottom', 'left', "Learn about Login Security", function() {
										self.tour('wfContentLoginSecurity', 'wfMarkerLoginSecurity', 'bottom', 'left', "Learn about Other Options", function() {
											self.tour('wfContentOtherOptions', 'wfMarkerOtherOptions', 'bottom', 'left', false, false);
										});
									});
								});
							});
						});
					}
				} else if (jQuery('#wordfenceMode_blockedIPs:visible').length > 0) {
					this.mode = 'blocked';
					this.staticTabChanged();
					this.updateTicker(true);
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContent4', 'wfHeading', 'top', 'left', "Learn about Auditing Passwords", function() {
							self.tourRedir('WordfenceTools');
						});
					}

					var self = this;
					var hasScrolled = false;
					$(window).on('scroll', function() {
						var win = $(this);
						var wrapper = $('#wfActivity_' + self.activityMode);
						// console.log(win.scrollTop() + window.innerHeight, liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top);
						var currentScrollBottom = win.scrollTop() + window.innerHeight;
						var scrollThreshold = wrapper.outerHeight() + wrapper.offset().top;
						if (hasScrolled && !self.loadingBlockedIPs && currentScrollBottom >= scrollThreshold) {
							// console.log('infinite scroll');
							hasScrolled = false;

							self.loadStaticPanelContent(true);
						} else if (currentScrollBottom < scrollThreshold) {
							hasScrolled = true;
							// console.log('no infinite scroll');
						}
					});
				} else if (jQuery('#wordfenceMode_passwd:visible').length > 0) {
					this.mode = 'passwd';
					startTicker = true;
					this.doPasswdAuditUpdate();
					if (this.needTour()) {
						this.tour('wfWelcomePasswd', 'wfHeading', 'top', 'left', "Learn about Cellphone Sign-in", function() {
							self.tourRedir('WordfenceTools#top#twofactor');
						});
					}
				} else if (jQuery('#wordfenceMode_twoFactor:visible').length > 0) {
					this.mode = 'twoFactor';
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeTwoFactor', 'wfHeading', 'top', 'left', "Learn how to Block Countries", function() {
							self.tourRedir('WordfenceBlocking#top#countryblocking');
						});
					}
					this.loadTwoFactor();

				} else if (jQuery('#wordfenceMode_countryBlocking:visible').length > 0) {
					this.mode = 'countryBlocking';
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContentCntBlk', 'wfHeading', 'top', 'left', "Learn how to Schedule Scans", function() {
							self.tourRedir('WordfenceScan#top#scheduling');
						});
					}
				} else if (jQuery('#wordfenceMode_rangeBlocking:visible').length > 0) {
					this.mode = 'rangeBlocking';
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContentRangeBlocking', 'wfHeading', 'top', 'left', "Learn how to Customize Wordfence", function() {
							self.tourRedir('WordfenceSecOpt');
						});
					}
					this.calcRangeTotal();
					this.loadBlockRanges();
				} else if (jQuery('#wordfenceMode_whois:visible').length > 0) {
					this.mode = 'whois';
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContentWhois', 'wfHeading', 'top', 'left', "Learn how to use Advanced Blocking", function() {
							self.tourRedir('WordfenceBlocking#top#advancedblocking');
						});
					}
					this.calcRangeTotal();
					this.loadBlockRanges();

				} else if (jQuery('#wordfenceMode_scanScheduling:visible').length > 0) {
					this.mode = 'scanScheduling';
					this.sched_modeChange();
					if (this.needTour()) {
						this.tour('wfWelcomeContentScanSched', 'wfHeading', 'top', 'left', "Learn about WHOIS", function() {
							self.tourRedir('WordfenceTools#top#whois');
						});
					}
				}
				
				if (this.mode) { //We are in a Wordfence page
					if (startTicker) {
						this.updateTicker();
						if (this.liveInt > 0) {
							clearInterval(this.liveInt);
							this.liveInt = 0;
						}
						this.liveInt = setInterval(function() {
							self.updateTicker();
						}, WordfenceAdminVars.actUpdateInterval);
					}
				}
			},
			needTour: function() {
				if ((!this.tourClosed) && this.welcomeClosed) {
					return true;
				} else {
					return false;
				}
			},
			sendTestEmail: function(email) {
				var self = this;
				this.ajax('wordfence_sendTestEmail', {email: email}, function(res) {
					if (res.result) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Test Email Sent", "Your test email was sent to the requested email address. The result we received from the WordPress wp_mail() function was: " +
							res.result + "<br /><br />A 'True' result means WordPress thinks the mail was sent without errors. A 'False' result means that WordPress encountered an error sending your mail. Note that it's possible to get a 'True' response with an error elsewhere in your mail system that may cause emails to not be delivered.");
					}
				});
			},
			loadAvgSitePerf: function() {
				var self = this;
				this.ajax('wordfence_loadAvgSitePerf', {limit: jQuery('#wfAvgPerfNum').val()}, function(res) {
					res['scale'] = self.performanceScale;
					res['min'] = self.performanceMinWidth;
					jQuery('#wfAvgSitePerfContent').empty();
					var newElem = jQuery('#wfAvgPerfTmpl').tmpl(res);
					newElem.prependTo('#wfAvgSitePerfContent').fadeIn();
				});
			},
			updateSwitch: function(elemID, configItem, cb) {
				var setting = jQuery('#' + elemID).is(':checked');
				this.updateConfig(configItem, jQuery('#' + elemID).is(':checked') ? 1 : 0, cb);
			},
			setupSwitches: function(elemID, configItem, cb) {
				jQuery('.wfOnOffSwitch-checkbox').change(function() {
					jQuery.data(this, 'lastSwitchChange', (new Date()).getTime());
				});
				var self = this;
				jQuery('div.wfOnOffSwitch').mouseup(function() {
					var elem = jQuery(this);
					setTimeout(function() {
						var checkedElem = elem.find('.wfOnOffSwitch-checkbox');
						if ((new Date()).getTime() - jQuery.data(checkedElem[0], 'lastSwitchChange') > 300) {
							checkedElem.prop('checked', !checkedElem.is(':checked'));
							self.updateSwitch(elemID, configItem, cb);
						}
					}, 50);
				});
			},
			scanTourStart: function() {
				var self = this;
				this.tour('wfWelcomeContent1', 'wfHeading', 'top', 'left', "Continue the Tour", function() {
					self.tour('wfWelcomeContent2', 'wfHeading', 'top', 'left', "Learn how to use Wordfence", function() {
						self.tourRedir('WordfenceScan');
					});
				});
			},
			tourRedir: function(menuItem) {
				window.location.href = 'admin.php?page=' + menuItem;
			},
			updateConfig: function(key, val, cb) {
				this.ajax('wordfence_updateConfig', {key: key, val: val}, function(ret) {
					if (cb) {
						cb(ret);
					}
				});
			},
			updateIPPreview: function(val, cb) {
				this.ajax('wordfence_updateIPPreview', val, function(ret) {
					if (cb) {
						cb(ret);
					}
				});
			},
			tourFinish: function() {
				this.ajax('wordfence_tourClosed', {}, function(res) {
				});
			},
			downgradeLicense: function() {
				this.colorbox((this.isSmallScreen ? '300px' : '400px'), "Confirm Downgrade", "Are you sure you want to downgrade your Wordfence Premium License? This will disable all Premium features and return you to the free version of Wordfence. <a href=\"https://www.wordfence.com/manage-wordfence-api-keys/\" target=\"_blank\" rel=\"noopener noreferrer\">Click here to renew your paid membership</a> or click the button below to confirm you want to downgrade.<br /><br /><input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Downgrade and disable Premium features\" onclick=\"WFAD.downgradeLicenseConfirm();\" /><br />");
			},
			downgradeLicenseConfirm: function() {
				jQuery.colorbox.close();
				this.ajax('wordfence_downgradeLicense', {}, function(res) {
					location.reload(true);
				});
			},
			tour: function(contentID, elemID, edge, align, buttonLabel, buttonCallback) {
				var self = this;
				if (this.currentPointer) {
					this.currentPointer.pointer('destroy');
					this.currentPointer = false;
				}
				var options = {
					buttons: function(event, t) {
						var buttonElem = jQuery('<div id="wfTourButCont"><a id="pointer-close" style="margin-left:5px" class="wf-btn wf-btn-default">End the Tour</a></div><div><a id="wfRateLink" href="http://wordpress.org/extend/plugins/wordfence/" target="_blank" rel="noopener noreferrer" style="font-size: 10px; font-family: Verdana;">Help spread the word by rating us 5&#9733; on WordPress.org</a></div>');
						buttonElem.find('#pointer-close').bind('click.pointer', function(evtObj) {
							var evtSourceElem = evtObj.srcElement ? evtObj.srcElement : evtObj.target;
							if (evtSourceElem.id == 'wfRateLink') {
								return true;
							}
							self.tourFinish();
							t.element.pointer('close');
							return false;
						});
						return buttonElem;
					},
					close: function() {
					},
					content: jQuery('#' + contentID).tmpl().html(),
					pointerWidth: 400,
					position: {
						edge: edge,
						align: align
					}
				};
				this.currentPointer = jQuery('#' + elemID).pointer(options).pointer('open');
				if (buttonLabel && buttonCallback) {
					jQuery('#pointer-close').after('<a id="pointer-primary" class="wf-btn wf-btn-primary">' + buttonLabel + '</a>');
					jQuery('#pointer-primary').click(buttonCallback);
				}

				$('html, body').animate({
					scrollTop: $('.wp-pointer').offset().top - 100
				}, 1000);
			},
			startTourAgain: function() {
				var self = this;
				this.ajax('wordfence_startTourAgain', {}, function(res) {
					self.tourClosed = false;
					self.scanTourStart();
				});
			},
			showLoading: function() {
				this.loadingCount++;
				if (this.loadingCount == 1) {
					jQuery('<div id="wordfenceWorking">Wordfence is working...</div>').appendTo('body');
				}
			},
			removeLoading: function() {
				this.loadingCount--;
				if (this.loadingCount == 0) {
					jQuery('#wordfenceWorking').remove();
				}
			},
			startActivityLogUpdates: function() {
				var self = this;
				setInterval(function() {
					self.updateActivityLog();
				}, parseInt(WordfenceAdminVars.actUpdateInterval));
			},
			updateActivityLog: function() {
				if (this.activityLogUpdatePending || (!this.windowHasFocus() && WordfenceAdminVars.allowsPausing == '1')) {
					if (!jQuery('body').hasClass('wordfenceLiveActivityPaused') && !this.activityLogUpdatePending) {
						jQuery('body').addClass('wordfenceLiveActivityPaused');
					}
					return;
				}
				if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
					jQuery('body').removeClass('wordfenceLiveActivityPaused');
				}
				this.activityLogUpdatePending = true;
				var self = this;
				this.ajax('wordfence_activityLogUpdate', {
					lastctime: this.lastALogCtime
				}, function(res) {
					self.doneUpdateActivityLog(res);
				}, function() {
					self.activityLogUpdatePending = false;
				}, true);

			},
			doneUpdateActivityLog: function(res) {
				this.actNextUpdateAt = (new Date()).getTime() + parseInt(WordfenceAdminVars.actUpdateInterval);
				if (res.ok) {
					if (res.items.length > 0) {
						this.activityQueue.push.apply(this.activityQueue, res.items);
						this.lastALogCtime = res.items[res.items.length - 1].ctime;
						this.processActQueue(res.currentScanID);
					}
					if (res.signatureUpdateTime) {
						this.updateSignaturesTimestamp(res.signatureUpdateTime);
					}
					
					if (res.scanFailed) {
						jQuery('#wf-scan-failed-time-ago').text(res.scanFailedTiming);
						jQuery('#wf-scan-failed').show();
					}
					else {
						jQuery('#wf-scan-failed').hide();
					}
				}
				this.activityLogUpdatePending = false;
			},

			updateSignaturesTimestamp: function(signatureUpdateTime) {
				var date = new Date(signatureUpdateTime * 1000);

				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}

				var sigTimestampEl = $('#wf-scan-sigs-last-update');
				var newText = 'Last Updated: ' + dateString;
				if (sigTimestampEl.text() !== newText) {
					sigTimestampEl.text(newText)
						.css({
							'opacity': 0
						})
						.animate({
							'opacity': 1
						}, 500);
				}
			},

			processActQueue: function(currentScanID) {
				if (this.activityQueue.length > 0) {
					this.addActItem(this.activityQueue.shift());
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:first').remove();
						this.totalActAdded--;
					}
					var timeTillNextUpdate = this.actNextUpdateAt - (new Date()).getTime();
					var maxRate = 50 / 1000; //Rate per millisecond
					var bulkTotal = 0;
					while (this.activityQueue.length > 0 && this.activityQueue.length / timeTillNextUpdate > maxRate) {
						var item = this.activityQueue.shift();
						if (item) {
							bulkTotal++;
							this.addActItem(item);
						}
					}
					this.totalActAdded += bulkTotal;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:lt(' + bulkTotal + ')').remove();
						this.totalActAdded -= bulkTotal;
					}
					var minDelay = 100;
					var delay = minDelay;
					if (timeTillNextUpdate < 1) {
						delay = minDelay;
					} else {
						delay = Math.round(timeTillNextUpdate / this.activityQueue.length);
						if (delay < minDelay) {
							delay = minDelay;
						}
					}
					var self = this;
					setTimeout(function() {
						self.processActQueue();
					}, delay);
				}
				jQuery('#consoleActivity').scrollTop(jQuery('#consoleActivity').prop('scrollHeight'));
			},
			processActArray: function(arr) {
				for (var i = 0; i < arr.length; i++) {
					this.addActItem(arr[i]);
				}
			},
			addActItem: function(item) {
				if (!item) {
					return;
				}
				if (!item.msg) {
					return;
				}
				if (item.msg.indexOf('SUM_') == 0) {
					this.processSummaryLine(item);
					jQuery('#consoleSummary').scrollTop(jQuery('#consoleSummary').prop('scrollHeight'));
					jQuery('#wfStartingScan').addClass('wfSummaryOK').html('Done.');
				} else if (this.debugOn || item.level < 4) {

					var html = '<div class="wfActivityLine';
					if (this.debugOn) {
						html += ' wf' + item.type;
					}
					html += '">[' + item.date + ']&nbsp;' + item.msg + '</div>';
					jQuery('#consoleActivity').append(html);
					if (/Scan complete\./i.test(item.msg) || /Scan interrupted\./i.test(item.msg)) {
						this.loadIssues();
					}
				}
			},
			processSummaryLine: function(item) {
				var msg, summaryUpdated;
				if (item.msg.indexOf('SUM_START:') != -1) {
					msg = item.msg.replace('SUM_START:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDBAD') != -1) {
					msg = item.msg.replace('SUM_ENDBAD:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Problems found.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDFAILED') != -1) {
					msg = item.msg.replace('SUM_ENDFAILED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Failed.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDOK') != -1) {
					msg = item.msg.replace('SUM_ENDOK:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Secure.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDSUCCESS') != -1) {
					msg = item.msg.replace('SUM_ENDSUCCESS:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Success.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDERR') != -1) {
					msg = item.msg.replace('SUM_ENDERR:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryErr').html('An error occurred.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDSKIPPED') != -1) {
					msg = item.msg.replace('SUM_ENDSKIPPED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryResult').html('Skipped.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDIGNORED') != -1) {
					msg = item.msg.replace('SUM_ENDIGNORED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryIgnored').html('Ignored.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_DISABLED:') != -1) {
					msg = item.msg.replace('SUM_DISABLED:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult">Disabled [<a href="admin.php?page=WordfenceSecOpt">Visit Options to Enable</a>]</div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_PAIDONLY:') != -1) {
					msg = item.msg.replace('SUM_PAIDONLY:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><a href="https://www.wordfence.com/wordfence-signup/" target="_blank"  rel="noopener noreferrer">Paid Members Only</a></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_FINAL:') != -1) {
					msg = item.msg.replace('SUM_FINAL:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg wfSummaryFinal">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_PREP:') != -1) {
					msg = item.msg.replace('SUM_PREP:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult" id="wfStartingScan"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_KILLED:') != -1) {
					msg = item.msg.replace('SUM_KILLED:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				}
			},
			processActQueueItem: function() {
				var item = this.activityQueue.shift();
				if (item) {
					jQuery('#consoleActivity').append('<div class="wfActivityLine wf' + item.type + '">[' + item.date + ']&nbsp;' + item.msg + '</div>');
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:first').remove();
						this.totalActAdded--;
					}
					if (item.msg == 'Scan complete.') {
						this.loadIssues();
					}
				}
			},
			updateTicker: function(forceUpdate) {
				if ((!forceUpdate) && (this.tickerUpdatePending || (!this.windowHasFocus() && WordfenceAdminVars.allowsPausing == '1'))) {
					if (!jQuery('body').hasClass('wordfenceLiveActivityPaused') && !this.tickerUpdatePending) {
						jQuery('body').addClass('wordfenceLiveActivityPaused');
					}
					return;
				}
				if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
					jQuery('body').removeClass('wordfenceLiveActivityPaused');
				}
				this.tickerUpdatePending = true;
				var self = this;
				var alsoGet = '';
				var otherParams = '';
				var data = '';
				if (this.mode == 'liveTraffic') {
					alsoGet = 'liveTraffic';
					otherParams = this.newestActivityTime;
					data += this.wfLiveTraffic.getCurrentQueryString({
						since: this.newestActivityTime
					});

				} else if (this.mode == 'activity' && /^(?:404|hit|human|ruser|gCrawler|crawler|loginLogout)$/.test(this.activityMode)) {
					alsoGet = 'logList_' + this.activityMode;
					otherParams = this.newestActivityTime;
				} else if (this.mode == 'perfStats') {
					alsoGet = 'perfStats';
					otherParams = this.newestActivityTime;
				}
				data += '&alsoGet=' + encodeURIComponent(alsoGet) + '&otherParams=' + encodeURIComponent(otherParams);
				this.ajax('wordfence_ticker', data, function(res) {
					self.handleTickerReturn(res);
				}, function() {
					self.tickerUpdatePending = false;
				}, true);
			},
			handleTickerReturn: function(res) {
				this.tickerUpdatePending = false;
				var newMsg = "";
				var oldMsg = jQuery('.wf-live-activity-message').text();
				if (res.msg) {
					newMsg = res.msg;
				} else {
					newMsg = "Idle";
				}
				if (newMsg && newMsg != oldMsg) {
					jQuery('.wf-live-activity-message').hide().html(newMsg).fadeIn(200);
				}
				var haveEvents, newElem;
				this.serverTimestampOffset = (new Date().getTime() / 1000) - res.serverTime;
				this.serverMicrotime = res.serverMicrotime;

				if (this.mode == 'liveTraffic') {
					if (res.events.length > 0) {
						this.newestActivityTime = res.events[0]['ctime'];
					}
					if (typeof WFAD.wfLiveTraffic !== undefined) {
						WFAD.wfLiveTraffic.prependListings(res.events, res);
						this.reverseLookupIPs();
						this.updateTimeAgo();
					}

				} else if (this.mode == 'activity') { // This mode is deprecated as of 6.1.0
					if (res.alsoGet != 'logList_' + this.activityMode) {
						return;
					} //user switched panels since ajax request started
					if (res.events.length > 0) {
						this.newestActivityTime = res.events[0]['ctime'];
					}
					haveEvents = false;
					if (jQuery('#wfActivity_' + this.activityMode + ' .wfActEvent').length > 0) {
						haveEvents = true;
					}
					if (res.events.length > 0) {
						if (!haveEvents) {
							jQuery('#wfActivity_' + this.activityMode).empty();
						}
						for (i = res.events.length - 1; i >= 0; i--) {
							var elemID = '#wfActEvent_' + res.events[i].id;
							if (jQuery(elemID).length < 1) {
								res.events[i]['activityMode'] = this.activityMode;
								if (this.activityMode == 'loginLogout') {
									newElem = jQuery('#wfLoginLogoutEventTmpl').tmpl(res.events[i]);
								} else {
									newElem = jQuery('#wfHitsEventTmpl').tmpl(res.events[i]);
								}
								jQuery(newElem).find('.wfTimeAgo').data('wfctime', res.events[i].ctime);
								newElem.prependTo('#wfActivity_' + this.activityMode).fadeIn();
							}
						}
						this.reverseLookupIPs();
					} else {
						if (!haveEvents) {
							jQuery('#wfActivity_' + this.activityMode).html('<div>No events to report yet.</div>');
						}
					}
					var self = this;
					this.updateTimeAgo();
				} else if (this.mode == 'perfStats') {
					haveEvents = false;
					if (jQuery('#wfPerfStats .wfPerfEvent').length > 0) {
						haveEvents = true;
					}
					if (res.events.length > 0) {
						if (!haveEvents) {
							jQuery('#wfPerfStats').empty();
						}
						var curLength = parseInt(jQuery('#wfPerfStats').css('width'));
						if (res.longestLine > curLength) {
							jQuery('#wfPerfStats').css('width', (res.longestLine + 200) + 'px');
						}
						this.newestActivityTime = res.events[0]['ctime'];
						for (var i = res.events.length - 1; i >= 0; i--) {
							res.events[i]['scale'] = this.performanceScale;
							res.events[i]['min'] = this.performanceMinWidth;
							newElem = jQuery('#wfPerfStatTmpl').tmpl(res.events[i]);
							jQuery(newElem).find('.wfTimeAgo').data('wfctime', res.events[i].ctime);
							newElem.prependTo('#wfPerfStats').fadeIn();
						}
					} else {
						if (!haveEvents) {
							jQuery('#wfPerfStats').html('<p>No events to report yet.</p>');
						}
					}
					this.updateTimeAgo();
				}
			},
			reverseLookupIPs: function() {
				var self = this;
				var ips = [];
				jQuery('.wfReverseLookup').each(function(idx, elem) {
					var txt = jQuery(elem).text().trim();
					if (/^\d+\.\d+\.\d+\.\d+$/.test(txt) && (!jQuery(elem).data('wfReverseDone'))) {
						jQuery(elem).data('wfReverseDone', true);
						ips.push(txt);
					}
				});
				if (ips.length < 1) {
					return;
				}
				var uni = {};
				var uniqueIPs = [];
				for (var i = 0; i < ips.length; i++) {
					if (!uni[ips[i]]) {
						uni[ips[i]] = true;
						uniqueIPs.push(ips[i]);
					}
				}
				this.ajax('wordfence_reverseLookup', {
						ips: uniqueIPs.join(',')
					},
					function(res) {
						if (res.ok) {
							jQuery('.wfReverseLookup').each(function(idx, elem) {
								var txt = jQuery(elem).text().trim();
								for (var ip in res.ips) {
									if (txt == ip) {
										if (res.ips[ip]) {
											jQuery(elem).html('<strong>Hostname:</strong>&nbsp;' + self.htmlEscape(res.ips[ip]));
										} else {
											jQuery(elem).html('');
										}
									}
								}
							});
						}
					}, false, false);
			},
			killScan: function() {
				var self = this;
				this.ajax('wordfence_killScan', {}, function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Kill requested", "A termination request has been sent to any running scans.");
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Kill failed", "We failed to send a termination request.");
					}
				});
			},
			startScan: function() {
				var spinnerValues = [
					'|', '/', '-', '\\'
				];
				var count = 0;
				var scanReqAnimation = setInterval(function() {
					var ch = spinnerValues[count++ % spinnerValues.length];
					jQuery('#wfStartScanButton1,#wfStartScanButton2').html("Requesting a New Scan <span class='wf-spinner'>" + ch + "</span>");
				}, 100);
				setTimeout(function(res) {
					clearInterval(scanReqAnimation);
					jQuery('#wfStartScanButton1,#wfStartScanButton2').text("Start a Wordfence Scan");
				}, 3000);
				this.ajax('wordfence_scan', {}, function(res) {

				});
			},
			displayPWAuditJobs: function(res) {
				if (res && res.results && res.results.length > 0) {
					var wfAuditJobs = $('#wfAuditJobs');
					jQuery('#wfAuditJobs').empty();
					jQuery('#wfAuditJobsTable').tmpl().appendTo(wfAuditJobs);
					var wfAuditJobsBody = wfAuditJobs.find('.wf-pw-audit-tbody');
					for (var i = 0; i < res.results.length; i++) {
						jQuery('#wfAuditJobsInProg').tmpl(res.results[i]).appendTo(wfAuditJobsBody);
					}
				} else {
					jQuery('#wfAuditJobs').empty().html("<p>You don't have any password auditing jobs in progress or completed yet.</p>");
				}
			},
			loadIssues: function(callback, offset, limit) {
				if (this.mode != 'scan') {
					return;
				}
				offset = offset || 0;
				limit = limit || WordfenceAdminVars.scanIssuesPerPage;
				var self = this;
				this.ajax('wordfence_loadIssues', {offset: offset, limit: limit}, function(res) {
					var newCount = parseInt(res.issueCounts.new) || 0;
					var ignoredCount = (parseInt(res.issueCounts.ignoreP) || 0) + (parseInt(res.issueCounts.ignoreC) || 0);
					jQuery('#wfNewIssuesTab .wfIssuesCount').text(' (' + newCount + ')');
					jQuery('#wfIgnoredIssuesTab .wfIssuesCount').text(' (' + ignoredCount + ')'); 
					self.displayIssues(res, callback);
				});
			},
			loadMoreIssues: function(callback, offset, limit) {
				offset = offset || 0;
				limit = limit || WordfenceAdminVars.scanIssuesPerPage;
				var self = this;
				this.ajax('wordfence_loadIssues', {offset: offset, limit: limit}, function(res) {
					self.appendIssues(res.issuesLists, callback);
				});
			},
			sev2num: function(str) {
				if (/wfProbSev1/.test(str)) {
					return 1;
				} else if (/wfProbSev2/.test(str)) {
					return 2;
				} else {
					return 0;
				}
			},
			displayIssues: function(res, callback) {
				var self = this;
				try {
					res.summary['lastScanCompleted'] = res['lastScanCompleted'];
				} catch (err) {
					res.summary['lastScanCompleted'] = 'Never';
				}
				jQuery('.wfIssuesContainer').hide();
				for (var issueStatus in res.issuesLists) {
					var containerID = 'wfIssues_dataTable_' + issueStatus;
					var tableID = 'wfIssuesTable_' + issueStatus;
					if (jQuery('#' + containerID).length < 1) {
						//Invalid issue status
						continue;
					}
					if (res.issuesLists[issueStatus].length < 1) {
						if (issueStatus == 'new') {
							if (res.lastScanCompleted == 'ok') {
								jQuery('#' + containerID).html('<p class="wf-scan-no-issues">Congratulations! No security problems were detected by Wordfence.</p>');
							} else if (res['lastScanCompleted']) {
								//jQuery('#' + containerID).html('<p style="font-size: 12px; color: #A00;">The latest scan failed: ' + res.lastScanCompleted + '</p>');
							} else {
								jQuery('#' + containerID).html();
							}

						} else {
							jQuery('#' + containerID).html('<p>There are currently <strong>no issues</strong> being ignored on this site.</p>');
						}
						continue;
					}
					jQuery('#' + containerID).html('<table cellpadding="0" cellspacing="0" border="0" class="display wf-issues-table" id="' + tableID + '"></table>');

					jQuery.fn.wfDataTableExt.oSort['severity-asc'] = function(y, x) {
						x = WFAD.sev2num(x);
						y = WFAD.sev2num(y);
						if (x < y) {
							return 1;
						}
						if (x > y) {
							return -1;
						}
						return 0;
					};
					jQuery.fn.wfDataTableExt.oSort['severity-desc'] = function(y, x) {
						x = WFAD.sev2num(x);
						y = WFAD.sev2num(y);
						if (x > y) {
							return 1;
						}
						if (x < y) {
							return -1;
						}
						return 0;
					};

					jQuery('#' + tableID).WFDataTable({ 
						"searching": false,
						"info": false,
						"paging": false,
						"lengthChange": false,
						"autoWidth": false,
						"columnDefs": [
							{
								"targets": 0,
								"title": '<div class="th_wrapp wf-hidden-xs">Severity</div>',
								"className": "center wf-scan-severity",
								"type": 'severity',
								"render": function(data, type, row) {
									var cls = 'wfProbSev' + row.severity;
									return '<span class="wf-hidden-xs ' + cls + '"></span><div class="wf-visible-xs wf-scan-severity-' + row.severity + '"></div>';
								}
							},
							{
								"targets": 1,
								"title": '<div class="th_wrapp">Issue</div>',
								"orderable": false,
								"type": 'html',
								"render": function(data, type, row) {
									var issueType = (row.type == 'knownfile' ? 'file' : row.type);
									var tmplName = 'issueTmpl_' + issueType;
									return jQuery('#' + tmplName).tmpl(row).html();
								}
							}
						]
					});
				}
				
				this.appendIssues(res.issuesLists, callback);
				
				return true;
			},
			appendIssues: function(issuesLists, callback) {
				for (var issueStatus in issuesLists) {
					var tableID = 'wfIssuesTable_' + issueStatus;
					if (jQuery('#' + tableID).length < 1) {
						//Invalid issue status
						continue;
					}

					var table = jQuery('#' + tableID).WFDataTable();
					table.rows.add(issuesLists[issueStatus]).draw();
				}

				if (callback) {
					jQuery('#wfIssues_' + this.visibleIssuesPanel).fadeIn(500, function() {
						callback();
					});
				} else {
					jQuery('#wfIssues_' + this.visibleIssuesPanel).fadeIn(500);
				}
			},
			ajax: function(action, data, cb, cbErr, noLoading) {
				if (typeof(data) == 'string') {
					if (data.length > 0) {
						data += '&';
					}
					data += 'action=' + action + '&nonce=' + this.nonce;
				} else if (typeof(data) == 'object' && data instanceof Array) {
					// jQuery serialized form data
					data.push({
						name: 'action',
						value: action
					});
					data.push({
						name: 'nonce',
						value: this.nonce
					});
				} else if (typeof(data) == 'object') {
					data['action'] = action;
					data['nonce'] = this.nonce;
				}
				if (!cbErr) {
					cbErr = function() {
					};
				}
				var self = this;
				if (!noLoading) {
					this.showLoading();
				}
				jQuery.ajax({
					type: 'POST',
					url: WordfenceAdminVars.ajaxURL,
					dataType: "json",
					data: data,
					success: function(json) {
						if (!noLoading) {
							self.removeLoading();
						}
						if (json && json.nonce) {
							self.nonce = json.nonce;
						}
						if (json && json.errorMsg) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', json.errorMsg);
						}
						cb(json);
					},
					error: function() {
						if (!noLoading) {
							self.removeLoading();
						}
						cbErr();
					}
				});
			},
			colorbox: function(width, heading, body, settings) {
				if (typeof settings === 'undefined') {
					settings = {};
				}
				this.colorboxQueue.push([width, heading, body, settings]);
				this.colorboxServiceQueue();
			},
			colorboxServiceQueue: function() {
				if (this.colorboxIsOpen) {
					return;
				}
				if (this.colorboxQueue.length < 1) {
					return;
				}
				var elem = this.colorboxQueue.shift();
				this.colorboxOpen(elem[0], elem[1], elem[2], elem[3]);
			},
			colorboxOpen: function(width, heading, body, settings) {
				var self = this;
				this.colorboxIsOpen = true;
				jQuery.extend(settings, {
					width: width,
					html: "<h3>" + heading + "</h3><p>" + body + "</p>",
					onClosed: function() {
						self.colorboxClose();
					}
				});
				jQuery.colorbox(settings);
			},
			colorboxClose: function() {
				this.colorboxIsOpen = false;
				jQuery.colorbox.close();
			},
			scanRunningMsg: function() {
				this.colorbox((this.isSmallScreen ? '300px' : '400px'), "A scan is running", "A scan is currently in progress. Please wait until it finishes before starting another scan.");
			},
			errorMsg: function(msg) {
				this.colorbox((this.isSmallScreen ? '300px' : '400px'), "An error occurred:", msg);
			},
			bulkOperation: function(op) {
				var self = this;
				if (op == 'del' || op == 'repair') {
					var ids = jQuery('input.wf' + op + 'Checkbox:checked').map(function() {
						return jQuery(this).val();
					}).get();
					if (ids.length < 1) {
						this.colorbox((self.isSmallScreen ? '300px' : '400px'), "No files were selected", "You need to select files to perform a bulk operation. There is a checkbox in each issue that lets you select that file. You can then select a bulk operation and hit the button to perform that bulk operation.");
						return;
					}
					if (op == 'del') {
						this.colorbox((self.isSmallScreen ? '300px' : '400px'), "Are you sure you want to delete?", "Are you sure you want to delete a total of " + ids.length + " files? Do not delete files on your system unless you're ABSOLUTELY sure you know what you're doing. If you delete the wrong file it could cause your WordPress website to stop functioning and you will probably have to restore from backups. If you're unsure, Cancel and work with your hosting provider to clean your system of infected files.<br /><br /><input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Delete Files\" onclick=\"WFAD.bulkOperationConfirmed('" + op + "');\" />&nbsp;&nbsp;<input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Cancel\" onclick=\"jQuery.colorbox.close();\" /><br />");
					} else if (op == 'repair') {
						this.colorbox((self.isSmallScreen ? '300px' : '400px'), "Are you sure you want to repair?", "Are you sure you want to repair a total of " + ids.length + " files? Do not repair files on your system unless you're sure you have reviewed the differences between the original file and your version of the file in the files you are repairing. If you repair a file that has been customized for your system by a developer or your hosting provider it may leave your system unusable. If you're unsure, Cancel and work with your hosting provider to clean your system of infected files.<br /><br /><input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Repair Files\" onclick=\"WFAD.bulkOperationConfirmed('" + op + "');\" />&nbsp;&nbsp;<input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Cancel\" onclick=\"jQuery.colorbox.close();\" /><br />");
					}
				} else {
					return;
				}
			},
			bulkOperationConfirmed: function(op) {
				jQuery.colorbox.close();
				var self = this;
				this.ajax('wordfence_bulkOperation', {
					op: op,
					ids: jQuery('input.wf' + op + 'Checkbox:checked').map(function() {
						return jQuery(this).val();
					}).get()
				}, function(res) {
					self.doneBulkOperation(res);
				});
			},
			doneBulkOperation: function(res) {
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), res.bulkHeading, res.bulkBody);
					});
				} else {
					this.loadIssues(function() {
					});
				}
			},
			deleteFile: function(issueID, force) {
				var self = this;
				this.ajax('wordfence_deleteFile', {
					issueID: issueID,
					forceDelete: force
				}, function(res) {
					if (res.needsCredentials) {
						document.location.href = res.redirect;
					} else {
						self.doneDeleteFile(res);
					}
				});
			},
			doneDeleteFile: function(res) {
				var cb = false;
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Success deleting file", "The file " + res.file + " was successfully deleted.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.cerrorMsg);
					});
				}
			},
			deleteDatabaseOption: function(issueID) {
				var self = this;
				this.ajax('wordfence_deleteDatabaseOption', {
					issueID: issueID
				}, function(res) {
					self.doneDeleteDatabaseOption(res);
				});
			},
			doneDeleteDatabaseOption: function(res) {
				var cb = false;
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Success removing option", "The option " + res.option_name + " was successfully removed.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.cerrorMsg);
					});
				}
			},
			useRecommendedHowGetIPs: function(issueID) {
				var self = this;
				this.ajax('wordfence_misconfiguredHowGetIPsChoice', {
					issueID: issueID,
					choice: 'yes'
				}, function(res) {
					if (res.ok) {
						jQuery('#wordfenceMisconfiguredHowGetIPsNotice').fadeOut();
						
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Success updating option", "The 'How does Wordfence get IPs' option was successfully updated to the recommended value.");
						});
					} else if (res.cerrorMsg) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.cerrorMsg);
						}); 
					}
				});	
			},
			fixFPD: function(issueID) {
				var self = this;
				var title = "Full Path Disclosure";
				issueID = parseInt(issueID);

				this.ajax('wordfence_checkHtaccess', {}, function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wfFPDNextBut\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a><br /><br /><input type="button" class="wf-btn wf-btn-default" name="but1" id="wfFPDNextBut" value="Click to fix .htaccess" disabled="disabled" onclick="WFAD.fixFPD_WriteHtAccess(' + issueID + ');" />');
					} else if (res.nginx) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'You are using an Nginx web server and using a FastCGI processor like PHP5-FPM. You will need to manually modify your php.ini to disable <em>display_error</em>');
					} else if (res.err) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				});
			},
			fixFPD_WriteHtAccess: function(issueID) {
				var self = this;
				self.colorboxClose();
				this.ajax('wordfence_fixFPD', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "File restored OK", "The Full Path disclosure issue has been fixed");
						});
					} else {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.cerrorMsg);
						});
					}
				});
			},

			_handleHtAccess: function(issueID, callback, title, nginx) {
				var self = this;
				return function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a id="dlButton" href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '">Click here to download a backup copy of your .htaccess file now</a>'
							+ '<br /><br /><input type="button" class="wf-btn wf-btn-default" name="but1" id="wfFPDNextBut" value="Click to fix .htaccess" disabled="disabled" />'
						);
						jQuery('#dlButton').click('click', function() {
							jQuery('#wfFPDNextBut').prop('disabled', false);
						});
						jQuery('#wfFPDNextBut').one('click', function() {
							self[callback](issueID);
						});
					} else if (res.nginx) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'You are using an Nginx web server and using a FastCGI processor like PHP5-FPM. ' + nginx);
					} else if (res.err) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				};
			},
			_hideFile: function(issueID) {
				var self = this;
				var title = 'Modifying .htaccess';
				this.ajax('wordfence_hideFileHtaccess', {
					issueID: issueID
				}, function(res) {
					jQuery.colorbox.close();
					self.loadIssues(function() {
						if (res.ok) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'Your .htaccess file has been updated successfully.');
						} else {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'We encountered a problem while trying to update your .htaccess file.');
						}
					});
				});
			},
			hideFile: function(issueID) {
				var self = this;
				var title = "Backup your .htaccess file";
				var nginx = "You will need to manually delete those files";
				issueID = parseInt(issueID, 10);

				this.ajax('wordfence_checkHtaccess', {}, this._handleHtAccess(issueID, '_hideFile', title, nginx));
			},

			restoreFile: function(issueID) {
				var self = this;
				this.ajax('wordfence_restoreFile', {
					issueID: issueID
				}, function(res) {
					if (res.needsCredentials) {
						document.location.href = res.redirect;
					} else {
						self.doneRestoreFile(res);
					}
				});
			},
			doneRestoreFile: function(res) {
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "File restored OK", "The file " + res.file + " was restored successfully.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.cerrorMsg);
					});
				}
			},

			disableDirectoryListing: function(issueID) {
				var self = this;
				var title = "Disable Directory Listing";
				issueID = parseInt(issueID);

				this.ajax('wordfence_checkHtaccess', {}, function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), title, 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wf-htaccess-confirm\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a>' +
							'<br /><br />' +
							'<button class="wf-btn wf-btn-default" type="button" id="wf-htaccess-confirm" disabled="disabled" onclick="WFAD.confirmDisableDirectoryListing(' + issueID + ');">Add code to .htaccess</button>');
					} else if (res.nginx) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "You are using Nginx as your web server. " +
							"You'll need to disable autoindexing in your nginx.conf. " +
							"See the <a target='_blank'  rel='noopener noreferrer' href='http://nginx.org/en/docs/http/ngx_http_autoindex_module.html'>Nginx docs for more info</a> on how to do this.");
					} else if (res.err) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				});
			},
			confirmDisableDirectoryListing: function(issueID) {
				var self = this;
				this.colorboxClose();
				this.ajax('wordfence_disableDirectoryListing', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Directory Listing Disabled", "Directory listing has been disabled on your server.");
						});
					} else {
						//self.loadIssues(function() {
						//	self.colorbox('400px', 'An error occurred', res.errorMsg);
						//});
					}
				});
			},

			deleteIssue: function(id) {
				var self = this;
				this.ajax('wordfence_deleteIssue', {id: id}, function(res) {
					self.loadIssues();
				});
			},
			updateIssueStatus: function(id, st) {
				var self = this;
				this.ajax('wordfence_updateIssueStatus', {id: id, 'status': st}, function(res) {
					if (res.ok) {
						self.loadIssues();
					}
				});
			},
			updateAllIssues: function(op) { // deleteIgnored, deleteNew, ignoreAllNew
				var head = "Please confirm";
				var body;
				if (op == 'deleteIgnored') {
					body = "You have chosen to remove all ignored issues. Once these issues are removed they will be re-scanned by Wordfence and if they have not been fixed, they will appear in the 'new issues' list. Are you sure you want to do this?";
				} else if (op == 'deleteNew') {
					body = "You have chosen to mark all new issues as fixed. If you have not really fixed these issues, they will reappear in the new issues list on the next scan. If you have not fixed them and want them excluded from scans you should choose to 'ignore' them instead. Are you sure you want to mark all new issues as fixed?";
				} else if (op == 'ignoreAllNew') {
					body = "You have chosen to ignore all new issues. That means they will be excluded from future scans. You should only do this if you're sure all new issues are not a problem. Are you sure you want to ignore all new issues?";
				} else {
					return;
				}
				this.colorbox((this.isSmallScreen ? '300px' : '450px'), head, body + '<br /><br /><center><input class="wf-btn wf-btn-default" type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />&nbsp;&nbsp;&nbsp;<input class="wf-btn wf-btn-default" type="button" name="but2" value="Yes I\'m sure" onclick="jQuery.colorbox.close(); WFAD.confirmUpdateAllIssues(\'' + op + '\');" /><br />');
			},
			confirmUpdateAllIssues: function(op) {
				var self = this;
				this.ajax('wordfence_updateAllIssues', {op: op}, function(res) {
					self.loadIssues();
				});
			},
			es: function(val) {
				if (val) {
					return val;
				} else {
					return "";
				}
			},
			noQuotes: function(str) {
				return str.replace(/"/g, '&#34;').replace(/\'/g, '&#145;');
			},
			commify: function(num) {
				return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
			},
			switchToLiveTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				var self = this;
				jQuery('#wfActivity').fadeIn(function() {
					self.completeLiveTabSwitch();
				});
			},
			completeLiveTabSwitch: function() {
				this.ajax('wordfence_loadActivityLog', {}, function(res) {
					var html = '<a href="#" class="wfALogMailLink" onclick="WFAD.emailActivityLog(); return false;"></a><a href="#" class="wfALogReloadLink" onclick="WFAD.reloadActivityData(); return false;"></a>';
					if (res.events && res.events.length > 0) {
						jQuery('#wfActivity').empty();
						for (var i = 0; i < res.events.length; i++) {
							var timeTaken = '0.0000';
							if (res.events[i + 1]) {
								timeTaken = (res.events[i].ctime - res.events[i + 1].ctime).toFixed(4);
							}
							var red = "";
							if (res.events[i].type == 'error') {
								red = ' class="wfWarn" ';
							}
							html += '<div ' + red + 'class="wfALogEntry"><span ' + red + 'class="wfALogTime">[' + res.events[i].type + '&nbsp;:&nbsp;' + timeTaken + '&nbsp;:&nbsp;' + res.events[i].timeAgo + ' ago]</span>&nbsp;' + res.events[i].msg + "</div>";
						}
						jQuery('#wfActivity').html(html);
					} else {
						jQuery('#wfActivity').html("<p>&nbsp;&nbsp;No activity to report yet. Please complete your first scan.</p>");
					}
				});
			},
			emailActivityLog: function() {
				this.colorbox((this.isSmallScreen ? '300px' : '400px'), 'Email Wordfence Activity Log', "Enter the email address you would like to send the Wordfence activity log to. Note that the activity log may contain thousands of lines of data. This log is usually only sent to a member of the Wordfence support team. It also contains your PHP configuration from the phpinfo() function for diagnostic data.<br /><br /><input type='text' value='wftest@wordfence.com' size='20' id='wfALogRecip' /><input class='wf-btn wf-btn-default' type='button' value='Send' onclick=\"WFAD.completeEmailActivityLog();\" /><input class='wf-btn wf-btn-default' type='button' value='Cancel' onclick='jQuery.colorbox.close();' /><br /><br />");
			},
			completeEmailActivityLog: function() {
				jQuery.colorbox.close();
				var email = jQuery('#wfALogRecip').val();
				if (!/^[^@]+@[^@]+$/.test(email)) {
					alert("Please enter a valid email address.");
					return;
				}
				var self = this;
				this.ajax('wordfence_sendActivityLog', {email: jQuery('#wfALogRecip').val()}, function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Activity Log Sent', "Your Wordfence activity log was sent to " + email + "<br /><br /><input class='wf-btn wf-btn-default' type='button' value='Close' onclick='jQuery.colorbox.close();' /><br /><br />");
					}
				});
			},
			reloadActivityData: function() {
				jQuery('#wfActivity').html('<div class="wfLoadingWhite32"></div>'); //&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
				this.completeLiveTabSwitch();
			},
			switchToSummaryTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				jQuery('#wfSummaryTables').fadeIn();
			},
			switchIssuesTab: function(elem, type) {
				jQuery('.wfTab2').removeClass('selected');
				jQuery('.wfIssuesContainer').hide();
				jQuery(elem).addClass('selected');
				this.visibleIssuesPanel = type;
				jQuery('#wfIssues_' + type).fadeIn();
			},
			switchTab: function(tabElement, tabClass, contentClass, selectedContentID, callback) {
				jQuery('.' + tabClass).removeClass('selected');
				jQuery(tabElement).addClass('selected');
				jQuery('.' + contentClass).hide().html('<div class="wfLoadingWhite32"></div>');
				var func = function() {
				};
				if (callback) {
					func = function() {
						callback();
					};
				}
				jQuery('#' + selectedContentID).fadeIn(func);
			},
			activityTabChanged: function() {
				var mode = jQuery('.wfDataPanel:visible')[0].id.replace('wfActivity_', '');
				if (!mode) {
					return;
				}
				this.activityMode = mode;
				this.reloadActivities();
			},
			reloadActivities: function() {
				jQuery('#wfActivity_' + this.activityMode).html('<div class="wfLoadingWhite32"></div>');
				this.newestActivityTime = 0;
				this.updateTicker(true);
			},
			staticTabChanged: function() {
				var mode = jQuery('.wfDataPanel:visible')[0].id.replace('wfActivity_', '');
				if (!mode) {
					return;
				}
				this.activityMode = mode;

				this.loadStaticPanelContent(false);
			},
			loadStaticPanelContent: function(append) {
				append = !!append;
				var self = this;
				var offset = append ? $('tr.' + self.activityMode + 'Record').length : 0;
				self.loadingBlockedIPs = true;
				$('.wfLoadMoreButton').attr("disabled", "disabled");
				self.ajax('wordfence_loadStaticPanel', {
					mode: self.activityMode,
					offset: offset
				}, function(res) {
					self.completeLoadStaticPanel(res);
					self.loadingBlockedIPs = false;
				});
			},
			completeLoadStaticPanel: function(res) {
				var contentElem = '#wfActivity_' + this.activityMode;
				if (!res.continuation) {
					jQuery(contentElem).empty();
				}
				
				if (res.hasMore) {
					$('.wfLoadMoreButton').removeAttr("disabled");
				}
				
				if ((res.results && res.results.length > 0) || res.continuation) {
					if (!(res.results && res.results.length > 0)) {
						return;
					}
					
					var tmpl;
					var wrapperTmpl;
					var wrapperID;
					if (this.activityMode == 'topScanners' || this.activityMode == 'topLeechers') {
						tmpl = '#wfLeechersTmpl';
						wrapperTmpl = '#wfLeechersWrapperTmpl';
						wrapperID = '#wfLeechersWrapper';
					} else if (this.activityMode == 'blockedIPs') {
						tmpl = '#wfBlockedIPsTmpl';
						wrapperTmpl = '#wfBlockedIPsWrapperTmpl';
						wrapperID = '#wfBlockedIPsWrapper';
					} else if (this.activityMode == 'lockedOutIPs') {
						tmpl = '#wfLockedOutIPsTmpl';
						wrapperTmpl = '#wfLockedOutIPsWrapperTmpl';
						wrapperID = '#wfLockedOutIPsWrapper';
					} else if (this.activityMode == 'throttledIPs') {
						tmpl = '#wfThrottledIPsTmpl';
						wrapperTmpl = '#wfThrottledIPsWrapperTmpl';
						wrapperID = '#wfThrottledIPsWrapper';
					} else {
						return;
					}

					if (!res.continuation) {
						jQuery(wrapperTmpl).tmpl(res).appendTo(contentElem);
						
						var self = this;
						$('.wfLoadMoreButton').on('click', function(event) {
							event.stopPropagation();
							event.preventDefault();
							self.loadStaticPanelContent(true);
						});
					}

					jQuery(tmpl).tmpl(res).appendTo(jQuery(wrapperID));
					this.reverseLookupIPs();
				}
				else {
					$('.wfLoadMoreButton').hide();
					if (this.activityMode == 'topScanners' || this.activityMode == 'topLeechers') {
						jQuery(contentElem).html("No site hits have been logged yet. Check back soon.");
					} else if (this.activityMode == 'blockedIPs') {
						jQuery(contentElem).html("No IP addresses have been blocked yet. If you manually block an IP address or if Wordfence automatically blocks one, it will appear here.");
					} else if (this.activityMode == 'lockedOutIPs') {
						jQuery(contentElem).html("No IP addresses have been locked out from signing in or using the password recovery system.");
					} else if (this.activityMode == 'throttledIPs') {
						jQuery(contentElem).html("No IP addresses have been throttled yet. If an IP address accesses the site too quickly and breaks one of the Wordfence rules, it will appear here.");
					} else {
						return;
					}
				}
			},
			loadPasswdAuditResults: function() {
				var self = this;
				this.ajax('wordfence_passwdLoadResults', {}, function(res) {
					self.displayPWAuditResults(res);
				});
			},
			doPasswdAuditUpdate: function(freq) {
				this.loadPasswdAuditJobs();
				this.loadPasswdAuditResults();
			},
			stopPasswdAuditUpdate: function() {
				clearInterval(this.passwdAuditUpdateInt);
			},
			killPasswdAudit: function(jobID) {
				var self = this;
				this.ajax('wordfence_killPasswdAudit', {jobID: jobID}, function(res) {
					if (res.ok) {
						self.colorbox('300px', "Stop Requested", "We have sent a request to stop the password audit in progress. It may take a few minutes before results stop appearing. You can immediately start another audit if you'd like.");
					}
				});
			},
			displayPWAuditResults: function(res) {
				if (res && res.results && res.results.length > 0) {
					var wfAuditResults = $('#wfAuditResults');
					jQuery('#wfAuditResults').empty();
					jQuery('#wfAuditResultsTable').tmpl().appendTo(wfAuditResults);
					var wfAuditResultsBody = wfAuditResults.find('.wf-pw-audit-tbody');
					for (var i = 0; i < res.results.length; i++) {
						jQuery('#wfAuditResultsRow').tmpl(res.results[i]).appendTo(wfAuditResultsBody);
					}
				} else {
					jQuery('#wfAuditResults').empty().html("<p>You don't have any user accounts with a weak password at this time.</p>");
				}
			},
			loadPasswdAuditJobs: function() {
				var self = this;
				this.ajax('wordfence_passwdLoadJobs', {}, function(res) {
					if (res && res.results && res.results.length > 0) {
						var stat = res.results[0].jobStatus;
						if (stat == 'running' || stat == 'queued') {
							setTimeout(function() {
								self.doPasswdAuditUpdate()
							}, 10000);
						}
					}

					self.displayPWAuditJobs(res);
				});
			},
			deletePasswdAudit: function(jobID) {
				var self = this;
				this.ajax('wordfence_deletePasswdAudit', {jobID: jobID}, function(res) {
					self.loadPasswdAuditJobs(res);
				});
			},
			doFixWeakPasswords: function() {
				var self = this;
				var mode = jQuery('#wfPasswdFixAction').val();
				var ids = jQuery('input.wfUserCheck:checked').map(function() {
					return jQuery(this).val();
				}).get();
				if (ids.length < 1) {
					self.colorbox('300px', "Please select users", "You did not select any users from the list. Select which site members you want to email or to change their passwords.");
					return;
				}
				this.ajax('wordfence_weakPasswordsFix', {
					mode: mode,
					ids: ids.join(',')
				}, function(res) {
					if (res.ok && res.title && res.msg) {
						self.colorbox('300px', res.title, res.msg);
					}
				});
			},
			ucfirst: function(str) {
				str = "" + str;
				return str.charAt(0).toUpperCase() + str.slice(1);
			},
			makeIPTrafLink: function(IP) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=IPTraf&nonce=' + this.nonce + '&IP=' + encodeURIComponent(IP);
			},
			makeDiffLink: function(dat) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=diff&nonce=' + this.nonce +
					'&file=' + encodeURIComponent(this.es(dat['file'])) +
					'&cType=' + encodeURIComponent(this.es(dat['cType'])) +
					'&cKey=' + encodeURIComponent(this.es(dat['cKey'])) +
					'&cName=' + encodeURIComponent(this.es(dat['cName'])) +
					'&cVersion=' + encodeURIComponent(this.es(dat['cVersion']));
			},
			makeViewFileLink: function(file) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=view&nonce=' + this.nonce + '&file=' + encodeURIComponent(file);
			},
			makeViewOptionLink: function(option, siteID) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=viewOption&nonce=' + this.nonce + '&option=' + encodeURIComponent(option) + '&site_id=' + encodeURIComponent(siteID);
			},
			makeTimeAgo: function(t) {
				var months = Math.floor(t / (86400 * 30));
				var days = Math.floor(t / 86400);
				var hours = Math.floor(t / 3600);
				var minutes = Math.floor(t / 60);
				if (months > 0) {
					days -= months * 30;
					return this.pluralize(months, 'month', days, 'day');
				} else if (days > 0) {
					hours -= days * 24;
					return this.pluralize(days, 'day', hours, 'hour');
				} else if (hours > 0) {
					minutes -= hours * 60;
					return this.pluralize(hours, 'hour', minutes, 'min');
				} else if (minutes > 0) {
					//t -= minutes * 60;
					return this.pluralize(minutes, 'minute');
				} else {
					return Math.round(t) + " seconds";
				}
			},
			pluralize: function(m1, t1, m2, t2) {
				if (m1 != 1) {
					t1 = t1 + 's';
				}
				if (m2 != 1) {
					t2 = t2 + 's';
				}
				if (m1 && m2) {
					return m1 + ' ' + t1 + ' ' + m2 + ' ' + t2;
				} else {
					return m1 + ' ' + t1;
				}
			},
			calcRangeTotal: function() {
				var range = jQuery('#ipRange').val();
				if (!range) {
					return;
				}
				range = range.replace(/ /g, '');
				range = range.replace(/[\u2013-\u2015]/g, '-'); //Non-hyphen dashes to hyphen
				if (range && /^[^\-]+\-[^\-]+$/.test(range)) {
					var count = 1;
					var countOverflow = false;
					var badRange = false;
					var badIP = false;
					
					var ips = range.split('-');
					var ip1 = this.inet_pton(ips[0]);
					var ip2 = this.inet_pton(ips[1]);
					
					if (ip1 === false || ip2 === false) {
						badIP = true;
					}
					else {
						//Both to 16-byte binary strings
						var binStart = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip1).slice(-16);
						var binEnd = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip2).slice(-16);
						
						for (var i = 0; i < binStart.length; i++) {
							var n0 = binStart.charCodeAt(i);
							var n1 = binEnd.charCodeAt(i);
							
							if (i < 11 && n1 - n0 > 0) { //Based on Number.MAX_SAFE_INTEGER, which equals 2 ^ 53 - 1. Any of the first 9 bytes and part of the 10th that add to the range will put us over that
								countOverflow = true;
								break;
							}
							else if (i < 11 && n1 - n0 < 0) {
								badRange = true;
								break;
							}
							
							count += (n1 - n0) << (8 * (15 - i));
							if (count < 1) {
								badRange = true;
								break;
							}
						}
					}
					
					if (badIP) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #F00;\">Invalid IP entered.</span>"); 
						return;
					}
					else if (badRange) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #F00;\">Invalid. Starting IP is greater than ending IP.</span>");
						return;
					}
					else if (countOverflow) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #0A0;\">Valid: &gt;281474976710656 addresses in range.</span>");
						return;
					}

					jQuery('#wfShowRangeTotal').html("<span style=\"color: #0A0;\">Valid: " + count + " addresses in range.</span>"); 
				}
				else {
					jQuery('#wfShowRangeTotal').empty();
				}
			},
			loadBlockRanges: function() {
				var self = this;
				this.ajax('wordfence_loadBlockRanges', {}, function(res) {
					self.completeLoadBlockRanges(res);
				});

			},
			completeLoadBlockRanges: function(res) {
				jQuery('#currentBlocks').empty();
				if (res.results && res.results.length > 0) {
					jQuery('#wfBlockedRangesTmpl').tmpl(res).prependTo('#currentBlocks');
				} else {
					jQuery('#currentBlocks').html("You have not blocked any IP ranges or other patterns yet.");
				}
			},
			whois: function(val) {
				val = val.replace(' ', '');
				if (!/\w+/.test(val)) {
					this.colorbox('300px', "Enter a valid IP or domain", "Please enter a valid IP address or domain name for your whois lookup.");
					return;
				}
				var self = this;
				jQuery('#whoisbutton').attr('disabled', 'disabled');
				jQuery('#whoisbutton').attr('value', 'Loading...');
				this.ajax('wordfence_whois', {
					val: val
				}, function(res) {
					jQuery('#whoisbutton').removeAttr('disabled');
					jQuery('#whoisbutton').attr('value', 'Look up IP or Domain');
					if (res.ok) {
						self.completeWhois(res);
					}
				});
			},
			completeWhois: function(res) {
				var self = this;
				if (res.ok && res.result && res.result.rawdata && res.result.rawdata.length > 0) {
					var rawhtml = "";
					for (var i = 0; i < res.result.rawdata.length; i++) {
						res.result.rawdata[i] = jQuery('<div />').text(res.result.rawdata[i]).html();
						res.result.rawdata[i] = res.result.rawdata[i].replace(/([^\s\t\r\n:;]+@[^\s\t\r\n:;\.]+\.[^\s\t\r\n:;]+)/, "<a href=\"mailto:$1\">$1<\/a>");
						res.result.rawdata[i] = res.result.rawdata[i].replace(/(https?:\/\/[^\/]+[^\s\r\n\t]+)/, "<a target=\"_blank\" rel=\"noopener noreferrer\" href=\"$1\">$1<\/a>");
						var redStyle = "";
						if (this.getQueryParam('wfnetworkblock')) {
							redStyle = " style=\"color: #F00;\"";
						}

						function wfm21(str, ipRange, offset, totalStr) {
							var ips = ipRange.split(/\s*\-\s*/);
							var totalIPs = NaN;
							if (ips[0].indexOf(':') < 0) {
								var ip1num = self.inet_aton(ips[0]);
								var ip2num = self.inet_aton(ips[1]);
								totalIPs = ip2num - ip1num + 1;
							}
							return "<a href=\"admin.php?page=WordfenceBlocking&wfBlockRange=" + ipRange + "#top#advancedblocking\"" + redStyle + ">" + ipRange + " [" + (!isNaN(totalIPs) ? "<strong>" + totalIPs + "</strong> addresses in this network. " : "") + "Click to block this network]<\/a>";
						}

						function buildRangeLink2(str, octet1, octet2, octet3, octet4, cidrRange) {

							octet3 = octet3.length > 0 ? octet3 : '0';
							octet4 = octet4.length > 0 ? octet4 : '0';

							var rangeStart = [octet1, octet2, octet3, octet4].join('.');
							var rangeStartNum = self.inet_aton(rangeStart);
							cidrRange = parseInt(cidrRange, 10);
							if (!isNaN(rangeStartNum) && cidrRange > 0 && cidrRange < 32) {
								var rangeEndNum = rangeStartNum;
								for (var i = 32, j = 1; i >= cidrRange; i--, j *= 2) {
									rangeEndNum |= j;
								}
								rangeEndNum = rangeEndNum >>> 0;
								var ipRange = self.inet_ntoa(rangeStartNum) + '-' + self.inet_ntoa(rangeEndNum);
								var totalIPs = rangeEndNum - rangeStartNum;
								return "<a href=\"admin.php?page=WordfenceBlocking&wfBlockRange=" + ipRange + "#top#advancedblocking\"" + redStyle + ">" + ipRange + " [" + (!isNaN(totalIPs) ? "<strong>" + totalIPs + "</strong> addresses in this network. " : "") + "Click to block this network]<\/a>";
							}
							return str;
						}

						res.result.rawdata[i] = res.result.rawdata[i].replace(/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} - \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|[a-f0-9:.]{3,} - [a-f0-9:.]{3,})/i, wfm21);
						res.result.rawdata[i] = res.result.rawdata[i].replace(/(\d{1,3})\.(\d{1,3})\.?(\d{0,3})\.?(\d{0,3})\/(\d{1,3})/i, buildRangeLink2);
						rawhtml += res.result.rawdata[i] + "<br />";
					}
					jQuery('#wfrawhtml').html(rawhtml);
				} else {
					jQuery('#wfrawhtml').html('<span style="color: #F00;">Sorry, but no data for that IP or domain was found.</span>');
				}
			},
			blockIPUARange: function(ipRange, hostname, uaRange, referer, reason) {
				if (!/\w+/.test(reason)) {
					this.colorbox('300px', "Please specify a reason", "You forgot to include a reason you're blocking this IP range. We ask you to include this for your own record keeping.");
					return;
				}
				ipRange = ipRange.replace(/ /g, '').toLowerCase();
				ipRange = ipRange.replace(/[\u2013-\u2015]/g, '-'); //Non-hyphen dashes to hyphen
				if (ipRange) {
					var range = ipRange.split('-'),
						validRange;
					if (range.length !== 2) {
						validRange = false;
					} else if (range[0].match(':')) {
						validRange = this.inet_pton(range[0]) !== false && this.inet_pton(range[1]) !== false;
					} else if (range[0].match('.')) {
						validRange = this.inet_aton(range[0]) !== false && this.inet_aton(range[1]) !== false;
					}
					if (!validRange) {
						this.colorbox('300px', 'Specify a valid IP range', "Please specify a valid IP address range in the form of \"1.2.3.4 - 1.2.3.5\" without quotes. Make sure the dash between the IP addresses in a normal dash (a minus sign on your keyboard) and not another character that looks like a dash.");
						return;
					}
				}
				if (hostname && !/^[a-z0-9\.\*\-]+$/i.test(hostname)) {
					this.colorbox('300px', 'Specify a valid hostname', '<i>' + this.htmlEscape(hostname) + '</i> is not valid hostname');
					return;
				}
				if (!(/\w+/.test(ipRange) || /\w+/.test(uaRange) || /\w+/.test(referer) || /\w+/.test(hostname))) {
					this.colorbox('300px', 'Specify an IP range, Hostname or Browser pattern', "Please specify either an IP address range, Hostname or a web browser pattern to match.");
					return;
				}
				var self = this;
				this.ajax('wordfence_blockIPUARange', {
					ipRange: ipRange,
					hostname: hostname,
					uaRange: uaRange,
					referer: referer,
					reason: reason
				}, function(res) {
					if (res.ok) {
						self.loadBlockRanges();
						return;
					}
				});
			},
			unblockRange: function(id) {
				var self = this;
				this.ajax('wordfence_unblockRange', {
					id: id
				}, function(res) {
					self.loadBlockRanges();
				});
			},
			blockIP: function(IP, reason, callback) {
				var self = this;
				this.ajax('wordfence_blockIP', {
					IP: IP,
					reason: reason
				}, function(res) {
					if (res.errorMsg) {
						return;
					} else {
						self.reloadActivities();
						typeof callback === 'function' && callback();
					}
				});
			},
			blockIPTwo: function(IP, reason, perm) {
				var self = this;
				this.ajax('wordfence_blockIP', {
					IP: IP,
					reason: reason,
					perm: (perm ? '1' : '0')
				}, function(res) {
					if (res.errorMsg) {
						return;
					} else {
						self.staticTabChanged();
					}
				});
			},
			unlockOutIP: function(IP) {
				var self = this;
				this.ajax('wordfence_unlockOutIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			unblockIP: function(IP, callback) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.reloadActivities();
					typeof callback === 'function' && callback();
				});
			},
			unblockNetwork: function(id) {
				var self = this;
				this.ajax('wordfence_unblockRange', {
					id: id
				}, function(res) {
					self.reloadActivities();
				});
			},
			unblockIPTwo: function(IP) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			permBlockIP: function(IP) {
				var self = this;
				this.ajax('wordfence_permBlockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			makeElemID: function() {
				return 'wfElemGen' + this.elementGeneratorIter++;
			},
			pulse: function(sel) {
				jQuery(sel).fadeIn(function() {
					setTimeout(function() {
						jQuery(sel).fadeOut();
					}, 2000);
				});
			},
			saveConfig: function() {
				var qstr = jQuery('#wfConfigForm').serialize();
				var self = this;
				jQuery('.wfSavedMsg').hide();
				jQuery('.wfAjax24').show();
				this.ajax('wordfence_saveConfig', qstr, function(res) {
					jQuery('.wfAjax24').hide();
					if (res.ok) {
						if (res['paidKeyMsg']) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Congratulations! You have been upgraded to Premium Scanning.", "You have upgraded to a Premium API key. Once this page reloads, you can choose which premium scanning options you would like to enable and then click save. Click the button below to reload this page now.<br /><br /><center><input class='wf-btn wf-btn-default' type='button' name='wfReload' value='Reload page and enable Premium options' onclick='window.location.reload(true);' /></center>");
							return;
						} else if (res['reload'] == 'reload' || WFAD.reloadConfigPage) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Please reload this page", "You selected a config option that requires a page reload. Click the button below to reload this page to update the menu.<br /><br /><center><input class='wf-btn wf-btn-default' type='button' name='wfReload' value='Reload page' onclick='window.location.reload(true);' /></center>");
							return;
						} else {
							self.pulse('.wfSavedMsg');
						}

						$('#howGetIPs-preview-all').html(res.ipAll);
						$('#howGetIPs-preview-single').html(res.ip);
					} else if (res.errorMsg) {
						return;
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', 'We encountered an error trying to save your changes.');
					}
				});
			},
			savePartialConfig: function(formSelector) {
				var qstr = jQuery(formSelector).serialize();
				jQuery(formSelector).find('input:checkbox:not(:checked)').each(function(idx, el) {
					qstr += '&' + encodeURIComponent(jQuery(el).attr('name')) + '=0';
				});
				
				var self = this;
				jQuery('.wfSavedMsg').hide();
				jQuery('.wfAjax24').show();
				this.ajax('wordfence_savePartialConfig', qstr, function(res) {
					jQuery('.wfAjax24').hide();
					if (res.ok) {
						self.pulse('.wfSavedMsg');
					} else if (res.errorMsg) {
						return;
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', 'We encountered an error trying to save your changes.');
					}
				});
			},
			saveDebuggingConfig: function() {
				var qstr = jQuery('#wfDebuggingConfigForm').serialize();
				var self = this;
				jQuery('.wfSavedMsg').hide();
				jQuery('.wfAjax24').show();
				this.ajax('wordfence_saveDebuggingConfig', qstr, function(res) {
					jQuery('.wfAjax24').hide();
					if (res.ok) {
						self.pulse('.wfSavedMsg');
					} else if (res.errorMsg) {
						return;
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', 'We encountered an error trying to save your changes.');
					}
				});
			},
			changeSecurityLevel: function() {
				var level = jQuery('#securityLevel').val();
				for (var k in WFSLevels[level].checkboxes) {
					if (k != 'liveTraf_ignorePublishers') {
						jQuery('#' + k).prop("checked", WFSLevels[level].checkboxes[k]);
					}
				}
				for (var k in WFSLevels[level].otherParams) {
					if (!/^(?:apiKey|securityLevel|alertEmails|liveTraf_ignoreUsers|liveTraf_ignoreIPs|liveTraf_ignoreUA|liveTraf_hitsMaxSize|maxMem|maxExecutionTime|actUpdateInterval)$/.test(k)) {
						jQuery('#' + k).val(WFSLevels[level].otherParams[k]);
					}
				}
			},
			clearAllBlocked: function(op) {
				if (op == 'blocked') {
					body = "Are you sure you want to clear all blocked IP addresses and allow visitors from those addresses to access the site again?";
				} else if (op == 'locked') {
					body = "Are you sure you want to clear all locked IP addresses and allow visitors from those addresses to sign in again?";
				} else {
					return;
				}
				this.colorbox((this.isSmallScreen ? '300px' : '450px'), "Please confirm", body +
					'<br /><br /><center><input class="wf-btn wf-btn-default" type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />&nbsp;&nbsp;&nbsp;' +
					'<input class="wf-btn wf-btn-default" type="button" name="but2" value="Yes I\'m sure" onclick="jQuery.colorbox.close(); WFAD.confirmClearAllBlocked(\'' + op + '\');"><br />');
			},
			confirmClearAllBlocked: function(op) {
				var self = this;
				this.ajax('wordfence_clearAllBlocked', {op: op}, function(res) {
					self.staticTabChanged();
				});
			},
			setOwnCountry: function(code) {
				this.ownCountry = (code + "").toUpperCase();
			},
			loadBlockedCountries: function(str) {
				var codes = str.split(',');
				for (var i = 0; i < codes.length; i++) {
					jQuery('#wfCountryCheckbox_' + codes[i]).addClass('active');
				}
			},
			saveCountryBlocking: function() {
				var action = jQuery('#wfBlockAction').val();
				var redirURL = jQuery('#wfRedirURL').val();
				var bypassRedirURL = jQuery('#wfBypassRedirURL').val();
				var bypassRedirDest = jQuery('#wfBypassRedirDest').val();
				var bypassViewURL = jQuery('#wfBypassViewURL').val();

				if (action == 'redir' && (!/^https?:\/\/[^\/]+/i.test(redirURL))) {
					this.colorbox((this.isSmallScreen ? '300px' : '400px'), "Please enter a URL for redirection", "You have chosen to redirect blocked countries to a specific page. You need to enter a URL in the text box provided that starts with http:// or https://");
					return;
				}
				if (bypassRedirURL || bypassRedirDest) {
					if (!(bypassRedirURL && bypassRedirDest)) {
						this.colorbox((this.isSmallScreen ? '300px' : '400px'), "Missing data from form", "If you want to set up a URL that will bypass country blocking, you must enter a URL that a visitor can hit and the destination they will be redirected to. You have only entered one of these components. Please enter both.");
						return;
					}
					if (bypassRedirURL == bypassRedirDest) {
						this.colorbox((this.isSmallScreen ? '300px' : '400px'), "URLs are the same", "The URL that a user hits to bypass country blocking and the URL they are redirected to are the same. This would cause a circular redirect. Please fix this.");
						return;
					}
				}
				if (bypassRedirURL && (!/^(?:\/|http:\/\/)/.test(bypassRedirURL))) {
					this.invalidCountryURLMsg(bypassRedirURL);
					return;
				}
				if (bypassRedirDest && (!/^(?:\/|http:\/\/)/.test(bypassRedirDest))) {
					this.invalidCountryURLMsg(bypassRedirDest);
					return;
				}
				if (bypassViewURL && (!/^(?:\/|http:\/\/)/.test(bypassViewURL))) {
					this.invalidCountryURLMsg(bypassViewURL);
					return;
				}

				var codesArr = [];
				var ownCountryBlocked = false;
				var self = this;
				jQuery('.wf-blocked-countries li').each(function(idx, elem) {
					if (jQuery(elem).hasClass('active')) {
						var code = jQuery(elem).data('country');
						codesArr.push(code);
						if (code == self.ownCountry) {
							ownCountryBlocked = true;
						}
					}
				});
				this.countryCodesToSave = codesArr.join(',');
				if (ownCountryBlocked) {
					this.colorbox((this.isSmallScreen ? '300px' : '400px'), "Please confirm blocking yourself", "You are about to block your own country. This could lead to you being locked out. Please make sure that your user profile on this machine has a current and valid email address and make sure you know what it is. That way if you are locked out, you can send yourself an unlock email. If you're sure you want to block your own country, click 'Confirm' below, otherwise click 'Cancel'.<br />" +
						'<input class="wf-btn wf-btn-default" type="button" name="but1" value="Confirm" onclick="jQuery.colorbox.close(); WFAD.confirmSaveCountryBlocking();" />&nbsp;<input class="wf-btn wf-btn-default" type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />');
				} else {
					this.confirmSaveCountryBlocking();
				}
			},
			invalidCountryURLMsg: function(URL) {
				this.colorbox((this.isSmallScreen ? '300px' : '400px'), "Invalid URL", "URL's that you provide for bypassing country blocking must start with '/' or 'http://' without quotes. The URL that is invalid is: " + this.htmlEscape(URL));
				return;
			},
			confirmSaveCountryBlocking: function() {
				var action = jQuery('#wfBlockAction').val();
				var redirURL = jQuery('#wfRedirURL').val();
				var loggedInBlocked = jQuery('#wfLoggedInBlocked').is(':checked') ? '1' : '0';
				var loginFormBlocked = jQuery('#wfLoginFormBlocked').is(':checked') ? '1' : '0';
				var restOfSiteBlocked = jQuery('#wfRestOfSiteBlocked').is(':checked') ? '1' : '0';
				var bypassRedirURL = jQuery('#wfBypassRedirURL').val();
				var bypassRedirDest = jQuery('#wfBypassRedirDest').val();
				var bypassViewURL = jQuery('#wfBypassViewURL').val();

				jQuery('.wfAjax24').show();
				var self = this;
				this.ajax('wordfence_saveCountryBlocking', {
					blockAction: action,
					redirURL: redirURL,
					loggedInBlocked: loggedInBlocked,
					loginFormBlocked: loginFormBlocked,
					restOfSiteBlocked: restOfSiteBlocked,
					bypassRedirURL: bypassRedirURL,
					bypassRedirDest: bypassRedirDest,
					bypassViewURL: bypassViewURL,
					codes: this.countryCodesToSave
				}, function(res) {
					jQuery('.wfAjax24').hide();
					self.pulse('.wfSavedMsg');
				});
			},
			paidUsersOnly: function(msg) {
				var pos = jQuery('#paidWrap').position();
				var width = jQuery('#paidWrap').width();
				var height = jQuery('#paidWrap').height();
				jQuery('<div style="position: absolute; left: ' + pos.left + 'px; top: ' + pos.top + 'px; background-color: #FFF; width: ' + width + 'px; height: ' + height + 'px;"><div class="paidInnerMsg">' + msg + ' <a href="https://www.wordfence.com/wordfence-signup/" target="_blank" rel="noopener noreferrer">Click here to upgrade and gain access to this feature.</div></div>').insertAfter('#paidWrap').fadeTo(10000, 0.7);
			},
			sched_modeChange: function() {
				var self = this;
				if (jQuery('#schedMode').val() == 'auto') {
					jQuery('.wfSchedCheckbox').attr('disabled', true);
				} else {
					jQuery('.wfSchedCheckbox').attr('disabled', false);
				}
			},
			sched_shortcut: function(mode) {
				if (jQuery('#schedMode').val() == 'auto') {
					this.colorbox((this.isSmallScreen ? '300px' : '400px'), 'Change the scan mode', "You need to change the scan mode to manually scheduled scans if you want to select scan times.");
					return;
				}
				jQuery('.wfSchedCheckbox').prop('checked', false);
				if (this.schedStartHour === false) {
					this.schedStartHour = Math.floor((Math.random() * 24));
				} else {
					this.schedStartHour++;
					if (this.schedStartHour > 23) {
						this.schedStartHour = 0;
					}
				}
				if (mode == 'onceDaily') {
					for (var i = 0; i <= 6; i++) {
						jQuery('#wfSchedDay_' + i + '_' + this.schedStartHour).attr('checked', true);
					}
				} else if (mode == 'twiceDaily') {
					var secondHour = this.schedStartHour + 12;
					if (secondHour >= 24) {
						secondHour = secondHour - 24;
					}
					for (var i = 0; i <= 6; i++) {
						jQuery('#wfSchedDay_' + i + '_' + this.schedStartHour).attr('checked', true);
						jQuery('#wfSchedDay_' + i + '_' + secondHour).attr('checked', true);
					}
				} else if (mode == 'oddDaysWE') {
					var startDay = Math.floor((Math.random()));
					jQuery('#wfSchedDay_1_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_3_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_5_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_6_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_0_' + this.schedStartHour).attr('checked', true);
				} else if (mode == 'weekends') {
					var startDay = Math.floor((Math.random()));
					jQuery('#wfSchedDay_6_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_0_' + this.schedStartHour).attr('checked', true);
				} else if (mode == 'every6hours') {
					for (var i = 0; i <= 6; i++) {
						for (var hour = this.schedStartHour; hour < this.schedStartHour + 24; hour = hour + 6) {
							var displayHour = hour;
							if (displayHour >= 24) {
								displayHour = displayHour - 24;
							}
							jQuery('#wfSchedDay_' + i + '_' + displayHour).attr('checked', true);
						}
					}
				}

			},
			sched_save: function() {
				var schedMode = jQuery('#schedMode').val();
				var schedule = [];
				for (var day = 0; day <= 6; day++) {
					var hours = [];
					for (var hour = 0; hour <= 23; hour++) {
						var elemID = '#wfSchedDay_' + day + '_' + hour;
						hours[hour] = jQuery(elemID).is(':checked') ? '1' : '0';
					}
					schedule[day] = hours.join(',');
				}
				var scheduleTxt = schedule.join('|');
				var self = this;
				this.ajax('wordfence_saveScanSchedule', {
					schedMode: schedMode,
					schedTxt: scheduleTxt
				}, function(res) {
					jQuery('#wfScanStartTime').html(res.nextStart);
					jQuery('.wfAjax24').hide();
					self.pulse('.wfSaveMsg');
				});
			},
			twoFacStatus: function(msg) {
				jQuery('#wfTwoFacMsg').html(msg);
				jQuery('#wfTwoFacMsg').fadeIn(function() {
					setTimeout(function() {
						jQuery('#wfTwoFacMsg').fadeOut();
					}, 2000);
				});
			},
			addTwoFactor: function(username, phone, mode) {
				var self = this;
				this.ajax('wordfence_addTwoFactor', {
					username: username,
					phone: phone,
					mode: mode
				}, function(res) {
					if (res.ok) {
						if (mode == 'authenticator') {
							var totpURL = "otpauth://totp/" + encodeURI(res.homeurl) + encodeURI(" (" + res.username + ")") + "?" + res.uriQueryString + "&issuer=Wordfence"; 
							self.twoFacStatus('User added! Scan the QR code with your authenticator app to add it.');
							
							var message = "Scan the code below with your authenticator app to add this account. Some authenticator apps also allow you to type in the text version instead.<br><div id=\"wfTwoFactorQRCodeTable\"></div><br><strong>Key:</strong> <input type=\"text\"" + (self.isSmallScreen ? "" : " size=\"45\"") + " value=\"" + res.base32Secret + "\" onclick=\"this.select();\" readonly>";
							if (res.recoveryCodes.length > 0) {
								message = message + "<br><br><strong>Recovery Codes</strong><br><p>Use one of these " + res.recoveryCodes.length + " codes to log in if you lose access to your authenticator device. Codes are 16 characters long, plus optional spaces. Each one may be used only once.</p><ul id=\"wfTwoFactorRecoveryCodes\">";

								var recoveryCodeFileContents = "Cellphone Sign-In Recovery Codes - " + res.homeurl + " (" + res.username + ")\r\n";
								recoveryCodeFileContents = recoveryCodeFileContents + "\r\nEach line of 16 letters and numbers is a single recovery code, with optional spaces for readability. When typing your password, enter \"wf\" followed by the entire code like \"mypassword wf1234 5678 90AB CDEF\". If your site shows a separate prompt for entering a code after entering only your username and password, enter only the code like \"1234 5678 90AB CDEF\". Your recovery codes are:\r\n\r\n";
								var splitter = /.{4}/g;
								for (var i = 0; i < res.recoveryCodes.length; i++) { 
									var code = res.recoveryCodes[i];
									var chunks = code.match(splitter);
									message = message + "<li>" + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "</li>";
									recoveryCodeFileContents = recoveryCodeFileContents + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "\r\n"; 
								}
								
								message = message + "</ul>";
								
								message = message + "<p class=\"wf-center\"><a href=\"#\" class=\"wf-btn wf-btn-default\" id=\"wfTwoFactorDownload\" target=\"_blank\" rel=\"noopener noreferrer\"><i class=\"dashicons dashicons-download\"></i> Download</a></p>";
							}

							message = message + "<p><em>This will be shown only once. Keep these codes somewhere safe.</em></p>";
							
							self.colorbox((self.isSmallScreen ? '300px' : '440px'), "Authentication Code", message, {onComplete: function() { 
								jQuery('#wfTwoFactorQRCodeTable').qrcode({text: totpURL, width: (self.isSmallScreen ? 175 : 256), height: (self.isSmallScreen ? 175 : 256)});
								jQuery('#wfTwoFactorDownload').on('click', function(e) {
									e.preventDefault();
									e.stopPropagation();
									saveAs(new Blob([recoveryCodeFileContents], {type: "text/plain;charset=" + document.characterSet}), self.htmlEscape(res.homeurl) + "_" + self.htmlEscape(res.username) + "_recoverycodes.txt");
								});
							}});
						}
						else {
							self.twoFacStatus('User added! Check the user\'s phone to get the activation code.');

							if (res.recoveryCodes.length > 0) {
								var message = "<p>Use one of these " + res.recoveryCodes.length + " codes to log in if you are unable to access your phone. Codes are 16 characters long, plus optional spaces. Each one may be used only once.</p><ul id=\"wfTwoFactorRecoveryCodes\">";

								var recoveryCodeFileContents = "Cellphone Sign-In Recovery Codes - " + res.homeurl + " (" + res.username + ")\r\n";
								recoveryCodeFileContents = recoveryCodeFileContents + "\r\nEach line of 16 letters and numbers is a single recovery code, with optional spaces for readability. When typing your password, enter \"wf\" followed by the entire code like \"mypassword wf1234 5678 90AB CDEF\". If your site shows a separate prompt for entering a code after entering only your username and password, enter only the code like \"1234 5678 90AB CDEF\". Your recovery codes are:\r\n\r\n";
								var splitter = /.{4}/g;
								for (var i = 0; i < res.recoveryCodes.length; i++) {
									var code = res.recoveryCodes[i];
									var chunks = code.match(splitter);
									message = message + "<li>" + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "</li>";
									recoveryCodeFileContents = recoveryCodeFileContents + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "\r\n";
								}

								message = message + "<p class=\"wf-center\"><a href=\"#\" class=\"wf-btn wf-btn-default\" id=\"wfTwoFactorDownload\" target=\"_blank\" rel=\"noopener noreferrer\"><i class=\"dashicons dashicons-download\"></i> Download</a></p>";

								message = message + "</ul><p><em>This will be shown only once. Keep these codes somewhere safe.</em></p>";

								self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Recovery Codes", message, {onComplete: function() {
									jQuery('#wfTwoFactorDownload').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();
										saveAs(new Blob([recoveryCodeFileContents], {type: "text/plain;charset=" + document.characterSet}), self.htmlEscape(res.homeurl) + "_" + self.htmlEscape(res.username) + "_recoverycodes.txt");
									});
								}});
							}
						}
						
						var updatedTwoFac = jQuery('#wfTwoFacUserTmpl').tmpl({users: [res]});
						jQuery('#twoFactorUser-none').remove();
						jQuery('#wfTwoFacUsers > table > tbody:last-child').append(updatedTwoFac.find('tbody > tr'));
					}
				});
			},
			twoFacActivate: function(userID, code) {
				var self = this;
				this.ajax('wordfence_twoFacActivate', {
					userID: userID,
					code: code
				}, function(res) {
					if (res.ok) {
						var updatedTwoFac = jQuery('#wfTwoFacUserTmpl').tmpl({users: [res]});
						updatedTwoFac.find('tbody > tr').each(function(index, element) {
							jQuery('#' + jQuery(element).attr('id')).replaceWith(element);
						});
						self.twoFacStatus('Cellphone Sign-in activated for user.');
					}
				});
			},
			delTwoFac: function(userID) {
				this.ajax('wordfence_twoFacDel', {
					userID: userID
				}, function(res) {
					if (res.ok) {
						jQuery('#twoFactorUser-' + res.userID).fadeOut(function() {
							jQuery(this).remove();
							
							if (jQuery('#wfTwoFacUsers > table > tbody:last-child').children().length == 0) {
								jQuery('#wfTwoFacUsers').html(jQuery('#wfTwoFacUserTmpl').tmpl({users: []}));
							}
						});
					}
				});
			},
			loadTwoFactor: function() {
				this.ajax('wordfence_loadTwoFactor', {}, function(res) {
					jQuery('#wfTwoFacUsers').html(jQuery('#wfTwoFacUserTmpl').tmpl(res));
				});
			},
			getQueryParam: function(name) {
				name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
				var regexS = "[\\?&]" + name + "=([^&#]*)";
				var regex = new RegExp(regexS);
				var results = regex.exec(window.location.search);
				if (results == null) {
					return "";
				} else {
					return decodeURIComponent(results[1].replace(/\+/g, " "));
				}
			},
			inet_aton: function(dot) {
				var d = dot.split('.');
				return ((((((+d[0]) * 256) + (+d[1])) * 256) + (+d[2])) * 256) + (+d[3]);
			},
			inet_ntoa: function(num) {
				var d = num % 256;
				for (var i = 3; i > 0; i--) {
					num = Math.floor(num / 256);
					d = num % 256 + '.' + d;
				}
				return d;
			},

			inet_pton: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_pton/
				// original by: Theriault
				//   example 1: inet_pton('::');
				//   returns 1: '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'
				//   example 2: inet_pton('127.0.0.1');
				//   returns 2: '\x7F\x00\x00\x01'

				var r, m, x, i, j, f = String.fromCharCode;
				m = a.match(/^(?:\d{1,3}(?:\.|$)){4}/); // IPv4
				if (m) {
					m = m[0].split('.');
					m = f(m[0]) + f(m[1]) + f(m[2]) + f(m[3]);
					// Return if 4 bytes, otherwise false.
					return m.length === 4 ? m : false;
				}
				r = /^((?:[\da-f]{1,4}(?::|)){0,8})(::)?((?:[\da-f]{1,4}(?::|)){0,8})$/i;
				m = a.match(r); // IPv6
				if (m) {
					if (a == '::') {
						return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
					}

					var colonCount = a.split(':').length - 1;
					var doubleColonPos = a.indexOf('::');
					if (doubleColonPos > -1) {
						var expansionLength = ((doubleColonPos == 0 || doubleColonPos == a.length - 2) ? 9 : 8) - colonCount;
						var expansion = '';
						for (i = 0; i < expansionLength; i++) {
							expansion += ':0000';
						}
						a = a.replace('::', expansion + ':');
						a = a.replace(/(?:^\:|\:$)/, '', a);
					}
					
					var ipGroups = a.split(':');
					var ipBin = '';
					for (i = 0; i < ipGroups.length; i++) {
						var group = ipGroups[i];
						if (group.length > 4) {
							return false;
						}
						group = ("0000" + group).slice(-4);
						var b1 = parseInt(group.slice(0, 2), 16);
						var b2 = parseInt(group.slice(-2), 16);
						if (isNaN(b1) || isNaN(b2)) {
							return false;
						}
						ipBin += f(b1) + f(b2);
					}
					
					return ipBin.length == 16 ? ipBin : false;
				}
				return false; // Invalid IP.
			},
			inet_ntop: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_ntop/
				// original by: Theriault
				//   example 1: inet_ntop('\x7F\x00\x00\x01');
				//   returns 1: '127.0.0.1'
				//   example 2: inet_ntop('\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1');
				//   returns 2: '::1'

				var i = 0,
					m = '',
					c = [];
				a += '';
				if (a.length === 4) { // IPv4
					return [
						a.charCodeAt(0), a.charCodeAt(1), a.charCodeAt(2), a.charCodeAt(3)].join('.');
				} else if (a.length === 16) { // IPv6
					for (i = 0; i < 16; i++) {
						c.push(((a.charCodeAt(i++) << 8) + a.charCodeAt(i))
							.toString(16));
					}
					return c.join(':')
						.replace(/((^|:)0(?=:|$))+:?/g, function(t) {
							m = (t.length > m.length) ? t : m;
							return t;
						})
						.replace(m || ' ', '::');
				} else { // Invalid length
					return false;
				}
			},

			exportSettings: function() {
				var self = this;
				this.ajax('wordfence_exportSettings', {}, function(res) {
					if (res.ok && res.token) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Export Successful", "We successfully exported your site settings. To import your site settings on another site, copy and paste the token below into the import text box on the destination site. Keep this token secret. It is like a password. If anyone else discovers the token it will allow them to import your settings excluding your API key.<br /><br />Token:<input type=\"text\" size=\"20\" value=\"" + res.token + "\" onclick=\"this.select();\" /><br />");
					} else if (res.err) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error during Export", res.err);
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "An unknown error occurred", "An unknown error occurred during the export. We received an undefined error from your web server.");
					}
				});
			},
			importSettings: function(token) {
				var self = this;
				this.ajax('wordfence_importSettings', {token: token}, function(res) {
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Import Successful", "You successfully imported " + res.totalSet + " options. Your import is complete. Please reload this page or click the button below to reload it:<br /><br /><input class=\"wf-btn wf-btn-default\" type=\"button\" value=\"Reload Page\" onclick=\"window.location.reload(true);\" />");
					} else if (res.err) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error during Import", res.err);
					} else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error during Export", "An unknown error occurred during the import");
					}
				});
			},
			startPasswdAudit: function(auditType, emailAddr) {
				var self = this;
				this.ajax('wordfence_startPasswdAudit', {auditType: auditType, emailAddr: emailAddr}, function(res) {
					self.loadPasswdAuditJobs();
					if (res.ok) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Password Audit Started", "Your password audit started successfully. The results will appear here once it is complete. You will also receive an email letting you know the results are ready at: " + emailAddr);
					} else if (!res.errorMsg) { //error displayed
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Error Starting Audit", "An unknown error occurred when trying to start your password audit.");
					}
				});
			},

			deleteAdminUser: function(issueID) {
				var self = this;
				this.ajax('wordfence_deleteAdminUser', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Successfully deleted admin", "The admin user " +
								self.htmlEscape(res.user_login) + " was successfully deleted.");
						});
					} else if (res.errorMsg) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.errorMsg);
						});
					}
				});
			},

			revokeAdminUser: function(issueID) {
				var self = this;
				this.ajax('wordfence_revokeAdminUser', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), "Successfully revoked admin", "All capabilties of admin user " +
								self.htmlEscape(res.user_login) + " were successfully revoked.");
						});
					} else if (res.errorMsg) {
						self.loadIssues(function() {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'An error occurred', res.errorMsg);
						});
					}
				});
			},

			windowHasFocus: function() {
				if (typeof document.hasFocus === 'function') {
					return document.hasFocus();
				}
				// Older versions of Opera
				return this._windowHasFocus;
			},

			htmlEscape: function(html) {
				return String(html)
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#39;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;');
			},

			permanentlyBlockAllIPs: function(type) {
				var self = this;
				this.ajax('wordfence_permanentlyBlockAllIPs', {
					type: type
				}, function(res) {
					$('#wfTabs').find('.wfTab1').eq(0).trigger('click');
				});
			},

			showTimestamp: function(timestamp, serverTime, format) {
				serverTime = serverTime === undefined ? new Date().getTime() / 1000 : serverTime;
				format = format === undefined ? '${dateTime} (${timeAgo} ago)' : format;
				var date = new Date(timestamp * 1000);

				return jQuery.tmpl(format, {
					dateTime: date.toLocaleDateString() + ' ' + date.toLocaleTimeString(),
					timeAgo: this.makeTimeAgo(serverTime - timestamp)
				});
			},

			updateTimeAgo: function() {
				var self = this;
				jQuery('.wfTimeAgo-timestamp').each(function(idx, elem) {
					var el = jQuery(elem);
					var testEl = el;
					if (typeof jQuery === "function" && testEl instanceof jQuery) {
						testEl = testEl[0];
					}

					var rect = testEl.getBoundingClientRect();
					if (!(rect.top >= 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && rect.right <= (window.innerWidth || document.documentElement.clientWidth))) {
						return;
					}
					
					var timestamp = el.data('wfctime');
					if (!timestamp) {
						timestamp = el.attr('data-timestamp');
					}
					var serverTime = self.serverMicrotime;
					var format = el.data('wfformat');
					if (!format) {
						format = el.attr('data-format');
					}
					el.html(self.showTimestamp(timestamp, serverTime, format));
				});
			},

			wafData: {
				whitelistedURLParams: []
			},

			wafConfigSave: function(action, data, onSuccess, showColorBox) {
				showColorBox = showColorBox === undefined ? true : !!showColorBox;
				var self = this;
				if (typeof(data) == 'string') {
					if (data.length > 0) {
						data += '&';
					}
					data += 'wafConfigAction=' + action;
				} else if (typeof(data) == 'object' && data instanceof Array) {
					// jQuery serialized form data
					data.push({
						name: 'wafConfigAction',
						value: action
					});
				} else if (typeof(data) == 'object') {
					data['wafConfigAction'] = action;
				}

				this.ajax('wordfence_saveWAFConfig', data, function(res) {
					if (typeof res === 'object' && res.success) {
						if (showColorBox) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Firewall Configuration', 'The Wordfence Web Application Firewall ' +
								'configuration was saved successfully.');
						}
						self.wafData = res.data;
						self.wafConfigPageRender();
						if (typeof onSuccess === 'function') {
							return onSuccess.apply(this, arguments);
						}
					}
					else if (typeof res === 'object' && res.errorMsg) {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Error saving Firewall configuration', 'There was an error saving the ' +
							'Web Application Firewall configuration settings: ' + res.errorMsg);
					}
					else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Error saving Firewall configuration', 'There was an error saving the ' +
							'Web Application Firewall configuration settings.');
					}
				});
			},

			wafWhitelistURLAdd: function(url, param, onSuccess) {
				this.wafData.whitelistedURLParams.push({
					'path': url,
					'paramKey': param,
					'ruleID': ['all']
				});
				var index = this.wafData.whitelistedURLParams.length;
				var inputPath = $('<input name="whitelistedURLParams[' + index + '][path]" type="hidden" />');
				var inputParam = $('<input name="whitelistedURLParams[' + index + '][paramKey]" type="hidden" />');
				var inputEnabled = $('<input name="whitelistedURLParams[' + index + '][enabled]" type="hidden" value="1" />');
				inputPath.val(url);
				inputParam.val(param);
				$('#waf-config-form').append(inputPath)
						.append(inputParam)
						.append(inputEnabled);
				this.wafConfigSave(onSuccess);
				inputPath.remove();
				inputParam.remove();
				inputEnabled.remove();
			},

			wafConfigPageRender: function() {
				var whitelistedIPsEl = $('#waf-whitelisted-urls-tmpl').tmpl(this.wafData);
				$('#waf-whitelisted-urls-wrapper').html(whitelistedIPsEl);

				var rulesEl = $('#waf-rules-tmpl').tmpl(this.wafData);
				$('#waf-rules-wrapper').html(rulesEl);

				if (this.wafData['rulesLastUpdated']) {
					var date = new Date(this.wafData['rulesLastUpdated'] * 1000);
					this.renderWAFRulesLastUpdated(date);
				}
				$(window).trigger('wordfenceWAFConfigPageRender');
			},

			renderWAFRulesLastUpdated: function(date) {
				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}
				$('#waf-rules-last-updated').text('Last Updated: ' + dateString)
					.css({
						'opacity': 0
					})
					.animate({
						'opacity': 1
					}, 500);
			},

			renderWAFRulesNextUpdate: function(date) {
				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}
				$('#waf-rules-next-update').text('Next Update Check: ' + dateString)
					.css({
						'opacity': 0
					})
					.animate({
						'opacity': 1
					}, 500);
			},

			wafUpdateRules: function(onSuccess) {
				var self = this;
				this.ajax('wordfence_updateWAFRules', {}, function(res) {
					self.wafData = res;
					self.wafConfigPageRender();
					if (self.wafData['updated']) {
						if (!self.wafData['isPaid']) {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Rules Updated', 'Your rules have been updated successfully. You are ' +
								'currently using the the free version of Wordfence. ' +
								'Upgrade to Wordfence premium to have your rules updated automatically as new threats emerge. ' +
								'<a href="https://www.wordfence.com/wafUpdateRules1/wordfence-signup/">Click here to purchase a premium API key</a>. ' +
								'<em>Note: Your rules will still update every 30 days as a free user.</em>');
						} else {
							self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Rules Updated', 'Your rules have been updated successfully.');
						}
					}
					else {
						self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Rule Update Failed', 'No rules were updated. Please verify you have permissions to write to the /wp-content/wflogs directory.');
					}
					if (typeof onSuccess === 'function') {
						return onSuccess.apply(this, arguments);
					}
				});
			},

			dateFormat: function(date) {
				if (date instanceof Date) {
					if (date.toLocaleString) {
						return date.toLocaleString();
					}
					return date.toString();
				}
				return date;
			},

			wafAddBootstrap: function() {
				var self = this;
				this.ajax('wordfence_wafAddBootstrap', {}, function(res) {
					self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'File Created', "");
				});
			},

			wafConfigureAutoPrepend: function() {
				var self = this;
				self.colorbox((self.isSmallScreen ? '300px' : '400px'), 'Backup .htaccess before continuing', 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file before proceeding.'
					+ '<br/>'
					+ '<a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wf-htaccess-confirm\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a>' +
					'<br /><br />' +
					'<button class="wf-btn wf-btn-default" type="button" id="wf-htaccess-confirm" disabled="disabled" onclick="WFAD.confirmWAFConfigureAutoPrepend();">Add code to .htaccess</button>');
			},

			confirmWAFConfigureAutoPrepend: function() {
				var self = this;
				this.ajax('wordfence_wafConfigureAutoPrepend', {}, function(res) {
					self.colorbox((self.isSmallScreen ? '300px' : '400px'), '.htaccess Updated', "Your .htaccess has been updated successfully. Please " +
						"verify your site is functioning normally.");
				});
			},

			base64_decode: function(s) {
				var e = {}, i, b = 0, c, x, l = 0, a, r = '', w = String.fromCharCode, L = s.length;
				var A = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
				for (i = 0; i < 64; i++) {
					e[A.charAt(i)] = i;
				}
				for (x = 0; x < L; x++) {
					c = e[s.charAt(x)];
					b = (b << 6) + c;
					l += 6;
					while (l >= 8) {
						((a = (b >>> (l -= 8)) & 0xff) || (x < (L - 2))) && (r += w(a));
					}
				}
				return r;
			}
		};

		window['WFAD'] = window['wordfenceAdmin'];
		setInterval(function() {
			WFAD.updateTimeAgo();
		}, 1000);
	}
	jQuery(function() {
		wordfenceAdmin.init();
		jQuery(window).on('focus', function() {
			if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
				jQuery('body').removeClass('wordfenceLiveActivityPaused');
			}
		});
	});
})(jQuery);

/*! @source http://purl.eligrey.com/github/FileSaver.js/blob/master/FileSaver.js */
var saveAs=saveAs||function(e){"use strict";if(typeof e==="undefined"||typeof navigator!=="undefined"&&/MSIE [1-9]\./.test(navigator.userAgent)){return}var t=e.document,n=function(){return e.URL||e.webkitURL||e},r=t.createElementNS("http://www.w3.org/1999/xhtml","a"),o="download"in r,i=function(e){var t=new MouseEvent("click");e.dispatchEvent(t)},a=/constructor/i.test(e.HTMLElement),f=/CriOS\/[\d]+/.test(navigator.userAgent),u=function(t){(e.setImmediate||e.setTimeout)(function(){throw t},0)},d="application/octet-stream",s=1e3*40,c=function(e){var t=function(){if(typeof e==="string"){n().revokeObjectURL(e)}else{e.remove()}};setTimeout(t,s)},l=function(e,t,n){t=[].concat(t);var r=t.length;while(r--){var o=e["on"+t[r]];if(typeof o==="function"){try{o.call(e,n||e)}catch(i){u(i)}}}},p=function(e){if(/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(e.type)){return new Blob([String.fromCharCode(65279),e],{type:e.type})}return e},v=function(t,u,s){if(!s){t=p(t)}var v=this,w=t.type,m=w===d,y,h=function(){l(v,"writestart progress write writeend".split(" "))},S=function(){if((f||m&&a)&&e.FileReader){var r=new FileReader;r.onloadend=function(){var t=f?r.result:r.result.replace(/^data:[^;]*;/,"data:attachment/file;");var n=e.open(t,"_blank");if(!n)e.location.href=t;t=undefined;v.readyState=v.DONE;h()};r.readAsDataURL(t);v.readyState=v.INIT;return}if(!y){y=n().createObjectURL(t)}if(m){e.location.href=y}else{var o=e.open(y,"_blank");if(!o){e.location.href=y}}v.readyState=v.DONE;h();c(y)};v.readyState=v.INIT;if(o){y=n().createObjectURL(t);setTimeout(function(){r.href=y;r.download=u;i(r);h();c(y);v.readyState=v.DONE});return}S()},w=v.prototype,m=function(e,t,n){return new v(e,t||e.name||"download",n)};if(typeof navigator!=="undefined"&&navigator.msSaveOrOpenBlob){return function(e,t,n){t=t||e.name||"download";if(!n){e=p(e)}return navigator.msSaveOrOpenBlob(e,t)}}w.abort=function(){};w.readyState=w.INIT=0;w.WRITING=1;w.DONE=2;w.error=w.onwritestart=w.onprogress=w.onwrite=w.onabort=w.onerror=w.onwriteend=null;return m}(typeof self!=="undefined"&&self||typeof window!=="undefined"&&window||this.content);if(typeof module!=="undefined"&&module.exports){module.exports.saveAs=saveAs}else if(typeof define!=="undefined"&&define!==null&&define.amd!==null){define([],function(){return saveAs})}

!function(t){"use strict";if(t.URL=t.URL||t.webkitURL,t.Blob&&t.URL)try{return void new Blob}catch(e){}var n=t.BlobBuilder||t.WebKitBlobBuilder||t.MozBlobBuilder||function(t){var e=function(t){return Object.prototype.toString.call(t).match(/^\[object\s(.*)\]$/)[1]},n=function(){this.data=[]},o=function(t,e,n){this.data=t,this.size=t.length,this.type=e,this.encoding=n},i=n.prototype,a=o.prototype,r=t.FileReaderSync,c=function(t){this.code=this[this.name=t]},l="NOT_FOUND_ERR SECURITY_ERR ABORT_ERR NOT_READABLE_ERR ENCODING_ERR NO_MODIFICATION_ALLOWED_ERR INVALID_STATE_ERR SYNTAX_ERR".split(" "),s=l.length,u=t.URL||t.webkitURL||t,d=u.createObjectURL,f=u.revokeObjectURL,R=u,p=t.btoa,h=t.atob,b=t.ArrayBuffer,g=t.Uint8Array,w=/^[\w-]+:\/*\[?[\w\.:-]+\]?(?::[0-9]+)?/;for(o.fake=a.fake=!0;s--;)c.prototype[l[s]]=s+1;return u.createObjectURL||(R=t.URL=function(t){var e,n=document.createElementNS("http://www.w3.org/1999/xhtml","a");return n.href=t,"origin"in n||("data:"===n.protocol.toLowerCase()?n.origin=null:(e=t.match(w),n.origin=e&&e[1])),n}),R.createObjectURL=function(t){var e,n=t.type;return null===n&&(n="application/octet-stream"),t instanceof o?(e="data:"+n,"base64"===t.encoding?e+";base64,"+t.data:"URI"===t.encoding?e+","+decodeURIComponent(t.data):p?e+";base64,"+p(t.data):e+","+encodeURIComponent(t.data)):d?d.call(u,t):void 0},R.revokeObjectURL=function(t){"data:"!==t.substring(0,5)&&f&&f.call(u,t)},i.append=function(t){var n=this.data;if(g&&(t instanceof b||t instanceof g)){for(var i="",a=new g(t),l=0,s=a.length;s>l;l++)i+=String.fromCharCode(a[l]);n.push(i)}else if("Blob"===e(t)||"File"===e(t)){if(!r)throw new c("NOT_READABLE_ERR");var u=new r;n.push(u.readAsBinaryString(t))}else t instanceof o?"base64"===t.encoding&&h?n.push(h(t.data)):"URI"===t.encoding?n.push(decodeURIComponent(t.data)):"raw"===t.encoding&&n.push(t.data):("string"!=typeof t&&(t+=""),n.push(unescape(encodeURIComponent(t))))},i.getBlob=function(t){return arguments.length||(t=null),new o(this.data.join(""),t,"raw")},i.toString=function(){return"[object BlobBuilder]"},a.slice=function(t,e,n){var i=arguments.length;return 3>i&&(n=null),new o(this.data.slice(t,i>1?e:this.data.length),n,this.encoding)},a.toString=function(){return"[object Blob]"},a.close=function(){this.size=0,delete this.data},n}(t);t.Blob=function(t,e){var o=e?e.type||"":"",i=new n;if(t)for(var a=0,r=t.length;r>a;a++)Uint8Array&&t[a]instanceof Uint8Array?i.append(t[a].buffer):i.append(t[a]);var c=i.getBlob(o);return!c.slice&&c.webkitSlice&&(c.slice=c.webkitSlice),c};var o=Object.getPrototypeOf||function(t){return t.__proto__};t.Blob.prototype=o(new t.Blob)}("undefined"!=typeof self&&self||"undefined"!=typeof window&&window||this.content||this);