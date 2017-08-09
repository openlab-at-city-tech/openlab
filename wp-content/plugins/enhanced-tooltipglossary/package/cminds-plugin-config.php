<?php

$cminds_plugin_config = array(
	'plugin-is-pro'					 => false,
	'plugin-has-addons'				 => TRUE,
	'plugin-version'				 => '3.5.5',
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
	'plugin-compare-table'			 => '
              <div class="suite-package" style="padding-left:10px;"><h2>The premium version of this plugin is included in CreativeMinds All plugins suite:</h2><a href="https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/" target="_blank"><img src="'.plugin_dir_url( __FILE__ ).'CMWPPluginssuite.png"></a></div>
            <hr style="width:1000px; height:3px;">
            <div class="pricing-table" id="pricing-table"><h2 style="padding-left:10px;">Upgrade The Tooltip Glossary Plugin:</h2>
                <ul>
                    <li class="heading" style="background-color:black;">Current Edition</li>
                    <li class="price">FREE<br /></li>
                 <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Show in Posts & Pages <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Show tooltip for terms found in pages or posts"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>A-Z Glossary Index <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Show an A-B-C navigation bar in the glossary index page"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>500 glossary terms <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support all letters defined in UTF8 encoding including non latin characters"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>UTF8 characters Support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Limited to 500 terms in the index page"></span></li>
                 </ul>

                <ul>
                    <li class="heading">Pro<a href="https://www.cminds.com/store/tooltipglossary/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$29.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/store/tooltipglossary/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=2" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free features are supported in the pro"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Unlimited glossary terms <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="While free version is limited to 500 terms the pro has no restriction and glossary can have even 10,000 terms "></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Optimized for Speed <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All pro versions have been optimized for speed to be able to support large amount of terms in Glossary. We have many sites running more than 3000 terms or more "></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Show tooltip in all custom posts <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Can parse the content of any custom post type to show links to glossary terms and tooltips"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Index page pagination <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Admin can define how many terms to show in the index page and where to place the pagination navigation"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Import / Export <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support import and export of terms from and to the glossary using a CSV file format"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Customize tooltip style <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Tooltip color, font size, font color and link color can be easily customized in the plugin settings"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Related posts/terms <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Show in each post or page a widget at the bottom of the page showing all related terms with a link to the glossary term page. In the glossary term page a widget showing all related articles will be shown at the bottom of the page with a link to the article "></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Multisite support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Works in WordPress multisite environment. Please check licensing information regarding Multisite support"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Synonyms, singular & plural <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Each term can have other words associated to it. When a synonym is defined and found in a post, it will be linked to the term page just like when the term itself was found. Glossary index however will not show synonyms in the terms list."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Mobile friendly <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Tooltips and glossary index are mobile responsive. Tooltip can be adjusted for mobile devices to replace hover action. Plugin includes detailed mobile settings."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Custom permalink <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Glossary permalink can be changed to reflect your own language or terminlogy. For example www.yoursute.com/lexicon"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>WPML support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Fully compliant with WP WPML plugin. Will support multi-lingual websites"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>ACF Fields Support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Allows to parse the ACF fields with of various(selectable) types"></span></li>
                    <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                </ul>

              <ul>
                     <li class="heading">Pro+<a href="https://www.cminds.com/store/tooltipglossary/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$45.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/store/tooltipglossary/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=5" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free and Pro Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free and pro features are supported in the pro+"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Share This widget <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Option to show social sharing icons in the glossary term page"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Customize term template <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Ability to customize the glossary term template and style it in your own way"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Google translate integration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="While using Google Translate API you can show a translation of the term content inside the tooltip"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Glossary categories <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Create glossary categories and assign each term to a category. This will let your visitors filter terms by categories. It also support building multiple glossaries on the same site"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Glossary search <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Show a search box in the glossary index page allowing users to search terms"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Glossary shortcodes <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Several shortcodes are available. They help you embed content from the glossary inside a post or show an index of all terms from a specific category in any post. Other shortcodes support showing local tooltips and also restrict parsing of content"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Merriam-Webster dictionary / thesaurus <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Using Merriam-Webster API you can show a definition of the term inside the tooltip or inside the term page. This requires acquiring APi keys from Merriam-Webster"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Search & Replace engine <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Robust search and replace engine which help you replace content before it is parsed without making a change to the databse"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Eastern languages support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support the option to find terms in text which does not have spaces between words. This is usefull in eastern languages like for example Japanese"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Term Abbreviations <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Each term can have abbreviations defined. When an abbreviation is defined and found in a post, It will be linked to the term page just like when the term itself was found. Glossary index however will not show abbreviation in the terms list."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Index page styles <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Offer several templates to support the way terms are shown in the glossary index page. For each template admin can make small adjustments in the plugin settings"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Highlight terms in Comments <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support parsing WordPress comments to highlight terms found in the comment content"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Frontend turn tooltip off button <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support a widget which is shown in each page or post and let the visitor toggle the tooltip off or on"></span></li>
                    <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                </ul>
                            <ul>
                    <li class="heading">Ecommerce<a href="https://www.cminds.com/store/tooltipglossary/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$59.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/store/tooltipglossary/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=693&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=7" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free, Pro and Pro+ Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free, pro and Pro+ features are supported"></span></li>
                  <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Embed audio in tooltip <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support an audio player inside the tooltip. This is usefull once adding an audio file to the glossary term page"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Embed video in tooltip <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support a video player inside the tooltip. This is usefull once adding a video file to the glossary term page"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Wikipedia integration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support showing content taken from WikiPedia for the defined term. WikiPedia content can be shown inside the tooltip or inside the term page"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Amazon Products Integration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support the option to show Amazon product recommendation widget inside the tooltip content. Widget size and Amazon product category can be set by admin for each term. The widget is based on the defined glossary term"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>WooCommerce Products Integration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Integrate with WooCommerce product catalog and shows a clickable product snippet from your Woo store inside the tooltip "></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Term tags <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Supports adding tags to each term in the glossary. This helps in filtering terms by tags"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Double click support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Supports double click action. Once set admin can define which content to show once a word in a post is double clicked. It can be a definition from WikPedia, Merriam-Webster or additional resources"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Featured image <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Show the featured image which was defined in the term page inside the tooltip"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Parse image Alt to show tooltip <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Parse the content found in the image alt tag. Once content related to a term in the glossary is found, an icon will be shown in the corner of the image. Once icon is hoverd the tooltip with the related content will be displayed"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Contextual Terms Support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support the order in which similar terms are parsed"></span></li>
                    <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
               </ul>


            </div>',
);
