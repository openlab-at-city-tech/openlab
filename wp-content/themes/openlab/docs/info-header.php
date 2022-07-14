<div class="docs-info-header">
	<div class="row">
		<div class="col-sm-16 col-xs-24">
			<?php openlab_bp_docs_info_header_message() ?>
		</div>
		<div class="col-sm-8 col-xs-24">
			<div class="doc-search-element pull-right align-right">
				<form action="" method="get" class="form-inline">
					<label class="sr-only" for="docSearch">Search Docs</label>
					<input id="docSearch" name="s" value="<?php the_search_query() ?>">
					<button class="button" name="search_submit" type="submit"><?php _e('Search', 'bp-docs') ?></button>
				</form>
			</div>
		</div>
	</div>
</div>