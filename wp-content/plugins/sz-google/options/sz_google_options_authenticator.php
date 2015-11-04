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
	'authenticator_login_enable'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'authenticator_login_type'      => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '1'),
	'authenticator_discrepancy'     => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '1'),
	'authenticator_emergency_codes' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'authenticator_emergency'       => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'authenticator_emergency_file'  => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
);