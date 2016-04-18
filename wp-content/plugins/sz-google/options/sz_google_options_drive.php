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
	'drive_sitename'             => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'Website'),
	'drive_embed_shortcode'      => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'drive_embed_s_width'        => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_s_height'       => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_s_height_p'     => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_s_height_v'     => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_widget'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'drive_embed_w_width'        => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_w_height'       => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_w_height_p'     => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_embed_w_height_v'     => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_viewer_shortcode'     => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'drive_viewer_s_width'       => array('N'=>'0','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_viewer_s_height'      => array('N'=>'0','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_viewer_s_t_position'  => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'drive_viewer_s_t_align'     => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'drive_viewer_s_wrap_pre'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'drive_viewer_widget'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'drive_viewer_w_width'       => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_viewer_w_height'      => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'drive_viewer_w_t_position'  => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'drive_viewer_w_t_align'     => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'drive_viewer_w_wrap_pre'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'drive_savebutton_shortcode' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'drive_savebutton_widget'    => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
);