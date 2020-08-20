/*
Plugin Name: Media Cleaner
Description: Clean your Media Library and Uploads Folder.
Author: Jordy Meow
*/

if (WPMC_E) { // WPMC_E is supposed to be passed from PHP with wp_localize_script()
	// Since wp_localize_script() forces any values to be strings,
	// we need to cast these error-codes back into integers.
	// This issue will be fixed in a future version of WP.
	for (code in WPMC_E) WPMC_E[code] = parseInt(WPMC_E[code]);
}

const WPMC_TARGET_FILES = 'files';
const WPMC_TARGET_MEDIAS = 'media';
const WPMC_SOURCE_CONTENT = 'content';
const WPMC_SOURCE_MEDIAS = 'media';

var wpmc = null; // Global Context

function wpmc_pop_array(items, count) {
	var newItems = [];
	while ( newItems.length < count && items.length > 0 ) {
		newItems.push( items.pop() );
	}
	return newItems;
}

/**
 *
 * RECOVER
 *
 */

function wpmc_recover() {
	var items = [];
	jQuery('#wpmc-table input:checked').each(function (index) {
		if (jQuery(this)[0].value != 'on') {
			items.push(jQuery(this)[0].value);
		}
	});
	wpmc_recover_do(items, items.length);
}

function wpmc_recover_all() {
	var items = [];
	var data = { action: 'wpmc_get_all_deleted' };
	jQuery.post(ajaxurl, data, function (response) {
		reply = jQuery.parseJSON(response);
		if ( !reply.success ) {
			alert( reply.message );
			return;
		}
		wpmc_recover_do(reply.results.ids, reply.results.ids.length);
	});
}

function wpmc_recover_do(items, totalcount) {
	wpmc_update_progress(totalcount - items.length, totalcount);
	if (items.length > 0) {
		newItems = wpmc_pop_array(items, 5);
		data = { action: 'wpmc_recover_do', data: newItems };
	}
	else {
		jQuery('#wpmc_pause').hide();
		jQuery('#wpmc_progression').html("Done. Please <a href='?page=media-cleaner'>refresh</a> this page.");
		return;
	}
	jQuery.post(ajaxurl, data, function (response) {
		reply = jQuery.parseJSON(response);
		if ( !reply.success ) {
			alert( reply.message );
			return;
		}
		wpmc_recover_do(items, totalcount);
	});
}

/**
 *
 * DELETE
 *
 */

function wpmc_ignore() {
	var items = [];
	jQuery('#wpmc-table input:checked').each(function (index) {
		if (jQuery(this)[0].value != 'on') {
			items.push(jQuery(this)[0].value);
		}
	});
	wpmc_ignore_do(items, items.length);
}

function wpmc_delete() {
	var items = [];
	jQuery('#wpmc-table input:checked').each(function (index) {
		if (jQuery(this)[0].value != 'on') {
			items.push(jQuery(this)[0].value);
		}
	});
	wpmc_delete_do(items, items.length);
}

function wpmc_delete_all(isTrash, filter = {}) {
	var data = {
		action: 'wpmc_get_all_issues',
		isTrash: isTrash ? 1 : 0,
		filter: filter
	};

	jQuery.post(ajaxurl, data, function (response) {
		reply = jQuery.parseJSON(response);
		if ( !reply.success ) {
			alert( reply.message );
			return;
		}
		wpmc_delete_do(reply.results.ids, reply.results.ids.length);
	});
}

function wpmc_update_progress(current, totalcount, isDeleting) {
	if (isDeleting === undefined)
  	isDeleting = false;
	var action = isDeleting ? "Deleting" : "Analyzing";
	jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-play"></span> ' + 
		action + ' ' + current + "/" + totalcount + " (" + Math.round(current / totalcount * 100) + "%)");
}

function wpmc_delete_do(items, totalcount) {
	wpmc_update_progress(totalcount - items.length, totalcount, true);
	if (items.length > 0) {
		newItems = wpmc_pop_array(items, 5);
		data = { action: 'wpmc_delete_do', data: newItems };
	}
	else {
		jQuery('#wpmc_progression').html("Done. Please <a href='?page=media-cleaner'>refresh</a> this page.");
		return;
	}
	jQuery.post(ajaxurl, data, function (response) {
		reply = jQuery.parseJSON(response);
		if ( !reply.success ) {
			alert( reply.message );
			return;
		}
		wpmc_delete_do(items, totalcount);
	});
}

