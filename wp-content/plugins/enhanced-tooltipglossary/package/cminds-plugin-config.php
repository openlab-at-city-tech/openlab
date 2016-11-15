<?php

$cminds_plugin_config = array(
	'plugin-is-pro'					 => false,
	'plugin-has-addons'				 => TRUE,
	'plugin-version'				 => '3.3.7',
	'plugin-abbrev'					 => 'cmtt',
	'plugin-affiliate'				 => '',
	'plugin-redirect-after-install'	 => admin_url( 'admin.php?page=cmtt_settings' ),
	'plugin-show-guide'				 => TRUE,
	'plugin-guide-text'				 => '    <div style="display:block">
        <ol>
            <li>Go to <strong>"Add New"</strong> under the CM Tooltip Glossary menu</li>
            <li>Fill the <strong>"Title"</strong> of the glossary item and <strong>"Content"</strong></li>
            <li>Click <strong>"Publish" </strong> in the right column.</li>
            <li><strong>View</strong> this glossary item</li>
            <li>From the plugin settings click on the Link to the <strong>Glossary Index Page</strong></li>
            <li><strong>Troubleshooting:</strong> If you get a 404 error once viewing the glossary item,  make sure your WordPress permalinks are set and save them again to refresh</li>
            <li><strong>Troubleshooting:</strong> Make sure your site does not have any JavaScript error which might prevent tooltip from appearing</li>
        </ol>
    </div>',
	'plugin-guide-video-height'		 => 240,
	'plugin-guide-videos'			 => array(
		array( 'title' => 'Installation tutorial', 'video_id' => '157868636' ),
	),
	'plugin-file'					 => CMTT_PLUGIN_FILE,
	'plugin-dir-path'				 => plugin_dir_path( CMTT_PLUGIN_FILE ),
	'plugin-dir-url'				 => plugin_dir_url( CMTT_PLUGIN_FILE ),
	'plugin-basename'				 => plugin_basename( CMTT_PLUGIN_FILE ),
	'plugin-icon'					 => '',
	'plugin-name'					 => CMTT_NAME,
	'plugin-license-name'			 => CMTT_CANONICAL_NAME,
	'plugin-slug'					 => '',
	'plugin-short-slug'				 => 'tooltip',
	'plugin-menu-item'				 => CMTT_MENU_OPTION,
	'plugin-textdomain'				 => CMTT_SLUG_NAME,
	'plugin-userguide-key'			 => '6-cm-tooltip',
	'plugin-store-url'				 => 'https://www.cminds.com/store/tooltipglossary/',
	'plugin-support-url'			 => 'https://wordpress.org/support/plugin/enhanced-tooltipglossary/',
	'plugin-review-url'				 => 'https://wordpress.org/support/view/plugin-reviews/enhanced-tooltipglossary/',
	'plugin-changelog-url'			 => CMTT_RELEASE_NOTES,
	'plugin-licensing-aliases'		 => array( CMTT_LICENSE_NAME ),
	'plugin-compare-table'			 => '<div class="pricing-table" id="pricing-table">
                <ul>
                    <li class="heading">Current Edition</li>
                    <li class="price">$0.00</li>
                    <li class="noaction"><span>Free Download</span></li>
                   <li>Show Tooltip in Posts & Pages Only</li>
                    <li>A-Z Glossary Index</li>
                    <li>500 glossary terms</li>
                    <li>UTF8 characters Support</li>
                     <li>X</li>
                     <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                     <li>X</li>
                    <li>X</li>
                   <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                     <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li class="price">$0.00</li>
                    <li class="noaction"><span>Free Download</span></li>
                </ul>

                <ul>
                    <li class="heading">Pro</li>
                    <li class="price">$29.00</li>
                    <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=2" target="_blank">Buy Now</a></li>
                     <li>Show tooltip in all custom posts</li>
                    <li>A-Z glossary index</li>
                    <li>Unlimited glossary terms</li>
                    <li>UTF8 characters Support</li>
                    <li>Optimized for speed</li>
                    <li>Index page pagination</li>
                    <li>Import / Export</li>
                    <li>Customize tooltip style</li>
                    <li>Related posts/terms</li>
                    <li>Multisite support</li>
                    <li>Synonyms, singular & plural</li>
                    <li>Mobile friendly</li>
                    <li>Custom permalink</li>
                    <li>WPML support</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                     <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                   <li class="price">$29.00</li>
                    <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=2" target="_blank">Buy Now</a></li>
                </ul>

              <ul>
                    <li class="heading">Pro+</li>
                    <li class="price">$45.00</li>
                    <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=5" target="_blank">Buy Now</a></li>
                     <li>Show tooltip in all custom posts</li>
                    <li>A-Z glossary index</li>
                     <li>Unlimited glossary terms</li>
                    <li>UTF8 characters Support</li>
                    <li>Optimized for speed</li>
                    <li>Index page pagination</li>
                    <li>Import / Export</li>
                    <li>Customize tooltip style</li>
                    <li>Related posts/terms</li>
                    <li>Multisite support</li>
                    <li>Synonyms, singular & plural</li>
                    <li>Mobile friendly</li>
                    <li>Custom permalink</li>
                    <li>WPML support</li>
                    <li>Share This widget</li>
                    <li>Customize term template</li>
                    <li>Google translate integration</li>
                    <li>Glossary categories</li>
                    <li>Glossary search</li>
                    <li>Glossary shortcodes</li>
                    <li>Merriam-Webster dictionary / thesaurus</li>
                    <li>Search & Replace engine</li>
                    <li>Eastern languages support</li>
                    <li>Abbreviations</li>
                    <li>Index page styles</li>
                    <li>Frontend turn tooltip off button</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                    <li>X</li>
                     <li class="price">$45.00</li>
                   <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=5" target="_blank">Buy Now</a></li>
                </ul>
                            <ul>
                    <li class="heading">Ecommerce</li>
                    <li class="price">$59.00</li>
                    <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=7" target="_blank">Buy Now</a></li>
                    <li>Show tooltip in all custom posts</li>
                    <li>A-Z glossary index</li>
                    <li>Unlimited glossary terms</li>
                    <li>UTF8 characters Support</li>
                    <li>Optimized for speed</li>
                    <li>Index page pagination</li>
                    <li>Import / Export</li>
                    <li>Customize tooltip style</li>
                    <li>Related posts/terms</li>
                    <li>Multisite support</li>
                    <li>Synonyms, singular & plural</li>
                    <li>Mobile friendly</li>
                    <li>Custom permalink</li>
                    <li>WPML support</li>
                    <li>Share This widget</li>
                    <li>Customize term template</li>
                    <li>Google translate integration</li>
                    <li>Glossary categories</li>
                    <li>Glossary search</li>
                    <li>Glossary shortcodes</li>
                    <li>Merriam-Webster dictionary / thesaurus</li>
                    <li>Search & Replace engine</li>
                    <li>Eastern languages support</li>
                    <li>Abbreviations</li>
                    <li>Index page styles</li>
                    <li>Frontend turn tooltip off button</li>
                   <li>Embed audio in tooltip</li>
                    <li>Embed video in tooltip</li>
                    <li>Wikipedia integration</li>
                    <li>Amazon products integration</li>
                    <li>Embed WooCommerce products</li>
                    <li>Term tags</li>
                    <li>Double click support</li>
                    <li>Featured image</li>
                    <li>Parse image Alt to show tooltip</li>
                   <li class="price">$59.00</li>
                    <li class="action"><a href="https://www.cminds.com/store/tooltipglossary/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=7" target="_blank">Buy Now</a></li>
                </ul>


            </div>',
);
