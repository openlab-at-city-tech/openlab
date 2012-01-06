<?php

class CACFeaturedContentHelper {
	
	/**
	 * Somewhat surprisingly, this function doesn't (or at least, this functionality) doesn't seem to exist in the core. 
	 * So here it is! A fast and efficient way to get data for a particular blog by providing the blog's domain. I imagine this
	 * would work in a set-up where the blog is at a subdirectory (yourwpmusite.com/oneofmanyblogs) but I'm not sure because 
	 * this hasn't been tested in that environment. If that's the case, this function should maybe be renamed, but until then...
	 * 
	 * @param string $domain The domain of the blog you're looking for info on, e.g. mygreatblog.wordpress.com. Don't include
	 * a trailing slash nor the leading protocol string (i.e. 'http://')
	 * @return object An object containing information about and data related to the blog. 
	 */
	function getBlogByDomain($domain) {
		global $wpdb;
		$row = $wpdb->get_results("SELECT blog_id FROM wp_blogs WHERE domain = '".mysql_real_escape_string($domain)."'");
		if($row[0]->blog_id) {
			$blog_id = $row[0]->blog_id;
		}
		$blog_data = get_blog_details($blog_id);
		return (object) $blog_data;
	}


	/**
	 * Given a post slug and a blog id, this function retrieves a post object. 
	 * @param string $slug The post url slug
	 * @param int $blog_id Id of the blog you the post is part of. 
	 * @return object 
	 */
	function getPostBySlug($slug, $blog_id) {
		switch_to_blog($blog_id);
	       
	       	$posts = new WP_Query( array( 'name' => $slug ) );
	       
	       	foreach($posts as $post) {
			if ( $post->post_name == $slug ) {
				$single_post = $post;
			}
		}

		restore_current_blog();
		return (object) $single_post;
	}

	/**
	 * Given an array of arrays, this function will return an html select list for WPMU blogs.
	 * @param string $arrays An array of arrays, like what is returned by the WP function get_blog_list()
	 * @param string $value The key for the value that you want be placed int he 'value' attribute of the option
	 * @param string $display The key for the value that is displayed to the user.
	 * @dropDown string $dropDown An HTML select element, populated with options. 
	 * @return string 
	 */
	function blogDropDown($arrays, $value, $display) {
		$options = array();
		foreach($arrays as $array) {
			$attrValue = $array[$value];
			$displayValue = get_blog_details($attrValue, true)->$display;

			$options[(string) $attrValue] = $displayValue;
		}	
		natcasesort($options);
		return $options;
	}
	
