/**
 * Glossary "search & replace in existing translations" page.
 *
 * Collects, per target language, the new translation and the existing
 * translation to find, then asks the server to replace occurrences of the
 * existing translation with the new one across the dictionary tables.
 */
( function ( $ ) {
    'use strict';

    if ( typeof trp_glossary_replace_data === 'undefined' ) {
        return;
    }

    var data      = trp_glossary_replace_data;
    var $form     = $( '#trp-glossary-replace-form' );
    var $response = $( '#trp-glossary-replace-response' );
    var $button   = $( '#trp-glossary-replace-submit' );
    var $spinner  = $( '.trp-glossary-replace-spinner' );

    /* ---------------------------------------------------------------------
     * Search Existing Translations (standalone lookup, independent of the form)
     * ------------------------------------------------------------------- */

    var $searchInput   = $( '#trp-glossary-search-dictionary-input' );
    var $searchButton  = $( '#trp-glossary-search-dictionary-submit' );
    var $searchSpinner = $( '.trp-glossary-search-spinner' );
    var $searchResults = $( '#trp-glossary-search-dictionary-results' );

    var SEARCH_PER_PAGE = 5;                 // fallback; the server also reports per_page
    var currentResults  = [];                // [{ code, name, entries, total, page, per_page }]
    var currentTerm     = '';                // term used for the active search (for page fetches)

    function escapeHtml( text ) {
        return $( '<div/>' ).text( text == null ? '' : text ).html();
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
     * Build a WP-style pagination bar for one language table (same look as the
     * glossary table). data-lang identifies which table the controls belong to.
     */
    function paginationBar( code, totalItems, totalPages, page ) {
        var links = navControl( 'first-page', '«', data.i18n.first_page, page > 1 ) +
            navControl( 'prev-page', '‹', data.i18n.prev_page, page > 1 ) +
            '<span class="paging-input"><span class="tablenav-paging-text">' +
            page + ' ' + escapeHtml( data.i18n.of ) + ' <span class="total-pages">' + totalPages + '</span></span></span>' +
            navControl( 'next-page', '›', data.i18n.next_page, page < totalPages ) +
            navControl( 'last-page', '»', data.i18n.last_page, page < totalPages );

        return '<div class="tablenav bottom trp-glossary-tablenav" data-lang="' + escapeHtml( code ) + '">' +
            '<div class="tablenav-pages">' +
            '<span class="displaying-num">' + totalItems + ' ' + escapeHtml( data.i18n.items ) + '</span>' +
            '<span class="pagination-links">' + links + '</span>' +
            '</div></div>';
    }

    function perPageOf( lang ) {
        return lang.per_page || SEARCH_PER_PAGE;
    }

    function totalPagesOf( lang ) {
        return Math.max( 1, Math.ceil( ( lang.total || 0 ) / perPageOf( lang ) ) );
    }

    /**
     * Render the per-language search results. Each language's entries are already
     * the server-side page slice; `total` drives the (accurate) page count.
     */
    function renderSearchResults() {
        $searchResults.empty();

        if ( ! currentResults || ! currentResults.length ) {
            $searchResults.append( $( '<p/>' ).text( data.i18n.no_entries ) );
            return;
        }

        currentResults.forEach( function ( lang ) {
            $( '<h4/>' ).text( lang.name ).appendTo( $searchResults );

            if ( ! lang.total ) {
                $( '<p/>' ).text( data.i18n.no_entries ).appendTo( $searchResults );
                return;
            }

            var totalPages = totalPagesOf( lang );
            var page       = lang.page || 1;

            var $table = $( '<table class="wp-list-table widefat fixed striped"/>' );
            var $head  = $( '<tr/>' )
                .append( $( '<th scope="col" class="manage-column"/>' ).text( data.i18n.col_original ) )
                .append( $( '<th scope="col" class="manage-column"/>' ).text( data.i18n.col_translated ) );
            $table.append( $( '<thead/>' ).append( $head ) );

            var $body = $( '<tbody/>' );
            ( lang.entries || [] ).forEach( function ( entry ) {
                $( '<tr/>' )
                    .append( $( '<td/>' ).text( entry.original ) )
                    .append( $( '<td/>' ).text( entry.translated ) )
                    .appendTo( $body );
            } );
            $table.append( $body );
            $searchResults.append( $table );

            // Pagination only when a table has more than one page (> per_page items).
            if ( totalPages > 1 ) {
                $searchResults.append( paginationBar( lang.code, lang.total, totalPages, page ) );
            }
        } );
    }

    function findResult( code ) {
        for ( var i = 0; i < currentResults.length; i++ ) {
            if ( currentResults[ i ].code === code ) {
                return currentResults[ i ];
            }
        }
        return null;
    }

    /**
     * Fetch a single language's page from the server and re-render.
     */
    function fetchPage( code, targetPage ) {
        var lang = findResult( code );
        if ( ! lang ) {
            return;
        }
        var totalPages = totalPagesOf( lang );
        if ( targetPage < 1 ) { targetPage = 1; }
        if ( targetPage > totalPages ) { targetPage = totalPages; }
        if ( targetPage === lang.page ) {
            return;
        }

        $.post( data.admin_ajax, {
            action: 'trp_glossary_search_dictionary',
            nonce: data.search_nonce,
            term: currentTerm,
            lang: code,
            page: targetPage
        } ).done( function ( response ) {
            if ( response && response.success && response.data && response.data.results && response.data.results[ 0 ] ) {
                var idx = -1;
                for ( var i = 0; i < currentResults.length; i++ ) {
                    if ( currentResults[ i ].code === code ) { idx = i; break; }
                }
                if ( idx !== -1 ) {
                    currentResults[ idx ] = response.data.results[ 0 ];
                }
                renderSearchResults();
            }
        } );
    }

    /**
     * Run the dictionary search for the current input value.
     */
    function runSearch() {
        if ( ! $searchInput.length ) {
            return;
        }

        currentTerm = $searchInput.val();

        $searchButton.prop( 'disabled', true );
        $searchSpinner.addClass( 'is-active' );

        $.post( data.admin_ajax, {
            action: 'trp_glossary_search_dictionary',
            nonce: data.search_nonce,
            term: currentTerm
        } ).done( function ( response ) {
            if ( response && response.success && response.data ) {
                currentResults = response.data.results || [];
                renderSearchResults();
            } else {
                $searchResults.html( $( '<p/>' ).text( data.i18n.generic_error ) );
            }
        } ).fail( function () {
            $searchResults.html( $( '<p/>' ).text( data.i18n.generic_error ) );
        } ).always( function () {
            $searchButton.prop( 'disabled', false );
            $searchSpinner.removeClass( 'is-active' );
        } );
    }

    // Per-table pagination clicks (delegated; data-lang picks the table, server returns the page).
    $searchResults.on( 'click', '.trp-glossary-tablenav .first-page', function ( e ) {
        e.preventDefault();
        fetchPage( $( this ).closest( '.trp-glossary-tablenav' ).data( 'lang' ), 1 );
    } );
    $searchResults.on( 'click', '.trp-glossary-tablenav .prev-page', function ( e ) {
        e.preventDefault();
        var code = $( this ).closest( '.trp-glossary-tablenav' ).data( 'lang' );
        var lang = findResult( code );
        fetchPage( code, ( lang ? lang.page : 1 ) - 1 );
    } );
    $searchResults.on( 'click', '.trp-glossary-tablenav .next-page', function ( e ) {
        e.preventDefault();
        var code = $( this ).closest( '.trp-glossary-tablenav' ).data( 'lang' );
        var lang = findResult( code );
        fetchPage( code, ( lang ? lang.page : 1 ) + 1 );
    } );
    $searchResults.on( 'click', '.trp-glossary-tablenav .last-page', function ( e ) {
        e.preventDefault();
        var code = $( this ).closest( '.trp-glossary-tablenav' ).data( 'lang' );
        var lang = findResult( code );
        fetchPage( code, lang ? totalPagesOf( lang ) : 1 );
    } );

    $searchButton.on( 'click', runSearch );
    $searchInput.on( 'keydown', function ( e ) {
        if ( e.which === 13 ) {          // Enter runs the search without submitting anything
            e.preventDefault();
            runSearch();
        }
    } );

    // Run once on load with the pre-filled term.
    if ( $searchInput.length && $.trim( $searchInput.val() ) !== '' ) {
        runSearch();
    }

    if ( ! $form.length ) {
        return;
    }

    /**
     * Render a WP admin notice into the response container.
     *
     * @param {string} type    'success' or 'error'.
     * @param {string} message Text to display.
     */
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
    }

    /**
     * Collect the per-language new / existing inputs into { code: value } maps.
     *
     * @return {{newValues: Object, existing: Object}}
     */
    function collect() {
        var newValues = {};
        var existing  = {};
        $form.find( '.trp-glossary-replace-new' ).each( function () {
            newValues[ $( this ).attr( 'data-lang' ) ] = $( this ).val();
        } );
        $form.find( '.trp-glossary-replace-existing' ).each( function () {
            existing[ $( this ).attr( 'data-lang' ) ] = $( this ).val();
        } );
        return { newValues: newValues, existing: existing };
    }

    $button.on( 'click', function () {
        if ( ! window.confirm( data.i18n.confirm_replace + '\n\n' + data.i18n.confirm_replace_question ) ) {
            return;
        }

        var collected = collect();

        $button.prop( 'disabled', true );
        $spinner.addClass( 'is-active' );

        $.post( data.admin_ajax, {
            action: 'trp_glossary_replace_in_dictionary',
            nonce: data.replace_nonce,
            term: $form.attr( 'data-term' ),
            'new': collected.newValues,
            existing: collected.existing
        } ).done( function ( response ) {
            if ( response && response.success && response.data ) {
                showNotice( 'success', response.data.message );
                $form.find( '.trp-glossary-replace-existing' ).val( '' );
            } else {
                showNotice( 'error', ( response && response.data && response.data.message ) || data.i18n.generic_error );
            }
        } ).fail( function () {
            showNotice( 'error', data.i18n.generic_error );
        } ).always( function () {
            $button.prop( 'disabled', false );
            $spinner.removeClass( 'is-active' );
        } );
    } );

} )( jQuery );
