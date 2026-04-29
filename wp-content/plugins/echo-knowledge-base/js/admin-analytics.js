jQuery(document).ready(function($) {

	// Date Range Filter -----------------------------------------------------------/
	const $dateRangePreset = $('#epkb-analytics-date-range-preset');
	const $dateRangeCustom = $('.epkb-analytics-date-range-custom');
	const $dateRangeApply = $('#epkb-analytics-date-range-apply');
	const $dateStart = $('#epkb-analytics-date-start');
	const $dateEnd = $('#epkb-analytics-date-end');
	const $analyticsContainer = $('.epkb-analytics-page-container');

	// Track the current active preset for filtering
	let currentActivePreset = 'all-time';
	const $quickButtonsContainer = $('.epkb-analytics-date-range-filter__quick-buttons');

	// Preset labels for dynamic buttons
	const presetLabels = {
		'today': 'Today',
		'yesterday': 'Yesterday',
		'this-week': 'This Week',
		'this-month': 'This Month',
		'last-6-months': 'Last 6 Months',
		'this-year': 'This Year',
		'last-year': 'Last Year',
		'custom': 'Custom Range'
	};

	// Show/hide custom date range fields and apply filter for non-custom presets
	if ( $dateRangePreset.length ) {
		$dateRangePreset.on('change', function() {
			const selectedVal = $(this).val();
			if ( selectedVal === 'custom' ) {
				$dateRangeCustom.show();
				// Clear quick button highlights and remove temp button
				$('.epkb-analytics-date-range-quick-btn').removeClass('is-active');
				$('.epkb-analytics-date-range-quick-btn--temp').remove();
			} else if ( selectedVal ) {
				$dateRangeCustom.hide();
				currentActivePreset = selectedVal;
				// Update quick button highlights - add temp button if needed
				updateQuickButtonHighlight(selectedVal);
				applyDateRangeFilter(selectedVal);
				// Reset dropdown to "Choose Range"
				$(this).val('');
			}
		});
	}

	// Quick buttons click handler (delegated for dynamic buttons)
	$quickButtonsContainer.on('click', '.epkb-analytics-date-range-quick-btn', function() {
		const preset = $(this).data('preset');
		currentActivePreset = preset;
		$dateRangeCustom.hide();
		// Remove temp button if clicking a permanent button
		if ( ! $(this).hasClass('epkb-analytics-date-range-quick-btn--temp') ) {
			$('.epkb-analytics-date-range-quick-btn--temp').remove();
		}
		// Update quick button highlights
		$('.epkb-analytics-date-range-quick-btn').removeClass('is-active');
		$(this).addClass('is-active');
		// Reset dropdown to "Choose Range"
		$dateRangePreset.val('');
		applyDateRangeFilter(preset);
	});

	// Update quick button highlight based on selected preset
	function updateQuickButtonHighlight(preset) {
		// Remove any existing temp button
		$('.epkb-analytics-date-range-quick-btn--temp').remove();
		$('.epkb-analytics-date-range-quick-btn').removeClass('is-active');

		// Check if there's a button for this preset
		const $matchingBtn = $('.epkb-analytics-date-range-quick-btn[data-preset="' + preset + '"]');
		if ( $matchingBtn.length ) {
			$matchingBtn.addClass('is-active');
		} else {
			// Create temporary button at the front
			const label = presetLabels[preset] || preset;
			const $tempBtn = $('<button type="button" class="epkb-analytics-date-range-quick-btn epkb-analytics-date-range-quick-btn--temp is-active" data-preset="' + preset + '">' + label + '</button>');
			$quickButtonsContainer.prepend($tempBtn);
		}
	}

	// Apply date range filter (for custom range Apply button)
	if ( $dateRangeApply.length ) {
		$dateRangeApply.on('click', function() {
			applyDateRangeFilter('custom');
		});
	}

	// Apply date range filter function
	function applyDateRangeFilter(presetOverride) {
		const preset = presetOverride || currentActivePreset || 'all-time';
		const startDate = $dateStart.val();
		const endDate = $dateEnd.val();
		const kbId = $analyticsContainer.data('kb-id');

		// Validate custom range
		if ( preset === 'custom' && ( ! startDate || ! endDate ) ) {
			alert( 'Please select both start and end dates.' );
			return;
		}

		if ( preset === 'custom' && new Date(startDate) > new Date(endDate) ) {
			alert( 'Start date must be before end date.' );
			return;
		}

		// Show loading dialog
		showLoadingDialog('Loading...');

		// Make AJAX request
		$.ajax({
			url: window.ajaxurl || ( window.epkb_vars && window.epkb_vars.ajax_url ),
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'epkb_get_filtered_analytics',
				kb_id: kbId,
				preset: preset,
				start_date: startDate,
				end_date: endDate,
				_wpnonce_epkb_ajax_action: window.epkb_vars && window.epkb_vars.nonce ? window.epkb_vars.nonce : ''
			}
		}).done(function(response) {
			if ( response && response.success && response.data && response.data.sections ) {
				// Update all tab panels with filtered content
				const sections = response.data.sections;

				$.each(sections, function(slug, html) {
					const $panel = $('.epkb-analytics-tab-panel[data-analytics-panel="' + slug + '"]');
					if ( $panel.length ) {
						$panel.find('.epkb-analytics-tab-panel__inner').html(html);
					}
				});

				// Re-render charts based on active tab
				const activeTab = $('.epkb-analytics-tab-button.is-active').data('analytics-tab');
				setTimeout(function() {
					if ( activeTab === 'ai-chat' ) {
						renderAIChatEngagementChart();
					} else if ( activeTab === 'time-based-analytics' ) {
						renderTimeBasedCharts();
					} else if ( activeTab === 'article-views' ) {
						renderArticleViewCharts();
					} else if ( activeTab === 'rating' ) {
						renderRatingCharts();
					} else if ( activeTab === 'all-data' || activeTab === 'kb-search' || activeTab === 'search-shortcode' || activeTab === 'widgets' ) {
						renderSearchAnalyticsCharts();
					}
				}, 100);
			} else {
				alert( 'Failed to load analytics data. Please try again.' );
			}
		}).fail(function() {
			alert( 'Failed to load analytics data. Please try again.' );
		}).always(function() {
			// Hide loading dialog
			hideLoadingDialog();
		});
	}

	// Layout Tabs -----------------------------------------------------------------/
	const $analyticsTabButtons = $('.epkb-analytics-tab-button');
	const $analyticsPanels = $('.epkb-analytics-tab-panel');

	if ( $analyticsTabButtons.length ) {
		$analyticsTabButtons.on('click', function() {
			const tab = $(this).data('analytics-tab');

			$analyticsTabButtons.removeClass('is-active');
			$(this).addClass('is-active');

			$analyticsPanels.removeClass('is-active');
			$analyticsPanels.filter(`[data-analytics-panel="${tab}"]`).addClass('is-active');

			if ( tab === 'ai-chat' ) {
				setTimeout(renderAIChatEngagementChart, 80);
			} else if ( tab === 'time-based-analytics' ) {
				setTimeout(renderTimeBasedCharts, 80);
			} else if ( tab === 'article-views' ) {
				setTimeout(renderArticleViewCharts, 80);
			} else if ( tab === 'rating' ) {
				setTimeout(renderRatingCharts, 80);
			} else if ( tab === 'all-data' || tab === 'kb-search' || tab === 'search-shortcode' || tab === 'widgets' ) {
				setTimeout(renderSearchAnalyticsCharts, 80);
			}
		});
	}

	// Charts -----------------------------------------------------------------------/

	/**
	 * Displays Pie Chart from Chart.js Library using data pulled from the page.
	 * This Pie chart only accepts 10 Words.
	 *
	 * @param {string}   container_id       The top most container ID of this Box
	 *
	 */
	function display_epkb_pie_chart( container_id ) {

		let $first_ten_count;
		let $first_ten_words;
		let $remaining_count;
		let $remaining_words;
		let total;

		if ($('.epkb-analytics-page-container').length > 0) {

			//Calculate First 10 list items. -------------------------------/
			$first_ten_count = [];
			$first_ten_words = [];
			total = $('#' + container_id + ' .epkb-pie-data-list .epkb-first-10').length;

			//Get Values from Data List that's beside the pie chart diagram. Pull the Words and the count number.
			$('#' + container_id + ' .epkb-pie-data-list .epkb-first-10').each(function () {
				$first_ten_count.push($(this).find('.epkb-pie-chart-count').text());
				$first_ten_words.push($(this).find('.epkb-pie-chart-word').text());
			});

			//Calculate Remaining list items.  -----------------------------/
			//If Show Data Class exists, then gather all remaining list item data.
			if ($('#' + container_id).hasClass('epkb-pie-chart-container-show-data')) {
				$remaining_count = 0;
				$remaining_words = 'Remaining Results';
				total = $('#' + container_id + ' .epkb-pie-data-list li').length;

				//Count up the total number remaining results counts.
				$('#' + container_id + ' .epkb-pie-data-list .epkb-after-10').each(function () {
					$remaining_count += Number(($(this).find('.epkb-pie-chart-count').text()));
				});
				$first_ten_count.push($remaining_count);
				$first_ten_words.push($remaining_words);

			}

			//Display Total beside the title.
			$('#' + container_id + ' .epkb-pie-data-total').remove();
			$('#' + container_id + ' h4').append('<span class="epkb-pie-data-total"> ( ' + total + ' )</span>');

			//Clear Canvas Tag for new Pie chart.
			$('#' + container_id + '-chart').remove();
			$('#' + container_id + ' .epkb-pie-chart-right-col #epkb-pie-chart').append('<canvas id="' + container_id + '-chart' + '"><canvas>');

			const ctx = document.getElementById(container_id + '-chart').getContext('2d');
			new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: $first_ten_words,
					datasets: [{
						data: $first_ten_count,
						backgroundColor: [
							'#aed581',
							'#4fc3f7',
							'#FED32F',
							'#ef5350',
							'#D0D8E0',
							'#ff8a65',
							'#ba68c8',
							'#4A7BEC',
							'#768CA3',
							'#8d6e63',
							'#444444',
						],
					}]
				},
				options: {
					maintainAspectRatio: false,
					legend: {
						display: false,
						position: 'left',
					},
				}
			});
		}
	}

	function renderArticleViewCharts() {
		if ( ! $( '#epkb-article-views-data-content' ).length ) {
			return;
		}

		// Only render charts if containers exist (they won't exist if there's no data)
		if ( $( '#epkb-popular-articles' ).length ) {
			display_epkb_pie_chart( 'epkb-popular-articles' );
		}
		if ( $( '#epkb-not-popular-articles' ).length ) {
			display_epkb_pie_chart( 'epkb-not-popular-articles' );
		}
		if ( $( '#epkb-high-performers' ).length ) {
			display_epkb_pie_chart( 'epkb-high-performers' );
		}
		if ( $( '#epkb-low-performers' ).length ) {
			display_epkb_pie_chart( 'epkb-low-performers' );
		}
	}

	function renderTimeBasedCharts() {
		if ( ! $( '.epkb-time-based-analytics-container' ).length ) {
			return;
		}

		// Render Views Over Time line chart
		const $timeChart = $( '#epkb-time-chart' );
		if ( $timeChart.length ) {
			const weeklyData = $timeChart.data( 'weekly-data' );
			if ( weeklyData && weeklyData.length > 0 ) {
				const labels = weeklyData.map( item => item.week_label );
				const data = weeklyData.map( item => item.total_views );

				const ctx = document.getElementById( 'epkb-time-chart' ).getContext( '2d' );

				// Destroy existing chart if it exists
				if ( window.epkbTimeChart && typeof window.epkbTimeChart.destroy === 'function' ) {
					window.epkbTimeChart.destroy();
				}

				window.epkbTimeChart = new Chart( ctx, {
					type: 'line',
					data: {
						labels: labels,
						datasets: [{
							label: 'Views',
							data: data,
							borderColor: '#4A7BEC',
							backgroundColor: 'rgba(74, 123, 236, 0.1)',
							borderWidth: 2,
							fill: true,
							tension: 0.4,
							pointRadius: 4,
							pointBackgroundColor: '#4A7BEC',
							pointBorderColor: '#fff',
							pointBorderWidth: 2,
							pointHoverRadius: 6,
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								mode: 'index',
								intersect: false,
								backgroundColor: 'rgba(0, 0, 0, 0.8)',
								padding: 12,
								titleColor: '#fff',
								bodyColor: '#fff',
								borderColor: '#4A7BEC',
								borderWidth: 1,
								displayColors: false,
								callbacks: {
									title: function( context ) {
										return context[0].label;
									},
									label: function( context ) {
										return 'Views: ' + context.parsed.y.toLocaleString();
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									precision: 0
								},
								grid: {
									color: 'rgba(0, 0, 0, 0.05)'
								}
							},
							x: {
								grid: {
									display: false
								},
								ticks: {
									maxRotation: 45,
									minRotation: 45
								}
							}
						}
					}
				} );
			}
		}

		// Render Article Engagement Distribution bar chart
		const $engagementChart = $( '#epkb-engagement-distribution-chart' );
		if ( $engagementChart.length ) {
			const distributionData = $engagementChart.data( 'distribution' );
			if ( distributionData && distributionData.length > 0 ) {
				const labels = distributionData.map( item => item.label );
				const data = distributionData.map( item => item.count );

				const ctx = document.getElementById( 'epkb-engagement-distribution-chart' ).getContext( '2d' );

				// Destroy existing chart if it exists
				if ( window.epkbEngagementChart && typeof window.epkbEngagementChart.destroy === 'function' ) {
					window.epkbEngagementChart.destroy();
				}

				// Assign colors based on the label (handle dynamic segments)
				const colorMap = {
					'0 Views': '#ef5350',        // Red
					'1-10 Views': '#ff8a65',     // Orange
					'11-50 Views': '#FED32F',    // Yellow
					'51-100 Views': '#aed581',   // Light green
					'101-500 Views': '#4fc3f7',  // Blue
					'500+ Views': '#4A7BEC'      // Dark blue
				};

				const backgroundColors = labels.map( label => colorMap[label] || '#999999' );

				window.epkbEngagementChart = new Chart( ctx, {
					type: 'bar',
					data: {
						labels: labels,
						datasets: [{
							label: 'Number of Articles',
							data: data,
							backgroundColor: backgroundColors,
							borderColor: backgroundColors,
							borderWidth: 1,
							borderRadius: 4,
						}]
					},
					options: {
						indexAxis: 'y',
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								backgroundColor: 'rgba(0, 0, 0, 0.8)',
								padding: 12,
								titleColor: '#fff',
								bodyColor: '#fff',
								borderColor: '#4A7BEC',
								borderWidth: 1,
								displayColors: false,
								callbacks: {
									label: function( context ) {
										const value = context.parsed.x;
										const total = context.dataset.data.reduce( ( a, b ) => a + b, 0 );
										const percentage = ( ( value / total ) * 100 ).toFixed( 1 );
										return value + ' articles (' + percentage + '%)';
									}
								}
							}
						},
						scales: {
							x: {
								beginAtZero: true,
								ticks: {
									precision: 0
								},
								grid: {
									color: 'rgba(0, 0, 0, 0.05)'
								}
							},
							y: {
								grid: {
									display: false
								}
							}
						}
					}
				} );
			}
		}

		// Render Searches Over Time line chart
		const $searchesChart = $( '#epkb-searches-chart' );
		if ( $searchesChart.length ) {
			const searchesData = $searchesChart.data( 'searches-data' );
			if ( searchesData && searchesData.length > 0 ) {
				const labels = searchesData.map( item => item.week_label );
				const data = searchesData.map( item => item.total_searches );

				const ctx = document.getElementById( 'epkb-searches-chart' ).getContext( '2d' );

				// Destroy existing chart if it exists
				if ( window.epkbSearchesChart && typeof window.epkbSearchesChart.destroy === 'function' ) {
					window.epkbSearchesChart.destroy();
				}

				window.epkbSearchesChart = new Chart( ctx, {
					type: 'line',
					data: {
						labels: labels,
						datasets: [{
							label: 'Searches',
							data: data,
							borderColor: '#f39c12',
							backgroundColor: 'rgba(243, 156, 18, 0.1)',
							borderWidth: 2,
							fill: true,
							tension: 0.4,
							pointRadius: 4,
							pointBackgroundColor: '#f39c12',
							pointBorderColor: '#fff',
							pointBorderWidth: 2,
							pointHoverRadius: 6,
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								mode: 'index',
								intersect: false,
								backgroundColor: 'rgba(0, 0, 0, 0.8)',
								padding: 12,
								titleColor: '#fff',
								bodyColor: '#fff',
								borderColor: '#f39c12',
								borderWidth: 1,
								displayColors: false,
								callbacks: {
									title: function( context ) {
										return context[0].label;
									},
									label: function( context ) {
										return 'Searches: ' + context.parsed.y.toLocaleString();
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									precision: 0
								},
								grid: {
									color: 'rgba(0, 0, 0, 0.05)'
								}
							},
							x: {
								grid: {
									display: false
								},
								ticks: {
									maxRotation: 45,
									minRotation: 45
								}
							}
						}
					}
				} );
			}
		}

		// Render Ratings Over Time line chart (with positive and negative)
		const $ratingsChart = $( '#epkb-ratings-chart' );
		if ( $ratingsChart.length ) {
			const ratingsData = $ratingsChart.data( 'ratings-data' );
			if ( ratingsData && ratingsData.length > 0 ) {
				const labels = ratingsData.map( item => item.week_label );
				const positiveData = ratingsData.map( item => item.positive_ratings );
				const negativeData = ratingsData.map( item => item.negative_ratings );

				const ctx = document.getElementById( 'epkb-ratings-chart' ).getContext( '2d' );

				// Destroy existing chart if it exists
				if ( window.epkbRatingsChart && typeof window.epkbRatingsChart.destroy === 'function' ) {
					window.epkbRatingsChart.destroy();
				}

				window.epkbRatingsChart = new Chart( ctx, {
					type: 'line',
					data: {
						labels: labels,
						datasets: [
							{
								label: 'Positive Feedback',
								data: positiveData,
								borderColor: '#27ae60',
								backgroundColor: 'rgba(39, 174, 96, 0.1)',
								borderWidth: 2,
								fill: true,
								tension: 0.4,
								pointRadius: 4,
								pointBackgroundColor: '#27ae60',
								pointBorderColor: '#fff',
								pointBorderWidth: 2,
								pointHoverRadius: 6,
							},
							{
								label: 'Negative Feedback',
								data: negativeData,
								borderColor: '#e74c3c',
								backgroundColor: 'rgba(231, 76, 60, 0.1)',
								borderWidth: 2,
								fill: true,
								tension: 0.4,
								pointRadius: 4,
								pointBackgroundColor: '#e74c3c',
								pointBorderColor: '#fff',
								pointBorderWidth: 2,
								pointHoverRadius: 6,
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: true,
								position: 'top',
								labels: {
									usePointStyle: true,
									padding: 15
								}
							},
							tooltip: {
								mode: 'index',
								intersect: false,
								backgroundColor: 'rgba(0, 0, 0, 0.8)',
								padding: 12,
								titleColor: '#fff',
								bodyColor: '#fff',
								borderColor: '#4A7BEC',
								borderWidth: 1,
								displayColors: true,
								callbacks: {
									title: function( context ) {
										return context[0].label;
									},
									label: function( context ) {
										return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									precision: 0
								},
								grid: {
									color: 'rgba(0, 0, 0, 0.05)'
								}
							},
							x: {
								grid: {
									display: false
								},
								ticks: {
									maxRotation: 45,
									minRotation: 45
								}
							}
						}
					}
				} );
			}
		}
	}

	function renderRatingCharts() {
		if ( ! $( '#eprf-rating-data-content' ).length ) {
			return;
		}

		if ( typeof display_eprf_pie_chart === 'function' ) {
			display_eprf_pie_chart( 'eprf-best-ratinges-data' );
			display_eprf_pie_chart( 'eprf-worst-ratinges-data' );
			display_eprf_pie_chart( 'eprf-popular-ratinges-data' );
			display_eprf_pie_chart( 'eprf-no-result-popular-ratinges-data' );
		}
	}

	function renderSearchAnalyticsCharts() {
		// Check if ASEA chart function is available
		if ( typeof display_asea_pie_chart !== 'function' ) {
			return;
		}

		// Find all ASEA pie chart containers in the currently active tab
		const $activePanel = $('.epkb-analytics-tab-panel.is-active');
		$activePanel.find('.asea-pie-chart-container').each(function() {
			const chartId = $(this).attr('id');
			if ( chartId ) {
				display_asea_pie_chart( chartId );
			}
		});
	}

	/**
	 * Render all AI Chat charts
	 */
	function renderAIChatEngagementChart() {
		renderAIChatConversationsChart();
		renderAIChatMessagesChart();
		renderAIChatEngagementStackedChart();
	}

	/**
	 * Render AI Chat Conversations Over Time Chart
	 */
	function renderAIChatConversationsChart() {
		const $chartCanvas = $('#epkb-ai-chat-conversations-chart');
		if ( ! $chartCanvas.length ) {
			return;
		}

		const chartDataAttr = $chartCanvas.data('chart-data');
		if ( ! chartDataAttr || ! Array.isArray(chartDataAttr) || chartDataAttr.length === 0 ) {
			return;
		}

		// Destroy existing chart if it exists
		if ( window.epkbAIChatConversationsChart && typeof window.epkbAIChatConversationsChart.destroy === 'function' ) {
			window.epkbAIChatConversationsChart.destroy();
		}

		const labels = chartDataAttr.map(item => {
			const date = new Date(item.date);
			return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
		});
		const conversationsData = chartDataAttr.map(item => item.conversations || 0);

		const ctx = document.getElementById('epkb-ai-chat-conversations-chart').getContext('2d');

		window.epkbAIChatConversationsChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: 'Conversations',
					data: conversationsData,
					borderColor: '#2271b1',
					backgroundColor: 'rgba(34, 113, 177, 0.1)',
					borderWidth: 2,
					fill: true,
					tension: 0.3,
					pointBackgroundColor: '#2271b1',
					pointBorderColor: '#fff',
					pointBorderWidth: 2,
					pointRadius: 4,
					pointHoverRadius: 6
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						titleColor: '#fff',
						bodyColor: '#fff'
					}
				},
				scales: {
					x: {
						grid: { display: false },
						ticks: { maxRotation: 45, minRotation: 45 }
					},
					y: {
						beginAtZero: true,
						ticks: { precision: 0 },
						grid: { color: 'rgba(0, 0, 0, 0.05)' }
					}
				}
			}
		});
	}

	/**
	 * Render AI Chat Messages Over Time Chart
	 */
	function renderAIChatMessagesChart() {
		const $chartCanvas = $('#epkb-ai-chat-messages-chart');
		if ( ! $chartCanvas.length ) {
			return;
		}

		const chartDataAttr = $chartCanvas.data('chart-data');
		if ( ! chartDataAttr || ! Array.isArray(chartDataAttr) || chartDataAttr.length === 0 ) {
			return;
		}

		// Destroy existing chart if it exists
		if ( window.epkbAIChatMessagesChart && typeof window.epkbAIChatMessagesChart.destroy === 'function' ) {
			window.epkbAIChatMessagesChart.destroy();
		}

		const labels = chartDataAttr.map(item => {
			const date = new Date(item.date);
			return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
		});
		const messagesData = chartDataAttr.map(item => item.messages || 0);

		const ctx = document.getElementById('epkb-ai-chat-messages-chart').getContext('2d');

		window.epkbAIChatMessagesChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: 'Messages',
					data: messagesData,
					borderColor: '#9b59b6',
					backgroundColor: 'rgba(155, 89, 182, 0.1)',
					borderWidth: 2,
					fill: true,
					tension: 0.3,
					pointBackgroundColor: '#9b59b6',
					pointBorderColor: '#fff',
					pointBorderWidth: 2,
					pointRadius: 4,
					pointHoverRadius: 6
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						titleColor: '#fff',
						bodyColor: '#fff'
					}
				},
				scales: {
					x: {
						grid: { display: false },
						ticks: { maxRotation: 45, minRotation: 45 }
					},
					y: {
						beginAtZero: true,
						ticks: { precision: 0 },
						grid: { color: 'rgba(0, 0, 0, 0.05)' }
					}
				}
			}
		});
	}

	/**
	 * Render AI Chat Engagement Stacked Chart
	 * Shows a stacked bar chart with thumbs up, thumbs down, and handoffs over time
	 */
	function renderAIChatEngagementStackedChart() {
		const $chartCanvas = $('#epkb-ai-chat-engagement-chart');
		if ( ! $chartCanvas.length ) {
			return;
		}

		const chartDataAttr = $chartCanvas.data('chart-data');
		if ( ! chartDataAttr || ! Array.isArray(chartDataAttr) || chartDataAttr.length === 0 ) {
			return;
		}

		// Destroy existing chart if it exists
		if ( window.epkbAIChatEngagementChart && typeof window.epkbAIChatEngagementChart.destroy === 'function' ) {
			window.epkbAIChatEngagementChart.destroy();
		}

		const labels = chartDataAttr.map(item => {
			const date = new Date(item.date);
			return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
		});
		const thumbsUpData = chartDataAttr.map(item => item.thumbs_up || 0);
		const thumbsDownData = chartDataAttr.map(item => item.thumbs_down || 0);
		const handoffsData = chartDataAttr.map(item => item.handoffs || 0);

		const ctx = document.getElementById('epkb-ai-chat-engagement-chart').getContext('2d');

		window.epkbAIChatEngagementChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [
					{
						label: 'Thumbs Up',
						data: thumbsUpData,
						backgroundColor: '#27ae60',
						borderColor: '#1e8449',
						borderWidth: 1,
					},
					{
						label: 'Thumbs Down',
						data: thumbsDownData,
						backgroundColor: '#e74c3c',
						borderColor: '#c0392b',
						borderWidth: 1,
					},
					{
						label: 'Handoffs',
						data: handoffsData,
						backgroundColor: '#f39c12',
						borderColor: '#d68910',
						borderWidth: 1,
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: true,
						position: 'top',
						labels: {
							usePointStyle: true,
							padding: 15
						}
					},
					tooltip: {
						mode: 'index',
						intersect: false,
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						titleColor: '#fff',
						bodyColor: '#fff',
						borderColor: '#4A7BEC',
						borderWidth: 1,
						displayColors: true
					}
				},
				scales: {
					x: {
						stacked: true,
						grid: {
							display: false
						},
						ticks: {
							maxRotation: 45,
							minRotation: 45
						}
					},
					y: {
						stacked: true,
						beginAtZero: true,
						ticks: {
							precision: 0
						},
						grid: {
							color: 'rgba(0, 0, 0, 0.05)'
						}
					}
				}
			}
		});
	}

	// show/hide full Pie chart data
	const $articleViewsToggle = $('.epkb-article-views-toggle__input');

	if ( $articleViewsToggle.length ) {
		$articleViewsToggle.on('change', function() {
			const $input = $(this);
			const isChecked = $input.is(':checked');

			if ( ! isChecked ) {
				$input.prop('checked', false);
				return;
			}

			const $container = $input.closest('.epkb-analytics-article-views-disabled');
			const kbId = $container.data('kb-id');
			const $status = $container.find('.epkb-article-views-toggle__status');
			const ajaxUrl = window.ajaxurl || ( window.epkb_vars && window.epkb_vars.ajax_url );

			if ( ! kbId || ! ajaxUrl ) {
				console.error('EPKB: Missing data to toggle article views counter.', { kbId: kbId, ajaxUrl: ajaxUrl });
				$input.prop('checked', false);
				return;
			}

			if ( $container.hasClass('is-processing') ) {
				return;
			}

			const enablingMessage = $container.data('enablingMessage') || 'Enabling article views counter...';
			const successMessage = $container.data('successMessage') || 'Article views counter enabled. Reloading...';
			const errorMessage = $container.data('errorMessage') || 'Unable to update setting. Please try again.';

			$container.addClass('is-processing');
			$input.prop('disabled', true);

			if ( $status.length ) {
				$status.text(enablingMessage);
			}

			$.ajax({
				url: ajaxUrl,
				method: 'POST',
				dataType: 'json',
				data: {
					action: 'epkb_toggle_article_views_counter',
					kb_id: kbId,
					enable: 'on',
					_wpnonce_epkb_ajax_action: window.epkb_vars && window.epkb_vars.nonce ? window.epkb_vars.nonce : ''
				}
			}).done(function(response) {
				if ( response && response.success ) {
					if ( $status.length ) {
						$status.text(successMessage);
					}
					// Reload page to show updated analytics data
					setTimeout(function() {
						window.location.reload();
					}, 600);
					return;
				}

				handleToggleError(response && response.data && response.data.message ? response.data.message : errorMessage);
			}).fail(function() {
				handleToggleError(errorMessage);
			});

			function handleToggleError(message) {
				$container.removeClass('is-processing');
				$input.prop('disabled', false).prop('checked', false);
				if ( $status.length ) {
					$status.text(message);
				}
			}
		});
	}

	if( $( '.epkb-analytics-page-container' ).length > 0 ) {

		$( document ).on( 'click', '.epkb-pie-chart__more-button', function() {

			//Get this containers ID
			const id = $( this ).closest( '.epkb-pie-chart-container' ).attr( 'id' );

			//Toggle Class for the main container
			$( '#' + id ).toggleClass( 'epkb-pie-chart-container-show-data' );

			// Toggle More/Less Button Class for the text
			$( `#${id} .epkb-pie-chart__more-button__more-text` ).toggleClass('epkb-hidden');
			$( `#${id} .epkb-pie-chart__more-button__less-text` ).toggleClass('epkb-hidden');

			//Show all Data
			display_epkb_pie_chart( id );
		});

		// Handle show more/less for article lists
		$( document ).on( 'click', '.epkb-article-list__more-button', function() {
			const $button = $( this );
			const $container = $button.closest( '.epkb-list-container, .epkb-improvement-container' );
			const $hiddenItems = $container.find( '.epkb-after-20' );

			// Toggle visibility
			$hiddenItems.toggle();

			// Toggle button text
			$button.find( '.epkb-article-list__more-button__more-text' ).toggleClass('epkb-hidden');
			$button.find( '.epkb-article-list__more-button__less-text' ).toggleClass('epkb-hidden');
		});

	}

	// Render charts on page load if needed
	if ( $('.epkb-analytics-tab-button[data-analytics-tab="ai-chat"]').hasClass('is-active') ) {
		setTimeout(renderAIChatEngagementChart, 80);
	}
	if ( $('.epkb-analytics-tab-button[data-analytics-tab="article-views"]').hasClass('is-active') ) {
		setTimeout(renderArticleViewCharts, 80);
	}
	if ( $('.epkb-analytics-tab-button[data-analytics-tab="time-based-analytics"]').hasClass('is-active') ) {
		setTimeout(renderTimeBasedCharts, 80);
	}
	if ( $('.epkb-analytics-tab-button[data-analytics-tab="rating"]').hasClass('is-active') ) {
		setTimeout(renderRatingCharts, 80);
	}

	// Loading Dialog Functions ----------------------------------------------------/

	/**
	 * Show loading dialog
	 * @param {string} message - Message to display in the dialog
	 */
	function showLoadingDialog( message ) {
		// Remove any existing dialogs first
		hideLoadingDialog();

		const output =
			'<div class="epkb-admin-dialog-box-loading">' +
				'<div class="epkb-admin-dbl__header">' +
					'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>' +
					( message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>' +
			'</div>' +
			'<div class="epkb-admin-dialog-box-overlay"></div>';

		$( 'body' ).append( output );
	}

	/**
	 * Hide loading dialog
	 */
	function hideLoadingDialog() {
		$( '.epkb-admin-dialog-box-loading' ).remove();
		$( '.epkb-admin-dialog-box-overlay' ).remove();
	}
});
