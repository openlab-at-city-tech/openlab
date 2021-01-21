<?php if (Mappress::$pro) { ?>
<# if (map.query && mappl10n.options.filter) { #>
    <div class='mapp-filters'>
        <div class='mapp-filters-list'>
            <?php $filter = new Mappress_Filter(array('key' => Mappress::$options->filter, 'format' => null)); ?>
            <div class='mapp-filter mapp-<?php echo $filter->key; ?>'>
                <div class='mapp-filter-label'><?php echo $filter->get_label(); ?></div>
                <div class='mapp-filter-values'><?php echo $filter->get_html(); ?></div>
            </div>
        </div>
        <div class='mapp-filters-toolbar'>
            <span class='mapp-button-submit mapp-filters-done' data-mapp-action='filters-toggle'><?php _e('Done', 'mappress-google-maps-for-wordpress');?></span>
            <span class='mapp-button mapp-filters-reset' data-mapp-action='filters-reset'><?php _e('Reset', 'mappress-google-maps-for-wordpress');?></span>
        </div>
    </div>
<# } #>
<?php } ?>