(function ($) {
  ('use strict');
  
  /*
   * Skin live preview
   */
  function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
      parseInt(result[1], 16),
      parseInt(result[2], 16),
      parseInt(result[3], 16)
    ] : null;
  }
  
  function rgb2hex(rgb){
    rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
    return (rgb && rgb.length === 4) ? "#" +
      ("0" + parseInt(rgb[1],10).toString(16)).slice(-2).toUpperCase() +
      ("0" + parseInt(rgb[2],10).toString(16)).slice(-2).toUpperCase() +
      ("0" + parseInt(rgb[3],10).toString(16)).slice(-2).toUpperCase() : '';
  }

  
  function sanitizeCSS(val) {
    if (val.indexOf('#rgba') > -1) {
      val = val.replace(/#rgba/g, 'rgba');
    } else if (val.indexOf('#transparent') > -1) {
      val = val.replace(/#transparent/g, 'transparent');
    }
    
    if( val.match(/# !/) ) {
      val = false;
    }
    
    return val;
  }

  if (!String.prototype.endsWith)
    String.prototype.endsWith = function(searchStr, Position) {
      // This works much better than >= because
      // it compensates for NaN:
      if (!(Position < this.length))
        Position = this.length;
      else
        Position |= 0; // round position
      return this.substr(Position - searchStr.length,
        searchStr.length) === searchStr;
    };

  $(document).ready(function () {
    $('[data-fv-skin]').on('input click', function () {
      $('[data-fv-skin]').each( function() {
        $('.flowplayer').removeClass( 'skin-'+$(this).val() );
      });
      $('.flowplayer').addClass( 'skin-'+$(this).val() );
      
      // hide currently visible settings tables
      $('#skin-Custom-settings, #skin-Slim-settings, #skin-YouTuby-settings').hide();

      // show the relevant settings table
      $('#' + this.id + '-settings').show();
      
      

      // update CSS
      skinPreviewInputChanged();
    });

    // cache this, it's quite expensive to select via data attribute
    var $previewElements = $('[data-fv-preview]');

    // dropdown value changes (slim type, icon types)
    function skinPreviewDropdownChanged() {
      $previewElements.each(function() {
        var
          $this = $(this),
          $parent = $this.closest('table');

        // don't change to values of another skin but to our currently visible skin type
        if ($parent.css('display') == 'none') {
          return;
        }

        // playlist design style change
        if ($this.attr('name').endsWith('playlist-design')) {
          var
            $external_playlist = $('.fp-playlist-external'),
            match = $external_playlist.attr('class').match(/fv-playlist-design-\S+/);

          if (match) {
            $external_playlist.removeClass(match[0]);
          }

          $external_playlist
            .removeClass('visible-captions')
            .addClass('fv-playlist-design-' + $this.val());

        } else if ($this.attr('name').endsWith('design-timeline]')) {
          // timeline design style change
          $('.flowplayer')
            .removeClass('fp-slim fp-full fp-fat fp-minimal')
            .addClass($this.val());
        } else if ($this.attr('name').endsWith('design-icons]')) {
          $('.flowplayer')
            .removeClass('fp-edgy fp-outlined fp-playful')
            .addClass($this.val());
        }
      });
    }

    // input (textbox, checkbox) value changes
    function skinPreviewInputChanged() {
      var style = '';

      $previewElements.each(function () {
        
        var
          newStyle = '',
          $this = $(this),
          $parent = $this.closest('table');
        
        var preview = $this.data('fv-preview').replace(/\.flowplayer/g,'.flowplayer.skin-'+jQuery('[data-fv-skin]:checked').val() );

        if ($parent.css('display') == 'none') {
          return;
        }

        if ($this.attr('name').endsWith('player-position]')) {
          if ($this.val() === 'left')
            style += preview;

        } else if ($this.attr('name').endsWith('bottom-fs]')) {
          if ($this.prop('checked') || $this.attr('type') == 'hidden' && $this.val() ) {
            jQuery('.flowplayer').addClass('bottom-fs');
          } else {
            jQuery('.flowplayer').removeClass('bottom-fs');
          }

        } else if($this.attr('type') == 'checkbox' ) {          
          if ($this.prop('checked')) {
            newStyle = preview.replace(/%val%/g, '1');
          } else {
            newStyle = preview.replace(/%val%/g, '0');
          }
          style += sanitizeCSS(newStyle);
          
        } else {
          var value = $this.val().replace(/^#/,'');
          if( opacity = $this.minicolors('opacity') ) {
            value = hexToRgb(value);
            value = 'rgba('+value[0]+','+value[1]+','+value[2]+','+opacity+')';
          }
          newStyle = preview.replace(/%val%/g, value);
          style += sanitizeCSS(newStyle);
          
        }
      }, 0);
      
      $('#fv-style-preview').html(style);

      // update progress bar + icons style
      skinPreviewDropdownChanged();
    }

    // color inputs + checkbox changes
    $previewElements.on('input change', skinPreviewInputChanged).trigger('input');

    $('[data-fv-preview]').on('select change', skinPreviewDropdownChanged);
  });

  $(document).ready( function() {
    var settings = {
      animationSpeed: 0,
      changeDelay: 10,
      letterCase: 'uppercase'      
    }
    $('input.color').minicolors(settings);
    settings.opacity = true;
    $('input.color-opacity').minicolors(settings);    
    
    $('input.color, input.color-opacity').on('change', color_inputs);
    $('input.color, input.color-opacity').each(color_inputs);
    
    $('form#wpfp_options').submit( function(e) {
      $('input.color-opacity').each( function() {
        var input = $(this);
        if( opacity = input.minicolors('opacity') ) {          
          var color = hexToRgb( input.val() );
          input.val( 'rgba('+color[0]+','+color[1]+','+color[2]+','+opacity+')' );
        }
      })
    });    
  });
  
  function color_inputs() {
    var input = $(this);
    var color = input.val();
    var rgba = hexToRgb( color );
    if( color.match(/rgba\(.*?\)/) ) {
      rgba = hexToRgb( rgb2hex(color) );
      input.val( rgb2hex(color) );
    }
    
    if( rgba && ( opacity = input.minicolors('opacity') ) ) {      
      input.css('box-shadow', 'inset 0 0 0 1000px rgba('+rgba[0]+','+rgba[1]+','+rgba[2]+','+opacity+')' );
    } else {
      input.css('background-color', input.val());
    }
    
    if( rgba && (
      (rgba[0] < 160 && rgba[1] < 160) || (rgba[1] < 160 && rgba[2] < 160) || (rgba[0] < 160 && rgba[2] < 160)
    ) ) {
      input.css('color', 'white');
    } else {
      input.css('color', '');
    }
  }

}(jQuery));