function wpmc_ignore_do(items, totalcount) {
	wpmc_update_progress(totalcount - items.length, totalcount);
	if (items.length > 0) {
		newItems = wpmc_pop_array(items, 5);
		data = { action: 'wpmc_ignore_do', data: newItems };
	}
	else {
		jQuery('#wpmc_progression').html("Done. Please <a href='?page=media-cleaner'>refresh</a> this page.");
		return;
	}
	jQuery.post(ajaxurl, data, function (response) {
		reply = jQuery.parseJSON(response);
		if ( !reply.success ) {
			alert( reply.message );
			return;
		}
		wpmc_ignore_do(items, totalcount);
	});
}

/**
 *
 * SCAN
 *
 */

/**
 * Creates a context object that preserves various states and variables for scanning
 * @return {object}
 * 
 * TODO: Make this an actual class
 * TODO: Create 'Phase' class as the base class of each kind of phase
 */
function wpmc_new_context() {
	return {
		dirs: [],
		files: [],
		medias: [],
		current: 0,
		total: 0,
		issues: 0,
		isPause: false,
		isPendingPause: false,
		currentPhase: null,
		errorHandler: new ErrorHandler(),

		phases: {
			extractReferencesFromPosts: {
				init: function () {
					this.progress = 0; // Scanned posts count
					this.progressPrev = 0;
					this.progressMax = 0;
					return this;
				},
				run: function () {
					var me = this;
					var src = WPMC_SOURCE_CONTENT;
					
					if (!this.progressMax) {
						jQuery.ajax(ajaxurl, {
							type: 'POST',
							dataType: 'text',
							data: {
								action: 'wpmc_get_num_posts',
								source: src
							},
							timeout: wpmc_cfg.timeout + 5000
							
						}).done(function (result) {
							result = wpmc_parse_response(result);
							me.progressMax = result.data.num;
							wpmc_extract_references_from(src);
							
						}).fail(function (e) {
							wpmc.errorHandler.handle(e.statusText, e.status);
						});
						
						return;
					}
					
					if (this.progress >= this.progressMax) {
						wpmc.currentPhase = this.nextPhase().init();
						wpmc.currentPhase.run();
						return;
					}
					
					wpmc_extract_references_from(src);
				},
				pause: function () {
					jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-pause"></span> Paused at preparing posts (' + 
						this.progress + ' posts)');
				},
				skip: function () {
					this.progress += wpmc_cfg.postsBuffer;
				},
				nextPhase: function () {
					return wpmc.phases.extractReferencesFromMedias;
				}
			},
			extractReferencesFromMedias: {
				init: function () {
					this.progress = 0; // Scanned posts count
					this.progressPrev = 0;
					this.progressMax = 0;
					return this;
				},
				run: function () {
					var me = this;
					var src = WPMC_SOURCE_MEDIAS;
					
					if (!this.progressMax) {
						jQuery.ajax(ajaxurl, {
							type: 'POST',
							dataType: 'text',
							data: {
								action: 'wpmc_get_num_posts',
								source: src
							},
							timeout: wpmc_cfg.timeout + 5000
							
						}).done(function (result) {
							result = wpmc_parse_response(result);
							me.progressMax = result.data.num;
							wpmc_extract_references_from(src);
							
						}).fail(function (e) {
							wpmc.errorHandler.handle(e.statusText, e.status);
						});
						
						return
					}
					
					if (this.progress >= this.progressMax) {
						wpmc.currentPhase = this.nextPhase().init();
						wpmc.currentPhase.run();
						return;
					}

					wpmc_extract_references_from(src);
				},
				pause: function () {
					jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-pause"></span> Paused at preparing medias (' + 
						this.progress + ' medias)');
				},
				skip: function () {
					this.progress += wpmc_cfg.mediasBuffer;
				},
				nextPhase: function () {
					return wpmc.phases.retrieveTargetsToCheck;
					console.error('Configuration Error'); // This shouldn't happen
				}
			},
			retrieveTargetsToCheck: {
				init: function () {
					this.progress = 0; // Scanned media count
					this.progressPrev = 0;
					return this;
				},
				run: function () {
					wpmc_retrieve_targets_for(this.method, this.method === WPMC_TARGET_FILES ? this.progress : null, 
						this.method === WPMC_TARGET_MEDIAS ? this.progress : 0);
				},
				pause: function () {
					jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-pause"></span> Paused at preparing media (' + 
						this.progress + ' media)');
				},
				skip: function () {
					this.progress += wpmc_cfg.mediasBuffer;
				},
				nextPhase: function () {
					return wpmc.phases.analyze;
				}
			},
			analyze: {
				init: function () {
					this.currentFiles = [];
					this.currentMedia = [];
					return this;
				},
				run: function () {
					wpmc_check_targets();
				},
				pause: function () {
					var current = wpmc.total - (wpmc.files.length + wpmc.medias.length);
					var totalcount = wpmc.total;
					jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-pause"></span> Paused at ' + 
						current + "/" + totalcount + " (" + Math.round(current / totalcount * 100) + "%)");
				},
				skip: function () {
					if (wpmc.files.length)
						this.currentFiles = wpmc_pop_array(wpmc.files, wpmc_cfg.analysisBuffer);
					if (wpmc.medias.length)
						this.currentMedia = wpmc_pop_array(wpmc.medias, wpmc_cfg.analysisBuffer);
				},
				rollback: function () {
					wpmc.files = wpmc.files.concat(this.currentFiles.reverse()); // @see wpmc_pop_array
					this.currentFiles = [];
					wpmc.medias = wpmc.medias.concat(this.currentMedia.reverse()); // @see wpmc_pop_array
					this.currentMedia = [];
				}
			}
		}
	};
}

