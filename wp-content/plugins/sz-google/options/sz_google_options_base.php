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
	'plus'              => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'analytics'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'authenticator'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'drive'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'fonts'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'groups'            => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'hangouts'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'maps'              => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'panoramio'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'recaptcha'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'translate'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'youtube'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'documentation'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'tinymce'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'API_enable'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'API_client_ID'     => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'API_client_secret' => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
);