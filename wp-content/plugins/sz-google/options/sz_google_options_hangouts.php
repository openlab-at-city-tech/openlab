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
	'hangouts_start_widget'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'hangouts_start_shortcode' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'hangouts_start_logged'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'hangouts_start_guest'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
);