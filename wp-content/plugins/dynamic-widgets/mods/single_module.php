<?php
/**
 * Single Post Module
 *
 * @version $Id: single_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Single extends DWModule {
		protected static $info = 'When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won\'t be applied. If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.';
		public static $option = array( 'single' => 'Single Posts' );
		protected static $question = 'Show widget default on single posts?';
		protected static $type = 'custom';
		protected static $wpml = TRUE;

		public static function admin() {
			$DW = &$GLOBALS['DW'];
			$widget_id = $GLOBALS['widget_id'];

			parent::admin();

			self::$opt = $DW->getDWOpt($widget_id, 'single');
			$authors = DW_Author::getAuthors();

			if ( count($authors) > DW_LIST_LIMIT ) {
				$author_condition_select_style = DW_LIST_STYLE;
			}

			$js_count = 0;
			$opt_single_author = $DW->getDWOpt($widget_id, 'single-author');
			$js_author_array = array();
			if ( $opt_single_author->count > 0 ) {
				$js_count = $js_count + $opt_single_author->count - 1;
			}

			// -- Category
			$category = get_categories( array('hide_empty' => FALSE) );
			if ( count($category) > DW_LIST_LIMIT ) {
				$category_condition_select_style = DW_LIST_STYLE;
			}

			// For JS
			$js_category_array = array();
			foreach ( $category as $cat ) {
				$js_category_array[ ] = '\'single_category_act_' . $cat->cat_ID . '\'';
				$js_category_array[ ] = '\'single_category_childs_act_' . $cat->cat_ID . '\'';
			}

			$catmap = DW_Category::getCatChilds(array(), 0, array());

			$opt_single_category = $DW->getDWOpt($widget_id, 'single-category');
			if ( $opt_single_category->count > 0 ) {
				$js_count = $js_count + $opt_single_category->count - 1;
			}

			$opt_single_post = $DW->getDWOpt($widget_id, 'single-post');
			$opt_single_tag = $DW->getDWOpt($widget_id, 'single-tag');

			self::GUIHeader(self::$option[self::$name], self::$question, self::$info);
			self::GUIOption();

			// Individual posts and tags
			foreach ( $opt_single_post->act as $singlepost ) {
				echo '<input type="hidden" name="single_post_act[]" value="' . $singlepost . '" />';
			}

			foreach ( $opt_single_tag->act as $tag ) {
				echo '<input type="hidden" name="single_tag_act[]" value="'. $tag . '" />';
			}

			// JS array authors
			foreach ( array_keys($authors) as $id ) {
				$js_author_array[ ] = '\'single_author_act_' . $id . '\'';
			}
?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
  	<?php  DW_Author::mkGUI(self::$type, self::$option[self::$name], self::$question, TRUE); ?>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
  	<?php $opt = $DW->getDWOpt($widget_id, 'single-category'); ?>
  	<?php $DW->dumpOpt($opt); ?>
		<?php DW_Category::GUIComplex(NULL, NULL, TRUE, $opt); ?>
    </div>
  </td>
</tr>
</table>
<?php
	$type = 'post';
	$tax_list = get_object_taxonomies($type, 'objects');
	$tax_list = apply_filters('dynwid_taxonomies', $tax_list);

	foreach ( $tax_list as $tax_type ) {
		if ( $tax_type->name != 'post_tag' && $tax_type->name != 'category' ) {
			// Prepare
			$opt_tax = $DW->getDWOpt($widget_id, 'single-tax_' . $tax_type->name);
			if ( $tax_type->hierarchical ) {
				$opt_tax_childs = $DW->getDWOpt($widget_id, 'single-tax_' . $tax_type->name . '-childs');
			} else {
				unset($opt_tax_childs);
			}

			$tax = get_terms($tax_type->name, array('get' => 'all'));
			if ( count($tax) > 0 ) {
				if ( count($tax) > DW_LIST_LIMIT ) {
					$tax_condition_select_style = DW_LIST_STYLE;
				}

				$tree = DW_CustomPost::getTaxChilds($tax_type->name, array(), 0, array());

				echo '<br />';
				$DW->dumpOpt($opt_tax);
				if ( isset($opt_tax_childs) ) {
					$DW->dumpOpt($opt_tax_childs);
				}

				echo '<input type="hidden" name="single_tax_list[]" value="single-tax_' . $tax_type->name . '" />';
				echo __('Except for', DW_L10N_DOMAIN) . ' ' . $tax_type->label . ':<br />';
				echo '<div id="single-tax_' . $tax_type->name . '-select" class="condition-select" ' . ( (isset($tax_condition_select_style)) ? $tax_condition_select_style : '' ) . '>';
				echo '<div style="position:relative;left:-15px">';

				if (! isset($opt_tax_childs) ) {
					$childs = FALSE;
				} else {
					$childs = $opt_tax_childs->act;
				}

				echo '<input type="hidden" id="single-tax_' . $tax_type->name . '_act" name="single-tax_' . $tax_type->name . '_act" value="' . implode(',', $opt_tax->act) . '" />';
				if ( isset($opt_tax_childs) ) {
					echo '<input type="hidden" id="single-tax_' . $tax_type->name . '_childs_act" name="single-tax_' . $tax_type->name . '_childs_act" value="' . implode(',', $opt_tax_childs->act) . '" />';
				}

				DW_CustomPost::prtTax($widget_id, $tax_type->name, $tree, $opt_tax->act, $childs, 'single-tax_' . $tax_type->name);

				echo '</div>';
				echo '</div>';
			}
		}
	} // foreach

	self::GUIFooter();
?>
<script type="text/javascript">
/* <![CDATA[ */
  function chkInPosts() {
    var posts = <?php echo $opt_single_post->count; ?>;
    var tags = <?php echo $opt_single_tag->count; ?>;

    if ( (posts > 0 || tags > 0) && jQuery('#individual').is(':checked') == false ) {
      if ( confirm('Are you sure you want to disable the exception rule for individual posts and tags?\nThis will remove the options set to individual posts and/or tags for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, false);
        swChb(cCat, false);
      } else {
        jQuery('#individual').attr('checked', true);
      }
    } else if ( icount > 0 && jQuery('#individual').is(':checked') ) {
      if ( confirm('Are you sure you want to enable the exception rule for individual posts and tags?\nThis will remove the exceptions set for Author and/or Category on single posts for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
        icount = 0;
      } else {
        jQuery('#individual').attr('checked', false);
      }
    } else if ( jQuery('#individual').is(':checked') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
    } else {
        swChb(cAuthors, false);
        swChb(cCat, false);
    }
  }

 	function ci(id) {
    if ( jQuery('#'+id).is(':checked') ) {
      icount++;
    } else {
      icount--;
    }
  }

  var icount = <?php echo $js_count; ?>;
  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);

  if ( jQuery('#individual').is(':checked') ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }

  if ( jQuery('#single-yes').is(':checked') && jQuery('#single_conf :checkbox').is(':checked')  ) {
  	jQuery('#single').append( ' <img src="<?php echo $DW->plugin_url; ?>img/checkmark.gif" alt="Checkmark" />' );
  }
/* ]]> */
</script>
<?php
		}
	}
?>