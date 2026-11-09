/**
 * Glossary settings subpage.
 *
 * The terms table is populated via admin-ajax on load (trp_glossary_get_terms),
 * then rendered client-side from an in-memory list with search filtering and
 * 20-per-page pagination. Add / inline quick-edit / delete all update that list
 * and re-render. State changes are persisted through admin-ajax.
 */
( function ( $ ) {
    'use strict';

    if ( typeof trp_glossary_data === 'undefined' ) {
        return;
    }

    var data        = trp_glossary_data;
    var $response   = $( '#trp-glossary-ajax-response' );
    var $tableBody  = $( '#the-list' );
    var $addButton  = $( '#trp-glossary-add-term' );
    var $addSpinner = $( '.trp-glossary-spinner' );

    var $searchInput  = $( '#trp-glossary-search-input' );
    var $searchButton = $( '#trp-glossary-search-submit' );

    var $tablenavs       = $( '.trp-glossary-tablenav' );
    var $paginationPages = $tablenavs.find( '.tablenav-pages' );

    var PER_PAGE = 20;
    var allTerms = [];   // [{ term, translations }]
    var query    = '';
    var page     = 1;

    /* ---------------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------------- */

    function escapeHtml( text ) {
        return $( '<div/>' ).text( text == null ? '' : text ).html();
    }

    function showNotice( type, message ) {
        var cssClass = type === 'success' ? 'notice-success' : 'notice-error';
        var $notice = $(
            '<div class="notice ' + cssClass + ' is-dismissible" role="alert"><p></p>' +
            '<button type="button" class="notice-dismiss"></button></div>'
        );
        $notice.find( 'p' ).text( message );
        $response.html( $notice );
        $notice.on( 'click', '.notice-dismiss', function () {
            $notice.remove();
        } );
        return $notice;
    }

    /**
     * Build the translations cell content, e.g. "<strong>German:</strong> term".
     */
    function buildTranslationsHtml( translations ) {
        var parts = [];
        $.each( translations || {}, function ( code, value ) {
            if ( ! value ) {
                return;
            }
            var label = data.language_names[ code ] || code;
            parts.push( '<strong>' + escapeHtml( label ) + ':</strong> ' + escapeHtml( value ) );
        } );
        return parts.join( '<br />' );
    }

    /**
     * Build a full term row, matching the server-rendered markup.
     */
    function buildRow( term, translations ) {
        var $row = $( '<tr/>' )
            .attr( 'data-term', term )
            .attr( 'data-translations', JSON.stringify( translations || {} ) );

        var $termCell = $( '<td class="column-primary"/>' ).attr( 'data-colname', data.columns.term );
        $( '<span class="trp-glossary-term-text"/>' ).text( term ).appendTo( $termCell );
        $(
            '<div class="row-actions">' +
            '<span class="edit"><button type="button" class="button-link trp-glossary-edit"></button> | </span>' +
            '<span class="delete"><button type="button" class="button-link trp-glossary-delete"></button></span>' +
            '</div>'
        ).appendTo( $termCell );
        $termCell.find( '.trp-glossary-edit' ).text( data.i18n.edit );
        $termCell.find( '.trp-glossary-delete' ).text( data.i18n.delete );

        var $translationsCell = $( '<td class="trp-glossary-translations-cell"/>' )
            .attr( 'data-colname', data.columns.translations )
            .html( buildTranslationsHtml( translations ) );

        return $row.append( $termCell ).append( $translationsCell );
    }

    /* ---------------------------------------------------------------------
     * Rendering + pagination
     * ------------------------------------------------------------------- */

    /**
     * The terms matching the current search query (all terms if none).
     */
    function getFiltered() {
        if ( ! query ) {
            return allTerms;
        }
        var q = query.toLowerCase();
        return allTerms.filter( function ( item ) {
            var haystack = ( item.term || '' );
            $.each( item.translations || {}, function ( code, value ) {
                haystack += ' ' + value;
            } );
            return haystack.toLowerCase().indexOf( q ) !== -1;
        } );
    }

    /**
     * One first/prev/next/last control (anchor when usable, disabled span otherwise).
     */
    function navControl( cssClass, symbol, label, enabled ) {
        if ( enabled ) {
            return '<a class="' + cssClass + ' button" href="#">' +
                '<span class="screen-reader-text">' + escapeHtml( label ) + '</span>' +
                '<span aria-hidden="true">' + symbol + '</span></a>';
        }
        return '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">' + symbol + '</span>';
    }

    /**
     * Build the WP-style .tablenav-pages markup.
     */
    function paginationHtml( totalItems, totalPages ) {
        var html = '<span class="displaying-num">' + totalItems + ' ' + escapeHtml( data.i18n.items ) + '</span>';
        html += '<span class="pagination-links">';
        html += navControl( 'first-page', '«', data.i18n.first_page, page > 1 );
        html += navControl( 'prev-page', '‹', data.i18n.prev_page, page > 1 );
        html += '<span class="paging-input"><span class="tablenav-paging-text">' +
            page + ' ' + escapeHtml( data.i18n.of ) + ' <span class="total-pages">' + totalPages + '</span></span></span>';
        html += navControl( 'next-page', '›', data.i18n.next_page, page < totalPages );
        html += navControl( 'last-page', '»', data.i18n.last_page, page < totalPages );
        html += '</span>';
        return html;
    }

    /**
     * Re-render the table body and pagination from current state.
     */
    function render() {
        var filtered   = getFiltered();
        var totalItems = filtered.length;
        var totalPages = Math.max( 1, Math.ceil( totalItems / PER_PAGE ) );

        if ( page > totalPages ) { page = totalPages; }
        if ( page < 1 ) { page = 1; }

        $tableBody.empty();

        if ( totalItems === 0 ) {
            var $empty = $( '<tr class="no-items"><td class="colspanchange" colspan="2"></td></tr>' );
            $empty.find( 'td' ).text( query ? data.i18n.no_matches : data.i18n.no_items );
            $tableBody.append( $empty );
        } else {
            var start = ( page - 1 ) * PER_PAGE;
            filtered.slice( start, start + PER_PAGE ).forEach( function ( item ) {
                $tableBody.append( buildRow( item.term, item.translations ) );
            } );
        }

        // Pagination controls only when there is more than one page (i.e. > 20 rows).
        if ( totalPages > 1 ) {
            $paginationPages.html( paginationHtml( totalItems, totalPages ) );
            $tablenavs.show();
        } else {
            $paginationPages.empty();
            $tablenavs.hide();
        }
    }

    function goToPage( target ) {
        page = target;
        render();
    }

    /**
     * Index of a term in allTerms (exact match), or -1.
     */
    function indexOfTerm( term ) {
        for ( var i = 0; i < allTerms.length; i++ ) {
            if ( allTerms[ i ].term === term ) {
                return i;
            }
        }
        return -1;
    }

    /**
     * Load the glossary terms via AJAX and render the table.
     */
    function loadTerms() {
        $.post( data.admin_ajax, {
            action: 'trp_glossary_get_terms',
            nonce: data.get_terms_nonce
        } ).done( function ( response ) {
            allTerms = ( response && response.success && response.data && response.data.terms )
                ? response.data.terms
                : [];
            render();
        } ).fail( function () {
            allTerms = [];
            showNotice( 'error', data.i18n.load_error );
            render();
        } );
    }

    /* ---------------------------------------------------------------------
     * Quick edit
     * ------------------------------------------------------------------- */

    function closeQuickEdit() {
        $tableBody.find( 'tr.trp-glossary-quick-edit' ).each( function () {
            var $editRow = $( this );
            $editRow.data( 'origRow' ).show();
            $editRow.remove();
        } );
    }

    function buildEditField( code, name, value ) {
        var $line = $( '<div class="trp-glossary-quick-edit-field"/>' );
        $( '<label/>' ).text( name ).appendTo( $line );
        $( '<input type="text" class="trp-glossary-edit-input"/>' )
            .attr( 'data-lang', code )
            .val( value || '' )
            .appendTo( $line );
        return $line;
    }

    function openQuickEdit( $row ) {
        closeQuickEdit();

        var term = $row.attr( 'data-term' );
        var translations = {};
        try {
            translations = JSON.parse( $row.attr( 'data-translations' ) || '{}' );
        } catch ( e ) {
            translations = {};
        }

        var $wrap = $( '<div class="trp-glossary-quick-edit-wrap"/>' );
        $( '<h4/>' ).text( data.i18n.quick_edit ).appendTo( $wrap );

        $wrap.append( buildEditField( data.default_language.code, data.columns.term, term ) );
        $.each( data.target_languages, function ( i, lang ) {
            $wrap.append( buildEditField( lang.code, lang.name, translations[ lang.code ] || '' ) );
        } );

        var $actions = $( '<div class="trp-glossary-quick-edit-actions"/>' );
        var $update  = $( '<button type="button" class="button button-primary trp-glossary-update"/>' ).text( data.i18n.update );
        var $cancel  = $( '<button type="button" class="button trp-glossary-cancel"/>' ).text( data.i18n.cancel );
        var $spinner = $( '<span class="spinner"/>' );
        var $error   = $( '<span class="trp-glossary-quick-edit-error"/>' );
        $actions.append( $update ).append( $cancel ).append( $spinner ).append( $error );
        $wrap.append( $actions );

        var $editRow = $( '<tr class="inline-edit-row trp-glossary-quick-edit"/>' );
        $( '<td colspan="2"/>' ).append( $wrap ).appendTo( $editRow );
        $editRow.data( 'origRow', $row );

        $row.hide().after( $editRow );
        $editRow.find( '.trp-glossary-edit-input' ).first().trigger( 'focus' );
    }

    function collectEditTerm( $editRow ) {
        var term = {};
        $editRow.find( '.trp-glossary-edit-input' ).each( function () {
            term[ $( this ).attr( 'data-lang' ) ] = $( this ).val();
        } );
        return term;
    }

    /* ---------------------------------------------------------------------
     * Add term
     * ------------------------------------------------------------------- */

    function collectAddTerm() {
        var term = {};
        $( 'input[name^="trp_glossary_settings[term]"]' ).each( function () {
            var match = $( this ).attr( 'name' ).match( /\[term\]\[([^\]]+)\]/ );
            if ( match ) {
                term[ match[ 1 ] ] = $( this ).val();
            }
        } );
        return term;
    }

    $addButton.on( 'click', function () {
        $addButton.prop( 'disabled', true );
        $addSpinner.addClass( 'is-active' );

        $.post( data.admin_ajax, {
            action: 'trp_glossary_add_term',
            nonce: data.add_term_nonce,
            term: collectAddTerm()
        } ).done( function ( response ) {
            if ( response && response.success && response.data ) {
                allTerms.unshift( { term: response.data.term, translations: response.data.translations } );
                // Clear any active search + go to the first page so the new term is visible.
                query = '';
                $searchInput.val( '' );
                page = 1;
                render();

                var $notice = showNotice( 'success', data.i18n.term_added );
                if ( response.data.found_in_dictionary && response.data.replace_url ) {
                    var $p = $notice.find( 'p' ).first();
                    var parts = String( data.i18n.found_message ).split( '%s' );
                    $p.append( document.createTextNode( ' ' + parts[ 0 ] ) );
                    $( '<a/>' ).attr( 'href', response.data.replace_url ).text( data.i18n.replace_link ).appendTo( $p );
                    if ( parts.length > 1 ) {
                        $p.append( document.createTextNode( parts[ 1 ] ) );
                    }
                }

                $( 'input[name^="trp_glossary_settings[term]"]' ).val( '' );
            } else {
                showNotice( 'error', ( response && response.data && response.data.message ) || data.i18n.generic_error );
            }
        } ).fail( function () {
            showNotice( 'error', data.i18n.generic_error );
        } ).always( function () {
            $addButton.prop( 'disabled', false );
            $addSpinner.removeClass( 'is-active' );
        } );
    } );

    /* ---------------------------------------------------------------------
     * Row actions (delegated)
     * ------------------------------------------------------------------- */

    $tableBody.on( 'click', '.trp-glossary-edit', function () {
        openQuickEdit( $( this ).closest( 'tr' ) );
    } );

    $tableBody.on( 'click', '.trp-glossary-cancel', function () {
        closeQuickEdit();
    } );

    $tableBody.on( 'click', '.trp-glossary-update', function () {
        var $editRow = $( this ).closest( 'tr.trp-glossary-quick-edit' );
        var $row     = $editRow.data( 'origRow' );
        var $update  = $editRow.find( '.trp-glossary-update' );
        var $spinner = $editRow.find( '.spinner' );
        var $error   = $editRow.find( '.trp-glossary-quick-edit-error' );

        $error.text( '' );
        $update.prop( 'disabled', true );
        $spinner.addClass( 'is-active' );

        $.post( data.admin_ajax, {
            action: 'trp_glossary_edit_term',
            nonce: data.edit_term_nonce,
            original_term: $row.attr( 'data-term' ),
            term: collectEditTerm( $editRow )
        } ).done( function ( response ) {
            if ( response && response.success && response.data ) {
                var idx = indexOfTerm( response.data.original_term );
                if ( idx !== -1 ) {
                    allTerms[ idx ] = { term: response.data.term, translations: response.data.translations };
                }
                render();
                showNotice( 'success', data.i18n.term_updated );
            } else {
                $error.text( ( response && response.data && response.data.message ) || data.i18n.generic_error );
                $update.prop( 'disabled', false );
                $spinner.removeClass( 'is-active' );
            }
        } ).fail( function () {
            $error.text( data.i18n.generic_error );
            $update.prop( 'disabled', false );
            $spinner.removeClass( 'is-active' );
        } );
    } );

    $tableBody.on( 'click', '.trp-glossary-delete', function () {
        var $row = $( this ).closest( 'tr' );
        var term = $row.attr( 'data-term' );

        if ( ! window.confirm( data.i18n.confirm_delete ) ) {
            return;
        }

        $.post( data.admin_ajax, {
            action: 'trp_glossary_delete_term',
            nonce: data.delete_term_nonce,
            term: term
        } ).done( function ( response ) {
            if ( response && response.success ) {
                var idx = indexOfTerm( term );
                if ( idx !== -1 ) {
                    allTerms.splice( idx, 1 );
                }
                render();
                showNotice( 'success', data.i18n.term_deleted );
            } else {
                showNotice( 'error', ( response && response.data && response.data.message ) || data.i18n.generic_error );
            }
        } ).fail( function () {
            showNotice( 'error', data.i18n.generic_error );
        } );
    } );

    /* ---------------------------------------------------------------------
     * Search / filter (client-side, across all terms) + pagination controls
     * ------------------------------------------------------------------- */

    function applySearch() {
        query = $.trim( $searchInput.val() );
        page = 1;
        render();
    }

    $searchButton.on( 'click', applySearch );
    $searchInput.on( 'input', applySearch );
    $searchInput.on( 'keydown', function ( e ) {
        if ( e.which === 13 ) {
            e.preventDefault();
            applySearch();
        }
    } );

    $tablenavs.on( 'click', '.first-page', function ( e ) { e.preventDefault(); goToPage( 1 ); } );
    $tablenavs.on( 'click', '.prev-page',  function ( e ) { e.preventDefault(); goToPage( page - 1 ); } );
    $tablenavs.on( 'click', '.next-page',  function ( e ) { e.preventDefault(); goToPage( page + 1 ); } );
    $tablenavs.on( 'click', '.last-page',  function ( e ) { e.preventDefault(); goToPage( Number.MAX_SAFE_INTEGER ); } );

    /* ---------------------------------------------------------------------
     * Initial load
     * ------------------------------------------------------------------- */

    loadTerms();

} )( jQuery );
