( function() {
  var el = wp.element.createElement;
  
  wp.blocks.registerBlockType( 'fv-player-gutenberg/basic', {
      title: 'FV Player',
  
      icon: 'controls-play',
  
      category: 'layout',
      
      attributes: {
        content: {
          type: 'string',
          source: 'text'
        }
      },
  
      edit: function( props ) {
        var content = props.attributes.content;
        function onChangeContent( newContent ) {
          props.setAttributes( { content: newContent } );
        }
        
        return el('div', {
          className: 'fv-player-gutenberg'
        }, el(
            'a',{
              'class': 'button fv-wordpress-flowplayer-button'
            },
            el(
              'span'  
            ),
            'FV Player'
          ),
          el(
            wp.components.TextareaControl,
            {
              'class': 'components-textarea-control__input fv-player-editor-field',
              onChange: onChangeContent,
              value: content,
            }
          )
        );
      },
  
      save: function( props ) {
        return el( wp.element.RawHTML,
                  null,
                  props.attributes.content
        );
      },
  } );
}() );