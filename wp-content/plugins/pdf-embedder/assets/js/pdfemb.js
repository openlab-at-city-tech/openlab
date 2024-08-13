import { viewer } from './modules/viewer-core.js';
import { jQueryPDFEmbedder } from './modules/pdfEmbedder.js';

window.PDFEMB_NS = viewer;

window.PDFEMB_NS.pdfembGetPDF = function( url, callback ) {
	callback( url, false );
};

/**
 * Register a jQuery plugin.
 */
jQuery.fn.pdfEmbedder = jQueryPDFEmbedder;

/**
 * Render PDFs on a page.
 */
jQuery( document ).ready( function( $ ) {

	var pdfembPagesViewer = window.PDFEMB_NS.pdfembPagesViewer;

	var pdfembPagesViewerBasic = function() {
		pdfembPagesViewer.apply( this, arguments );
	};

	pdfembPagesViewerBasic.prototype = new pdfembPagesViewer();

	window.PDFEMB_NS.pdfembPagesViewerUsable = pdfembPagesViewerBasic;

	// Convert all references to PDFs to actual viewers.
	$( '.pdfemb-viewer' ).pdfEmbedder( pdfemb_trans.cmap_url );
} );