// Extract References from Posts

function wpmc_extract_references_from(source) {
	if (!wpmc.currentPhase) return; // Aborted
	if (wpmc.isPendingPause)
		return wpmc_update_to_pause();
	setTimeout(
		function() {
			if (!wpmc.currentPhase) return; // Aborted
			jQuery('#wpmc_progression').html('<span class="dashicons dashicons-portfolio"></span> Extract references (' + 
				wpmc.currentPhase.progress + ' ' + source + ')...');
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'text',
				data: {
					action: 'wpmc_extract_references',
					source: source,
					limit: wpmc.currentPhase.progress
				},
				timeout: wpmc_cfg.timeout + 5000 // Extra 5sec for fail-safe
			}).done(function (response) {
				if (!wpmc.currentPhase) return; // Aborted
				var reply = wpmc_parse_response(response);
				if (!reply.success)
					return wpmc.errorHandler.handle(reply.message);
				if (!reply.finished) {
					wpmc.currentPhase.progressPrev = wpmc.currentPhase.progress;
					wpmc.currentPhase.progress = reply.limit;
				}
				else wpmc.currentPhase = wpmc.currentPhase.nextPhase().init();
				return wpmc.currentPhase.run();
			}).fail(function (e) { // Server Error
				wpmc.errorHandler.handle(e.statusText, e.status);
			});
		}, wpmc_cfg.delay
	);
}

function wpmc_retrieve_targets_for(target, path = null, limit = 0) {
	if (!wpmc.currentPhase) return; // Aborted

	if (wpmc.isPendingPause)
		return wpmc_update_to_pause();

	if (path) {
		elpath = path.replace(/^.*[\\\/]/, '');
		jQuery('#wpmc_progression').html('<span class="dashicons dashicons-portfolio"></span> Preparing files (' + elpath + ')...');
	}
	else if (target === WPMC_TARGET_MEDIAS)
		jQuery('#wpmc_progression').html('<span class="dashicons dashicons-admin-media"></span> Preparing medias (' + limit + ' medias)...');
	else if (target === WPMC_TARGET_FILES)
		jQuery('#wpmc_progression').html('<span class="dashicons dashicons-portfolio"></span> Preparing files...');
	else
		jQuery('#wpmc_progression').html('<span class="dashicons dashicons-portfolio"></span> Preparing...');

	setTimeout(
		function() {
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'text',
				data: {
					action: 'wpmc_retrieve_targets',
					path: path,
					limit: limit
				},
				timeout: wpmc_cfg.timeout + 5000 // Extra 5sec for fail-safe

			}).done(function (response) {
				if (!wpmc.currentPhase) return; // Aborted

				var reply = wpmc_parse_response(response);
				var method = reply.method;
				wpmc.currentPhase.method = method;

				if (!reply.success)
					return wpmc.errorHandler.handle(reply.message);

				// Store results
				for (var i = 0, len = reply.results.length; i < len; i++) {
					var r = reply.results[i];
					if (method === WPMC_TARGET_FILES) {
						if ( r.type === 'dir' )
							wpmc.dirs.push( r.path );
						else if ( r.type === 'file' ) {
							wpmc.files.push( r.path );
							wpmc.total++;
						}
					}
					else if (method === WPMC_TARGET_MEDIAS) {
						wpmc.medias.push( r );
						wpmc.total++;
					}
				}

				// Next query
				if (method === WPMC_TARGET_MEDIAS) {
					if (!reply.finished) {
						wpmc.currentPhase.progressPrev = wpmc.currentPhase.progress;
						wpmc.currentPhase.progress = reply.limit;
					}
					else
						wpmc.currentPhase = wpmc.currentPhase.nextPhase().init();
				}
				else if (method === WPMC_TARGET_FILES) {
					var dir = wpmc.dirs.pop();
					if (dir) {
						wpmc.currentPhase.progressPrev = wpmc.currentPhase.progress;
						wpmc.currentPhase.progress = dir;
					}
					else
						wpmc.currentPhase = wpmc.currentPhase.nextPhase().init();
				}
				wpmc.currentPhase.run();

			}).fail(function (e) { // Server Error
				wpmc.errorHandler.handle(e.statusText, e.status);
			});

		}, wpmc_cfg.delay
	);
}

