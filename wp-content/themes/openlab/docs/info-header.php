<div class="docs-info-header img-rounded">
	<div class="row">
		<div class="col-sm-24">
			<div class="doc-search-element pull-right align-right">
				<form action="" method="get" class="form-inline">
					<div class="form-group">
						<label class="sr-only" for="docSearch">Search Docs</label>
						<input id="docSearch" class="form-control" name="s" value="<?php the_search_query() ?>">
						<input class="btn btn-primary top-align" name="search_submit" type="submit" value="<?php _e('Search', 'bp-docs') ?>" />
					</div>
				</form>
			</div>
			<?php bp_docs_info_header() ?>
		</div>
	</div>
</div>

