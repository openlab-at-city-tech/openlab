<?php
/**
 * Custom Post Type Module
 *
 * @version $Id: custompost_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_CustomPost extends DWModule {
		public static $plugin = array( 'custom_post_type' => FALSE, 'custom_taxonomy' => FALSE );
		protected static $post_types;
		protected static $type = 'custom';

		public static function admin() {
			parent::admin();
			self::customPosts();
			self::customTax();

			if ( array_key_exists('cp_archive', $GLOBALS['DW']->dwoptions) ) {
				self::customArchive();
			}

		}

		public static function customArchive() {
			$DW = $GLOBALS['DW'];

			if ( function_exists('is_post_type_archive') && count(self::$post_types) > 0 ) {
				self::$type = 'complex';
				$new_name = 'cp_archive';
				$title = 'Custom Post Type Archives';
				$question = 'Show widget on Custom Post Type Archives';
				$except = 'Except for';

				$list = array();
				foreach ( self::$post_types as $key => $value ) {
					$list[$key] = $value->label;
				}

				self::mkGUI(self::$type, $title, $question, FALSE, $except, $list, $new_name);
			}
		}

		public static function customPosts() {
			$DW = $GLOBALS['DW'];
			$widget_id = $GLOBALS['widget_id'];

			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);

			// Custom Post Type
			self::$post_types = get_post_types($args, 'objects', 'and');

			foreach ( self::$post_types as $type => $ctid ) {
				if (! array_key_exists($type, $GLOBALS['DW']->dwoptions) ) {
					continue;
				}

				// Prepare
				self::$opt = $DW->getDWOpt($widget_id, $type);
				$tax_list = get_object_taxonomies($type, 'objects');
				$tax_list = apply_filters('dynwid_taxonomies', $tax_list);

				// Output
				echo '<input type="hidden" name="post_types[]" value="' . $type . '" />';
				echo '<h4 id="cpt_' . $type . '" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b>' . $ctid->label . '</b> ' . ( self::$opt->count > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( $DW->wpml ? DW_WPML::$icon : '' ) . '</h4>';
				echo '<div id="cpt_' . $type . '_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">';
				echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '? ' . ( ($ctid->hierarchical || count($tax_list) > 0) ? '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $type . '\');" />' : '' ) . '<br />';
				echo '<div>';
				echo '<div id="custom_' . $type . '" class="infotext">';
				echo ( $ctid->hierarchical ) ? '<p>' . DW_Page::infoText() . '</p>' : '';
				echo ( (count($tax_list) > 0) ? '<p>' . __('All exceptions (Titles and Taxonomies) work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
				echo '</div>';
				echo '</div>';

				self::GUIOption($type);
				echo '<br />';

				$opt_single = $DW->getDWOpt($widget_id, $type);

				// Taxonomy in Custom Post Type
				foreach ( $tax_list as $tax_type ) {
					// Prepare
					$opt_tax = $DW->getDWOpt($widget_id, $type . '-tax_' . $tax_type->name);
					if ( $tax_type->hierarchical ) {
						$opt_tax_childs = $DW->getDWOpt($widget_id, $type . '-tax_' . $tax_type->name . '-childs');
					} else {
						unset($opt_tax_childs);
					}

					$tax = get_terms($tax_type->name, array('get' => 'all'));
					if ( count($tax) > 0 ) {
						if ( count($tax) > DW_LIST_LIMIT ) {
							$tax_condition_select_style = DW_LIST_STYLE;
						}

						$tree = self::getTaxChilds($tax_type->name, array(), 0, array());

						echo '<br />';
						$DW->dumpOpt($opt_tax);
						if ( isset($opt_tax_childs) ) {
							$DW->dumpOpt($opt_tax_childs);
						}

						echo '<input type="hidden" name="tax_list[]" value="' . $type . '-tax_' . $tax_type->name . '" />';
						echo __('Except for', DW_L10N_DOMAIN) . ' ' . $tax_type->label . ':<br />';
						echo '<div id="' . $type . '-tax_' . $tax_type->name . '-select" class="condition-select" ' . ( (isset($tax_condition_select_style)) ? $tax_condition_select_style : '' ) . '>';
						echo '<div style="position:relative;left:-15px">';
						if (! isset($opt_tax_childs) ) {
							$childs = FALSE;
						} else {
							$childs = $opt_tax_childs->act;
						}

						echo '<input type="hidden" id="' . $type . '-tax_' . $tax_type->name . '_act" name="' . $type . '-tax_' . $tax_type->name . '_act" value="' . implode(',', $opt_tax->act) . '" />';
						if ( isset($opt_tax_childs) ) {
							echo '<input type="hidden" id="' . $type . '-tax_' . $tax_type->name . '_childs_act" name="' . $type . '-tax_' . $tax_type->name . '_childs_act" value="' . implode(',', $opt_tax_childs->act) . '" />';
						}

						// self::prtTax($tax_type->name, $tree, $opt_tax->act, $childs, $type . '-tax_' . $tax_type->name);
						self::prtTax($widget_id, $tax_type->name, $tree, $opt_tax->act, $childs, $type . '-tax_' . $tax_type->name);
						echo '</div>';
						echo '</div>';
					}
				}

				self::GUIFooter();
			}
		}

		public static function customTax() {
			$DW = $GLOBALS['DW'];
			$widget_id = $GLOBALS['widget_id'];

			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);

			if ( function_exists('is_tax') ) {
				$taxlist = get_taxonomies($args, 'objects', 'and');
				$taxlist = apply_filters('dynwid_taxonomies', $taxlist);

				if ( count($taxlist) > 0 ) {
					foreach ( $taxlist as $tax_id => $tax ) {
						if (! array_key_exists('tax_' . $tax_id, $GLOBALS['DW']->dwoptions) ) {
							continue;
						}

						// Getting the linked post type : Only Pages and CPT supported
						$cpt_label = array();
						foreach ( $tax->object_type as $obj ) {
							switch ( $obj ) {
								case 'page':
									$cpt_label[ ] = _('Pages');
									break;

								case 'post':
									$cpt_label[ ] = _('Posts');
									break;

								default:
									$cpt_label[ ] = self::$post_types[$obj]->label;
							}

						}

						if ( count($cpt_label) > 0 ) {
							$ct = 'tax_' . $tax_id;
							$ct_archive_yes_selected = 'checked="checked"';
							$opt_ct_archive = $DW->getDWOpt($widget_id, $ct);
							if ( $tax->hierarchical ) {
								$opt_ct_archive_childs = $DW->getDWOpt($widget_id, $ct . '-childs');
							}

							$t = get_terms($tax->name, array('get' => 'all'));
							if ( count($t) > DW_LIST_LIMIT ) {
								$ct_archive_condition_select_style = DW_LIST_STYLE;
							}

							$tree = self::getTaxChilds($tax->name, array(), 0, array());

							echo '<h4 id="' . $ct . '" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b>' . $tax->label . ' ' . _('archive') . '</b> (<em>' . implode(', ', $cpt_label) . '</em>)' . ( ($opt_ct_archive->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . '</h4>';
							echo '<div id="' . $ct . '_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">';
							echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $tax->label . ' ' . _('archive') . '?' . ( ($tax->hierarchical || count($t) > 0) ? ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $ct . '\');" />' : '' ) . '<br />';
							echo '<input type="hidden" name="dw_taxonomy[]" value="' . $tax_id . '" />';
							$DW->dumpOpt($opt_ct_archive);
							if ( isset($opt_ct_archive_childs) ) {
								$DW->dumpOpt($opt_ct_archive_childs);
							}

							echo '<div>';
							echo '<div id="custom_' . $ct . '" class="infotext">';
							echo ( $tax->hierarchical ) ? '<p>' . DW_Page::infoText() . '</p>' : '';
							echo ( (count($t) > 0) ? '<p>' . __('All exceptions work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
							echo '</div>';
							echo '</div>';

							echo '<input type="radio" name="' . $ct . '" value="yes" id="' . $ct . '-yes" ' . ( ($opt_ct_archive->selectYes()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-yes">' . __('Yes') . '</label> ';
							echo '<input type="radio" name="' . $ct . '" value="no" id="' . $ct . '-no" ' . ( ($opt_ct_archive->selectNo()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-no">' . __('No') . '</label><br />';

							if ( count($t) > 0 ) {
								echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
								echo '<div id="' . $ct . '-select" class="condition-select" ' . ( (isset($ct_archive_condition_select_style)) ? $ct_archive_condition_select_style : '' ) . '>';
								echo '<div style="position:relative;left:-15px">';
								if (! isset($opt_ct_archive_childs) ) {
									$childs = FALSE;
								} else {
									$childs = $opt_ct_archive_childs->act;
								}

								echo '<input type="hidden" id="' . $ct . '_act" name="' . $ct . '_act" value="' . ( (is_array($opt_ct_archive->act)) ? implode(',', $opt_ct_archive->act) : '' ) . '" />';
								if ( isset($opt_ct_archive_childs) ) {
									echo '<input type="hidden" id="' . $ct . '_childs_act" name="' . $ct . '_childs_act" value="' . ( (is_array($opt_tax_childs->act)) ? implode(',', $opt_tax_childs->act) : '' ) . '" />';
								}

								self::prtTax($widget_id, $tax->name, $tree, $opt_ct_archive->act, $childs, $ct);

								echo '</div>';
								echo '</div>';
							}
							// echo '</div><!-- end dynwid_conf -->';
							self::GUIFooter();
						}
					}
				}
			}
		}

		public static function getTaxChilds($term, $arr, $id, $i) {
			$tax = get_terms($term, array('hide_empty' => FALSE, 'parent' => $id));
			return $tax;

/*			foreach ($tax as $t ) {
				if (! in_array($t->term_id, $i) && $t->parent == $id ) {
					$i[ ] = $t->term_id;
					$arr[$t->term_id] = array();
					$a = &$arr[$t->term_id];
					$a = self::getTaxChilds($term, $a, $t->term_id, $i);
				}
			}

			return $arr; */
		}

		public static function prtTax($widget_id, $tax, $terms, $terms_act, $terms_childs_act, $prefix) {
			$DW = &$GLOBALS['DW'];

			// foreach ( $terms as $pid => $childs ) {
			foreach ( $terms as $term ) {
				$run = TRUE;

/*				if ( $DW->wpml ) {
					include_once(DW_MODULES . 'wpml_module.php');
					$wpml_id = DW_WPML::getID($pid, 'tax_' . $tax);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				} */

				if ( $DW->wpml ) {
					include_once(DW_MODULES . 'wpml_module.php');

					$wpml_id = DW_WPML::getID($term->term_id, 'tax_' . $tax);
					if ( $wpml_id > 0 && $wpml_id <> $term->term_id ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					// $term = get_term_by('id', $pid, $tax);

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $prefix . '_act_' . $term->term_id . '" name="' . $prefix . '_chkbx[]" value="' . $term->term_id . '" ' . ( isset($terms_act) && count($terms_act) > 0 && in_array($term->term_id, $terms_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'' . $prefix . '\', ' . $term->term_id . ')" /> <label for="' . $prefix . '_act_' . $term->term_id . '">' . $term->name . '</label>';
					echo ( $terms_childs_act !== FALSE ) ? ' <span title=" Click to expand " onclick="term_tree(\'' . $widget_id . '\', \'' . $tax . '\', ' . $term->term_id . ', \'' . $prefix . '\');return false;"><img src="' . $DW->plugin_url .'/img/arrow-down.png" /></span>' : '';
					echo '<br />';

					if ( $terms_childs_act !== FALSE ) {
						echo '<div id="child_' . $prefix . $term->term_id  . '" style="position:relative;left:15px;display:none;">';
						echo '<input type="checkbox" id="' . $prefix . '_childs_act_' . $term->term_id . '" name="' . $prefix . '_childs_chkbx[]" value="' . $term->term_id . '" ' . ( isset($terms_childs_act) && count($terms_childs_act) > 0 && in_array($term->term_id, $terms_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'' . $prefix . '\', ' . $term->term_id . ')" /> <label for="' . $prefix . '_childs_act_' . $term->term_id . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
						echo '<div id="tree_' . $prefix . $term->term_id . '"></div>';
						echo '</div>';

						/* if ( count($childs) > 0 ) {
							self::prtTax($tax, $childs, $terms_act, $terms_childs_act, $prefix);
						} */
					}
					echo '</div>';
				}
			}
		}

		public static function registerOption($dwoption) {
			// $dwoption not used, but needs to be in the argument list for strict PHP reasons (see Mantis #174).
			$option = array( 'cp_archive'	=> 'Custom Post Type Archives' );

			// Adding Custom Post Types to $DW->dwoptions
			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);
			$post_types = get_post_types($args, 'objects', 'and');
			foreach ( $post_types as $type => $ctid ) {
				$option[$type] = $ctid->label;
			}

			// Adding Custom Taxonomies to $DW->dwoptions
			$taxonomy = get_taxonomies($args, 'objects', 'and');
			$taxonomy = apply_filters('dynwid_taxonomies', $taxonomy);
			foreach ( $taxonomy as $tax_id => $tax ) {
				$option['tax_' . $tax_id] = $tax->label;
			}
			parent::registerOption($option);
		}
	}
?>