<?php global $wpdb; ?>


<!-- START OF ZOTPRESS METABOX -------------------------------------------------------------------------->

<div id="zp-ZotpressMetaBox">
	<div id="zp-ZotpressMetaBox-Inner">

		<?php

		// If there's accounts ...
		if ( $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress;") > 1 )
		{
			// See if default exists
			$zp_default_account = false;
			if ( get_option("Zotpress_DefaultAccount") )
				$zp_default_account = get_option("Zotpress_DefaultAccount");

			if ( $zp_default_account !== false )
			{
				$zp_account = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress
						WHERE api_user_id = '%s'
						",
						$zp_default_account
					)
				);
			}
			else // Otherwise, assume one account
			{
				$zp_account = $wpdb->get_results(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."zotpress LIMIT 1;
					"
				);
			}

			if ( ! is_null($zp_account[0]->nickname) 
					&& $zp_account[0]->nickname != "" )
				$zp_default_account = $zp_account[0]->nickname . " (" . $zp_account[0]->api_user_id . ")";
		?>

		<!-- START OF ACCOUNT -->
		<div id="zp-ZotpressMetaBox-Account" rel="<?php echo $zp_account[0]->api_user_id; ?>">

			<div class="components-base-control">
		        <label class="components-base-control__label" for="zp-ZotpressMetaBox-Acccount-Select">
		            <?php _e('Searching','zotpress'); ?>:
		        </label>

		        <select id="zp-ZotpressMetaBox-Acccount-Select" name="zp-ZotpressMetaBox-Acccount-Select"><?php

		            $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress");

		            foreach ( $accounts as $num => $account )
		            {
		                $account_meta = array(
		                    'id' => $account->id,
		                    'api_user_id' => $account->api_user_id,
		                    'account_type' => $account->account_type
		                );

		                echo '<option value="'.$account->api_user_id.'"';
		                if ( $zp_account[0]->api_user_id == $account->api_user_id )
		                    echo ' selected="selected"';
						echo '>';
		                if (!is_null($account->nickname) && $account->nickname != "")
		                    echo $account->nickname . " - ";
		                echo $account->api_user_id.'</option>';
		                echo "\n";
		            }
		        ?></select>
			</div><!-- .components-base-control -->
		</div>
		<!-- END OF ACCOUNT -->
		<?php } ?>


		<!-- START OF SEARCH -->
		<div id="zp-ZotpressMetaBox-Search">
			<div id="zp-ZotpressMetaBox-Search-Inner">
				<input id="zp-ZotpressMetaBox-Search-Input" class="help" type="text" placeholder="<?php _e('Type to search','zotpress'); ?>">
				<input type="hidden" id="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" value="<?php echo ZOTPRESS_PLUGIN_URL; ?>">
			</div>
		</div>

		<div id="zp-ZotpressMetaBox-List">
			<div id="zp-ZotpressMetaBox-List-Inner"></div>
			<hr class="clear">
		</div>
		<!-- END OF SEARCH -->

		<!-- Start of shortcode type tabs -->
		<div id="zp-ZotpressMetaBox-Type" class="zp-ZotpressMetaBox-Sub">
			<h4><?php _e('Type','zotpress'); ?>:</h4>
			<ul class="ui-widget-header ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-corner-all">
				<li class="ui-tabs-active ui-state-active ui-state-default ui-corner-top"><a href="#zp-ZotpressMetaBox-Bibliography"><?php _e('Bib','zotpress'); ?></a></li>
				<li class="ui-state-default ui-corner-top"><a href="#zp-ZotpressMetaBox-InText"><?php _e('In-Text','zotpress'); ?></a></li>
				<li class="ui-state-default ui-corner-top"><a href="#zp-ZotpressMetaBox-InTextBib"><?php _e('In-Text Bib','zotpress'); ?></a></li>
			</ul>
	    </div>
		<!-- End of tabs -->



	    <!-- START OF ZOTPRESS BIBLIOGRAPHY ------------------------------------------------------------------>
	    <!-- NEXT: datatype [items, tags, collections], SEARCH items, tags, collections LIMIT -------------- -->

	    <div id="zp-ZotpressMetaBox-Bibliography" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

	        <!-- START OF OPTIONS -->
	        <div id="zp-ZotpressMetaBox-Biblio-Options" class="zp-ZotpressMetaBox-Sub">

	            <h4><span class='toggle'><?php _e('Options','zotpress'); ?>: <span class='toggle-button dashicons dashicons-arrow-down-alt2'></span></span></h4>

	            <div id="zp-ZotpressMetaBox-Biblio-Options-Inner">

	                <label for="zp-ZotpressMetaBox-Biblio-Options-Author"><?php _e('Filter by Author','zotpress'); ?>:</label>
	                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Author" value="">

	                <hr>

	                <label for="zp-ZotpressMetaBox-Biblio-Options-Year"><?php _e('Filter by Year','zotpress'); ?>:</label>
	                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Year" value="">

	                <hr>

	                <label for="zp-ZotpressMetaBox-Biblio-Options-Style"><?php _e('Style','zotpress'); ?>:</label>
	                <select id="zp-ZotpressMetaBox-Biblio-Options-Style">
	                    <?php

	                    if (!get_option("Zotpress_StyleList"))
	                        add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");

	                    $zp_styles = explode(", ", get_option("Zotpress_StyleList"));
	                    sort($zp_styles);

	                    // See if default exists
	                    $zp_default_style = "apa";
	                    if (get_option("Zotpress_DefaultStyle"))
	                        $zp_default_style = get_option("Zotpress_DefaultStyle");

	                    foreach($zp_styles as $zp_style)
	                        if ($zp_style == $zp_default_style)
	                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" rel='default' selected='selected'>".$zp_style."</option>\n";
	                        else
	                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";

	                    ?>
	                </select>
	                <p class="note"><?php _e('Add more styles','zotpress'); ?> <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>"><?php _e('here','zotpress'); ?></a>.</p>

	                <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-Biblio-Options-SortBy"><?php _e('Sort By','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-Biblio-Options-SortBy">
		                    <option id="zp-bib-default" value="default" rel="default" selected="selected"><?php _e('Default','zotpress'); ?></option>
		                    <option id="zp-bib-author" value="author"><?php _e('Author','zotpress'); ?></option>
		                    <option id="zp-bib-date" value="date"><?php _e('Date','zotpress'); ?></option>
		                    <option id="zp-bib-title" value="title"><?php _e('Title','zotpress'); ?></option>
		                </select>
					</div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Sort Order','zotpress'); ?>:
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC"><?php _e('Asc','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-ASC" name="sort" value="ASC" checked="checked">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Sort-DESC"><?php _e('Desc','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Sort-DESC" name="sort" value="DESC">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Images','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Image-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-Yes" name="images" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Image-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Image-No" name="images" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Title by Year','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Title-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-Yes" name="title" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Title-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Title-No" name="title" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Downloadable','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Download-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-Yes" name="download" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Download-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Download-No" name="download" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Abstract','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes" name="abstract" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Abstract-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Abstract-No" name="abstract" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Notes','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-Yes" name="notes" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Notes-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Notes-No" name="notes" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Cite with RIS','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-Yes" name="cite" value="yes">

	                        <label for="zp-ZotpressMetaBox-Biblio-Options-Cite-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-Biblio-Options-Cite-No" name="cite" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-Biblio-Options-Limit"><?php _e('Limit By','zotpress'); ?>:</label>
		                <input type="text" id="zp-ZotpressMetaBox-Biblio-Options-Limit" size="4">
					</div>

	            </div>
	        </div>
	        <!-- END OF OPTIONS -->

			<!-- START OF BIB SHORTCODE -->
			<div id="zp-ZotpressMetaBox-Biblio-Generate">

				<a id="zp-ZotpressMetaBox-Biblio-Generate-Button" class="button-primary zp-ZotpressMetaBox-Insert-Button" data-sctype="bib" href="javascript:void(0);"><?php _e('Generate Shortcode','zotpress'); ?></a>
				<a id="zp-ZotpressMetaBox-Biblio-Clear-Button" class="button" href="javascript:void(0);"><?php _e('Clear','zotpress'); ?></a>

				<hr class="clear">

				<div id="zp-ZotpressMetaBox-Biblio-Generate-Inner">
					<label for="zp-ZotpressMetaBox-Biblio-Generate-Text"><?php _e('Shortcode','zotpress'); ?>:</span></label>
					<textarea id="zp-ZotpressMetaBox-Biblio-Generate-Text">[zotpress]</textarea>
				</div>
			</div>
			<!-- END OF BIB SHORTCODE -->

	    </div><!-- #zp-ZotpressMetaBox-Bibliography -->

	    <!-- END OF ZOTPRESS BIBLIOGRAPHY --------------------------------------------------------------------->



	    <!-- START OF ZOTPRESS IN-TEXT ------------------------------------------------------------------------->

	    <div id="zp-ZotpressMetaBox-InText" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

	        <!-- START OF OPTIONS -->
	        <div id="zp-ZotpressMetaBox-InText-Options" class="zp-ZotpressMetaBox-Sub">

	            <h4><span class='toggle'><?php _e('Options','zotpress'); ?>: <span class='toggle-button dashicons dashicons-arrow-down-alt2'></span></span></h4>

	            <div id="zp-ZotpressMetaBox-InText-Options-Inner">

	                <label for="zp-ZotpressMetaBox-InText-Options-Format"><?php _e('Format','zotpress'); ?>:</label>
	                <input type="text" id="zp-ZotpressMetaBox-InText-Options-Format" value="(%a%, %d%, %p%)">
					<p class="note"><?php _e('Placeholders: <code>%a%</code> for author, <code>%d%</code> for date, <code>%p%</code> for page, <code>%num%</code> for list number.<br>','zotpress'); ?></p>
					<p class="note"><?php _e('Note: If you wish to use square brackets, do NOT type them here; instead, select "Yes" for the brackets attribute.','zotpress'); ?></p>

		            <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-InText-Options-Brackets"><?php _e('Brackets','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-InText-Options-Brackets">
		                    <option id="default" value="default" selected="selected"><?php _e('Default','zotpress'); ?></option>
		                    <option id="yes" value="yes"><?php _e('Yes','zotpress'); ?></option>
		                    <option id="no" value="no"><?php _e('No','zotpress'); ?></option>
		                </select>
					</div>

	                <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-InText-Options-Etal"><?php _e('Et al','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-InText-Options-Etal">
		                    <option id="default" value="default" selected="selected"><?php _e('Default','zotpress'); ?></option>
		                    <option id="yes" value="yes"><?php _e('Yes','zotpress'); ?></option>
		                    <option id="no" value="no"><?php _e('No','zotpress'); ?></option>
		                </select>
					</div>

	                <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-InText-Options-Separator"><?php _e('Separator','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-InText-Options-Separator">
		                    <option id="semicolon" value="default" selected="selected"><?php _e('Semicolon','zotpress'); ?></option>
		                    <option id="default" value="comma"><?php _e('Comma','zotpress'); ?></option>
		                </select>
					</div>

	                <hr>

					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-InText-Options-And"><?php _e('And','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-InText-Options-And">
		                    <option id="default" value="default" selected="selected"><?php _e('No','zotpress'); ?></option>
		                    <option id="and" value="and"><?php _e('and','zotpress'); ?></option>
		                    <option id="comma-and" value="comma-and"><?php _e(', and','zotpress'); ?></option>
		                </select>
					</div>
	            </div>
	        </div>
	        <!-- END OF OPTIONS -->

	        <!-- START OF IN-TEXT SHORTCODE -->
	        <div id="zp-ZotpressMetaBox-InText-Generate">

	            <a id="zp-ZotpressMetaBox-InText-Generate-Button" class="button-primary zp-ZotpressMetaBox-Insert-Button" data-sctype="intext" href="javascript:void(0);"><?php _e('Generate Shortcode','zotpress'); ?></a>
	            <a id="zp-ZotpressMetaBox-InText-Clear-Button" class="button" href="javascript:void(0);"><?php _e('Clear','zotpress'); ?></a>

	            <hr class="clear">

	            <div id="zp-ZotpressMetaBox-InText-Generate-Inner">
	                <label for="zp-ZotpressMetaBox-InText-Generate-Text"><?php _e('Shortcode','zotpress'); ?>:</span></label>
	                <textarea id="zp-ZotpressMetaBox-InText-Generate-Text">[zotpressInText]</textarea>
	            </div>
	        </div>
	        <!-- END OF IN-TEXT SHORTCODE -->

	    </div><!-- #zp-ZotpressMetaBox-InText -->

	    <!-- END OF ZOTPRESS IN-TEXT ---------------------------------------------------------------------------->


		<!-- START OF ZOTPRESS IN-TEXT BIB ------------------------------------------------------------------------->

	    <div id="zp-ZotpressMetaBox-InTextBib" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

	        <!-- START OF OPTIONS -->
	        <div id="zp-ZotpressMetaBox-InTextBib-Options" class="zp-ZotpressMetaBox-Sub">

	            <h4><span class='toggle'><?php _e('Options','zotpress'); ?>: <span class='toggle-button dashicons dashicons-arrow-down-alt2'></span></span></h4>

	            <div id="zp-ZotpressMetaBox-InTextBib-Options-Inner">
					<label for="zp-ZotpressMetaBox-InTextBib-Options-Style"><?php _e('Style','zotpress'); ?>:</label>
	                <select id="zp-ZotpressMetaBox-InTextBib-Options-Style">
	                    <?php

	                    if (!get_option("Zotpress_StyleList"))
	                        add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver");

	                    $zp_styles = explode(", ", get_option("Zotpress_StyleList"));
	                    sort($zp_styles);

	                    // See if default exists
	                    $zp_default_style = "apa";
	                    if (get_option("Zotpress_DefaultStyle")) $zp_default_style = get_option("Zotpress_DefaultStyle");

	                    foreach($zp_styles as $zp_style)
	                        if ($zp_style == $zp_default_style)
	                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\" rel='default' selected='selected'>".$zp_style."</option>\n";
	                        else
	                            echo "<option id=\"".$zp_style."\" value=\"".$zp_style."\">".$zp_style."</option>\n";

	                    ?>
	                </select>
	                <p class="note"><?php _e('Add more styles','zotpress'); ?> <a href="<?php echo admin_url( 'admin.php?page=Zotpress&options=true'); ?>"><?php _e('here','zotpress'); ?></a>.</p>

	                <hr>

	                <!--Sort by:-->
					<div class="zp-ZotpressMetaBox-Field">
		                <label for="zp-ZotpressMetaBox-InTextBib-Options-SortBy"><?php _e('Sort By','zotpress'); ?>:</label>
		                <select id="zp-ZotpressMetaBox-InTextBib-Options-SortBy">
		                    <option id="default" value="default" rel="default" selected="selected"><?php _e('Default','zotpress'); ?></option>
		                    <option id="author" value="author"><?php _e('Author','zotpress'); ?></option>
		                    <option id="date" value="date"><?php _e('Date','zotpress'); ?></option>
		                    <option id="title" value="title"><?php _e('Title','zotpress'); ?></option>
		                </select>
					</div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Sort Order','zotpress'); ?>:
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Sort-ASC"><?php _e('Asc','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Sort-ASC" name="sort" value="ASC" checked="checked">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Sort-DESC"><?php _e('Desc','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Sort-DESC" name="sort" value="DESC">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Images','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Image-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Image-Yes" name="images" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Image-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Image-No" name="images" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Title by Year','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Title-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Title-Yes" name="title" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Title-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Title-No" name="title" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Downloadable','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Download-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Download-Yes" name="download" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Download-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Download-No" name="download" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Abstract','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Abstract-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Abstract-Yes" name="abstract" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Abstract-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Abstract-No" name="abstract" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Notes','zotpress'); ?>?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Notes-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Notes-Yes" name="notes" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Notes-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Notes-No" name="notes" value="no" checked="checked">
	                    </div>
	                </div>

	                <hr>

	                <div class="zp-ZotpressMetaBox-Field">
	                    <?php _e('Cite with RIS','zotpress'); ?>S?
	                    <div class="zp-ZotpressMetaBox-Field-Radio">
	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Cite-Yes"><?php _e('Yes','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Cite-Yes" name="cite" value="yes">

	                        <label for="zp-ZotpressMetaBox-InTextBib-Options-Cite-No"><?php _e('No','zotpress'); ?></label>
	                        <input type="radio" id="zp-ZotpressMetaBox-InTextBib-Options-Cite-No" name="cite" value="no" checked="checked">
	                    </div>
	                </div>
				</div><!-- #zp-ZotpressMetaBox-InTextBib-Options-Inner -->
			</div><!-- #zp-ZotpressMetaBox-InTextBib-Options -->

			<!-- START OF IN-TEXT BIB SHORTCODE -->
	        <div id="zp-ZotpressMetaBox-InTextBib-Generate">

	            <a id="zp-ZotpressMetaBox-InTextBib-Generate-Button" class="button-primary zp-ZotpressMetaBox-Insert-Button" data-sctype="intextbib" href="javascript:void(0);"><?php _e('Generate Shortcode','zotpress'); ?></a>
	            <a id="zp-ZotpressMetaBox-InTextBib-Clear-Button" class="button" href="javascript:void(0);"><?php _e('Clear','zotpress'); ?></a>

	            <hr class="clear">

	            <div id="zp-ZotpressMetaBox-InTextBib-Generate-Inner">
	                <div id="zp-ZotpressMetaBox-InTextBib-Text-Container" class="inTextOnly">
	                    <label for="zp-ZotpressMetaBox-InTextBib-Generate-Text"><span><?php _e('Paste somewhere in the post','zotpress'); ?>:</span></label>
	                    <textarea id="zp-ZotpressMetaBox-InTextBib-Generate-Text">[zotpressInTextBib]</textarea>
	                </div>
	            </div>
	        </div>
	        <!-- END OF IN-TEXT BIB SHORTCODE -->

		</div><!-- #zp-ZotpressMetaBox-InTextBib -->

	</div><!-- #zp-ZotpressMetaBox-Inner -->

	<!-- Close button -->
	<button id="zp-ShortcodeBuilder-Close" class="components-button has-icon" type="button">
		<span class="dashicons dashicons-no-alt"></span>
	</button>

</div><!-- #zp-ZotpressMetaBox -->

<!-- END OF ZOTPRESS METABOX ------------------------------------------------------------------------------->
