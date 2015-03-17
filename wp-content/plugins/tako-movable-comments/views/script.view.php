<script id="bulk-edit-template" type="html/template">
<tr id="bulky-comment" class="inline-edit-row inline-edit-row-post inline-edit-post bulk-edit-row bulk-edit-row-post bulk-edit-post inline-editor" style="display: none;">
	<td colspan="4" class="colspanchange">
		<fieldset class="inline-edit-col-left" style="width: 70%">
			<div class="inline-edit-col">
				<h4>Move Comments</h4>
				<div id="bulk-title-div">
				<div id="bulk-titles">
					
				</div>
				</div>
			</div>
		</fieldset>
		<fieldset class="inline-edit-col-center inline-edit-categories">
			<span class="title inline-edit-categories-label">Post Types</span>
			<select name="tako_post_type" id="tako_post_type">
			</select>
		</fieldset>
		<fieldset class="inline-edit-col-center">
		<span class="title inline-edit-categories-label">Posts</span>
			<select name="tako_post" id="tako_post">
			</select>
			<img src="<?php echo admin_url('/images/wpspin_light.gif') ?>" class="waiting" id="tako_spinner" style="display:none;" />
		</fieldset>
	<p class="submit inline-edit-save">
		<a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft" id="tako-cancel-bulk">Cancel</a>
			<input type="submit" name="bulk_edit" id="bulk-edit" class="button button-primary alignright" value="Update" accesskey="s">
			<img src="<?php echo admin_url('/images/wpspin_light.gif') ?>" class="waiting alignright wait-padding" id="tako_bulk_spinner" style="display:none"/>
		<br class="clear">
	</p>
</td></tr>
</script>

<script type="text/javascript">
jQuery( document ).ready(function() {
	jQuery( '<option>' ).val( 'move' ).text( '<?php echo $this->display ?>' ).appendTo( "select[name='action']" );
	jQuery( '<option>' ).val( 'move' ).text( '<?php echo $this->display ?>' ).appendTo( "select[name='action2']" );
});
</script>

<script id="tako-move-list" type="text/x-handlebars-template">
<div class="tako-comment-indi clearfix" data-comment="{{id}}">
	<p>
		<img alt="" src="{{gravatar}}" class="avatar avatar-60 photo tako-bulk-gravatar" height="60" width="60">
		<strong>{{author}}</strong><br />	
		{{comment}}
	</p>
	<p style="color: #8e8e8d;"><a id="tako-delete-move" style="padding-top: 3px;" data-tako="{{id}}"></a>Submitted on {{date}} | {{post}}</p>
</div>
</script>

<script id="tako-post-type-opt" type="text/x-handlebars-template">
{{#each responses}}
	<option value="{{@key}}">{{this}}</option>
{{/each}}
</script>

<script id="tako-success-message" type="text/x-handlebars-template">
	<div id="moderated" class="updated below-h2"><p>{{message}}</p></div>
</script>