function wpmc_pause() {
	if (wpmc.isPause) { // Resume
		jQuery('#wpmc_pause').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-pause"></span>Pause');
		wpmc.isPause = false;
		wpmc.currentPhase.run();
	}
	else if (wpmc.isPendingPause) {
		wpmc.isPendingPause = false;
	}
	else {
		jQuery('#wpmc_pause').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-pause"></span>Pausing...');
		wpmc.isPendingPause = true;
	}
}

function wpmc_update_to_pause() {
	if (wpmc.isPendingPause) {
		wpmc.currentPhase.pause();
		jQuery('#wpmc_pause').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-play"></span>Continue');
		wpmc.isPendingPause = false;
		wpmc.isPause = true;
	}
}

/**
 * Parses a Ajax response into a valid JSON
 * @param {string} response The response content to parse
 * @return {object} The parsed result
 */
function wpmc_parse_response(response) {
	if (typeof response == 'object') return response;

	var r;
	try {
		r = jQuery.parseJSON(response);
	} catch (e) {
		r = null;
	}
	if (!r) { // Couldn't parse as a valid JSON
		r = {
			success: false,
			message: 'The reply from the server is broken. The reply will be displayed in your Javascript console. You should also check your PHP Error Logs.'
		};
		console.error('Media File Cleaner got a broken reply from the server:', response);
	}
	return r;
}

function wpmc_update_to_error() {
	var current = wpmc.total - (wpmc.files.length + wpmc.medias.length);
	var totalcount = wpmc.total;
	jQuery('#wpmc_progression').html('<span class="dashicons dashicons-controls-pause"></span> Error at ' + current + "/" + totalcount + " (" + Math.round(current / totalcount * 100) + "%): " + error);
	jQuery('#wpmc_pause').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-play"></span>Retry');
	wpmc.isPendingPause = false;
	wpmc.isPause = true;
}

