<script type="text/html" id="tmpl-attachment-remote">
  <div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
	  <# if ( data.uploading ) { #>
				<div class="media-progress-bar"><div></div></div>
			<# } else if ( 'image' === data.type ) { #>
				<div class="thumbnail">
					<div class="centered">
						<img src="{{ data.size.url }}" draggable="false" />
					</div>
				</div>
			<# } else if ('remote' === data.type) { #>
				<div class="thumbnail">
					<div class="centered">
						<img src="{{ data.icon }}" draggable="false" />
					</div>
				</div>
			<# } else { #>
				<img src="{{ data.icon }}" class="icon" draggable="false" />
				<div class="filename">
					<div>{{ data.filename }}</div>
				</div>
			<# } #>

			<# if ( data.buttons.close ) { #>
				<a class="close media-modal-icon" href="#" title="<?php _e( 'Remove', 'dropr' ); ?>"></a>
			<# } #>

			<# if ( data.buttons.check ) { #>
				<a class="check" href="#" title="<?php _e( 'Deselect', 'dropr' ); ?>"><div class="media-modal-icon"></div></a>
			<# } #>
		</div>
		<#
		var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
		if ( data.describe ) { #>
			<# if ( 'image' === data.type ) { #>
				<input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
					placeholder="<?php esc_attr_e( 'Caption this image&hellip;', 'dropr' ); ?>" {{ maybeReadOnly }} />
			<# } else { #>
				<input type="text" value="{{ data.title }}" class="describe" data-setting="title"
					<# if ( 'video' === data.type ) { #>
						placeholder="<?php esc_attr_e( 'Describe this video&hellip;', 'dropr' ); ?>"
					<# } else if ( 'audio' === data.type ) { #>
						placeholder="<?php esc_attr_e( 'Describe this audio file&hellip;', 'dropr' ); ?>"
					<# } else { #>
						placeholder="<?php esc_attr_e( 'Describe this media file&hellip;', 'dropr' ); ?>"
					<# } #> {{ maybeReadOnly }} />
			<# } #>
		<# } #>
</script>
