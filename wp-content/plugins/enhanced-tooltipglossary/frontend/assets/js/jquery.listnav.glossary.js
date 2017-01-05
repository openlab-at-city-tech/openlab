/*
 *
 * jQuery listnav plugin
 * Copyright (c) 2009 iHwy, Inc.
 * Author: Jack Killpatrick
 *
 * Version 2.1 (08/09/2009)
 * Requires jQuery 1.3.2, jquery 1.2.6 or jquery 1.2.x plus the jquery dimensions plugin
 *
 * Visit http://www.ihwy.com/labs/jquery-listnav-plugin.aspx for more information.
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 */

( function ( $ ) {

    $.fn.listnav = function ( options ) {
        var opts = $.extend( {
            'letters': [ '_', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '-' ]
        }, $.fn.listnav.defaults, options );
        var letters = opts.letters.concat( [ '-' ] );
        if ( opts.includeNums ) {
            letters = letters.concat( [ '_' ] );
        }
        var firstClick = false;
        opts.prefixes = $.map( opts.prefixes, function ( n ) {
            return n.toLowerCase();
        } );
        return this.each( function () {
            var $wrapper, $list, $letters, $letterCount, id;
            id = this.id;
            $wrapper = $( '#' + id + '-nav' );
            $list = $( this );
            var counts = { }, allCount = 0, isAll = true, numCount = 0, prevLetter = '';

            function init() {
                $wrapper.append( createLettersHtml() );
                $letters = $( '.ln-letters', $wrapper ).slice( 0, 1 );
                if ( opts.showCounts )
                    $letterCount = $( '.ln-letter-count', $wrapper ).slice( 0, 1 );
                addClasses();
                addNoMatchLI();
                if ( opts.flagDisabled )
                    addDisabledClass();
                bindHandlers();
                if ( !opts.includeAll )
                    $list.show();
                if ( !opts.includeAll )
                    $( '.ln-all', $letters ).remove();
                if ( !opts.includeNums )
                    $( '.ln-_', $letters ).remove();
                if ( !opts.includeOther )
                    $( '.ln--', $letters ).remove();
                $( ':last', $letters ).addClass( 'ln-last' );
                if ( $.cookie && ( opts.cookieName !== null ) ) {
                    var cookieLetter = $.cookie( opts.cookieName );
                    if ( cookieLetter !== null )
                        opts.initLetter = cookieLetter;
                }
                if ( opts.initLetter !== '' ) {
                    firstClick = true;
                    $( '.lnletter-' + opts.initLetter.toLowerCase(), $letters ).slice( 0, 1 ).click();
                } else {
                    if ( opts.includeAll ) {
                        $( '.all', $letters ).addClass( 'ln-selected' ).attr( 'aria-selected', 'true' );
                    } else {
                        for ( var i = ( ( opts.includeNums ) ? 1 : 0 ); i < letters.length; i++ ) {
                            if ( counts[letters[i]] > 0 ) {
                                firstClick = true;
                                $( '.ln-' + i, $letters ).slice( 0, 1 ).click();
                                break;
                            }
                        }
                    }
                }
                $list.listPaginate( { perPage: opts.perPage } );
            }

            function setLetterCountTop() {
                $letterCount.css( { top: $( '.ln-1', $letters ).slice( 0, 1 ).offset( { margin: false, border: true } ).top - $letterCount.outerHeight( { margin: true } ) } );
            }

            function addClasses() {
                var str, firstChar, firstWord, spl, $this, hasPrefixes = ( opts.prefixes.length > 0 );
                $( $list ).children().each( function () {
                    $this = $( this ), firstChar = '', str = $.trim( $this.text() ).toLowerCase();
                    if ( str !== '' ) {
                        if ( hasPrefixes ) {
                            spl = str.split( ' ' );
                            if ( ( spl.length > 1 ) && ( $.inArray( spl[0], opts.prefixes ) > -1 ) ) {
                                firstChar = spl[1].charAt( 0 );
                                addLetterClass( firstChar, $this, true );
                            }
                        }
                        firstChar = str.charAt( 0 );
                        addLetterClass( firstChar, $this );
                    }
                } );
            }

            function addLetterClass( firstChar, $el, isPrefix ) {
                if ( letters.indexOf( firstChar ) < 0 && isNaN( firstChar ) ) {
                    firstChar = '-';
                }
                if ( !isNaN( firstChar ) ) {
                    firstChar = '_';
                }
                $el.addClass( 'ln-' + ( ( firstChar === '-' || firstChar === '_' ) ? firstChar : letters.indexOf( firstChar ) ) );
                if ( counts[firstChar] === undefined ) {
                    counts[firstChar] = 0;
                }
                counts[firstChar]++;
                if ( !isPrefix ) {
                    allCount++;
                }
            }
            function addDisabledClass() {
                $.each( letters, function ( i, l ) {
                    if ( counts[l] === undefined ) {
                        if ( l === '_' || l === '-' )
                        {
                            $( '.ln-' + l, $letters ).addClass( 'ln-disabled' );
                        } else
                        {
                            $( '.ln-' + i, $letters ).addClass( 'ln-disabled' );
                        }
                    }
                } );
            }
            function addNoMatchLI() {
                $list.append( '<li class="ln-no-match" style="display:none">' + opts.noMatchText + '</li>' );
            }

            function getLetterCount( el ) {
                if ( $( el ).hasClass( 'ln-all' ) ) {
                    return allCount;
                } else {
                    var letter = $( el ).attr( 'class' ).split( ' ' )[0].substring( 3 );
                    if ( letter !== '-' && letter !== '_' ) {
                        var letter = letters[letter];
                    }
                    var count = counts[letter];
                    return ( count != undefined ) ? count : 0;
                }
            }

            function bindHandlers() {
                if ( opts.showCounts ) {
                    $wrapper.mouseover( function () {
                        setLetterCountTop();
                    } );
                }
                if ( opts.showCounts ) {
                    $( 'a', $letters ).mouseover( function () {
                        var left = $( this ).position().left;
                        var width = ( $( this ).outerWidth( { margin: true } ) - 1 ) + 'px';
                        var count = getLetterCount( this );
                        $letterCount.css( { left: left, width: width } ).text( count ).show();
                    } );
                    $( 'a', $letters ).mouseout( function () {
                        $letterCount.hide();
                    } );
                }
                $( 'a', $letters ).click( function () {
                    $( 'a.ln-selected', $letters ).removeClass( 'ln-selected' ).attr( 'aria-selected', 'false' );
                    var letter = $( this ).attr( 'class' ).split( ' ' )[0];
                    letter = letter.substring( 3 );
                    if ( letter === 'all' ) {
                        $list.children().show();
                        $list.children( '.ln-no-match' ).hide();
                        isAll = true;
                    } else {
                        if ( isAll ) {
                            $list.children().hide();
                            isAll = false;
                        } else if ( prevLetter !== '' )
                            $list.children( '.ln-' + prevLetter ).hide();
                        var count = getLetterCount( this );
                        if ( count > 0 ) {
                            $list.children( '.ln-no-match' ).hide();
                            $list.children( '.ln-' + letter ).show();
                        } else
                            $list.children( '.ln-no-match' ).show();
                        prevLetter = letter;
                    }
                    $list.listPaginate( { perPage: opts.perPage } );
                    if ( $.cookie && ( opts.cookieName !== null ) )
                        $.cookie( opts.cookieName, letter );
                    $( this ).addClass( 'ln-selected' ).attr( 'aria-selected', 'true' );
                    $( this ).blur();
                    if ( !firstClick && ( opts.onClick !== null ) )
                        opts.onClick( letter );
                    else
                        firstClick = false;
                    return false;
                } );
            }

            function createLettersHtml() {
                var html = [ ];
                for ( var i = 0; i < letters.length; i++ ) {
                    if ( html.length == 0 ) {
                        html.push( '<a class="ln-all" role="tab" aria-controls="panel-all" aria-selected="false" href="#">ALL</a>' );
                        if ( opts.includeNums ) {
                            html.push( '<a class="ln-_" tabindex="-1" role="tab" aria-controls="panel-_" aria-selected="false" href="#">0-9</a>' );
                        }
                    }
                    if ( letters[i] === '_' )
                    {
                        continue;
                    }
                    html.push( '<a class="ln-' + ( letters[i] === '-' ? '-' : i ) + ' lnletter-' + letters[i].toLowerCase() + '" tabindex="-1" role="tab" aria-selected="false" aria-controls="panel-' + letters[i].toLowerCase() + ' href="#" >' + ( ( letters[i] === '-' ) ? '...' : letters[i].toUpperCase() ) + '</a>' );
                }
                return '<div class="ln-letters">' + html.join( '' ) + '</div>' + ( ( opts.showCounts ) ? '<div class="ln-letter-count" style="display:none; position:absolute; top:0; left:0; min-width:20px;">0</div>' : '' );
            }
            init();
        } );
    };
    $.fn.listnav.defaults = { initLetter: '', includeAll: true, incudeOther: false, includeNums: true, flagDisabled: true, noMatchText: 'No matching entries', showCounts: true, cookieName: null, onClick: null, prefixes: [ ], perPage: 0, letter: [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ] };
} )( jQuery );

