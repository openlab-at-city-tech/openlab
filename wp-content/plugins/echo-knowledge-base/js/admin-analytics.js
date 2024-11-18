jQuery(document).ready(function($) {


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

	display_epkb_pie_chart( 'epkb-popular-articles' );
	display_epkb_pie_chart( 'epkb-not-popular-articles' );

	// show/hide full Pie chart data
	if( $( '.epkb-analytics-page-container' ).length > 0 ) {

		$( '#epkb-article-views-data-content .epkb-pie-chart__more-button' ).on( 'click', function() {

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

	}
});