
<div class="<?php bp_docs_container_class(); ?>">
	<?php include( apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ) ) ?>

	<?php if ( current_user_can( 'bp_docs_manage_folders' ) && bp_docs_is_folder_manage_view() ) : ?>
		<?php bp_docs_locate_template( 'manage-folders.php', true ); ?>
	<?php else : ?>

	<?php
	$has_docs = false;

	$doc_query_args = [
		'update_attachment_cache' => false,
		'posts_per_page'          => 20,

	];
	?>

	<?php if ( bp_docs_has_docs( $doc_query_args ) ) : ?>
		<?php $has_docs = true; ?>

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

		<div class="info-panel panel panel-default doctable-panel">
		<table class="doctable table table-striped">

			<thead>
				<tr valign="bottom">
					<th scope="column" class="title-cell<?php bp_docs_is_current_orderby_class('title') ?>">
						<a href="<?php bp_docs_order_by_link('title') ?>"><?php _e('Title', 'bp-docs'); ?></a>
					</th>

					<th scope="column" class="author-cell<?php bp_docs_is_current_orderby_class('author') ?> hidden-xs">
						<a href="<?php bp_docs_order_by_link('author') ?>"><?php _e('Author', 'bp-docs'); ?></a>
					</th>

					<th scope="column" class="created-date-cell<?php bp_docs_is_current_orderby_class('created') ?> hidden-sm hidden-xs">
						<a href="<?php bp_docs_order_by_link('created') ?>"><?php _e('Created', 'bp-docs'); ?></a>
					</th>

					<th scope="column" class="edited-date-cell<?php bp_docs_is_current_orderby_class('modified') ?> hidden-sm hidden-xs">
						<a href="<?php bp_docs_order_by_link('modified') ?>"><?php _e('Last Edited', 'bp-docs'); ?></a>
					</th>

					<th scope="column" class="tags-cell hidden-sm hidden-xs"><?php _e( 'Tags', 'bp-docs' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php while ( bp_docs_has_docs() ) : bp_docs_the_doc() ?>
					<tr<?php bp_docs_doc_row_classes(); ?> data-doc-id="<?php echo get_the_ID() ?>">
						<td class="title-cell">
							<span class="title-wrapper">
								<a class="hyphenate truncate-on-the-fly" href="<?php bp_docs_group_doc_permalink() ?>" data-basevalue="80" data-minvalue="55" data-basewidth="376"><?php the_title() ?></a>
								<span class="original-copy hidden"><?php the_title() ?></span>
							</span>

							<span class="hyphenate">
								<?php the_excerpt() ?>
							</span>

							<div class="row-actions">
								<?php bp_docs_doc_action_links() ?>
							</div>

							<div class="author-info-mobile visible-xs">
								<span class="bold"><?php _e('Author', 'bp-docs'); ?>:</span> <a href="<?php bp_docs_order_by_link('author') ?>"><?php _e('Author', 'bp-docs'); ?></a>
							</div>
						</td>

						<td class="author-cell hidden-xs">
							<a href="<?php echo bp_core_get_user_domain(get_the_author_meta('ID')) ?>" title="<?php echo bp_core_get_user_displayname(get_the_author_meta('ID')) ?>"><?php echo bp_core_get_user_displayname(get_the_author_meta('ID')) ?></a>
						</td>

						<td class="date-cell created-date-cell hidden-sm hidden-xs">
							<?php echo get_the_date() ?>
						</td>

						<td class="date-cell edited-date-cell hidden-sm hidden-xs">
							<?php echo get_the_modified_date() ?>
						</td>

						<?php
								$tags = wp_get_post_terms(get_the_ID(), buddypress()->bp_docs->docs_tag_tax_name);
								$tagtext = array();

								foreach ($tags as $tag) {
									$tagtext[] = bp_docs_get_tag_link(array('tag' => $tag->name));
								}
								?>

								<td class="tags-cell hidden-sm hidden-xs">
									<?php echo implode(', ', $tagtext) ?>
								</td>

					</tr>
				<?php endwhile ?>
			</tbody>

		</table>
		</div>

		<div id="bp-docs-pagination">
			<div id="bp-docs-pagination-count">
				<?php printf(__('Viewing %1$s-%2$s of %3$s docs', 'bp-docs'), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num()) ?>
			</div>

			<div id="bp-docs-paginate-links">
				<?php bp_docs_paginate_links() ?>
			</div>
		</div>

		<?php else: ?>
		<?php if (groups_is_user_member(get_current_user_id(),bp_get_group_id())): ?>
			<p class="no-docs bold"><?php printf(__('There are no docs for this view. Why not <a href="%s">create one</a>?', 'bp-docs'), bp_docs_get_item_docs_link() . BP_DOCS_CREATE_SLUG) ?></p>
		<?php else: ?>
			<p class="no-docs bold"><?php printf(__('There are no docs for this view.', 'bp-docs')) ?></p>
		<?php endif; ?>

		<?php endif ?>
	<?php endif; ?>

</div>