( function ( $ ) {
    $.fn.listPaginate = function ( options ) {
        var $container, $list;

        $list = $( this );
        $container = $list.parent();

        var $opts = $.extend( { perPage: 10, showPages: 17 }, options );

        var $elements = $( 'li:visible', $list );
        var pages = Math.ceil( $elements.length / $opts.perPage );
        var $pageNumbers = $( '.pageNumbers', $container );

        return this.each( function () {

            function showPage( page ) {

                var prevPage, nextPage, prevSectionPage, nextSectionPage, prevHalf, nextHalf, prevDiff, showFirst, showLast;

                prevPage = ( page - 1 < 1 ) ? 1 : page - 1;
                nextPage = ( page + 1 > pages ) ? pages : page + 1;

                prevHalf = ( page - Math.ceil( $opts.showPages / 2 ) ) <= 0 ? 0 : ( page - Math.ceil( $opts.showPages / 2 ) );
                prevDiff = ( Math.ceil( $opts.showPages / 2 ) - page >= 0 ) ? Math.ceil( $opts.showPages / 2 ) - page : 0;
                nextHalf = ( page + Math.ceil( $opts.showPages / 2 ) ) > pages ? pages : ( page + Math.ceil( $opts.showPages / 2 ) );

                showFirst = prevHalf > 1;
                showLast = nextHalf <= pages;

                $( '.page-next', $container ).css( 'display', 'inline-block' ).data( 'page', nextPage );
                $( '.page-prev', $container ).css( 'display', 'none' ).data( 'page', prevPage );

                $( '.page-prev-section', $container ).css( 'display', 'none' );
                $( '.page-next-section', $container ).css( 'display', 'none' );

                if ( page > 1 )
                {
                    $( '.page-prev', $container ).css( 'display', 'inline-block' );
                }
                if ( page === pages )
                {
                    $( '.page-next', $container ).css( 'display', 'none' );
                }

                if ( page > Math.ceil( $opts.showPages / 2 ) )
                {
                    prevSectionPage = ( page - Math.ceil( $opts.showPages / 2 ) ) < 1 ? 1 : page - Math.ceil( $opts.showPages / 2 );
                    $( '.page-prev-section', $container ).css( 'display', 'inline-block' ).data( 'page', prevSectionPage );
                }
                if ( ( page + Math.ceil( $opts.showPages / 2 ) ) < pages )
                {
                    nextSectionPage = ( page + Math.ceil( $opts.showPages / 2 ) ) > pages ? pages : page + Math.ceil( $opts.showPages / 2 );
                    $( '.page-next-section', $container ).css( 'display', 'inline-block' ).data( 'page', nextSectionPage );
                }

                $( 'li[data-page]', $pageNumbers ).hide().filter( function () {
                    return ( $( this ).attr( "data-page" ) > prevHalf && $( this ).attr( "data-page" ) < ( nextHalf + prevDiff ) );
                } ).show();

                if ( showFirst )
                {
                    $( 'li[data-page="1"]', $pageNumbers ).show();
                }
                if ( showLast )
                {
                    $( 'li[data-page="' + pages + '"]', $pageNumbers ).show();
                }

                $elements.hide().filter( '[data-page=' + page + ']' ).show();
                $( 'li', $pageNumbers ).removeClass( 'selected' ).filter( '[data-page=' + page + ']' ).addClass( 'selected' );
            }

            if ( $opts.perPage > 0 ) {

                $elements.removeClass( 'paginated' );
                $elements.each( function ( i ) {
                    $( this ).addClass( 'paginated' ).attr( 'data-page', parseInt( i / $opts.perPage ) + 1 );
                } );

                if ( $pageNumbers.length === 0 ) {
                    $pageNumbers = $( '<ul></ul>' ).addClass( 'pageNumbers' );
                    $container.append( $pageNumbers );
                }
                $pageNumbers.empty();
                if ( pages > 1 )
                {
                    var $li = $( '<li></li>' ).data( 'page', 1 ).text( '<<' ).css( 'cursor', 'pointer' ).css( 'display', 'none' ).addClass( 'page-prev' );
                    $pageNumbers.append( $li );

                    for ( var i = 1; i <= pages; i++ )
                    {
                        if ( i === pages && pages > $opts.showPages )
                        {
                            $li = $( '<li></li>' ).data( 'page', $opts.showPages + 1 ).text( '(...)' ).css( 'cursor', 'pointer' ).addClass( 'page-next-section' );
                            $pageNumbers.append( $li );
                        }

                        $li = $( '<li></li>' ).attr( 'data-page', i ).text( i ).css( 'cursor', 'pointer' );
                        if ( i > $opts.showPages && i < pages )
                        {
                            $li.css( 'display', 'none' );
                        }
                        $pageNumbers.append( $li );

                        if ( i === 1 )
                        {
                            $li = $( '<li></li>' ).data( 'page', 1 ).text( '(...)' ).css( 'cursor', 'pointer' ).css( 'display', 'none' ).addClass( 'page-prev-section' );
                            $pageNumbers.append( $li );
                        }
                    }

                    $li = $( '<li></li>' ).data( 'page', 2 ).text( '>>' ).css( 'cursor', 'pointer' ).addClass( 'page-next' );
                    $pageNumbers.append( $li );

                    $( 'li', $pageNumbers ).click( function () {
                        showPage( $( this ).data( 'page' ) );
                    } );
                    showPage( 1 );
                }

            }
        } );
    };
} )( jQuery );