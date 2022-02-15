<?php if (Mappress::$pro) { ?>
	<div class='mapp-filters'>
		<div class='mapp-filters-wrapper'>
			<div class='mapp-button mapp-caret mapp-filters-toggle' data-mapp-action='filters-toggle'><?php _e('Filter', 'mappress-google-maps-for-wordpress');?></div>
			<div class='mapp-filters-body'>
				<div class='mapp-filters-list'>
					<?php foreach(Mappress::$options->filters as $atts) { ?>
						<?php $filter = new Mappress_Filter($atts); ?>
						<?php echo $filter->get_html(); ?>
					<?php } ?>
				</div>
				<div class='mapp-filters-toolbar'>
					<div class='mapp-link-button mapp-filters-reset' data-mapp-action='filters-reset'><?php _e('Reset', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-filters-count'></div>
					<div class='mapp-submit-button mapp-filters-done' data-mapp-action='filters-toggle'><?php _e('Done', 'mappress-google-maps-for-wordpress');?></div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
