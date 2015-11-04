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
	'translate_meta'         => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'translate_mode'         => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'I1'),
	'translate_language'     => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '99'),
	'translate_to'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'translate_widget'       => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'translate_shortcode'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'translate_automatic'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'translate_multiple'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'translate_analytics'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'translate_analytics_ua' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
);