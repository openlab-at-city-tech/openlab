<?php if ($wrap) { ?><table><?php } ?>
	<?php foreach($fields as $field): ?>
	    <?php echo $field ?>
	<?php endforeach ?>
	<?php if ( !defined('NGG_PRO_PLUGIN_VERSION') && !defined('NGG_PLUS_PLUGIN_VERSION') && !is_multisite() ) { ?>
        <tr>
            <td colspan="2" class="ngg_options_promo">
                <?php esc_html_e('Want image protection, social sharing, or ecommerce for this display? ', 'nggallery'); ?><a href="https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/?utm_source=ngg&utm_medium=ngguser&utm_campaign=options" target="_blank"><?php esc_html_e('Upgrade to NextGEN Pro!', 'nggallery'); ?></a>
            </td>
        </tr>
    <?php } ?>
<?php if ($wrap) { ?></table><?php } ?>