function wpmc_check_targets() {
	if (!wpmc.currentPhase) return; // Aborted
	if (wpmc.isPendingPause)
		return wpmc_update_to_pause();

	wpmc_update_progress(wpmc.total - (wpmc.files.length + wpmc.medias.length), wpmc.total);
	var data = {};
	var expectedSuccess = 0;
	if (wpmc.files.length > 0) {
		wpmc.currentPhase.currentFiles = wpmc_pop_array(wpmc.files, wpmc_cfg.analysisBuffer);
		expectedSuccess = wpmc.currentPhase.currentFiles.length;
		data = { action: 'wpmc_check_targets', method: 'file', data: wpmc.currentPhase.currentFiles };
	}
	else if (wpmc.medias.length > 0) {
		wpmc.currentPhase.currentMedia = wpmc_pop_array(wpmc.medias, wpmc_cfg.analysisBuffer);
		expectedSuccess = wpmc.currentPhase.currentMedia.length;
		data = { action: 'wpmc_check_targets', method: 'media', data: wpmc.currentPhase.currentMedia };
	}
	else {
		jQuery('#wpmc_progression').html("");
		var issueMsg = 'No issues were found.';
		if (wpmc.issues > 1)
			issueMsg = wpmc.issues + ' issues were found.';
		else if (wpmc.issues === 1)
			issueMsg = 'Only 1 issue was found.';
		jQuery('#wpmc-table').html('<div style="padding: 20px; text-align: center;"><p style="font-size: 14px; margin: 0px 0px 10px 0px;">' + 
			issueMsg + '</p><a class="button-primary" href="?page=media-cleaner">Display the results</a></div>');
		wpmc = wpmc_new_context(); // Reset the context
		jQuery('#wpmc_pause').hide();
		jQuery('#wpmc_scan').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-search"></span>Start Scan');
		jQuery('#wpmc_actions').trigger('idle');
		//location.reload();
		return;
	}

	setTimeout(
		function () {
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'text',
				data: data,
				timeout: wpmc_cfg.timeout + 5000 // Extra 5sec for fail-safe
			}).done(function (response) {
				var reply = wpmc_parse_response(response);
				if (!reply.success)
					return wpmc.errorHandler.handle(reply.message);
				wpmc.issues += expectedSuccess - reply.results;
				wpmc_check_targets();
			}).fail(function (e) { // Server Error
				wpmc.errorHandler.handle(e.statusText, e.status);
			});
		}, wpmc_cfg.delay
	);
}

/**
 * Opens a dialog
 * @param {object} content
 * @param {string} content.title=null The title of the dialog
 * @param {string} content.head=null The heading
 * @param {string} content.body=null The body
 * @param {jQuery} content.prepend=null Additional element to prepend
 * @param {jQuery} content.append=null Additional element to append
 * @return {jQuery} The dialog element
 */
function wpmc_open_dialog(content) {
	var dialog = jQuery('#wpmc-dialog');
	dialog.html('');
	if (content.title)
		dialog.dialog('option', 'title', content.title);
	if (content.head)
		dialog.append('<h3 class="head">' + content.head + '</h3>');
	if (content.body)
		dialog.append('<div class="body">' + content.body + '</div>');
	if (content.prepend)
		dialog.prepend(content.prepend);
	if (content.append)
		dialog.append(content.append);
	dialog.dialog('open');
	return dialog;
}

/**
 * @constructor
 */
function ErrorHandler() {
	this.mode = 'ASK'; // 'RETRY', 'SKIP'
	this.MAX_RETRY = 30; // 30;
	this.RETRY_DELAY = 5; // in sec
	this.nRetried = 0;
	this.nSkipped = 0;
}
ErrorHandler.prototype.setMode = function (mode) {
	this.mode = mode;
	return this;
};
ErrorHandler.prototype.handle = function (msg = null, status = null) {
	var me = this;
	switch (this.mode) {
	case 'RETRY':
		if (this.nRetried >= this.MAX_RETRY - 1) {
			// Too many retries, switch back to ASK mode
			console.warn('Auto-Retry is turned off due to too many fruitless attempts');
			this.setMode('ASK');
			this.nRetried = 0;
		}
		// Retry after few seconds
		console.log('Retry starts in ' + this.RETRY_DELAY + ' seconds...');
		setTimeout(function () {
			me.nRetried++;
			wpmc.currentPhase.run();
			
		}, this.RETRY_DELAY * 1000);
		break;
	case 'SKIP':
		wpmc.currentPhase.skip();
		this.nSkipped++;
		console.warn(this.nSkipped + ' ' + (this.nSkipped > 1 ? 'errors are' : 'error is') + ' ignored automatically');
		wpmc.currentPhase.run();
		break;
	default: // ASK
		wpmc.isPendingPause = true;
		wpmc_update_to_pause();
		
		// Show Error Dialog
		var errDialog = jQuery('#wpmc-error-dialog');
		if (!msg) msg = 'An error happened'; // Default error message
		if (status) {
			console.error('Media Cleaner got an error from server:', status + ' ' + msg);
			msg = '<span class="error-status">' + status + '</span> ' + msg;
		} else
			console.error(msg);
		
		errDialog.find('h3').html(msg);
		errDialog.dialog('open');
	}
};

/**
 *
 * INIT
 *
 */
