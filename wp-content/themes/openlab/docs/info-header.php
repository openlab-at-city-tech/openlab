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

			<?php openlab_bp_docs_info_header(); ?>
		</div>
	</div>
</div>

