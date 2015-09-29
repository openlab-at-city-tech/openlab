<?php
/**
 * DWModule class
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	abstract class DWModule {
		protected static $classname;
		protected static $info = FALSE;
		protected static $name;
		public static $option;
		protected static $opt;
		protected static $overrule = FALSE;
		public static $plugin = FALSE;
		protected static $type = 'simple';
		protected static $wpml = FALSE;

		public function __construct() {
			self::$classname = get_class($this);
		}

		/**
		 * DWModule::admin() Basic admin init
		 *
		 */
		public static function admin() {
			$DW = &$GLOBALS['DW'];

			// $classname = self::getClassName();
			$vars = self::getVars(self::$classname);
			self::setName(self::$classname);

			// Would be so much easier if we could require PHP > 5.3: $name::
			self::checkOverrule();

			if ( $vars['plugin'] !== FALSE ) {
				self::registerPlugin($vars['plugin']);
			}

			if ( $vars['type'] == 'simple' ) {
				self::mkGUI($vars['type'], $vars['option'][self::$name], $vars['question'], $vars['info']);
			}
		}

		/**
		 * DWModule::checkOverrule() Registers an overrule module to $DW
		 *
		 */
		public static function checkOverrule($classname = NULL) {
			$DW = &$GLOBALS['DW'];

			if (! is_null($classname) ) {
				self::$classname = $classname;
			}
			// $classname = self::getClassName();

			$vars = self::getVars(self::$classname);
			self::setName(self::$classname);

			if ( isset($vars['overrule']) && $vars['overrule'] && ! in_array(self::$name, $DW->overrule_maintype) ) {
				$DW->overrule_maintype[ ] = self::$name;
			}
		}

		/**
		 * DWModule::getClassName() Gets the called class
		 *
		 * @return string
		 */
		protected static function getClassName() {
			// $classname = get_called_class();
			$classname = get_class();
			return $classname;
		}

		/**
		 * DWModule::getVars() Gets the properties from the class
		 *
		 * @param string $classname
		 * @return array
		 */
		protected static function getVars($classname) {
			$vars = get_class_vars($classname);
			return $vars;
		}

		/**
		 * DWModule::GUIComplex() GUI output of the complex list
		 *
		 * @param string $except Except string
		 * @param array $list List of options
		 * @param string $extra Extra option for the checkboxes
		 * @param string $name Name of the DWOpt type
		 */
		public static function GUIComplex($except, $list, $extra = NULL, $name = NULL) {
			if (! is_null($name) ) {
				self::$name = $name;
			}

			if ( count($list) > DW_LIST_LIMIT ) {
				$select_style = DW_LIST_STYLE;
			}

			if ( count($list) > 0 ) {
				echo '<br />' . "\n";
				_e($except, DW_L10N_DOMAIN);
				echo '<br />';
				echo '<div id="' . self::$name . '-select" class="condition-select" ' . ( (isset($select_style)) ? $select_style : '' ) . ' />';
				foreach ( $list as $key => $value ) {
					echo '<input type="checkbox" id="' . self::$name . '_act_' . $key . '" name="' . self::$name . '_act[]" value="' . $key . '" ' . ( (self::$opt->count > 0 && in_array($key, self::$opt->act)) ? 'checked="checked"' : '' ) . $extra  . ' /> <label for="' . self::$name . '_act_' . $key . '">' . $value . '</label><br />' . "\n";
				}
				echo '</div>' . "\n";
			}
		}

		/**
		 * DWModule::GUIFooter() GUI output of the footer module div
		 *
		 */
		public static function GUIFooter() {
			echo '</div><!-- end dynwid_conf -->' . "\n";
		}

		/**
		 * DWModule::GUIHeader() GUI output of the header module div
		 *
		 * @param string $title Title of the module
		 * @param string $question Main question
		 * @param string $info Extra info
		 * @param string $post_title Extra title info
		 * @param object $opt DWOpt object
		 */
		public static function GUIHeader($title, $question, $info, $post_title = NULL, $opt = NULL, $name = NULL) {
			$DW = &$GLOBALS['DW'];

			// $classname = self::getClassName();
			$vars = self::getVars(self::$classname);
			$wpml = FALSE;
			if ( $vars['wpml'] !== FALSE ) {
				$wpml = TRUE;
			}

			if (! is_null($post_title) ) {
				$title  = __($title, DW_L10N_DOMAIN);
				$title .= ' ' . $post_title;
			}

			if (! is_null($opt) ) {
				self::$opt = $opt;
			}

			if (! is_null($name) ) {
				self::$name = $name;
			}

			echo '<!-- ' . $title . ' //-->' . "\n";
			echo '<h3 id="' . self::$name . '" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b>' . __($title, DW_L10N_DOMAIN) . '</b>' . ( (self::$opt->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ' ' . ( ($DW->wpml && $wpml) ? DW_WPML::$icon : '' ) . '</h4>' . "\n";
			echo '<div id="' . self::$name . '_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">' . "\n";
			_e($question, DW_L10N_DOMAIN);

			if ( $info !== FALSE ) {
				echo ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" title="' . __('Click to toggle info', DW_L10N_DOMAIN) . '" onclick="divToggle(\'' . self::$name . '_info\')" /><br />' . "\n";
				echo '<div><div id="' . self::$name . '_info" class="infotext">' . "\n";
				_e($info, DW_L10N_DOMAIN);
				echo '</div></div>' . "\n";
			} else {
				echo '<br />' . "\n";
			}
		}

		/**
		 * DWModule::GUIOption() GUI output of the radio buttons
		 *
		 * @param string $name Name of the module
		 * @param object $opt DWOpt object
		 */
		public static function GUIOption($name = NULL, $opt = NULL) {
			$DW = &$GLOBALS['DW'];

			if (! is_null($name) ) {
				self::$name = $name;
			}

			if (! is_null($opt) ) {
				self::$opt = $opt;
			}

			$DW->dumpOpt(self::$opt);
			echo '<input type="radio" name="' . self::$name . '" value="yes" id="' . self::$name . '-yes" ' . ( (self::$opt->selectYes()) ? self::$opt->checked : '' ) . ' /> <label for="' . self::$name . '-yes">' . __('Yes') . '</label>' . "\n";
			echo '<input type="radio" name="' . self::$name . '" value="no" id="' . self::$name . '-no" ' . ( (self::$opt->selectNo()) ? self::$opt->checked : '' ) . ' /> <label for="' . self::$name . '-no">' . __('No') . '</label>' . "\n";
		}

		/**
		 * DWModule::mkGUI() Full GUI output
		 *
		 * @param string $type Type of output
		 * @param string $title Title of module
		 * @param string $question Main question
		 * @param string $info Extra info
		 * @param string $except Except string
		 * @param array $list List of options
		 * @param string $name Name of module
		 */
		public static function mkGUI($type, $title, $question, $info, $except = FALSE, $list = FALSE, $name = NULL) {
			/** @var $DW DynWid */
			global $DW;

			$widget_id = $GLOBALS['widget_id'];

			if (! is_null($name) ) {
				self::$name = $name;
			}

			self::$opt = $DW->getDWOpt($widget_id, self::$name);

			self::GUIHeader($title, $question, $info);
			self::GUIOption();
			if ( $type == 'complex' ) {
				self::GUIComplex($except, $list);
			}
			self::GUIFooter();
		}

		/**
		 * DWModule::registerOption() Register module to $DW
		 *
		 * @param array $dwoption Name and title of module
		 */
		public static function registerOption($dwoption) {
			/** @var $DW DynWid */
			global $DW;

			// For some reason when a widget is just added to the sidebar $dwoption is not an array
			if ( is_array($dwoption) ) {
				foreach ( $dwoption as $key => $value ) {
					$DW->dwoptions[$key] = __($value, DW_L10N_DOMAIN);
				}
			}
		}

		/**
		 * DWModule::registerPlugin() Regsiter plugin to $DW
		 *
		 * @param array $plugin Name and default value statuc of plugin
		 */
		public static function registerPlugin($plugin) {
			/** @var $DW DynWid */
			global $DW;

			foreach ( $plugin as $key => $value ) {
				if (! isset($DW->$key) ) {
					$DW->$key = $value;
				}
			}
		}

		/**
		 * DWModule::save() Basic save of Module options to the database via $DW
		 *
		 * @param string $name Name of module
		 * @param string $type Type of module
		 */
		public static function save($name, $type = 'simple') {
			/** @var $DW DynWid */
			global $DW;

			$widget_id = $GLOBALS['widget_id'];

			switch ( $type ) {
				case 'complex':
					$act = $name . '_act';

					if ( isset($_POST[$act]) && count($_POST[$act]) > 0 ) {
						$DW->addMultiOption($widget_id, $name, $_POST[$name], $_POST[$act]);
					} else if ( isset($_POST[$name]) && $_POST[$name] == 'no' ) {
						$DW->addSingleOption($widget_id, $name);
					}
					break;

					// simple
				default:
					if ( isset($_POST[$name]) && $_POST[$name] == 'no' ) {
						$DW->addSingleOption($widget_id, $name);
					}
				} // switch
		}

		public static function childSave($name) {
			/** @var $DW DynWid */
			global $DW;

			$widget_id = $GLOBALS['widget_id'];

			$act = $name . '_act';
			$child_act = $name . '_childs_act';
			$dwtype = $name . '-childs';

			// Workaround for lazy taxonomy tree
			if ( isset($_POST[$act]) && isset($_POST[$child_act]) ) {
				if (! is_array($_POST[$act]) ) {

					if ( substr($_POST[$act], 0, 1) == ',' ) {
						$_POST[$act] = substr($_POST[$act], 1);
					}
					$t = explode(',', $_POST[$act]);
					$t = array_unique($t);

					if ( substr($_POST[$child_act], 0, 1) == ',' ) {
						$_POST[$child_act] = substr($_POST[$child_act], 1);
					}
					$t_childs = explode(',', $_POST[$child_act]);
					$t_childs = array_unique($t_childs);

				} else {
					$t = $_POST[$act];
					$t_childs = $_POST[$child_act];
				}

				if ( count($t) > 0 && count($t_childs) > 0 ) {
					$DW->addChilds($widget_id, $dwtype, $_POST[$name], $t, $t_childs);
				}
			}

		}

		/**
		 * DWModule::setName() Auto registering name to DWModule class
		 *
		 * @param string $classname Full classname
		 */
		protected static function setName($classname) {
			self::$name = strtolower(substr($classname, 3));	// Chop off the "DW_"
			self::$name = str_replace('_', '-', self::$name);
		}
	}
?>