(function ($) {
	// Initialize Global Context
	wpmc = wpmc_new_context();

	// Bulk Selection
	$('#wpmc-cb-select-all').on('change', function (cb) {
		$('#wpmc-table input').prop('checked', cb.target.checked);
	});

	// Actions
	(function () {
		var wrap = $('#wpmc_actions');

		// Events: Busy, Idle
		wrap.on('busy', function () {
			wrap.addClass('busy');
			wrap.find('a.exclusive').addClass('disabled');
			wrap.find('input.exclusive').attr('disabled', true);

		}).on('idle', function () {
			wrap.find('a.exclusive').removeClass('disabled');
			wrap.find('input.exclusive').attr('disabled', false);
			wrap.removeClass('busy');
		});

		// Scan Button
		wrap.find('#wpmc_scan').on('click', function (ev) {
			ev.preventDefault();
			if (wpmc.currentPhase) { // Abort scan
				wpmc = wpmc_new_context(); // Reset the current context
				$('#wpmc_pause').hide();
				$('#wpmc_pause').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-controls-pause"></span>Pause');
				$('#wpmc_progression').text('');
				$('#wpmc_scan').html('<span style="top: 3px; position: relative; left: -5px;" class="dashicons dashicons-search"></span>Start Scan');
				wrap.trigger('idle');
				return;
			}
			wrap.trigger('busy');
			$('#wpmc_scan').html('Stop Scan');
			$('#wpmc_pause').show();
			wpmc.currentPhase = wpmc.phases.extractReferencesFromPosts.init();
			wpmc.currentPhase.run();
		});

		// Delete Button
		wrap.find('#wpmc_delete').on('click', function (ev) {
			ev.preventDefault();
			if ($(this).hasClass('disabled')) return;
			wpmc_delete();
		});

		// Delete All (Search Results) Button
		wrap.find('#wpmc_delete_all').on('click', function (ev) {
			ev.preventDefault();
			var $this = $(this);
			if ($this.hasClass('disabled')) return;
			var filter = $this.data('filter') || '';
			var search = $this.data('search') || '';
			if (search != wrap.find('.search-box input[name="s"]').val()) {
				var dialog = wpmc_open_dialog({
					title: 'Warning',
					body: 'You modified the search terms without clicking on the <i>Search</i> button to refresh the results. Therefore, the <b>current</b> results will be deleted. Do you want to continue?',
					append: $('<div class="prompt">')
						.append(
							// Cancel Button
							$('<a class="button cancel" href="#">Cancel</a>').on('click', function (ev) {
								ev.preventDefault();
								dialog.dialog('close');
							})
						).append(
							// Continue Button
							$('<a class="button button-primary continue" href="#">Continue</a>').on('click', function (ev) {
								ev.preventDefault();
								dialog.dialog('close');
								wpmc_delete_all(false, { filter, search });
							})
						)
				});
				return;
			}
			else {
				var dialog = wpmc_open_dialog({
					title: 'Delete all',
					body: '<b>All the issues will be moved to the trash</b>. Please make sure you have a backup. Do you want to continue?',
					append: $('<div class="prompt">')
						.append(
							// Cancel Button
							$('<a class="button cancel" href="#">Cancel</a>').on('click', function (ev) {
								ev.preventDefault();
								dialog.dialog('close');
							})
						).append(
							// Continue Button
							$('<a class="button button-primary continue" href="#">Continue</a>').on('click', function (ev) {
								ev.preventDefault();
								dialog.dialog('close');
								wpmc_delete_all(false, { filter, search });
							})
						)
				});
				return;
			}
			//wpmc_delete_all(false, filter);
		});

		// Ignore Button
		wrap.find('#wpmc_ignore').on('click', function (ev) {
			ev.preventDefault();
			if ($(this).hasClass('disabled')) return;
			wpmc_ignore();
		});

		// Recover All Button
		wrap.find('#wpmc_recover_all').on('click', function (ev) {
			ev.preventDefault();
			if ($(this).hasClass('disabled')) return;
			wpmc_recover_all();
		});

		// Empty Trash Button
		wrap.find('#wpmc_empty_trash').on('click', function (ev) {
			ev.preventDefault();
			if ($(this).hasClass('disabled')) return;
			wpmc_delete_all(true);
		});

		// Reset Button
		wrap.find('#wpmc_reset').on('click', function (ev) {
			ev.preventDefault();
			var $this = $(this);
			var dialog = wpmc_open_dialog({
				title: "Reset",
				body:  "The information related to the Media Cleaner's trash, latest scan and ignored entries will be lost. Do you want to continue?",
				append: $('<div class="prompt">')
					.append(
						// Cancel Button
						$('<a class="button cancel" href="#">Cancel</a>').on('click', function (ev) {
							ev.preventDefault();
							dialog.dialog('close');
						})
					).append(
						// Continue Button
						$('<a class="button button-primary continue" href="#">Continue</a>').on('click', function (ev) {
							ev.preventDefault();
							dialog.dialog('close');
							location.href = $this.attr('href');
						})
					)
			});
		});
		
		// "Enable MEDIA_TRASH" Button
		$('#wpmc_enable_media_trash').on('click', function (ev) {
			var self = $(this);
			var action = 'wpmc_define';
			
			$.ajax(ajaxurl, {
				type: 'POST',
				dataType: 'text',
				data: {
					action: action,
					nonce:  WPMC_NONCES[action],
					name:   'MEDIA_TRASH',
					value:  1
				}
			
			}).done(function (result) {
				result = wpmc_parse_response(result);
				if (!result.success) {
					console.error(result.data.message);
					var msg = {
						title: 'Warning',
						head:  '',
						body:  result.data.message
					};
					switch (result.data.code) {
					case WPMC_E['INVALID_NONCE']:
						msg.head = "Invalid Request";
						msg.body = "Something wrong is going on here.<br>Please try it again after reloading the page";
						break;
					case WPMC_E['FILE_OPEN_FAILURE']:
						msg.head = "Cannot open wp-config.php";
						msg.body = "You still have a chance to enable it by manually editing wp-config.php";
						break;
					case WPMC_E['FILE_WRITE_FAILURE']:
						msg.head = "Cannot modify wp-config.php";
						msg.body = "You still have a chance to enable it by manually editing wp-config.php";
						break;
					}
					wpmc_open_dialog(msg);
					return;
				}
				self.closest('.notice').remove();
				
			}).fail(function (e) {
				console.error(e.status + " " + e.statusText);
				var msg = {
					title: "Request Error",
					head:  e.status + " " + e.statusText,
					body:  "You still have a chance to enable it by manually editing wp-config.php"
				};
				wpmc_open_dialog(msg);
			});
		});
	})();

	// Dialog
	(function () {
		var dialog = $('#wpmc-dialog');

		dialog.dialog({
			dialogClass: 'wp-dialog',
			autoOpen: false,
			draggable: true,
			width: 'auto',
			modal: true,
			resizable: false,
			closeOnEscape: true,
			position: {
				my: "center",
				at: "center",
				of: window
			},
			open: function () {
				// Close dialog by clicking the overlay behind it
				$('.ui-widget-overlay').on('click', function () {
					dialog.dialog('close');
				});
			},
			create: function () {
				// Style fix for WordPress admin
				$('.ui-dialog-titlebar-close').addClass('ui-button');
			}
		});
	})();

	// Error Dialog
	(function () {
		var errDialog = $('#wpmc-error-dialog');

		errDialog.dialog({
			title: 'ERROR',
			dialogClass: 'wp-dialog',
			autoOpen: false,
			draggable: true,
			width: 'auto',
			modal: true,
			resizable: false,
			closeOnEscape: true,
			position: {
				my: "center",
				at: "center",
				of: window
			},
			open: function () {
				$('.ui-widget-overlay').on('click', function () {
					errDialog.dialog('close');
				});
			},
			create: function () {
				$('.ui-dialog-titlebar-close').addClass('ui-button');
			}
		});

		// Retry
		errDialog.find('a.retry').on('click', function (ev) {
			ev.preventDefault();

			wpmc_pause(); // Resume
			errDialog.dialog('close');
		});

		// Skip (Ignore error)
		errDialog.find('a.skip').on('click', function (ev) {
			ev.preventDefault();

			wpmc.currentPhase.skip();
			console.warn('1 error is ignored by you');

			wpmc_pause(); // Resume
			errDialog.dialog('close');
		});

		// Always Retry
		errDialog.find('a.always-retry').on('click', function (ev) {
			ev.preventDefault();
			
			wpmc.errorHandler.setMode('RETRY');

			wpmc_pause(); // Resume
			errDialog.dialog('close');
		});
		
		// Skip All
		errDialog.find('a.skip-all').on('click', function (ev) {
			ev.preventDefault();

			wpmc.errorHandler.setMode('SKIP');
			
			wpmc_pause(); // Resume
			errDialog.dialog('close');
		});

	})();

})(jQuery);
