/*
Copyright (C) <2013> <Ren Aysha>

This file is part of Tako.

Tako is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Tako is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Tako.  If not, see <http://www.gnu.org/licenses/>.
*/

jQuery( document ).ready(function( $ ) {

	var TakoDropdown = {

		types: $( '#tako_post_type' ),
		current: $( '#tako_current_comment' ).text(),
		select: $( '#tako_post' ),
		spinner: $( '#tako_spinner' ),

		init: function() {
			this.takoGetPostType();
		},

		takoGetPostType: function( options ) {
			var self = this, select, spinner, opt = options || {};

			select = opt.selector || self.types;
			spinner = opt.spinner || this.spinner;

			select.change(function() {
				spinner.show();
				var current = $( this ).find( 'option:selected' ).text();
				self.takoProcessData( self.takoData( current ), opt );
			}).change();
		},

		takoData: function( current ) {
			var data = {
				action: 'tako_chosen_post_type',
				postype: current,
				tako_ajax_nonce: tako_object.tako_ajax_nonce 
			};
			
			return data;
		},

		takoProcessData: function( data, options ) {
			var self = this, select, spinner, opt = options || {};

			select = opt.post || this.select;
			spinner =  opt.spinner || this.spinner;

			$.post( ajaxurl, data, function( response ) {
				var responses = JSON.parse( response );

				select.empty();

				$.map( responses, function( item ) {
					var option = $( '<option></option>' ).val( item.ID );
					select.append( option.text( item.title ) );
				});

				select.val( self.current );
				select.chosen( { width: opt.width || '30%' } );

				select.trigger( "liszt:updated" );
				spinner.hide();
			});
		}
	};

	var TakoBulk = {

		comments: $( '#the-comment-list' ),
		template: $( '#bulk-edit-template' ),
		one: $( 'select[name="action"]' ),
		two: $( 'select[name="action2"]' ),
		checkbox: $( 'input:checkbox[name=\'delete_comments[]\']' ),
		lists: $( '#bulk-titles' ),

		init: function() {
			this.fetch = [];

			this.comments.prepend( this.template.html() );

			this.button();
			this.ids();
			this.cancel();
			this.move();
		},

		button: function() {
			var self = this;

			self.one.add( self.two ).change(function() {
				if ( $( this ).val() === 'move' ) {
					self.action();
				}
			});
		},

		action: function() {
			var self = this, list = $( '#tako-move-list' ), tmpl;

			$( '#doaction, #doaction2' ).on( 'click', function( e ) {
				if ( self.fetch.length > 0 )
					self.listOut();
				
				e.preventDefault();
			});
		},

		listOut: function() {
			var self = this, list = $( '#tako-move-list' ), tmpl;

			self.extra();
			tmpl = self.tmpl( list.html() );

			for ( var i = 0; i < self.fetch.length; i++ ) {
				$( '#bulk-titles' ).append( tmpl( self.data( self.fetch[ i ] ) ) );
			}

			this.destroy();

			$( '#bulky-comment' ).css( 'display', 'table-row' );
		},

		extra: function() {
			var options = {
				selector: $( '#tako_post_type' ),
				spinner: $( '#tako_spinner' ),
				post: $( '#tako_post' ),
				width: '80%'
			};

			this.types();
			TakoDropdown.takoGetPostType( options );
		},

		tmpl: function( source ) {
			return Handlebars.compile( source );
		},

		ids: function() {
			var self = this, fetch, index, check = self.checkbox;
			
			$( '#cb-select-all-1' ).change(function() {
				for ( var i = 0; i < self.checkbox.length; i++ ) {
					if ( $( this ).attr( 'checked' ) ) {
						self.fetch.push( parseInt( check.eq( i ).val(), 10 ) );
					} else {
						index = $.inArray( parseInt( check.eq( i ).val(), 10 ), self.fetch );
						self.fetch.splice( index, 1 );
					}
				}
			});

			self.checkbox.change(function() {
				index = $.inArray( $( this ).val(), self.fetch );
				
				fetch = $( this ).attr( 'checked' ) ?
					self.fetch.push( parseInt( $( this ).val(), 10 ) ) :
					self.unset( parseInt( $( this ).val(), 10 ) );
			});

			return self.fetch;
		},

		data: function( index ) {
			return {
				id: index,
				author: $( '#comment-' + index + ' .author > strong' ).text(),
				comment: $( '#comment-' + index + ' .comment > p' ).text(),
				date: $( '#comment-' + index + ' .submitted-on > a' ).text(),
				post: $( '#comment-' + index + ' .post-com-count-wrapper > a:first' ).text(),
				gravatar: $( '#comment-' + index + ' .author img' ).attr( 'src' ).replace( /(s=32)/gi, 's=60' )
			};
		},

		cancel: function() {
			var self = this;

			$( '#tako-cancel-bulk' ).on( 'click', function() {
				$( '[data-comment]' ).remove();
				$( '#bulky-comment' ).css( 'display', 'none' );
			});
		},

		unset: function( index ) {
			var self = this, n;
			n = $.inArray( index, self.fetch );
			self.fetch.splice( n, 1 );
		},

		destroy: function() {
			var self = this, remove = $( '[data-tako]' ), index;
			
			remove.on( 'click', function( e ) {
				index = $( this ).data( 'tako' );
				
				$( '[data-comment=' + index + ']' ).remove();

				self.unset( index );
				e.preventDefault();
			});
		},

		types: function() {
			var self = this, template = $( '#tako-post-type-opt' ), 
				data, select, tmpl;

			select = $( '#tako_post_type' );

			data = {
				action: 'tako_post_types',
				tako_ajax_nonce: tako_object.tako_ajax_nonce
			};

			tmpl = self.tmpl( template.html() );

			$.get( ajaxurl, data, function( response ) {
				var responses = JSON.parse( response ), datum;
				datum = { responses: responses };
				select.append( tmpl( datum ) );
			});
		},

		posts: function( data ) {
			var comment, post, id, href;
			
			for ( var i = 0; i < data.comments.length; i++ ) {
				post = 'post=' + data.post_id; id = data.comments[ i ];
				comment = $( '#comment-' + id + ' .post-com-count-wrapper > a:first' );
				
				if ( comment.length > 0 ) {
					href = comment.attr( 'href' ).replace( /(post=[0-9]*)/g, post );
					comment.text( data.title );
					comment.attr( 'href', href );
				}
			}
		},

		move: function() {
			var self = this, data, comments, 
				scr = $( '#tako-success-message' ), tmpl;
		
			data = {
				action: 'tako_move_bulk',
				tako_ajax_nonce: tako_object.tako_ajax_nonce
			};

			$( '#bulk-edit' ).on( 'click', function( e ) {
				$( '#tako_bulk_spinner' ).show();

				data.post_id = parseInt( $( '#tako_post' ).val(), 10 );
				data.comments = JSON.stringify( self.fetch );

				tmpl = self.tmpl( scr.html() );

				$.post( ajaxurl, data, function( response ) {
					var responses = JSON.parse( response ), datum;	
					
					datum = { message: 'Successfully move comments!' };

					if ( responses.success === 1 ) {
						$( 'h2' ).append( tmpl( datum ) );
						self.posts( responses );
						
						$( '[data-comment]' ).remove();
						$( '#bulky-comment' ).css( 'display', 'none' );
						
						$( '#tako_bulk_spinner' ).hide();
					} else {
						datum.message = 'Comments are not moved. Are you sure you\'ve picked a post title that is different than the one that the comments currently belong to?';
						$( 'h2' ).append( tmpl( datum ) );
						self.posts( responses );

						$( '#tako_bulk_spinner' ).hide();
					}
				});

				return false;
			});
		}
	};

	TakoDropdown.init();
	TakoBulk.init();
});