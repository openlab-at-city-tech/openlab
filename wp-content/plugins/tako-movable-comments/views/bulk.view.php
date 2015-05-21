<script id="bulk-edit-template">
<tr id="bulk-edit" class="inline-edit-row inline-edit-row-post inline-edit-post bulk-edit-row bulk-edit-row-post bulk-edit-post inline-editor" style="display: table-row;"><td colspan="7" class="colspanchange">

		<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
			<h4>Bulk Edit</h4>
				<div id="bulk-title-div">
				<div id="bulk-titles"><div id="ttle2330"><a id="_2330" class="ntdelbutton" title="Remove From Bulk Edit">X</a>This is a new post created to test pagination</div><div id="ttle2324"><a id="_2324" class="ntdelbutton" title="Remove From Bulk Edit">X</a>A different approach on managing different stylesheet for custom templates in WordPress</div></div>
			</div>

	
	
	
		</div></fieldset><fieldset class="inline-edit-col-center inline-edit-categories"><div class="inline-edit-col">

	
			<span class="title inline-edit-categories-label">Categories</span>
			<input type="hidden" name="post_category[]" value="0">
			<ul class="cat-checklist category-checklist">
				
<li id="category-1" class="popular-category"><label class="selectit"><input value="1" type="checkbox" name="post_category[]" id="in-category-1"> Uncategorized</label></li>

<li id="category-79"><label class="selectit"><input value="79" type="checkbox" name="post_category[]" id="in-category-79"> WordPress</label></li>
			</ul>

	
		</div></fieldset>

	
		<fieldset class="inline-edit-col-right"><label class="inline-edit-tags">
				<span class="title">Tags</span>
				<textarea cols="22" rows="1" name="tax_input[post_tag]" class="tax_input_post_tag" autocomplete="off"></textarea>
			</label><div class="inline-edit-col">

	<label class="inline-edit-author"><span class="title">Author</span><select name="post_author" class="authors">
	<option value="-1">— No Change —</option>
	<option value="1">Ren Aysha</option>
</select></label>
	
	
			<div class="inline-edit-group">
					<label class="alignleft">
				<span class="title">Comments</span>
				<select name="comment_status">
					<option value="">— No Change —</option>
					<option value="open">Allow</option>
					<option value="closed">Do not allow</option>
				</select>
			</label>
					<label class="alignright">
				<span class="title">Pings</span>
				<select name="ping_status">
					<option value="">— No Change —</option>
					<option value="open">Allow</option>
					<option value="closed">Do not allow</option>
				</select>
			</label>
					</div>

	
			<div class="inline-edit-group">
				<label class="inline-edit-status alignleft">
					<span class="title">Status</span>
					<select name="_status">
							<option value="-1">— No Change —</option>
												<option value="publish">Published</option>
						
							<option value="private">Private</option>
												<option value="pending">Pending Review</option>
						<option value="draft">Draft</option>
					</select>
				</label>

	
	
				<label class="alignright">
					<span class="title">Sticky</span>
					<select name="sticky">
						<option value="-1">— No Change —</option>
						<option value="sticky">Sticky</option>
						<option value="unsticky">Not Sticky</option>
					</select>
				</label>

	
	
			</div>

			<label class="alignleft" for="post_format">
		<span class="title">Format</span>
		<select name="post_format">
			<option value="-1">— No Change —</option>
							<option value="standard">Standard</option>
								<option value="aside">Aside</option>
								<option value="chat">Chat</option>
								<option value="gallery">Gallery</option>
								<option value="link">Link</option>
								<option value="image">Image</option>
								<option value="quote">Quote</option>
								<option value="status">Status</option>
								<option value="video">Video</option>
								<option value="audio">Audio</option>
						</select></label>
	
		</div></fieldset>

			<p class="submit inline-edit-save">
			<a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft">Cancel</a>
			<input type="submit" name="bulk_edit" id="bulk_edit" class="button button-primary alignright" value="Update" accesskey="s">			<input type="hidden" name="post_view" value="list">
			<input type="hidden" name="screen" value="edit-post">
						<span class="error" style="display:none"></span>
			<br class="clear">
		</p>
		</td></tr>
</script>