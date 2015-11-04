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
	'recaptcha_key_site'          => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'recaptcha_key_secret'        => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'recaptcha_enable_login'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'recaptcha_emergency'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'recaptcha_emergency_file'    => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'recaptcha_style_login'       => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'LIGHT'),
	'recaptcha_style_login_CSS'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'recaptcha_style_login_width' => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
);