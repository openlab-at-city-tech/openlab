 <div class="dialog" id="add-to-protfolio-dialog" aria-hidden="true">
	<div class="dialog__overlay" tabindex="-1" data-a11y-dialog-hide"></div>
	<dialog class="dialog__content" aria-labelledby="dialog-title" role="dialog">
		<div class="dialog__header">
			<h1 id="dialog-title">Add to Portfolio</h1>
			<button data-a11y-dialog-hide class="dialog__close" aria-label="Close this dialog window">×</button>
		</div>
		<div class="dialog__body">Loading...</div>
		<div class="dialog__footer">
			<button data-a11y-dialog-hide type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
			<button type="button" class="btn btn-primary">Add to Portfolio</button>
		</div>
	</dialog>
</div>

<script type="text/template" id="tmpl-add-to-portfolio">
	<form action="#" method="post">
		<div class="form-group">
			<legend>Choose a Format:</legend>
			<div class="control-radio">
				<label for="type-posts">
					<input id="type-posts" type="radio" name="type" value="posts" <# if ( data.type === 'posts' || data.type === 'comments' ) { #>checked="checked"<# } #>/> Post
				</label>
			</div>
			<div class="control-radio">
				<label for="type-pages">
					<input id="type-pages" type="radio" name="type" value="pages" <# if ( data.type === 'pages' ) { #>checked="checked"<# } #>/> Page
				</label>
			</div>
		</div>
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text" name="title" id="title" value="{{ data.title }}">
		</div>
		<div class="form-group">
			<label for="citation">Citation</label>
			<div id="citation">This <a href="{{ data.url }}">entry</a> was originaly posted in "{{ data.site_name }}" on {{ data.date }}</div>
		</div>
		<div class="form-group">
			<label for="annotation">Annotation</label>
			<textarea id="annotation" name="annotation" rows="3"></textarea>
		</div>
	</form>
</script>
