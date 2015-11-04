<?php

/**
 * Array definition for plugin options linked to the corresponding
 * module with which will develop the function getOptions()
 *
 * @package SZGoogle
 * @subpackage Options
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Definition array() with all the options connected to the
// module which must be called by an include (setoptions)

return array(
	'groups_widget'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'groups_shortcode'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'groups_language'    => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '99'),
	'groups_name'        => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'adsense-api'),
	'groups_showsearch'  => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'groups_showtabs'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'groups_hidetitle'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'groups_hidesubject' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'groups_width'       => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'groups_height'      => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
);