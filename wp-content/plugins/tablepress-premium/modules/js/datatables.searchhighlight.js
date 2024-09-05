/*! SearchHighlight for DataTables v1.0.1
 * 2014 SpryMedia Ltd - datatables.net/license
 */

/* globals jQuery */

/**
 * @summary     SearchHighlight
 * @description Search term highlighter for DataTables
 * @version     1.0.1
 * @file        dataTables.searchHighlight.js
 * @author      SpryMedia Ltd (www.sprymedia.co.uk)
 * @contact     www.sprymedia.co.uk/contact
 * @copyright   Copyright 2014 SpryMedia Ltd.
 *
 * License      MIT - http://datatables.net/license/mit
 *
 * This feature plug-in for DataTables will highlight search terms in the
 * DataTable as they are entered into the main search input element, or via the
 * `search()` API method.
 *
 * It depends upon the jQuery Highlight plug-in by Bartek Szopka:
 * http://bartaz.github.io/sandbox.js/jquery.highlight.js
 *
 * Search highlighting in DataTables can be enabled by:
 *
 * * Adding the class `searchHighlight` to the HTML table
 * * Setting the `searchHighlight` parameter in the DataTables initialisation to
 *   be true
 * * Setting the `searchHighlight` parameter to be true in the DataTables
 *   defaults (thus causing all tables to have this feature) - i.e.
 *   `$.fn.dataTable.defaults.searchHighlight = true`.
 *
 * For more detailed information please see:
 *     http://datatables.net/blog/2014-10-22
 */

/*
 * jQuery Highlight plugin
 *
 * Based on highlight v3 by Johann Burkard
 * http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
 *
 * Code a little bit refactored and cleaned (in my humble opinion).
 * Most important changes:
 *  - has an option to highlight only entire words (wordsOnly - false by default),
 *  - has an option to be case sensitive (caseSensitive - false by default)
 *  - highlight element tag and class names can be specified in options
 *
 * Usage:
 *   // wrap every occurrence of text 'lorem' in content
 *   // with <span class='highlight'> (default options)
 *   $('#content').highlight('lorem');
 *
 *   // search for and highlight more terms at once
 *   // so you can save some time on traversing DOM
 *   $('#content').highlight(['lorem', 'ipsum']);
 *   $('#content').highlight('lorem ipsum');
 *
 *   // search only for entire word 'lorem'
 *   $('#content').highlight('lorem', { wordsOnly: true });
 *
 *   // don't ignore case during search of term 'lorem'
 *   $('#content').highlight('lorem', { caseSensitive: true });
 *
 *   // wrap every occurrence of term 'ipsum' in content
 *   // with <em class='important'>
 *   $('#content').highlight('ipsum', { element: 'em', className: 'important' });
 *
 *   // remove default highlight
 *   $('#content').unhighlight();
 *
 *   // remove custom highlight
 *   $('#content').unhighlight({ element: 'em', className: 'important' });
 *
 *
 * Copyright (c) 2009 Bartek Szopka
 *
 * Licensed under MIT license.
 *
 */

jQuery.extend({
	highlight(node, re, nodeName, className) {
		if (node.nodeType === 3) {
			const match = node.data.match(re);
			if (match) {
				const highlight = document.createElement(nodeName || 'span');
				highlight.className = className || 'highlight';
				const wordNode = node.splitText(match.index);
				wordNode.splitText(match[0].length);
				const wordClone = wordNode.cloneNode(true);
				highlight.appendChild(wordClone);
				wordNode.parentNode.replaceChild(highlight, wordNode);
				return 1; //skip added node in parent
			}
		} else if ((node.nodeType === 1 && node.childNodes) && // only element nodes that have children
				!/(script|style)/i.test(node.tagName) && // ignore script and style nodes
				!(node.tagName === nodeName.toUpperCase() && node.className === className)) { // skip if already highlighted
			for (let i = 0; i < node.childNodes.length; i++) {
				i += jQuery.highlight(node.childNodes[i], re, nodeName, className);
			}
		}
		return 0;
	}
});

jQuery.fn.unhighlight = function (options) {
	const settings = { className: 'highlight', element: 'span' };
	jQuery.extend(settings, options);

	return this.find(settings.element + '.' + settings.className).each(function () {
		const parent = this.parentNode;
		parent.replaceChild(this.firstChild, this);
		parent.normalize();
	}).end();
};

jQuery.fn.highlight = function (words, options) {
	const settings = { className: 'highlight', element: 'span', caseSensitive: false, wordsOnly: false };
	jQuery.extend(settings, options);

	if (words.constructor === String) {
		words = [words];
	}
	words = jQuery.grep(words, function(word/*, i*/){
		return word !== '';
	});
	words = jQuery.map(words, function(word/*, i*/) {
		return word.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
	});
	if (words.length === 0) { return this; }

	const flag = settings.caseSensitive ? '' : 'i';
	let pattern = '(' + words.join('|') + ')';
	if (settings.wordsOnly) {
		pattern = '\\b' + pattern + '\\b';
	}
	const re = new RegExp(pattern, flag);

	return this.each(function () {
		jQuery.highlight(this, re, settings.element, settings.className);
	});
};

(function(window, document, $){

	function highlight( body, table ) {
		// Removing the old highlighting first
		body.unhighlight();

		// Don't highlight the "not found" row, so we get the rows using the api
		if ( table.rows( { filter: 'applied' } ).data().length ) {
			table.columns().every( function () { // eslint-disable-line array-callback-return
				const column = this;
				column.nodes().flatten().to$().unhighlight({ className: 'column_highlight' });
				column.nodes().flatten().to$().highlight( column.search().trim().split(/\s+/), { className: 'column_highlight' } );
			} );
			body.highlight( table.search().trim().split(/\s+/) );
		}
	}

	// Listen for DataTables initialisations
	$(document).on( 'init.dt.dth', function (e, settings /*, json */) {
		if ( e.namespace !== 'dt' ) {
			return;
		}

		const table = new $.fn.dataTable.Api( settings );
		const body = $( table.table().body() );

		if (
			$( table.table().node() ).hasClass( 'searchHighlight' ) || // table has class
			settings.oInit.searchHighlight                          || // option specified
			$.fn.dataTable.defaults.searchHighlight                    // default set
		) {
			table
				.on( 'draw.dt.dth column-visibility.dt.dth column-reorder.dt.dth', function () {
					highlight( body, table );
				} )
				.on( 'destroy', function () {
					// Remove event handler
					table.off( 'draw.dt.dth column-visibility.dt.dth column-reorder.dt.dth' );
				} );

			// initial highlight for state saved conditions and initial states
			if ( table.search() ) {
				highlight( body, table );
			}
		}
	} );

})(window, document, jQuery);
