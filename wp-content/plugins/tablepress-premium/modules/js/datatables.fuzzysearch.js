/*!
 * Fuzzy Search for DataTables
 * 2021 SpryMedia Ltd - datatables.net/license MIT license
 *
 * Damerau-Levenshtein function courtesy of https://github.com/tad-lispy/node-damerau-levenshtein
 * BSD 2-Clause License
 * Copyright (c) 2018, Tadeusz Łazurski
 * All rights reserved.
 *
 * Extended, modernized, and corrected by Tobias Bäthge
 */

/* globals jQuery, DataTable */

(function(window, document, $) {

	function levenshtein(__this, that, limit) {
		const thisLength = __this.length;
		const thatLength = that.length;
		const matrix = [];
		// If the limit is not defined it will be calculate from this and that args.
		limit = (limit || (thatLength > thisLength ? thatLength : thisLength)) + 1;
		for (let i = 0; i < limit; i++) {
			matrix[i] = [i];
			matrix[i].length = limit;
		}
		for (let i = 0; i < limit; i++) {
			matrix[0][i] = i;
		}
		if (Math.abs(thisLength - thatLength) > (limit || 100)) {
			return prepare(limit || 100);
		}
		if (thisLength === 0) {
			return prepare(thatLength);
		}
		if (thatLength === 0) {
			return prepare(thisLength);
		}
		// Calculate matrix.
		let this_i, that_j, cost, min, t;
		for (let i = 1; i <= thisLength; ++i) {
			this_i = __this[i - 1];
			// Step 4
			for (let j = 1; j <= thatLength; ++j) {
				// Check the jagged ld total so far
				if (i === j && matrix[i][j] > 4) {
					return prepare(thisLength);
				}
				that_j = that[j - 1];
				cost = this_i === that_j ? 0 : 1; // Step 5
				// Calculate the minimum (much faster than Math.min(...)).
				min = matrix[i - 1][j] + 1; // Devarion.
				if ((t = matrix[i][j - 1] + 1) < min) {
					min = t; // Insertion.
				}
				if ((t = matrix[i - 1][j - 1] + cost) < min) {
					min = t; // Substitution.
				}
				// Update matrix.
				matrix[i][j] =
					( i > 1 &&
						j > 1 &&
						this_i === that[j - 2] &&
						__this[i - 2] === that_j &&
						(t = matrix[i - 2][j - 2] + cost) < min
					) ? t
					: min; // Transposition.
			}
		}
		return prepare(matrix[thisLength][thatLength]);

		function prepare(steps) {
			const length = Math.max(thisLength, thatLength);
			const relative = length === 0 ? 0 : steps / length;
			const similarity = 1 - relative;
			return {
				steps,
				relative,
				similarity,
			};
		}
	}

	function fuzzySearch(searchVal, data, initial) {
		// If no searchVal has been defined then return all rows.
		if (searchVal === undefined || searchVal.length === 0) {
			return {
				pass: true,
				score: '',
			};
		}
		const threshold = initial.threshold ?? 0.5;
		// Split the searchVal into individual words.
		const splitSearch = searchVal.split(/ /g);
		// Array to keep scores in
		const highestCollated = [];
		// Remove any empty words or spaces
		for (let x = 0; x < splitSearch.length; x++) {
			if (splitSearch[x].length === 0 || splitSearch[x] === ' ') {
				splitSearch.splice(x, 1);
				x--;
			}
			// Aside - Add to the score collection if not done so yet for this search word
			else if (highestCollated.length < splitSearch.length) {
				highestCollated.push({ pass: false, score: 0 });
			}
		}
		// Going to check each cell for potential matches
		for (let i = 0; i < data.length; i++) {
			// Convert all data points to lower case for insensitive sorting
			data[i] = data[i].toLowerCase();
			// Split the data into individual words
			const splitData = data[i].split(/ /g);
			// Remove any empty words or spaces
			for (let y = 0; y < splitData.length; y++) {
				if (splitData[y].length === 0 || splitData[y] === ' ') {
					splitData.splice(y, 1);
					y--;
				}
			}
			// Check each search term word
			for (let x = 0; x < splitSearch.length; x++) {
				// Reset highest score
				let highest = {
					pass: undefined,
					score: 0,
				};
				// Against each word in the cell
				for (let y = 0; y < splitData.length; y++) {
					// If this search Term word is the beginning of the word in the cell we want to pass this word
					if (splitData[y].indexOf(splitSearch[x]) === 0) {
						const newScore = splitSearch[x].length / splitData[y].length;
						highest = {
							pass: true,
							score: highest.score < newScore ? newScore : highest.score,
						};
					}
					// Get the levenshtein similarity score for the two words
					const steps = levenshtein(splitSearch[x], splitData[y]).similarity;
					// If the levenshtein similarity score is better than a previous one for the search word then var's store it
					if (steps > highest.score) {
						highest.score = steps;
					}
				}
				// If this cell has a higher scoring word than previously found to the search term in the row, store it
				if (highestCollated[x].score < highest.score || highest.pass) {
					highestCollated[x] = {
						pass: ( highest.pass || highestCollated[x].pass ) ? true : ( highest.score > threshold ),
						score: highest.score,
					};
				}
			}
		}
		// Check that all of the search words have passed
		for (let i = 0; i < highestCollated.length; i++) {
			if (!highestCollated[i].pass) {
				return {
					pass: false,
					score: Math.round((highestCollated.reduce((a, b) => a + b.score, 0) /
						highestCollated.length) *
						100) + '%',
				};
			}
		}
		// If we get to here, all scores greater than 0.5 so display the row
		return {
			pass: true,
			score: Math.round((highestCollated.reduce((a, b) => a + b.score, 0) /
				highestCollated.length) *
				100) + '%',
		};
	}

	DataTable.ext.search.push( function ( settings, data, dataIndex ) {
		const initial = settings.oInit.fuzzySearch;
		if ( ! initial ) {
			return true;
		}
		if (settings.aoData[dataIndex]) {
			// If fuzzy searching has not been implemented then pass all rows for this function
			if (settings.aoData[dataIndex]._fuzzySearch !== undefined) {
				// Read score to set the cell content and sort data
				const score = settings.aoData[dataIndex]._fuzzySearch.score;
				if ( undefined !== initial.rankColumn ) {
					settings.aoData[dataIndex].anCells[initial.rankColumn].innerHTML = score;
					// Remove '%' from the end of the score so can sort on a number
					if ( ! settings.aoData[dataIndex]._aSortData ) {
						settings.aoData[dataIndex]._aSortData = {};
					}
					settings.aoData[dataIndex]._aSortData[initial.rankColumn] = +score.substring(0, score.length - 1);
				}
				// Return the value for the pass as decided by the fuzzySearch function
				return settings.aoData[dataIndex]._fuzzySearch.pass;
			} else if ( undefined !== initial.rankColumn ) {
				settings.aoData[dataIndex].anCells[initial.rankColumn].innerHTML = '';
				if ( ! settings.aoData[dataIndex]._aSortData ) {
					settings.aoData[dataIndex]._aSortData = {};
				}
				settings.aoData[dataIndex]._aSortData[initial.rankColumn] = '';
			}
		}
		return true;
	});

	$( document ).on( 'init.dt', function ( e, settings ) {
		const api = new DataTable.Api(settings);
		const initial = api.init();
		const initialFuzzy = initial.fuzzySearch;

		// If this is not set then fuzzy searching is not enabled on the table so return.
		if ( ! initialFuzzy ) {
			return;
		}

		let fromPlugin = false;

		// Find the input element
		const input = $('div.dataTables_filter input', api.table().container());
		const fontBold = {
			'font-weight': '600',
			'background-color': 'rgba(0,0,0,0.05)',
		};
		const fontNormal = {
			'font-weight': '500',
			'background-color': 'transparent',
		};
		const toggleCSS = {
			// border: 'none',
			border: '1px solid #16232a',
			'border-radius': '4px',
			background: 'none',
			'font-size': '80%',
			width: 'calc(50% - 0.5em)',
			display: 'inline-block',
			// color: 'white',
			cursor: 'pointer',
			padding: '0.5em',
			margin: '0 0.25em',
		};
		// Only going to set the toggle if it is enabled
		let toggle, tooltip, exact, fuzzy, label;
		if ( true === initialFuzzy || initialFuzzy.toggleSmart ) {
			input.closest('.dataTables_filter').css({
				position: 'relative',
			});
			input.css({
				'padding-right': '30px',
			});
			toggle = $('<button class="dt-toggleSearch">Abc</button>')
				.insertAfter(input)
				.css({
					border: 'none',
					background: 'none',
					position: 'absolute',
					top: input.position().top,
					right: 0,
					height: input.outerHeight(),
					'margin-top': parseInt( input.css( 'marginTop' ), 10 ),
					cursor: 'pointer',
					color: '#3b5e99',
					padding: '1px 4px',
					'font-size': '14px',
					'font-family': 'sans-serif',
					'font-weight': 'normal',
					'vertical-align': 'middle',
				});

			const searchTypeText = settings.oLanguage.fuzzySearch?.searchType || 'Search Type';
			label = $('<div>' + searchTypeText + '</div>').css({
				'padding-bottom': '0.5em',
				'font-size': '0.8em',
			});
			const exactText = settings.oLanguage.fuzzySearch?.exact || 'Exact';
			exact = $('<button class="dt-toggleSearch">' + exactText + '</button>')
				.css(toggleCSS)
				.css(fontBold)
				.attr('highlighted', true);
			const fuzzyText = settings.oLanguage.fuzzySearch?.fuzzy || 'Fuzzy';
			fuzzy = $('<button class="dt-toggleSearch">' + fuzzyText + '</button>')
				.css(toggleCSS);

			tooltip = $('<div class="dt-fuzzyToolTip"></div>')
				.css({
					position: 'absolute',
					top: input.outerHeight() + 10,
					background: 'white',
					'border-radius': '4px',
					'text-align': 'center',
					padding: '0.5em',
					//'background-color': '#16232a',
					'box-shadow': '4px 4px 4px rgba(0, 0, 0, 0.5)',
					//color: 'white',
					border: '1px solid #16232a',
					transition: 'opacity 0.25s',
					'z-index': '30001',
					width: input.outerWidth() - 8,
					'box-sizing': 'border-box',
				})
				.append(label)
				.append(exact)
				.append(fuzzy);
		}

		function toggleFuzzy(event) {
			if (toggle.attr('blurred')) {
				toggle.css({ filter: 'blur(0px)' }).removeAttr('blurred');
				fuzzy.removeAttr('highlighted').css(fontNormal);
				exact.attr('highlighted', true).css(fontBold);
			} else {
				toggle.css({ filter: 'blur(1px)' }).attr('blurred', true);
				exact.removeAttr('highlighted').css(fontNormal);
				fuzzy.attr('highlighted', true).css(fontBold);
			}
			// Whenever the search mode is changed we need to re-search
			triggerSearchFunction(event);
		}

		// Turn off the default datatables searching events
		$(settings.nTable).off('search.dt.DT');

		let fuzzySearchVal = '';
		let searchVal = '';
		// The function that we want to run on search
		const triggerSearchFunction = function (event) {
			// If the search is only to be triggered on return wait for that
			if ((event.type === 'input' &&
				(initial.search === undefined || !initial.search.return)) ||
				event.key === 'Enter' ||
				event.type === 'click') {
				// If the toggle is set and isn't checked then perform a normal search
				if (toggle && !toggle.attr('blurred')) {
					api.rows().iterator('row', function (the_settings, rowIdx) {
						the_settings.aoData[rowIdx]._fuzzySearch = undefined;
					}, false);
					searchVal = input.val();
					fuzzySearchVal = searchVal;
					fromPlugin = true;
					api.search(searchVal);
					fromPlugin = false;
					searchVal = '';
				}
				// Otherwise perform a fuzzy search
				else {
					// Get the value from the input element and convert to lower case
					fuzzySearchVal = input.val();
					searchVal = '';
					if (fuzzySearchVal !== undefined && fuzzySearchVal.length !== 0) {
						fuzzySearchVal = fuzzySearchVal.toLowerCase();
					}
					// For each row call the fuzzy search function to get result
					api.rows().iterator('row', function (the_settings, rowIdx) {
						the_settings.aoData[rowIdx]._fuzzySearch = fuzzySearch(fuzzySearchVal, the_settings.aoData[rowIdx]._aFilterData, initialFuzzy);
					}, false);
					fromPlugin = true;
					// Empty the datatables search and replace it with our own
					api.search('');
					input.val(fuzzySearchVal);
					fromPlugin = false;
				}
				fromPlugin = true;
				api.draw();
				fromPlugin = false;
			}
		};

		DataTable.Api.register( 'search.fuzzy()', function ( value ) {
			if ( undefined === value ) {
				return fuzzySearchVal;
			}

			fuzzySearchVal = value.toLowerCase();
			searchVal = api.search();
			input.val( fuzzySearchVal );
			// For each row call the fuzzy search function to get result
			api.rows().iterator('row', function (the_settings, rowIdx) {
				the_settings.aoData[rowIdx]._fuzzySearch = fuzzySearch(fuzzySearchVal, the_settings.aoData[rowIdx]._aFilterData, initialFuzzy);
			}, false);
			// triggerSearchFunction({key: 'Enter'});
			return this;
		} );
		input.off();

		// Set listeners to occur on toggle and typing
		if ( toggle ) {
			// Highlights one of the buttons in the tooltip and un-highlights the other
			const highlightButton = (toHighlight, event) => {
				if ( ! toHighlight.attr('highlighted') ) {
					toggleFuzzy(event);
				}
			};

			// Removes the tooltip element
			const removeToolTip = () => {
				tooltip.remove();
			};

			// Actions for the toggle button
			toggle
				.on('click', toggleFuzzy)
				.on('mouseenter', function () {
					const thisToggle = this;
					const thisInput = $( thisToggle.previousElementSibling );
					tooltip.insertAfter( thisToggle ).on('mouseleave', removeToolTip);
					tooltip.css( 'left', thisInput.position().left + parseInt( thisInput.css('marginLeft'), 10 ) + 4 ); // 4 is half of the width difference.
					exact.on('click', event => highlightButton(exact, event));
					fuzzy.on('click', event => highlightButton(fuzzy, event));
				})
				.on('mouseleave', removeToolTip)
				.click(); // Start with fuzzy search.

			// Actions for the input element
			input
				.on( 'mouseenter', function () {
					const thisToggle = this.nextElementSibling;
					const thisInput = $( this );
					tooltip.insertAfter( thisToggle ).on( 'mouseleave', removeToolTip );
					tooltip.css( 'left', thisInput.position().left + parseInt( thisInput.css('marginLeft'), 10 ) + 4 ); // 4 is half of the width difference.
					exact.on( 'click', event => highlightButton( exact, event ) );
					fuzzy.on( 'click', event => highlightButton( fuzzy, event ) );
				} )
				.on( 'mouseleave', function () {
					let inToolTip = false;
					tooltip.on( 'mouseenter', () => ( inToolTip = true ) );
					toggle.on( 'mouseenter', () => ( inToolTip = true ) );
					setTimeout( function () {
						if ( ! inToolTip ) {
							removeToolTip();
						}
					}, 250 );
				} );

			const state = api.state.loaded();
			api.on('stateSaveParams', function (ev, the_settings, data) {
				data._fuzzySearch = {
					active: toggle.attr('blurred'),
					val: input.val(),
				};
			});
			if (state !== null && state._fuzzySearch !== undefined) {
				input.val(state._fuzzySearch.val);
				if (state._fuzzySearch.active === 'true') {
					toggle.click();
					api.page(state.start / state.length).draw('page');
				}
			}
		}

		api.on( 'search', function () {
			if ( ! fromPlugin ) {
				input.val(api.search() !== searchVal ? api.search() : fuzzySearchVal);
			}
		} );

		// Always add this event no matter if toggling is enabled
		input.on('input keydown', triggerSearchFunction);

		// Trigger a search on load if the toggle is not shown, so that e.g. auto-filtering works.
		if ( ! toggle ) {
			triggerSearchFunction({ key: 'Enter' });
		}
	} );

})(window, document, jQuery);