	/**
	* This method comes from typo3 v4.4.2
	* Original source file: typo3_src-4.4.2/typo3/sysext/cms/tslib/class.tslib_content.php
	* 
	*
	*  Copyright notice
	*
	*  (c) 1999-2010 Kasper Skaarhoj (kasperYYYY@typo3.com)
	*  All rights reserved
	*
	*  This script is part of the TYPO3 project. The TYPO3 project is
	*  free software; you can redistribute it and/or modify
	*  it under the terms of the GNU General Public License as published by
	*  the Free Software Foundation; either version 2 of the License, or
	*  (at your option) any later version.
	*
	*  The GNU General Public License can be found at
	*  http://www.gnu.org/copyleft/gpl.html.
	*  A copy is found in the textfile GPL.txt and important notices to the license
	*  from the author is found in LICENSE.txt distributed with these scripts.
	*
	*
	*  This script is distributed in the hope that it will be useful,
	*  but WITHOUT ANY WARRANTY; without even the implied warranty of
	*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*  GNU General Public License for more details.
	*
	*  This copyright notice MUST APPEAR in all copies of the script!
	*
	* Implements the stdWrap property "cropHTML" which is a modified "substr" function allowing to limit a string length
	* to a certain number of chars (from either start or end of string) and having a pre/postfix applied if the string
	* really was cropped.
	*
	* Compared to stdWrap.crop it respects HTML tags and entities.
 	*
	* @param	string		The string to perform the operation on
	* @param	string		The parameters splitted by "|": First parameter is the max number of chars of the string. Negative value means cropping from end of string. Second parameter is the pre/postfix string to apply if cropping occurs. Third parameter is a boolean value. If set then crop will be applied at nearest space.
	* @return	string		The processed input value.
	* @access private
	* @see stdWrap()
	*/
	function cropHTML($content, $options) {
		$options = explode('|', $options);
		$chars = intval($options[0]);
		$absChars = abs($chars);
		$replacementForEllipsis = trim($options[1]);
		$crop2space = $options[2] === '1' ? TRUE : FALSE;

		// Split $content into an array (even items in the array are outside the tags, odd numbers are tag-blocks).
		$tags= 'a|b|blockquote|body|div|em|font|form|h1|h2|h3|h4|h5|h6|i|li|map|ol|option|p|pre|sub|sup|select|span|strong|table|thead|tbody|tfoot|td|textarea|tr|u|ul|br|hr|img|input|area|link';
		// TODO We should not crop inside <script> tags.
		$tagsRegEx = "
			(
				(?:
					<!--.*?-->					# a comment
				)
				|
				</?(?:". $tags . ")+			# opening tag ('<tag') or closing tag ('</tag')
				(?:
					(?:
						\s+\w+					# EITHER spaces, followed by word characters (attribute names)
						(?:
							\s*=?\s*			# equals
							(?>
								\".*?\"			# attribute values in double-quotes
								|
								'.*?'			# attribute values in single-quotes
								|
								[^'\">\s]+		# plain attribute values
							)
						)?
					)+\s*
					|							# OR only spaces
					\s*
				)
				/?>								# closing the tag with '>' or '/>'
			)";
		$splittedContent = preg_split('%' . $tagsRegEx . '%xs', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

		// Reverse array if we are cropping from right.
		if ($chars < 0) {
			$splittedContent = array_reverse($splittedContent);
		}

		// Crop the text (chars of tag-blocks are not counted).
		$strLen = 0;
		$croppedOffset = NULL; // This is the offset of the content item which was cropped.
		$countSplittedContent = count($splittedContent);
		for ($offset = 0; $offset < $countSplittedContent; $offset++) {
			if ($offset%2 === 0) {
				$tempContent = utf8_encode($splittedContent[$offset]);
				$thisStrLen = strlen(html_entity_decode($tempContent));
				// We're in WordPress!!! $GLOBALS['TSFE'] is totally not avaialable here! -MOM
				// $tempContent = $GLOBALS['TSFE']->csConvObj->utf8_encode($splittedContent[$offset], $GLOBALS['TSFE']->renderCharset);
				// $thisStrLen = $GLOBALS['TSFE']->csConvObj->strlen('utf-8', html_entity_decode($tempContent, ENT_COMPAT, 'UTF-8'));
				if (($strLen + $thisStrLen > $absChars)) {
					$croppedOffset = $offset;
					$cropPosition = $absChars - $strLen;
					if ($crop2space) {
						$cropRegEx = $chars < 0 ? '#(?<=\s)(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}$#ui' : '#^(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}(?=\s)#ui';
					} else {
						// The snippets "&[^&\s;]{2,7};" in the RegEx below represents entities.
						$cropRegEx = $chars < 0 ? '#(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}$#ui' : '#^(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}#ui';
					}
					if (preg_match($cropRegEx, $tempContent, $croppedMatch)) {
						$tempContent = $croppedMatch[0];
					}
					// $splittedContent[$offset] = $GLOBALS['TSFE']->csConvObj->utf8_decode($tempContent, $GLOBALS['TSFE']->renderCharset);
					$splittedContent[$offset] = utf8_decode($tempContent);
					break;
				} else {
					$strLen += $thisStrLen;
				}
			}
		}

		// Close cropped tags.
		$closingTags = array();
		if($croppedOffset !== NULL) {
			$tagName = '';
			$openingTagRegEx = '#^<(\w+)(?:\s|>)#';
			$closingTagRegEx = '#^</(\w+)(?:\s|>)#';
			for ($offset = $croppedOffset - 1; $offset >= 0; $offset = $offset - 2) {
				if (substr($splittedContent[$offset], -2) === '/>') {
					// Ignore empty element tags (e.g. <br />).
					continue;
				}
				preg_match($chars < 0 ? $closingTagRegEx : $openingTagRegEx, $splittedContent[$offset], $matches);
				$tagName = isset($matches[1]) ? $matches[1] : NULL;
				if ($tagName !== NULL) {
					// Seek for the closing (or opening) tag.
					$seekingTagName = '';
					$countSplittedContent = count($splittedContent);
					for ($seekingOffset = $offset + 2; $seekingOffset < $countSplittedContent; $seekingOffset = $seekingOffset + 2) {
						preg_match($chars < 0 ? $openingTagRegEx : $closingTagRegEx, $splittedContent[$seekingOffset], $matches);
						$seekingTagName = isset($matches[1]) ? $matches[1] : NULL;
						if ($tagName === $seekingTagName) { // We found a matching tag.
							// Add closing tag only if it occurs after the cropped content item.
							if ($seekingOffset > $croppedOffset) {
								$closingTags[] = $splittedContent[$seekingOffset];
							}
							break;
						}
					}
				}
			}
			// Drop the cropped items of the content array. The $closingTags will be added later on again.
			array_splice($splittedContent, $croppedOffset + 1);
		}

		$splittedContent = array_merge($splittedContent, array($croppedOffset !== NULL ? $replacementForEllipsis : ''), $closingTags);

		// Reverse array once again if we are cropping from the end.
		if ($chars < 0) {
			$splittedContent = array_reverse($splittedContent);
		}

		return implode('', $splittedContent);
	}
}

?>