import { viewer } from './modules/viewer-core.js';
import { jQueryPDFEmbedder } from './modules/pdfEmbedder.js';

// PDF.js 2.6+ requires the worker URL to be set explicitly; otherwise it
// falls back to the in-thread pseudo-worker and logs a console warning.
if ( window.pdfjsLib && window.pdfemb_trans && window.pdfemb_trans.worker_src ) {
	window.pdfjsLib.GlobalWorkerOptions.workerSrc = window.pdfemb_trans.worker_src;
}

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

	let pdfembPagesViewer = window.PDFEMB_NS.pdfembPagesViewer;

	let pdfembPagesViewerBasic = function() {
		pdfembPagesViewer.apply( this, arguments );
	};

	pdfembPagesViewerBasic.prototype = new pdfembPagesViewer();

	window.PDFEMB_NS.pdfembPagesViewerUsable = pdfembPagesViewerBasic;

	// Convert all references to PDFs to actual viewers.
	$( '.pdfemb-viewer' ).pdfEmbedder( pdfemb_trans.cmap_url );
} );
