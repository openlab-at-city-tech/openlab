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
	'fonts_tinyMCE_family' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'fonts_tinyMCE_size'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'fonts_family_L1_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_L2_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_L3_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_L4_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_L5_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_L6_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_B1_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_P1_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_B2_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H1_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H2_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H3_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H4_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H5_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'fonts_family_H6_name' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
);