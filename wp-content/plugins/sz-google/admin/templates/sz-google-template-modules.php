<?php

/**
 * file containing the HTML structure of the templates 
 * related to some sections of the admin panel
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die(); 

// Generate array with all the internet resources where is
// mentioned or reviewed the SZ-Google plugin for WordPress

$reviewsLINK =  array(
	array('language' => __('italian','sz-google'),'module'=>'google+'             ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-plus/'),
	array('language' => __('italian','sz-google'),'module'=>'google analytics'    ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-analytics/'),
	array('language' => __('italian','sz-google'),'module'=>'google authenticator','author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-authenticator/'),
	array('language' => __('italian','sz-google'),'module'=>'google calendar'     ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-calendar/'),
	array('language' => __('italian','sz-google'),'module'=>'google drive'        ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-drive/'),
	array('language' => __('italian','sz-google'),'module'=>'google maps'         ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-maps/'),
	array('language' => __('italian','sz-google'),'module'=>'google youtube'      ,'author'=>'Massimo Della Rovere','authorlink'=>'https://plus.google.com/+MassimoDellaRovere','url'=>'https://otherplus.com/tech/wordpress-google-youtube/'),
);

// Generate array with all the internet resources 
// which are contained in the array created earlier

echo '<div class="help">';
echo '<table>';

echo '<tr>';
echo '<th>'.ucfirst(__('language'   ,'sz-google')).'</th>';
echo '<th>'.ucfirst(__('module'     ,'sz-google')).'</th>';
echo '<th>'.ucfirst(__('author'     ,'sz-google')).'</th>';
echo '<th>'.ucfirst(__('URL address','sz-google')).'</th>';
echo '</tr>';

foreach ($reviewsLINK as $key => $value) 
{
	echo '<tr>';
	echo '<td>'.ucfirst($value['language']).'</td>';
	echo '<td>'.ucwords($value['module']).'</td>';
	echo '<td><a target="_blank" href="'.$value['authorlink'].'">'.$value['author'].'</a></td>';
	echo '<td><a target="_blank" href="'.$value['url'].'">'.$value['url'].'</a></td>';
	echo '</tr>';
}

echo '</table>';
echo '</div>';