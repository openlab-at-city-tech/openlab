var custom_theme_file_frame;

jQuery(function($){

      // Uploads.
      jQuery(document).on('click', 'input.select-img', function( event ){

        var $this = $(this);

        event.preventDefault();

        var CustomThemeImage = wp.media.controller.Library.extend({
            defaults :  _.defaults({
                    id:        'custom-theme-insert-image',
                    title:      $this.data( 'uploader_title' ),
                    allowLocalEdits: false,
                    displaySettings: true,
                    displayUserSettings: false,
                    multiple : false,
                    library: wp.media.query( { type: 'image' } )
              }, wp.media.controller.Library.prototype.defaults )
        });

        // Create the media frame.
        custom_theme_file_frame = wp.media.frames.custom_theme_file_frame = wp.media({
          button: {
            text: jQuery( this ).data( 'uploader_button_text' )
          },
          state : 'custom-theme-insert-image',
              states : [
                  new CustomThemeImage()
              ],
          multiple: false
        });

        // When an image is selected, run a callback.
        custom_theme_file_frame.on( 'select', function() {

          var state = custom_theme_file_frame.state('custom-theme-insert-image');
          var selection = state.get('selection');
          var display = state.display( selection.first() ).toJSON();
          var obj_attachment = selection.first().toJSON();
          display = wp.media.string.props( display, obj_attachment );

          var image_field = $this.siblings('.img');
          var imgurl = display.src;

          // Copy image URL.
          image_field.val(imgurl);
          image_field.trigger('change');
          // Show in preview.
          var image_preview_wrap = $this.siblings('.image-preview-wrap');
          var image_html = '<img src="' + imgurl+ '" style="width:200px;height:200px;" />';
          image_preview_wrap.html( image_html );
          // Show Remove button.
          var image_remove_button = $this.siblings('.btn-image-remove');
          image_remove_button.css('display','inline-block');
        });

        // Finally, open the modal.
        custom_theme_file_frame.open();
      });

      // Remove image.
      jQuery(document).on('click', 'input.btn-image-remove', function( e ) {

      	e.preventDefault();
      	var $this = $(this);
      	var image_field = $this.siblings('.img');
      	image_field.val('');
      	var image_preview_wrap = $this.siblings('.image-preview-wrap');
      	image_preview_wrap.html('');
      	$this.css('display','none');
      	image_field.trigger('change');

      });

});
