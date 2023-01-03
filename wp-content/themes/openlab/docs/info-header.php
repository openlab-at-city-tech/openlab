<div class="docs-info-header img-rounded">
	<div class="row">
		<div class="col-sm-24">
			<div class="doc-search-element pull-right align-right">
				<form action="" method="get" class="form-inline">
					<div class="form-group">
						<label class="sr-only" for="docSearch">Search Docs</label>
						<input id="docSearch" name="s" value="<?php the_search_query() ?>">
						<input class="" name="search_submit" type="submit" value="<?php _e('Search', 'bp-docs') ?>" />
					</div>
				</form>
			</div>

			<div class="bp-docs-pagination">
				<div class="bp-docs-pagination-count">
					<?php printf(__('Viewing %1$s-%2$s of %3$s docs', 'bp-docs'), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num()) ?>
				</div>
			</div>
		</div>
	</div>
</div>

