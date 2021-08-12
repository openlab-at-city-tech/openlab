<?php if (Mappress::$pro) { ?>
<# if (map.query && mappl10n.options.filters && mappl10n.options.filters.length > 0) { #>
	<div class='mapp-filters'>
		<div class='mapp-filters-list'>
			<?php foreach(Mappress::$options->filters as $atts) { ?>
				<?php $filter = new Mappress_Filter($atts); ?>
					<div class='mapp-filter mapp-<?php echo $filter->key; ?>'>
						<div class='mapp-filter-label'><?php echo $filter->get_label(); ?></div>
						<div class='mapp-filter-values'><?php echo $filter->get_html(); ?></div>
					</div>
			<?php } ?>
		</div>
		<div class='mapp-filters-toolbar'>
			<span class='mapp-button-submit mapp-filters-done' data-mapp-action='filters-toggle'><?php _e('Done', 'mappress-google-maps-for-wordpress');?></span>
			<span class='mapp-button mapp-filters-reset' data-mapp-action='filters-reset'><?php _e('Reset', 'mappress-google-maps-for-wordpress');?></span>
		</div>
	</div>
<# } #>
<?php } ?>