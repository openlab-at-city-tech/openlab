jQuery(document).ready(function($) {
	var ajaxurl = dkpdfg_admin_data.ajaxurl;
	var selected_posts = [];

	// fires when selectize changes, updates dkpdfg_selected_posts option
	var selectizeOnChange = function( name ) {

		return function() {

			selected_posts = [];

			$('.dkpdf-posts-controller .selectize-input div').each(function( index ) {

			  	selected_posts.push( $( this ).attr('id') );

			});

			// enable/disable Create PDF button
			if( selected_posts.length > 0 ) {

				$('#dkpdfg-create-button').prop('disabled', false);

			} else {

				$('#dkpdfg-create-button').prop('disabled', true);

			}

			// updates dkpdfg_selected_posts option
			jQuery.ajax({

				type : 'post',
				dataType : 'json',
				url : ajaxurl,
				data : {
					action: 'update_selected_posts',
					ids: selected_posts
				},

				success: function( response ) {

					if( response.type == 'success') {

						console.log('response ok: ' + response.msg);

					} else {

						console.log('response ko: ' + response.msg);

					}

				}

			});

		};

	};

	// set selectize
	var select_search_posts = $('#dkpdfg-search-posts').selectize({

		onChange : selectizeOnChange('onChange'),
		plugins: ['drag_drop'],
	    valueField: 'name',
	    labelField: 'name',
	    searchField: 'name',
	    options: [],
	    load: function( query, callback ) {

	        if ( !query.length ) return callback();

	        $.ajax({
	            url: ajaxurl,
	            type: 'POST',
	            dataType: 'json',
	            data: {
	                name: query,
	                action:'dkpdfg_search_posts'
	            },
	            error: function() { callback(); },
	            success: function( res ) {

	                callback( res );

	            }
	        });

	    },
	    create: false,
	    render: {
	        option: function( item, escape ) {

	        	return '<div id="' + item.id + '">' + item.name + '</div>';

	        },
	        item: function( item ) {

	        	return '<div id="' + item.id + '">' + item.name + '</div>';

		    }
	    },

	});

	// adds dkpdfg_selected_posts as selectize items on page load
	if( $('#dkpdfg-search-posts').length ) {

		setTimeout(function(){

			jQuery.ajax({
				type : 'post',
				dataType : 'json',
				url : ajaxurl,
				data : {
					action: 'get_selected_posts',
				},
				success: function( response ) {

					if( response.type == 'success') {

						var control = select_search_posts[0].selectize;

						// enable/disable Create PDF button
						if( response.msg.length ) {

							$('#dkpdfg-create-button').prop('disabled', false);

						} else {

							$('#dkpdfg-create-button').prop('disabled', true);

						}

						for ( var i = 0; i < response.msg.length; i++) {

							/*
							console.log(response.msg[i][0]); // id
							console.log(response.msg[i][1]); // name
							console.log('-----------');
							*/

							control.addOption({id: response.msg[i][0], name: response.msg[i][1]});
							control.addItem( response.msg[i][1], true );

						};

					} else {

						//console.log( response.msg );

					}

				}

			});

		}, 1000);

	}

	// clear options button
	$('#dkpdfg-clearoptions').on('click', function(e) {

		e.preventDefault();

		var control = select_search_posts[0].selectize;

		control.clearOptions();

	});

	// select categories - since 1.3
	var selected_categories = [];

	// fires when selectize changes, updates dkpdfg_selected_categories option and date from/to
	var selectizeCategoriesOnChange = function( name ) {

		return function() {

			selected_categories = [];

			$('.dkpdf-categories-controller .selectize-input div').each(function( index ) {

			  	selected_categories.push( $( this ).attr('id') );

			});

			// enable/disable Create PDF button
			if( selected_categories.length > 0 ) {

				$('#dkpdfg-create-categories-button').prop('disabled', false);

			} else {

				$('#dkpdfg-create-categories-button').prop('disabled', true);

			}

			// updates dkpdfg_selected_categories option
			jQuery.ajax({

				type : 'post',
				dataType : 'json',
				url : ajaxurl,
				data : {
					action: 'update_selected_categories',
					ids: selected_categories
				},

				success: function( response ) {

					if( response.type == 'success') {

						console.log('response ok: ' + response.msg);

					} else {

						console.log('response ko: ' + response.msg);

					}

				}

			});

		};

	};

	// set selectize
	var select_search_categories = $('#dkpdfg-search-categories').selectize({

		onChange : selectizeCategoriesOnChange('onChange'),
		plugins: ['drag_drop'],
	    valueField: 'name',
	    labelField: 'name',
	    searchField: 'name',
	    options: [],
	    load: function( query, callback ) {

	        if ( !query.length ) return callback();

	        $.ajax({
	            url: ajaxurl,
	            type: 'POST',
	            dataType: 'json',
	            data: {
	                name: query,
	                action:'dkpdfg_search_categories'
	            },
	            error: function() { callback(); },
	            success: function( res ) {

	                callback( res );

	            }
	        });

	    },
	    create: false,
	    render: {
	        option: function( item, escape ) {

	        	return '<div id="' + item.id + '">' + item.name + '</div>';

	        },
	        item: function( item ) {

	        	return '<div id="' + item.id + '">' + item.name + '</div>';

		    }
	    },

	});

	// adds dkpdfg_selected_categories as selectize items on page load
	if( $('#dkpdfg-search-categories').length ) {

		setTimeout(function(){

			jQuery.ajax({
				type : 'post',
				dataType : 'json',
				url : ajaxurl,
				data : {
					action: 'get_selected_categories',
				},
				success: function( response ) {

					if( response.type == 'success') {

						var control = select_search_categories[0].selectize;

						// enable/disable Create PDF button
						if( response.msg.length ) {

							$('#dkpdfg-create-categories-button').prop('disabled', false);

						} else {

							$('#dkpdfg-create-categories-button').prop('disabled', true);

						}

						for ( var i = 0; i < response.msg.length; i++) {

							/*
							console.log(response.msg[i][0]); // id
							console.log(response.msg[i][1]); // name
							console.log('-----------');
							*/

							control.addOption({id: response.msg[i][0], name: response.msg[i][1]});
							control.addItem( response.msg[i][1], true );

						};

					} else {

						//console.log( response.msg );

					}

				}

			});

		}, 1000);

	}

	// clear options button
	$('#dkpdfg-categories-clearoptions').on('click', function(e) {
		e.preventDefault();
		var control = select_search_categories[0].selectize;
		control.clearOptions();
	});

	$('#dkpdfg-date-from').datepicker({
        dateFormat : 'yy-mm-dd'
    });
	$('#dkpdfg-date-to').datepicker({
		dateFormat : 'yy-mm-dd'
    });

	// update dates
	var date_from = '';
	var date_to = '';

    $('.dkpdfg-dates').change(function() {

    	date_from = $('#dkpdfg-date-from').val();
    	date_to = $('#dkpdfg-date-to').val();

		// updates dkpdfg_date_from and dkpdfg_date_to options
		jQuery.ajax({
			type : 'post',
			dataType : 'json',
			url : ajaxurl,
			data : {
				action: 'update_date_ranges',
				date_from: date_from,
				date_to : date_to,
			},
			success: function( response ) {
				if( response.type == 'success') {
					//console.log('response ok: ' + response.msg);
				} else {
					//console.log('response ko: ' + response.msg);
				}
			}
		});

	});


});


