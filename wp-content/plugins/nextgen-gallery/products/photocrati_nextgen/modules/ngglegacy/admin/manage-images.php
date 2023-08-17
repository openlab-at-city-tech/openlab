<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

/**
 * @param nggManageGallery|nggManageAlbum $controller
 */
function nggallery_picturelist($controller)
{
    global $ngg;

    $settings = C_NextGen_Settings::get_instance();

    $action_status = array('message' => '', 'status' => 'ok');

    // Look if its a search result
    $is_search = isset ($_GET['s']) ? true : false;
    $counter   = 0;

    $wp_list_table = new _NGG_Images_List_Table('nggallery-manage-images');

    // look for pagination
    $_GET['paged'] = isset($_GET['paged']) && ($_GET['paged'] > 0) ? absint($_GET['paged']) : 1;
    $items_per_page = (!empty($_GET['items']) ? $_GET['items'] : apply_filters('ngg_manage_images_items_per_page', 50));
    if ($items_per_page == 'all')
        $items_per_page = PHP_INT_MAX;
    else
        $items_per_page = (int)$items_per_page;

    $gallery_mapper = C_Gallery_Mapper::get_instance();

    if ($is_search)
    {
        // fetch the imagelist
        $picturelist = $ngg->manage_page->search_result;
        $total_number_of_images = count($picturelist);

        // we didn't set a gallery or a pagination
        $act_gid = 0;
    }
    else {
        // GET variables
        $act_gid    = $ngg->manage_page->gid;

        // Load the gallery metadata
        $gallery = $gallery_mapper->find($act_gid);

        if (!$gallery)
        {
            nggGallery::show_error(__('Gallery not found.', 'nggallery'));
            return;
        }

        // Check if you have the correct capability
        if (!nggAdmin::can_manage_this_gallery($gallery->author))
        {
            nggGallery::show_error(__('Sorry, you have no access here', 'nggallery'));
            return;
        }

        $start = ($_GET['paged'] - 1) * $items_per_page;

        // get picture values
        $image_mapper = C_Image_Mapper::get_instance();

        $total_number_of_images = count(
            $image_mapper->select($image_mapper->get_primary_key_column())
                ->where(array("galleryid = %d", $act_gid))
                ->run_query(FALSE, FALSE, TRUE)
        );

        $image_mapper->select()->where(array("galleryid = %d", $act_gid));

        if (($galSort = $settings->get('galSort', FALSE)) && ($galSortDir = $settings->get('galSortDir', FALSE)))
            $image_mapper->order_by($galSort, $galSortDir);
        $picturelist = $image_mapper->limit($items_per_page, $start)->run_query();
    }

    // list all galleries
    $gallerylist = $gallery_mapper->find_all();

    //get the columns
    $image_columns  = $wp_list_table->get_columns();
    $hidden_columns = get_hidden_columns('nggallery-manage-images');
    $num_columns    = count($image_columns) - count($hidden_columns);

    ?>

    <?php if ($action_status['message'] != '') { ?>
        <div id="message"
             class="<?php echo ($action_status['status'] == 'ok' ? 'updated' : $action_status['status']); ?> fade">
            <p>
                <strong><?php echo $action_status['message']; ?></strong>
            </p>
        </div>
    <?php } ?>

    <div class="wrap ngg_manage_images">

        <?php if ($is_search) :?>

        <div class="ngg_page_content_header">
            <img src="<?php echo(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>"
                 alt="">
            <h3>
                <?php printf(__('Search results for &#8220;%s&#8221;', 'nggallery'), esc_html(get_search_query())); ?>
            </h3>
        </div>

        <div class='ngg_page_content_main'>

            <form class="search-form" action="" method="get">
                <p class="search-box">
                    <label class="hidden"
                           for="media-search-input">
                        <?php _e('Search Images', 'nggallery'); ?>:
                    </label>

                    <input type="hidden"
                           id="page-name"
                           name="page"
                           value="nggallery-manage-gallery"/>

                    <input type="text"
                           id="media-search-input"
                           name="s"
                           placeholder="<?php _e('Search Images', 'nggallery'); ?>"
                           value="<?php the_search_query(); ?>"/>

                    <input type="submit"
                           value="<?php _e('Search Images', 'nggallery'); ?>"
                           class="button"/>
                </p>
            </form>

            <br style="clear: both;"/>

            <form id="updategallery"
                  class="nggform"
                  method="POST"
                  action="<?php echo $ngg->manage_page->base_page . '&amp;mode=edit&amp;s=' . get_search_query(); ?>"
                  accept-charset="utf-8">

                <?php wp_nonce_field('ngg_updategallery') ?>
                <input type="hidden" name="nggpage" value="manage-images"/>

                <!-- form#updategallery continues below end of if statement -->

                <!-- div.ngg_page_content_main continues below end of if statement -->

                <?php else :?>

                <div class="ngg_page_content_header">
                    <img src="<?php  echo(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>"
                         alt="">
                    <h3>
                        <?php echo _n('Gallery: ', 'Galleries: ', 1, 'nggallery'); ?>
                        <?php echo esc_html (M_I18N::translate($gallery->title)); ?>
                    </h3>
                </div>

                <div class='ngg_page_content_main'>

                    <form id="updategallery"
                          class="nggform"
                          method="POST"
                          action="<?php echo $ngg->manage_page->base_page . '&amp;mode=edit&amp;gid=' . $act_gid . '&amp;paged=' . esc_attr($_GET['paged']); ?>"
                          accept-charset="utf-8">

                        <?php wp_nonce_field('ngg_updategallery') ?>
                        <input type="hidden" name="nggpage" value="manage-images"/>

                        <?php if (nggGallery::current_user_can('NextGEN Edit gallery options')) : ?>

                            <div id="poststuff" class="meta-box-sortables">
                                <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
                                <div id="gallerydiv"
                                     class="postbox closed <?php echo postbox_classes('gallerydiv', 'ngg-manage-gallery'); ?>">
                                    <div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>">
                                        <span class="toggle-indicator"></span>
                                        <h3>
                                            <span>&nbsp;<?php _e('Gallery Settings', 'nggallery'); ?></span>
                                        </h3>

                                    </div>

                                    <div class="inside">
                                        <?php $controller->render_gallery_fields(); ?>

                                        <div class="submit">
                                            <?php if (wpmu_enable_function('wpmuImportFolder') && nggGallery::current_user_can('NextGEN Import image folder')) { ?>
                                                <input type="submit"
                                                       class="button-primary"
                                                       name="scanfolder"
                                                       value="<?php _e("Scan Folder for new images",'nggallery'); ?>"/>
                                            <?php } ?>
                                            <input type="submit"
                                                   class="button-primary action ngg_save_gallery_changes"
                                                   name="updatepictures"
                                                   value="<?php _e("Save Changes",'nggallery'); ?>"/>
                                        </div>

                                    </div>
                                </div>
                            </div> <!-- poststuff -->

                        <?php endif; ?>

                        <!-- form#updategallery continues below end of if statement -->

                        <!-- div.ngg_page_content_main continues below end of if statement -->

                        <?php endif; ?>

                        <!-- div.ngg_page_content_main continues here -->

                        <!-- form#updategallery continues here -->

                        <div class="tablenav top ngg-tablenav">

                            <?php
                            $ngg->manage_page->pagination('top', $_GET['paged'], $total_number_of_images, $items_per_page);

                            $items_per_page_array = apply_filters('ngg_manage_images_items_per_page_array', array(
                                '25'  => __(' 25', 'nggallery'),
                                '50'  => __(' 50', 'nggallery'),
                                '75'  => __(' 75', 'nggallery'),
                                '100' => __('100', 'nggallery'),
                                '200' => __('200', 'nggallery'),
                                'all' => __('All', 'nggallery')
                            ));
                            ?>

                            <select id="ngg-manage-images-items-per-page">
                                <?php foreach ($items_per_page_array as $val => $label) { ?>
                                    <?php
                                    $selected = '';
                                    if (!empty($_GET['items']) && $val == $_GET['items'])
                                        $selected = 'selected';
                                    elseif (empty($_GET['items']) && $val == $items_per_page)
                                        $selected = 'selected';
                                    ?>
                                    <option value="<?php echo esc_attr($val); ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label id="ngg-manage-images-items-per-page-label"
                                   for="ngg-manage-images-items-per-page"><?php echo __('Images per page:', 'nggallery'); ?></label>

                            <div class="alignleft actions">
                                <select id="bulkaction" name="bulkaction">
                                    <option value="no_action"><?php _e("Bulk actions",'nggallery'); ?></option>
                                    <option value="set_watermark"><?php _e("Set watermark",'nggallery'); ?></option>
                                    <option value="new_thumbnail"><?php _e("Create new thumbnails",'nggallery'); ?></option>
                                    <option value="resize_images"><?php _e("Resize images",'nggallery'); ?></option>
                                    <option value="recover_images"><?php _e("Recover from backup",'nggallery'); ?></option>
                                    <option value="delete_images"><?php _e("Delete images",'nggallery'); ?></option>
                                    <option value="import_meta"><?php _e("Import metadata",'nggallery'); ?></option>
                                    <option value="rotate_cw"><?php _e("Rotate images clockwise",'nggallery'); ?></option>
                                    <option value="rotate_ccw"><?php _e("Rotate images counter-clockwise",'nggallery'); ?></option>
                                    <option value="copy_to"><?php _e("Copy to...",'nggallery'); ?></option>
                                    <option value="move_to"><?php _e("Move to...",'nggallery'); ?></option>
                                    <option value="add_tags"><?php _e("Add tags",'nggallery'); ?></option>
                                    <option value="delete_tags"><?php _e("Delete tags",'nggallery'); ?></option>
                                    <option value="overwrite_tags"><?php _e("Overwrite tags",'nggallery'); ?></option>
                                    <option value="strip_orientation_tag"><?php _e("Remove EXIF Orientation",'nggallery'); ?></option>
                                </select>
                                <input class="button-primary"
                                       type="submit"
                                       name="showThickbox"
                                       value="<?php _e('Apply', 'nggallery'); ?>"
                                       onclick="if (!checkSelected()) return false;"/>

                                <?php if (($settings->galSort === "sortorder") && (!$is_search)) { ?>
                                    <input class="button-primary"
                                           type="submit"
                                           name="sortGallery"
                                           value="<?php _e('Sort gallery', 'nggallery');?>"/>
                                <?php } ?>

                                <input type="submit"
                                       name="updatepictures"
                                       class="button-primary action"
                                       value="<?php _e('Save Changes', 'nggallery');?>"/>
                            </div>
                        </div>

                        <table id="ngg-listimages" class="widefat fixed" cellspacing="0">

                            <thead>
                                <?php $controller->render_image_row_header() ?>
                            </thead>

                            <tfoot>
                                <?php $controller->render_image_row_header() ?>
                            </tfoot>

                            <tbody id="the-list">

                                <?php
                                if ($picturelist)
                                {
                                    $storage = C_Gallery_Storage::get_instance();
                                    $gallery_mapper = C_Gallery_Mapper::get_instance();

                                    foreach($picturelist as $picture) {

                                        if (empty($gallery) && $is_search)
                                            $gallery = $gallery_mapper->find($picture->galleryid, FALSE);

                                        //for search result we need to check the capability
                                        if (!nggAdmin::can_manage_this_gallery($gallery->author) && $is_search)
                                            continue;

                                        $counter++;
                                        $picture->imageURL  = $storage->get_image_url($picture);
                                        $picture->thumbURL  = $storage->get_image_url($picture, 'thumb');
                                        $picture->imagePath = $storage->get_image_abspath($picture);
                                        $picture->thumbPath = $storage->get_image_abspath($picture, 'thumb');
                                        echo apply_filters('ngg_manage_images_row', $picture, $counter);
                                    }
                                }

                                // In the case you have no capaptibility to see the search result
                                if ($counter == 0)
                                    echo '<tr><td colspan="' . $num_columns . '" align="center"><strong>'.__('No entries found','nggallery').'</strong></td></tr>';
                                ?>

                            </tbody>
                        </table>

                        <div class="tablenav bottom">
                            <input type="submit"
                                   class="button-primary action"
                                   name="updatepictures"
                                   value="<?php _e('Save Changes', 'nggallery'); ?>"/>
                            <?php $ngg->manage_page->pagination('bottom', $_GET['paged'], $total_number_of_images, $items_per_page); ?>
                        </div>

                    </form><!-- /form#updategallery  -->

                    <br class="clear"/>

                    <?php do_action('ngg_manage_images_marketing_block'); ?>

                </div><!-- /div.ngg_page_content_main -->

    </div><!-- /#wrap -->

    <!-- #entertags -->
    <div id="entertags" style="display: none;">
        <form id="form-tags" method="POST" accept-charset="utf-8">
            <?php wp_nonce_field('ngg_thickbox_form') ?>
            <input type="hidden" id="entertags_imagelist" name="TB_imagelist" value=""/>
            <input type="hidden" id="entertags_bulkaction" name="TB_bulkaction" value=""/>
            <input type="hidden" name="nggpage" value="manage-images"/>
            <input type="hidden" name="TB_EditTags" value="OK"/>
            <table width="100%" border="0" cellspacing="3" cellpadding="3">
                <tr>
                    <th>
                        <?php _e("Enter the tags",'nggallery'); ?> :
                        <input name="taglist"
                               type="text"
                               style="width:90%"
                               value=""/>
                    </th>
                </tr>
                <tr>
                    <td class="submit">
                        <input class="button-primary"
                               type="submit"
                               name="TB_EditTags"
                               onClick="jQuery(this).attr('disabled', 'disabled'); submit();"
                               value="<?php _e("OK",'nggallery'); ?>"/>
                        <input class="button-primary dialog-cancel"
                               type="reset"
                               value="&nbsp;<?php _e("Cancel",'nggallery'); ?>&nbsp;"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!-- /#entertags -->

    <!-- #selectgallery -->
    <div id="selectgallery" style="display: none;">
        <form id="form-select-gallery" method="POST" accept-charset="utf-8">
            <?php wp_nonce_field('ngg_thickbox_form') ?>
            <input type="hidden" id="selectgallery_imagelist" name="TB_imagelist" value=""/>
            <input type="hidden" id="selectgallery_bulkaction" name="TB_bulkaction" value=""/>
            <input type="hidden" name="nggpage" value="manage-images"/>
            <input type="hidden" name="TB_SelectGallery" value="OK"/>
            <table width="100%" border="0" cellspacing="3" cellpadding="3">
                <tr>
                    <th>
                        <?php _e('Select the destination gallery:', 'nggallery'); ?>&nbsp;
                        <select name="dest_gid" style="width:90%">
                            <?php
                            foreach ($gallerylist as $gallery) {
                                if ($gallery->gid != $act_gid) { ?>
                                    <option value="<?php echo esc_attr($gallery->gid); ?>">
                                        <?php
                                        print esc_attr(apply_filters('ngg_gallery_title_select_field', $gallery->title, $gallery, FALSE));
                                        ?>
                                    </option>
                                <?php }
                            }
                            ?>
                        </select>
                    </th>
                </tr>
                <tr>
                    <td class="submit">
                        <input type="submit"
                               class="button-primary"
                               name="TB_SelectGallery"
                               onClick="jQuery(this).attr('disabled', 'disabled'); submit();"
                               value="<?php _e("OK",'nggallery'); ?>"/>
                        <input class="button-primary dialog-cancel"
                               type="reset"
                               value="<?php _e("Cancel",'nggallery'); ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!-- /#selectgallery -->

    <!-- #resize_images -->
    <div id="resize_images" style="display: none;">
        <form id="form-resize-images" method="POST" accept-charset="utf-8">
            <?php wp_nonce_field('ngg_thickbox_form') ?>
            <input type="hidden" id="resize_images_imagelist" name="TB_imagelist" value=""/>
            <input type="hidden" id="resize_images_bulkaction" name="TB_bulkaction" value=""/>
            <input type="hidden" name="nggpage" value="manage-images"/>
            <input type="hidden" name="TB_ResizeImages" value="OK"/>
            <table width="100%" border="0" cellspacing="3" cellpadding="3">
                <tr valign="top">
                    <td>
                        <strong><?php _e('Resize Images to', 'nggallery'); ?>:</strong>
                    </td>
                    <td>
                        <input type="text"
                               size="5"
                               name="imgWidth"
                               value="<?php echo $settings->imgWidth ?>"/>
                        x
                        <input type="text"
                               size="5"
                               name="imgHeight"
                               value="<?php echo $settings->imgHeight; ?>"/>
                        <br/>
                        <small><?php _e('Width x height (in pixel). NextGEN Gallery will keep ratio size','nggallery') ?></small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="submit">
                        <input class="button-primary"
                               type="submit"
                               name="TB_ResizeImages"
                               onClick="jQuery(this).attr('disabled', 'disabled'); submit();"
                               value="<?php _e('OK', 'nggallery'); ?>"/>
                        <input class="button-primary dialog-cancel"
                               type="reset"
                               value="&nbsp;<?php _e('Cancel', 'nggallery'); ?>&nbsp;"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!-- /#resize_images -->

    <!-- #new_thumbnail -->
    <div id="new_thumbnail" style="display: none;">
        <form id="form-new-thumbnail" method="POST" accept-charset="utf-8">
            <?php wp_nonce_field('ngg_thickbox_form') ?>
            <input type="hidden" id="new_thumbnail_imagelist" name="TB_imagelist" value=""/>
            <input type="hidden" id="new_thumbnail_bulkaction" name="TB_bulkaction" value=""/>
            <input type="hidden" name="nggpage" value="manage-images"/>
            <input type="hidden" name="TB_NewThumbnail" value="OK"/>
            <table width="100%" border="0" cellspacing="3" cellpadding="3">
                <tr valign="top">
                    <th align="left">
                        <?php _e('Width x height (in pixel)','nggallery') ?>
                    </th>
                    <td>
                        <?php include(dirname(__FILE__) . '/thumbnails-template.php'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th align="left">
                        <?php _e('Set fix dimension','nggallery') ?>
                    </th>
                    <td>
                        <input type="checkbox"
                               name="thumbfix"
                               value="1"
                               <?php checked('1', $settings->thumbfix); ?>/>
                        <br/>
                        <small><?php _e('Ignore the aspect ratio, no portrait thumbnails','nggallery') ?></small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="submit">
                        <input class="button-primary"
                               type="submit"
                               name="TB_NewThumbnail"
                               onClick="jQuery(this).attr('disabled', 'disabled'); submit();"
                               value="<?php _e('OK', 'nggallery');?>"/>
                        <input class="button-primary dialog-cancel"
                               type="reset"
                               value="&nbsp;<?php _e('Cancel', 'nggallery'); ?>&nbsp;"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!-- /#new_thumbnail -->

    <script type="text/javascript">
		(function($) {
			$('#ngg-manage-images-items-per-page').on('change', function() {
				window.location.href = setURLParam('items', $(this).val());
			});

			// load a content via ajax
			$('a.ngg-dialog').on('click', function() {
				var dialogs = $('.ngg-overlay-dialog:visible');
				if (dialogs.length > 0) {
					return false;
				}

				if ($("#spinner").length === 0) {
					$("body").append('<div id="spinner"></div>');
				}

				var $this = $(this);
				var results = new RegExp('[\\?&]w=([^&#]*)').exec(this.href);
				var width  = (results) ? results[1] : 800;
				results = new RegExp('[\\?&]h=([^&#]*)').exec(this.href);
				var height = (results) ? results[1] : 500;
				var container = window;

				var screen_width = window.innerWidth - 120;
				var screen_height = window.innerHeight - 200;
				width = (width > screen_width) ? screen_width : width;
				height = (height > screen_height) ? screen_height : height;

				if (window.parent) {
					container = window.parent;
				}

				$('#spinner').fadeIn()
					.position({my: "center", at: "center", of: container });

				// load the remote content
				var dialog = $('<div class="ngg-overlay-dialog"></div>').appendTo('body');
				dialog.load(
					this.href,
					{},
					function() {
						$('#spinner').hide();

						dialog.dialog({
							title: ($this.attr('title')) ? $this.attr('title') : '',
							position: { my: "center center-30", at: "center", of: window.parent },
							width: width,
							height: height,
							modal: true,
							resizable: false,
							close: function() {
								dialog.remove();
							}
						}).width(width - 30)
							.height(height - 30);

						$('.ui-dialog-titlebar-close').text('X')
					}
				);

				//prevent the browser to follow the link
				return false;
			});

			// If too many of these are generated the cookie becomes so large servers will reject HTTP requests
			// Wait some time for other listeners to catch this event and then purge it from the browser
			Frame_Event_Publisher.listen_for('attach_to_post:thumbnail_modified', function(data) {
				setTimeout(function() {
					Frame_Event_Publisher.delete_cookie("X-Frame-Events_" + data.id);
				}, 400);
			});

			window.showDialog = function(windowId, title) {
				var form = document.getElementById('updategallery');
				var elementlist = "";

				for (var i = 0, n = form.elements.length; i < n; i++) {
					if (form.elements[i].type === "checkbox") {
						if (form.elements[i].name === "doaction[]") {
							if (form.elements[i].checked === true) {
								if (elementlist === "") {
									elementlist = form.elements[i].value;
								} else {
									elementlist += "," + form.elements[i].value;
								}
							}
						}
					}
				}

				$("#" + windowId + "_bulkaction").val($("#bulkaction").val());
				$("#" + windowId + "_imagelist").val(elementlist);

				// now show the dialog
				$("#" + windowId).dialog({
					width: 640,
					resizable: false,
					modal: true,
					title: title,
					position: {
						my: 'center',
						at: 'center',
						of: window.parent
					}
				});

				$("#" + windowId + ' .dialog-cancel').on('click', function() {
					$("#" + windowId).dialog("close");
				});
			}

			window.setURLParam = function(param, paramVal) {
				var url        = window.location.href;
				var params     = "";
				var tmp        = "";
				var tmpArray   = url.split("?");
				var base       = tmpArray[0];
				var additional = tmpArray[1];

				if (additional) {
					tmpArray = additional.split("&");
					for (i = 0; i < tmpArray.length; i++) {
						if (tmpArray[i].split('=')[0] !== param) {
							params += tmp + tmpArray[i];
							tmp = "&";
						}
					}
				}

				return base + "?" + params + tmp + "" + param + "=" + paramVal;
			}

			window.checkAll = function(form) {
				for (var i = 0, n = form.elements.length; i < n; i++) {
					if (form.elements[i].type === "checkbox") {
						if (form.elements[i].name === "doaction[]") {
							if (form.elements[i].checked == true) {
								form.elements[i].checked = false;
							} else {
								form.elements[i].checked = true;
							}
						}
					}
				}
			}

			window.getNumChecked = function(form) {
				var num = 0;
				for (var i = 0, n = form.elements.length; i < n; i++) {
					if (form.elements[i].type === "checkbox") {
						if (form.elements[i].name === "doaction[]") {
							if (form.elements[i].checked === true) {
								num++;
							}
						}
					}
				}
				return num;
			}

			// this function check for a the number of selected images, sumbmit false when no one selected
			window.checkSelected = function() {

				var numchecked = getNumChecked(document.getElementById('updategallery'));

				if (typeof document.activeElement == "undefined" && document.addEventListener) {
					document.addEventListener("focus", function (e) {
						document.activeElement = e.target;
					}, true);
				}

				if (document.activeElement.name === 'post_paged')
					return true;

				if (numchecked < 1) {
					alert('<?php echo esc_js(__('No images selected', 'nggallery')); ?>');
					return false;
				}

				var actionId = document.getElementById('bulkaction').value;

				switch (actionId) {
					case "copy_to":
						showDialog('selectgallery', '<?php echo esc_js(__('Copy image to...','nggallery')); ?>');
						return false;
						break;
					case "move_to":
						showDialog('selectgallery', '<?php echo esc_js(__('Move image to...','nggallery')); ?>');
						return false;
						break;
					case "add_tags":
						showDialog('entertags', '<?php echo esc_js(__('Add new tags','nggallery')); ?>');
						return false;
						break;
					case "delete_tags":
						showDialog('entertags', '<?php echo esc_js(__('Delete tags','nggallery')); ?>');
						return false;
						break;
					case "overwrite_tags":
						showDialog('entertags', '<?php echo esc_js(__('Overwrite','nggallery')); ?>');
						return false;
						break;
					case "resize_images":
						showDialog('resize_images', '<?php echo esc_js(__('Resize images','nggallery')); ?>');
						return false;
						break;
					case "new_thumbnail":
						showDialog('new_thumbnail', '<?php echo esc_js(__('Create new thumbnails','nggallery')); ?>');
						return false;
						break;
				}

				return confirm('<?php echo sprintf(esc_js(__("You are about to start the bulk edit for %s images \n \n 'Cancel' to stop, 'OK' to proceed.",'nggallery')), "' + numchecked + '") ; ?>');
			}

			if ($(this).data('ready')) {
				return;
			}

			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

            // Some third party plugins alter when postboxes are enqueued, so wait for window.postboxes to exist
            const checkTimer = setInterval(() => {
                if (typeof window.postboxes !== 'undefined') {
                    clearInterval(checkTimer);
                    postboxes.add_postbox_toggles('ngg-manage-gallery');
                }
            }, 1000);

			$(this).data('ready', true);

			// Wait for WordPress common.js to create the window.columns object
            (async() => {
                while(!window.hasOwnProperty('columns')) { await new Promise(resolve => setTimeout(resolve, 25)); }
                columns.init('nggallery-manage-images');
            })();

			// Ensure that thumb preview images are always up-to-date
			$('#ngg-listimages img.thumb').each(function () {
				var $this = $(this);
				var src = $this.attr('src');
				var matchData = src.match(/\?i=(\d+)$/);
				if (matchData) {
					var i = parseInt(matchData[1]) + 1;
					src = src.replace(matchData[0], "?i=" + i.toString());
					$this.attr('src', src);
				}
			})
		})(jQuery);
    </script>
    <?php
}

/**
 * Constructor class to create the table layout
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 1.8.0
 * @access private
 */
class _NGG_Images_List_Table extends WP_List_Table
{
    public $_screen;
    public $_columns;

    function __construct($screen)
    {
        if (is_string($screen))
            $screen = convert_to_screen($screen);

        $this->_screen = $screen;
        $this->_columns = array() ;

        add_filter('manage_' . $screen->id . '_columns', array($this, 'get_columns'), 0);
    }

    function get_column_info()
    {
        $columns   = get_column_headers($this->_screen);
        $hidden    = get_hidden_columns($this->_screen);
        $_sortable = $this->get_sortable_columns();
        $sortable  = array();

        foreach ($_sortable as $id => $data) {
            if (empty($data))
                continue;

            $data = (array) $data;
            if (!isset($data[1]))
                $data[1] = false;

            $sortable[$id] = $data;
        }

        return array($columns, $hidden, $sortable);
    }

    // define the columns to display, the syntax is 'internal name' => 'display name'
    function get_columns()
    {
        $columns = array();

        $columns['cb']             = '<input name="checkall" type="checkbox" onclick="checkAll(document.getElementById(\'updategallery\'));"/>';
        $columns['id']             = __('ID');
        $columns['thumbnail']      = __('Thumbnail', 'nggallery');
        $columns['filename']       = __('Filename', 'nggallery');
        $columns['alt_title_desc'] = __('Alt &amp; Title Text', 'nggallery') . ' / ' . __('Description', 'nggallery');
        $columns['tags']           = __('Tags (comma separated list)', 'nggallery');

        $columns = apply_filters('ngg_manage_images_columns', $columns);

        return $columns;
    }

    function get_sortable_columns()
    {
        return array();
    }

    function the_list()
    {
    }

}