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
	'ga_type'                 => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'classic'),
	'ga_uacode'               => array('N'=>'1','Y'=>'0','Z'=>'0','value' => ''),
	'ga_position'             => array('N'=>'0','Y'=>'0','Z'=>'0','value' => 'H'),
	'ga_compression'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_front'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'ga_enable_admin'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_administrator' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_logged'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_subdomain'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_multiple'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_advertiser'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_features'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_ip_none_cl'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_ip_none_ad'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_cl_proxy'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_cl_proxy_url'  => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '' ),
	'ga_enable_cl_proxy_adv'  => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '' ),
	'ga_enable_un_proxy'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'ga_enable_un_proxy_url'  => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '' ),
);