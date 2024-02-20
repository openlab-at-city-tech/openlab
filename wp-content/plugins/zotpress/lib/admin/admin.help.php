        <div id="zp-Zotpress" class="wrap">

            <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>

            <h3><?php _e('What is Zotpress','zotpress'); ?>?</h3>

            <div class="zp-Message">
                <h3><?php _e('About Zotpress','zotpress'); ?></h3>
                <p class="version">
                    <strong><?php _e('Version','zotpress'); ?>:</strong> <?php _e('You\'re using','zotpress'); ?> Zotpress <?php echo ZOTPRESS_VERSION; ?><br />
                    <strong><?php _e('Website','zotpress'); ?>:</strong> <a title="Zotpress on WordPress" rel="external" href="http://wordpress.org/plugins/zotpress/">Zotpress on WordPress.org</a><br />
                    <strong><?php _e('Support','zotpress'); ?>:</strong> <a id="zp-Donate" title="<?php _e('Donations always appreciated! Accepted through PayPal','zotpress'); ?>" rel="external" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5HQ8FXAXS9MUQ"><?php _e('Donate through PayPal','zotpress'); ?></a>
                </p>
                <p class="rate">
                    <?php echo sprintf(
                        wp_kses(
                            __( 'If you like Zotpress, let the world know with a <a class="zp-FiveStar" title="Rate Zotpress" rel="external" href="%s">rating</a>!', 'zotpress' ),
                            array(
                                'a' => array(
                                    'href' => array(),
                                    'title' => array(),
                                    'class' => array(),
                                    'rel' => array()
                                ),
                                'strong' => array()
                            )
                        ), esc_url( 'http://wordpress.org/plugins/zotpress/' )
                    ); ?>
                </p>
            </div>

            <p>
                <?php echo sprintf(
                    wp_kses(
                        __( '<a title="More of my plugins" href="%s">Zotpress</a> bridges <a title="Zotero" href="%s">Zotero</a> and WordPress by allowing you to display items from your Zotero library through shortcodes and widgets. It also extends the basic meta functionality offered by Zotero by allowing you to add images to and provide downloadable files associated with your citations.', 'zotpress' ),
                        array(
                            'a' => array(
                                'href' => array()
                            ),
                            'strong' => array()
                        )
                    ), esc_url( 'http://katieseaborn.com/plugins/'), esc_url( 'https://www.zotero.org/' )
                ); ?>
            </p>

            <p>
                <?php echo sprintf(
                    wp_kses(
                        __( 'You can use Zotpress by creating <a title="Stand-Alone Bibliography" href="%s" class="zp-Tab-Link">stand-alone bibliography</a>, applying <a title="In-Text Citations" class="zp-Tab-Link" href="%s">in-text citations</a>, displaying <a title="Library" class="zp-Tab-Link" href="%s">your library</a>, or adding <a title="Widget" href="%s">a sidebar widget</a> to your theme.', 'zotpress' ),
                        array(
                            'a' => array(
                                'href' => array()
                            ),
                            'strong' => array()
                        )
                    ), esc_url( '#zp-Tab-Bib' ), esc_url( '#zp-Tab-InText' ), esc_url( '#zp-Tab-Library' ), esc_url( 'widgets.php' )
                ); ?>
            </p>

            <p>
                <?php _e('You can build shortcodes and search for items in your library using the <strong>Zotpresss Reference</strong> widget on the post/page add/edit screens. Below, you can find attributes and options for each kind of Zotpress shortcode.','zotpress'); ?>
            </p>

            <p>
                <?php echo sprintf(
                    wp_kses(
                        __( 'Have questions? First, check the <a title="FAQ" class="zp-Tab-Link" href="%s">FAQ</a>. Then search the <a title="Zotpress Forums" href="%s">Zotpress Support Forums</a>. If you can\'t find an answer, feel free to post your question there.', 'zotpress' ),
                        array(
                            'a' => array(
                                'href' => array()
                            ),
                            'strong' => array()
                        )
                    ), esc_url( '#zp-Tab-FAQ' ), esc_url( 'http://wordpress.org/support/plugin/zotpress' )
                ); ?>
            </p>

            <a id="zp-Zotero-API-Hash"></a>

			<div id="zp-Zotero-API">

				<ul id="zp-Zotero-API-Menu">
					<li><a href="#zp-Tab-Bib"><?php _e('Stand-Alone Bibliography','zotpress'); ?></a></li>
					<li><a href="#zp-Tab-InText"><?php _e('In-Text Citations','zotpress'); ?></a></li>
					<li><a href="#zp-Tab-Library"><?php _e('Library','zotpress'); ?></a></li>
					<li><a href="#zp-Tab-FAQ"><?php _e('FAQ','zotpress'); ?></a></li>
				</ul>


				<div id="zp-Tab-Bib">

					<div class="zp-Zotero-API-Explanation">
                        <p><?php _e('Generate a bibliography wherever you can call shortcodes','zotpress'); ?>.</p>
						<p><?php _e('The basic shortcode is','zotpress'); ?>:</p>
						<p><code>[zotpress userid="000000"]</code></p>
						<p><?php _e('Use any of the attributes below to customize your bibliography','zotpress'); ?>.</p>
					</div><!-- .zp-Zotero-API-Explanation -->

                    <div class="zp-Zotero-API-Attributes">

                        <div class="zp-Zotero-API-Attributes-Search">
                            <input class="zp-Zotero-API-Attributes-Search-Input" type="text" placeholder="<?php _e('Search for an attribute using a keyword','zotpress'); ?>">
                            <div class="zp-Zotero-API-Attributes-Search-Status">
                                <div class="zp-Loading"></div>
                                <span class="dashicons dashicons-search"></span>
                            </div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="user,userid,user id,id,name,user account,account,account id,group,groupid,group id,group name,group account,my account">
                            <h4>Account > <strong>userid</strong></h4>
                            <div class="description"><p>Display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</p></div>
                            <div class="example"><p><code>[zotpress userid="000000"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="nickname,name,nick,user,user id,account,alias">
                            <h4>Account > <strong>nickname</strong></h4>
                            <div class="description"><p>Display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</p></div>
                            <div class="example"><p><code>[zotpress nickname="Katie"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="item,item key,item id,reference,citation,source">
                            <h4>Data > <strong>items</strong></h4>
                            <div class="description"><p>Alternative: <code>item</code>. Display an item or list of items using particular item keys.</p></div>
                            <div class="example"><p><code>[zotpress item="GMGCJU34"]</code></p><p><code>[zotpress items="GMGCJU34,U9Z5JTKC"]</code></p></div>
                            <div class="description"><p>Can also include items from multiple Zotero accounts using this format: <code>{api_user_id:item_key}</code></p></div>
                            <div class="example"><p><code>[zotpress item="{000001:XH4BS8MA},{000001:CN73PTWE},{000003:CZR96TX9}"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="collection,collection id,collection name,collection key,folder,category">
                            <h4>Data > <strong>collections</strong></h4>
                            <div class="description"><p>Alternative: <code>collection</code>. Display items from a collection or list of collections using particular collection keys.</p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34"]</code></p><p><code>[zotpress collections="GMGCJU34,U9Z5JTKC"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="tag,category">
                            <h4>Data > <strong>tags</strong></h4>
                            <div class="description"><p>Alternative: <code>tag</code>. Display items associated with one or more tags. <strong>Warning:</strong> Will break if the tag has a comma.</p></div>
                            <div class="example"><p><code>[zotpress tag="zotero"]</code></p><p><code>[zotpress tags="zotero,scholarly blogging"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="author,creator,writer,editor">
                            <h4>Data > <strong>authors</strong></h4>
                            <div class="description"><p>Alternative: <code>author</code>. Display a list of citations from a particular author or authors. For authors with the same last name, use this format: (last, first). For authors with multi-part last names, use a plus sign in place of the usual space, e.g., part1+part2. <strong>Note:</strong> "Carl Sagan","C. Sagan", "C Sagan", "Carl E. Sagan", "Carl E Sagan" and "Carl Edward Sagan" are <strong>not</strong> the same as "Sagan".</p></div>
                            <div class="example"><p><code>[zotpress author="Carl Sagan"]</code></p><p><code>[zotpress authors="Carl Sagan,Stephen Hawking"]</code></p><p><code>[zotpress authors="(Sagan, Carl),(Hawking, Stephen)"]</code></p><p><code>[zotpress authors="Bader+Ginsburg"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="year,date,when">
                            <h4>Data > <strong>years</strong></h4>
                            <div class="description"><p>Alternative: <code>year</code>. Display a list of citations from a particular year or years. <strong>Note:</strong> You <em>can</em> display by Author and Year together.</p></div>
                            <div class="example"><p><code>[zotpress year="1990"]</code></p><p><code>[zotpress years="1990,1998,2013"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="itemtype,item type,type">
                            <h4>Data > <strong>itemtype</strong></h4>
                            <div class="description"><p>Display a list of citations of a particular item type. <strong>Options:
                                book, bookSection, journalArticle, conferencePaper, thesis, report, encyclopediaArticle,
                                newspaperArticle, magazineArticle, presentation, interview,
                                dictionaryEntry, document, manuscript, patent, map, blogPost,
                                webpage, artwork, film, audioRecording, statute, bill, case,
                                hearing, forumPost, letter, email, instantMessage, software,
                                podcast, radioBroadcast, tvBroadcast, videoRecording, attachment, note </strong>
                            </p></div>
                            <div class="example"><p><code>[zotpress itemtype="bookSection"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="inclusive,exclusive,limit,filter">
                            <h4>Filtering > <strong>inclusive</strong></h4>
                            <div class="description"><p>Used with the author and tag attributes. By default, include all items that match ANY listed author/tag (inclusive "OR"). If set to "no," exclude items that don't have all listed authors/tags (exclusive "AND"). <strong>Options: yes [default], no.</strong></p></div>
                            <div class="example"><p><code>[zotpress author="Carl Sagan, Ada Lovelace" inclusive="no"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="sort,sort by,order,order by">
                            <h4>Sorting > <strong>sortby</strong></h4>
                            <div class="description"><p>Sort multiple citations using meta data as attributes. <strong>Options: title, author, date, dateAdded, dateModified, default (entry order) [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress author="Carl Sagan" sortby="date"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="order,order by,sort,sort by">
                            <h4>Sorting > <strong>order</strong></h4>
                            <div class="description"><p>Order of the sortby attribute. <strong>Options: asc [default], desc.</strong></p></div>
                            <div class="example"><p><code>[zotpress author="Carl Sagan" sortby="date" order="desc"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="title,heading,name,year,itemtype,date">
                            <h4>Display > <strong>title</strong></h4>
                            <div class="description"><p>Display citations under headings, arranged by year or item type. Note: The "order" and "sortby" attributes will act as a secondary sort. <strong>Options: year, itemtype, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress author="Carl Sagan" title="year" sortby="author" order="asc"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="limit,count,number,total">
                            <h4>Display > <strong>limit</strong></h4>
                            <div class="description"><p>Limit the item list to by a given number. Displays all items by default. <strong>Optional.</strong> Options: Any number between 1 and 100.</p></div>
                            <div class="example"><p><code>[zotpress limit="5"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="style,citation,reference,format,display,presentation">
                            <h4>Display > <strong>style</strong></h4>
                            <div class="description"><p>Citation style. <strong>Options: apsa, apa [default], asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver.</strong> Note: Support for more styles is coming; see <a title="Zotero Style Repository" href="http://www.zotero.org/styles">Zotero Style Repository</a> for details.</p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" style="apa"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="image,showimage,show image,photo,picture,cover,book">
                            <h4>Display > <strong>showimage</strong></h4>
                            <div class="description"><p>Whether or not to display the citation's image, if one has been set in WordPress (on the Zotpress <a href="?page=Zotpress" title="Zotpress Browse page">Browse</a> page). If using the "openlib" option, it will look for a WordPress-set image first and then, if none exists, it will search the <a href="https://openlibrary.org/" target="_blank" title="Open Library">Open Library</a> to find book covers by ISBN. <strong>Options: yes, no, openlib [default]</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" showimage="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="tag,list,categories,showtag">
                            <h4>Display > <strong>showtags</strong></h4>
                            <div class="description"><p>Whether or not to display the citation's tags, if one or more exists. <strong>Options: yes, no [default]</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" showtags="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="download,pdf,link">
                            <h4>Display > <strong>download</strong></h4>
                            <div class="description"><p>Alternative: <code>downloadable</code> Whether or not to display the citation's download URL, if one exists. <strong>Enable this option only if you are legally able to provide your files for download.</strong> Options: yes, no [default].></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" download="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="abstract,summary">
                            <h4>Display > <strong>abstract</strong></h4>
                            <div class="description"><p>Alternative: <code>abstracts</code> Whether or not to display the citation's abstract, if one exists. <strong>Options: yes, no [default].</p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" abstracts="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="note,message">
                            <h4>Display > <strong>notes</strong></h4>
                            <div class="description"><p>Alternative: <code>note</code> Whether or not to display the citation's notes, if one or more exist. <strong>Must have notes made public via the private key settings on Zotero. </strong>Options: yes, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" notes="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="cite,download,reference,source,link">
                            <h4>Display > <strong>cite</strong></h4>
                            <div class="description"><p>Alternative: <code>citeable</code> Make the displayed citations citable by generating RIS links. <strong>Options: yes, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" cite="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="number,format,display,presentation">
                            <h4>Display > <strong>forcenumber</strong></h4>
                            <div class="description"><p>Numbers bibliography items, even when the citation style, e.g. APA, doesn't normally. <strong>Options: true, false [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" forcenumber="true"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="target,_blank,blank,new window,window,open">
                            <h4>Display > <strong>target</strong></h4>
                            <div class="description"><p>Links open up in a new window or tab. Applies to citation links, e.g. "retrieved from." Compliant with HTML5 but not XHTML Strict. <strong>Options: new, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" target="new"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="urlwrap,url wrap,url title,title,title link,link,url">
                            <h4>Display > <strong>urlwrap</strong></h4>
                            <div class="description"><p>Wrap the title or image with the citation URL. <strong>Options: title, image, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" urlwrap="title"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="highlight,jump,hyperlink,anchor,color,colour">
                            <h4>Display > <strong>highlight</strong></h4>
                            <div class="description"><p>Highlight a piece of text, such as an author name, in the bibliography. <strong>Options: any text, [empty by default].</strong></p></div>
                            <div class="example"><p><code>[zotpress collection="GMGCJU34" highlight="Sagan, C."]</code></p></div>
                        </div>

                    </div><!-- .zp-Zotero-API-Attributes -->
				</div><!-- #zp-Tab-Bib -->


				<div id="zp-Tab-InText">

					<div class="zp-Zotero-API-Explanation">

                        <div id="zp-InText-Example">
                            <div class="zp-InText-Example-Col alt">
                                <h4><?php _e('Editor','zotpress'); ?></h4>
                            </div>
                            <div class="zp-InText-Example-Col">
                                <h4><?php _e('Front-End','zotpress'); ?></h4>
                            </div>
                            <div class="zp-InText-Example-Col alt">
                                <p>This is an example of a Zotpress in-text citation [zotpressInText item="{NCXAA92F,36}"]. Place a bibliography shortcode somewhere below the citations. This will generate the in-text citations and a bibliography.</p>

                                <span class="title">Bibliography:</span>
                                <p>[zotpressInTextBib]</p>
                            </div>
                            <div class="zp-InText-Example-Col">
                                <p>This is an example of a Zotpress in-text citation (Zotpress, 2018, p. 36). Place a bibliography shortcode somewhere below the citations. This will generate the in-text citations and a bibliography.</p>

                                <span class="title">Bibliography:</span>
                                <p>Zotpress (2018). Help documentation. Retrieved from your WordPress admin panel.</p>
                            </div>
                        </div>

                        <p><?php _e('Create and place in-text citations and auto-populate a bibliography','zotpress'); ?>.</p>

						<p>
							<?php _e('Use one or more <code>[zotpressInText]</code> shortcodes in your post, page or what-have-you to create placeholders for in-text citations','zotpress'); ?>.
							<?php _e('Then place the <strong>required</strong> <code>[zotpressInTextBib]</code> shortcode somewhere in your entry <strong>after</strong> the in-text citation shortcodes. It will then create the in-text shortcodes and auto-populate a bibliography where it\'s placed','zotpress'); ?>.
						</p>
						<p>
							<?php _e('The <code>[zotpressInTextBib]</code> shortcode takes the same attributes as the <code>[zotpress]</code> shortcode, minus the "userid," "nickname," and "limit" attributes','zotpress'); ?>.
						</p>
						<p>
							<strong><?php _e('Important Note','zotpress'); ?>:</strong> <?php _e('In-text citations, unlike the bibliography, are not automatically styled. Use the "format" attribute to manually style in-text citations. Support for automatically styled in-text citations is in the works','zotpress'); ?>.
						</p>
					</div><!-- .zp-Zotero-API-Explanation -->

                    <div class="zp-Zotero-API-Attributes">

                        <div class="zp-Zotero-API-Attributes-Search">
                            <input class="zp-Zotero-API-Attributes-Search-Input" type="text" placeholder="<?php _e('Search for an attribute using a keyword','zotpress'); ?>">
                            <div class="zp-Zotero-API-Attributes-Search-Status">
                                <div class="zp-Loading"></div>
                                <span class="dashicons dashicons-search"></span>
                            </div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="user,userid,user id,id,name,user account,account,account id,group,groupid,group id,group name,group account,my account">
                            <h4>Account > <strong>userid</strong></h4>
                            <div class="description"><p>Display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</p></div>
                            <div class="example"><p><code>[zotpressInText userid="000000"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="nickname,name,nick,user,user id,account,alias">
                            <h4>Account > <strong>nickname</strong></h4>
                            <div class="description"><p>Display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</p></div>
                            <div class="example"><p><code>[zotpressInText nickname="Katie"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="item,item key,item id,reference,citation,source,page">
                            <h4>Data > <strong>items</strong></h4>
                            <div class="description"><p>Alternative: <code>item</code> Item keys and page number pairs formatted like so: <code>item_key</code> or <code>{item_key,pages}</code> or <code>{item_key1,pages},{item_key2,pages},...</code>.</p></div>
                            <div class="example"><p><code>[zotpressInText item="NCXAA92F"]</code></p><p><code>[zotpressInText item="{NCXAA92F,10-15}"]</code></p><p><code>[zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]</code></p><p>Parentheses indicate multiple, non-contiguous references to pages in the same source.</p><p><code>[zotpressInText items="{3ITTIXHP,(3,5)}"]</code></p></div>
                            <div class="description"><p>Can also include items from multiple Zotero accounts using this format: <code>{api_user_id:item_key}</code></p></div>
                            <div class="example"><p><code>[zotpressInText item="{000001:NCXAA92F},{200000:DTA2KZXU}"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="format,style,editor,presentation,placeholders,display">
                            <h4>Display > <strong>format</strong></h4>
                            <div class="description">
                                <p>How the in-text citation should be presented. Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</p>
                                <p class="break"><strong>Hint:</strong> In WordPress shortcodes, the bracket characters <strong>[</strong> and <strong>]</strong> are special characters. To use in-text brackets, see the <code>brackets</code> attribute below.</p>
                            </div>
                            <div class="example">
                                <p><code>[zotpressInText item="NCXAA92F" format="%a% (%d%, %p%)"]</code> will display as: <span style="padding-left: 0.5em; font-family: monospace;">author (date, pages)</span></p>
                            </div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="format,brackets,numbers,numeric,presentation,display,placeholders,style,editor">
                            <h4>Display > <strong>brackets</strong></h4>
                            <div class="description"><p>A special format option for in-text citations. <strong>Options: true, false [default]</strong></p></div>
                            <div class="example"><p><code>[zotpressInText item="{NCXAA92F,DTA2KZXU}" format="%num%" brackets="yes"], which will display as: <span style="padding-left: 0.5em; font-family: monospace;">[1, 2]</span></code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="etal,et al,etc,authors,multiple authors,list authors">
                            <h4>Display > <strong>etal</strong></h4>
                            <div class="description"><p>How "et al." is applied to multiple instances of a citation if it has three or more authors. Default is full author list for first instance and "et al." for every other instance. <strong>Options: yes, no, default [default]</strong></p></div>
                            <div class="example"><p><code>[zotpressInText item="NCXAA92F" etal="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="separator,list,list citations,multiple citations,author list,comma,semicolon">
                            <h4>Display > <strong>separator</strong></h4>
                            <div class="description"><p>How a list of two or more citations is delineated. Default is with a comma. <strong>Options: comma, semicolon [default]</strong></p></div>
                            <div class="example"><p><code>[zotpressInText item="NCXAA92F" separator="semicolon"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">(Sagan 2013; Hawkings 2014)</span></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="and,ampersand,authors,multiple authors,author list,comma,comma-and,comma-amp">
                            <h4>Display > <strong>and</strong></h4>
                            <div class="description"><p>Whether some form of "and" is applied to citations with two or more authors. Default is "and". <strong>Options: ampersand [default], and, comma, comma-amp, comma-and</strong></p></div>
                            <div class="example"><p><code>[zotpressInText item="NCXAA92F" and="comma-and"]</code>, which will display as: <span style="padding-left: 0.5em; font-family: monospace;">(Sagan, and Hawkings 2014)</span></p></div>
                        </div>

                    </div><!-- #zp-Tab-InText -->
                </div><!-- .zp-Zotero-API-Attributes -->


                <div id="zp-Tab-Library">

                    <div class="zp-Zotero-API-Explanation">
                        <p>
                            <?php _e('To display your library on the front-end of your website so that visitors can browse it, use this shortcode on a post or page','zotpress'); ?>:
                        </p>
                        <p><code>[zotpressLib userid="00000"]</code></p>
                    </div><!-- .zp-Zotero-API-Explanation -->

                    <div class="zp-Zotero-API-Attributes">

                        <div class="zp-Zotero-API-Attributes-Search">
                            <input class="zp-Zotero-API-Attributes-Search-Input" type="text" placeholder="<?php _e('Search for an attribute using a keyword','zotpress'); ?>">
                            <div class="zp-Zotero-API-Attributes-Search-Status">
                                <div class="zp-Loading"></div>
                                <span class="dashicons dashicons-search"></span>
                            </div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="user,userid,user id,id,name,user account,account,account id,group,groupid,group id,group name,group account,my account">
                            <h4>Account > <strong>userid</strong></h4>
                            <div class="description"><p>Display a list of citations from a particular user or group. <strong>REQUIRED if you have multiple accounts and are not using the "nickname" parameter.</strong> If neither is entered, it will default to the first user account listed.</p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="nickname,name,nick,user,user id,account,alias">
                            <h4>Account > <strong>nickname</strong></h4>
                            <div class="description"><p>Display a list of citations by a particular Zotero account nickname. <strong>Hint:</strong> You can give your Zotero account a nickname on the <a title="Accounts" href="admin.php?page=Zotpress&amp;accounts=true">Accounts page</a>.</p></div>
                            <div class="example"><p><code>[zotpressLib nickname="Katie"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="searchby,search by,search">
                            <h4>Data > <strong>searchby</strong></h4>
                            <div class="description"><p><strong>Search bar only.</strong> Set what content types can be used in the search. <strong>Options: items [default], tags</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="searchbar" searchby="tags"]</code></p><p>Or multiple:<p><code>[zotpressLib userid="00000" type="searchbar" searchby="items,tags"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="minlength,min length,minimum,query">
                            <h4>Data > <strong>minlength</strong></h4>
                            <div class="description"><p><strong>Search bar only.</strong> Minimum length of query before autcomplete starts searching. <strong>Options: 3 [default] or any number (although 3+ is best)</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="searchbar" minlength="4"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="maxresults,max results,maximum,query,limit">
                            <h4>Data > <strong>maxresults</strong></h4>
                            <div class="description"><p><strong>Search bar only.</strong> Maximum number of results to request per query. <strong>Options: 50 [default] or any number between 1 and 100 (although lower is better)</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="searchbar" maxresults="20"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="maxperpage,max per page,maximum,pagination,limit">
                            <h4>Data > <strong>maxperpage</strong></h4>
                            <div class="description"><p><strong>Search bar only.</strong> Maximum number of result items per pagination page. <strong>Options: 10 [default] or any number (although lower is better)</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="searchbar" maxperpage="5"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="maxtags,max tags,maximum,limit">
                            <h4>Data > <strong>maxtags</strong></h4>
                            <div class="description"><p><strong>Dropdown only.</strong> Maximum number of tags to display in dropdown. <strong>Options: 100 [default] or any number (although lower is better)</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="dropdown" maxtags="15"]</code></p></div>
                        </div>

                        <div class="zp-Zotero-API-Attribute" data-keywords="type,navigation,dropdown,drop down,searchbar,search bar">
                            <h4>Display > <strong>type</strong></h4>
                            <div class="description">
                                <p>Type of library navigation used. <strong>Options: dropdown [default], searchbar.</strong></p>
                            </div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="searchbar"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="browse,bar,dropdown,select,drop down,browse bar, navigation,menu,nav">
                            <h4>Display > <strong>browsebar</strong></h4>
                            <div class="description"><p>Show or hide the browse bar. Only for the dropdown version of the library. <strong>Options: hide, show [default].</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" browsebar="hide"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="sortby,sort by,order,order by,category">
                            <h4>Display > <strong>sortby</strong></h4>
                            <div class="description"><p>Sort multiple citations using meta data as attributes. <strong>Options: title, author, date, default (latest added) [default].</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" sortby="date"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="sortby,sort by,order,order by,category">
                            <h4>Display > <strong>order</strong></h4>
                            <div class="description"><p>Order of the sortby attribute. <strong>Options: asc [default], desc.</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" sortby="date" order="desc"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="download,file,url,link,view,pdf,read">
                            <h4>Display > <strong>download</strong></h4>
                            <div class="description"><p>Alternative: <code>downloadable</code> Whether or not to display the citation's download URL, if one exists. <strong>Enable this option only if you are legally able to provide your files for download.</strong> Options: yes, no [default].</p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" download="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="cite,reference,source,link,url,ris">
                            <h4>Display > <strong>cite</strong></h4>
                            <div class="description"><p>Alternative: <code>citeable</code> Make the displayed citations citable by generating RIS links. <strong>Options: yes, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" cite="yes"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="target,_blank,blank,new window,window,open">
                            <h4>Display > <strong>target</strong></h4>
                            <div class="description"><p>Links open up in a new window or tab. Applies to citation links, e.g. "retrieved from." Compliant with HTML5 but not XHTML Strict. <strong>Options: new, no [default].</strong></p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" target="new"]</code></p></div>
                        </div>
                        <div class="zp-Zotero-API-Attribute" data-keywords="toplevel,default,collection,dropdown">
                            <h4>Display > <strong>toplevel</strong></h4>
                            <div class="description"><p>Set a collection as the top level or default starting collection for the dropdown version. Takes a single collection ID.</p></div>
                            <div class="example"><p><code>[zotpressLib userid="00000" type="dropdown" toplevel="NCXAA92F"]</code></p></div>
                        </div>
                    </div><!-- .zp-Zotero-API-Attributes -->

				</div><!-- #zp-Tab-Library -->


				<div id="zp-Tab-FAQ">

					<div class="zp-Zotero-API-Explanation">
						<p>
							<?php _e('Check out the answered questions below. If you can\'t find what you\'re looking for, feel free to post your question at the','zotpress'); ?>
							<a title="Zotpress Forums" href="http://wordpress.org/support/plugin/zotpress"><?php _e('Zotpress Support Forums','zotpress'); ?></a>.
						</p>

						<h4>Does Zotpress auto-update or auto-sync my Zotero library?</h4>
						<p>Yes. Zotpress now uses a realtime data management approach with cURL and AJAX.</p>

						<h4>How can I edit a Zotero account listed on the Accounts page?</h4>
						<p>You can't, but you <em>can</em> delete the account and re-add it with the new information.</p>

						<h4>How do I find a group ID?</h4>
						<p>
							There are two ways, depending on the age of the group.
							Older Zotero groups will have their group ID listed in the URL: a number 1-6+ digits in length after "groups". New Zotero groups may hide their group ID behind a moniker.
							If you're the group owner, you can login to <a title="Zotero" href="http://www.zotero.org/">Zotero</a>, click on "Groups", and then hover over or click on "Manage Group" under the group's title.
							Everyone else can view the RSS Feed of the group and note the group id in the URL.
						</p>

						<h4>I've added a group to Zotpress, but it's not displaying citations. How do I display a group's citations?</h4>
						<p>
							You can list any group on Zotpress as long as you have the correct private key.
							If you're not the group owner, you can try sending the owner a request for one.
						</p>

						<h4>How do I find a collection ID?</h4>
						<p>It's displayed next to the collection name on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page.</p>

						<h4>How do I find an item key (citation ID)?</h4>
						<p>It's displayed beneath the citation on the <a title="Browse" href="admin.php?page=Zotpress">Browse</a> page. It's also listed on the dropdown associated with each item you search via the Reference widget (found on post add/edit screens).</p>

						<h4>Zotpress won't retrieve my library, or only retrieves some of my library.</h4>
						<p>First, check with your web host or server admin to make sure that one of cURL, fopen with Streams (PHP 5), or fsockopen is enabled. If so, check to see if your server has any restrictions on timeouts (Zotpress sometimes needs more than 30 seconds to process a request to the Zotero servers).</p>

                        <h4>I'm trying to use an in-text citation, but I'm just getting a spinning wheel.</h4>
                        <p>Make sure you've placed a [zotpressInTextBib] somewhere below the in-text shortcode(s) on the same page.</p>
					</div><!-- .zp-Zotero-API-Explanation -->

				</div><!-- #zp-Tab-FAQ -->

			</div>

        </div>
