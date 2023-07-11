<?php
if (!function_exists('cminds_parse_php_info')) {

	function cminds_parse_php_info() {
		$obstartresult = ob_start();
		if ($obstartresult) {
			$phpinforesult = phpinfo(INFO_MODULES);
			if ($phpinforesult == false) {
				return array();
			}
			$s = ob_get_clean();
		} else {
			return array();
		}

		$s = strip_tags($s, '<h2><th><td>');
		$s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', "<info>\\1</info>", $s);
		$s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', "<info>\\1</info>", $s);
		$vTmp = preg_split('/(<h2>[^<]+<\/h2>)/', $s, - 1, PREG_SPLIT_DELIM_CAPTURE);
		$vModules = array();
		for ($i = 1; $i < count($vTmp); $i++) {
			if (preg_match('/<h2>([^<]+)<\/h2>/', $vTmp[$i], $vMat)) {
				$vName = trim($vMat[1]);
				$vTmp2 = explode("\n", $vTmp[$i + 1]);
				foreach ($vTmp2 AS $vOne) {
					$vPat = '<info>([^<]+)<\/info>';
					$vPat3 = "/$vPat\s*$vPat\s*$vPat/";
					$vPat2 = "/$vPat\s*$vPat/";
					if (preg_match($vPat3, $vOne, $vMat)) { // 3cols
						$vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]), trim($vMat[3]));
					} elseif (preg_match($vPat2, $vOne, $vMat)) { // 2cols
						$vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
					}
				}
			}
		}
		return $vModules;
	}

}

if (!function_exists('cminds_file_exists_remote')) {

	/**
	 * Checks whether remote file exists
	 * @param type $url
	 * @return boolean
	 */
	function cminds_file_exists_remote($url) {
		if (!function_exists('curl_version')) {
			return false;
		}

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		/*
		 * Don't wait more than 5s for a file
		 */
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		//Check connection only
		$result = curl_exec($curl);
		//Actual request
		$ret = false;
		if ($result !== false) {
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			//Check HTTP status code
			if ($statusCode == 200) {
				$ret = true;
			}
		}
		curl_close($curl);
		return $ret;
	}

}

if (!function_exists('cminds_sort_WP_posts_by_title_length')) {

	function cminds_sort_WP_posts_by_title_length($a, $b) {
		$sortVal = 0;
		if (property_exists($a, 'post_title') && property_exists($b, 'post_title')) {
			$sortVal = strlen($b->post_title) - strlen($a->post_title);
		}
		return $sortVal;
	}

}

if (!function_exists('cminds_strip_only')) {

	/**
	 * Strips just one tag
	 * @param type $str
	 * @param type $tags
	 * @param type $stripContent
	 * @return type
	 */
	function cminds_strip_only($str, $tags, $stripContent = false) {
		$content = '';
		if (!is_array($tags)) {
			$tags = ( strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags) );
			if (end($tags) == '') {
				array_pop($tags);
			}
		}
		foreach ($tags as $tag) {
			if ($stripContent) {
				$content = '(.+</' . $tag . '[^>]*>|)';
			}
			$str = preg_replace('#</?' . $tag . '[^>]*>' . $content . '#is', '', $str);
		}
		return $str;
	}

}

if (!function_exists('cminds_truncate')) {

	/**
	 * From: http://stackoverflow.com/a/2398759/2107024
	 * @param type $text
	 * @param type $length
	 * @param type $ending
	 * @param type $exact
	 * @param type $considerHtml
	 * @return string
	 */
	function cminds_truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
		if (is_array($ending)) {
			extract($ending);
		}
		if ($considerHtml) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen($ending);
			$openTags = array();
			$truncate = '';
			$tags = array(); //inistialize empty array
			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					$closeTag = array();

					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					$entities = array();
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length - strlen($ending));
			}
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if (isset($spacepos)) {
				if ($considerHtml) {
					$bits = mb_substr($truncate, $spacepos);
					$droppedTags = array();
					preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
					if (!empty($droppedTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					}
				}
				$truncate = mb_substr($truncate, 0, $spacepos);
			}
		}

		$truncate .= $ending;

		if ($considerHtml) {
			foreach ($openTags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}

}

if (!function_exists('cminds_show_message')) {

	/**
	 * Generic function to show a message to the user using WP's
	 * standard CSS classes to make use of the already-defined
	 * message colour scheme.
	 *
	 * @param $message The message you want to tell the user.
	 * @param $errormsg If true, the message is an error, so use
	 * the red message style. If false, the message is a status
	 * message, so use the yellow information message style.
	 */
	function cminds_show_message($message, $errormsg = false) {
		if ($errormsg) {
			echo '<div id="message" class="error">';
		} else {
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>";
	}

}

if (!function_exists('cminds_units2bytes')) {

	/**
	 * Converts the Apache memory values to number of bytes ini_get('upload_max_filesize') or ini_get('post_max_size')
	 * @param type $str
	 * @return type
	 */
	function cminds_units2bytes($str) {
		$units = array('B', 'K', 'M', 'G', 'T');
		$unit = preg_replace('/[0-9]/', '', $str);
		$unitFactor = array_search(strtoupper($unit), $units);
		if ($unitFactor !== false) {
			return preg_replace('/[a-z]/i', '', $str) * pow(2, 10 * $unitFactor);
		}
	}

}

if (!function_exists('array_column')) {

	/**
	 * Returns the values from a single column of the input array, identified by
	 * the $columnKey.
	 *
	 * Optionally, you may provide an $indexKey to index the values in the returned
	 * array by the values from the $indexKey column in the input array.
	 *
	 * @param array $input A multi-dimensional array (record set) from which to pull
	 *                     a column of values.
	 * @param mixed $columnKey The column of values to return. This value may be the
	 *                         integer key of the column you wish to retrieve, or it
	 *                         may be the string key name for an associative array.
	 * @param mixed $indexKey (Optional.) The column to use as the index/keys for
	 *                        the returned array. This value may be the integer key
	 *                        of the column, or it may be the string key name.
	 * @return array
	 */
	function array_column($input = null, $columnKey = null, $indexKey = null) {
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc = func_num_args();
		$params = func_get_args();

		if ($argc < 2) {
			trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return null;
		}

		if (!is_array($params[0])) {
			trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
			return null;
		}

		if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !( is_object($params[1]) && method_exists($params[1], '__toString') )
		) {
			trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
			return false;
		}

		if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !( is_object($params[2]) && method_exists($params[2], '__toString') )
		) {
			trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
			return false;
		}

		$paramsInput = $params[0];
		$paramsColumnKey = ( $params[1] !== null ) ? (string) $params[1] : null;

		$paramsIndexKey = null;
		if (isset($params[2])) {
			if (is_float($params[2]) || is_int($params[2])) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}

		$resultArray = array();

		foreach ($paramsInput as $row) {

			$key = $value = null;
			$keySet = $valueSet = false;

			if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}

			if ($paramsColumnKey === null) {
				$valueSet = true;
				$value = $row;
			} elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}

			if ($valueSet) {
				if ($keySet) {
					$resultArray[$key] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}

		return $resultArray;
	}

}

if (!function_exists('cminds_cmtt_admin_tooltip_preview')) {

	add_action('cminds_cmtt_admin_tooltip_preview', 'cminds_cmtt_admin_tooltip_preview_fun');

	function cminds_cmtt_admin_tooltip_preview_fun() {
		?>
		<style>
            #tt-btn-close {
                float: right;
            }

            #tt.admin-tt-wraper {
                display: flex !important;
                justify-content: flex-end;
                position: static;
            }

            .admin-tt {
                position: fixed;
                bottom: 50px;
                max-width: 300px;
                min-width: 200px;
                padding: 2px 12px 3px 7px;
                background-color: rgb(102, 102, 102);
                color: rgb(255, 255, 255);
                border-width: 0;
                border-style: none;
                border-color: rgb(0, 0, 0);
                border-radius: 6px;
                font-size: 13px;
            }
		</style>
		<script>
			jQuery(document).ready(function ($) {

				jQuery("input[type=\"text\"].colorpicker").each(function () {
					var attr = $(this).attr('name');
					$(this).wpColorPicker({
						change: function (event, ui) {
							if (attr === 'cmtt_tooltipBackground') {
								jQuery('#tt .admin-tt').css('background', ui.color.toString());
							}
							if (attr === 'cmtt_tooltipBorderColor') {
								jQuery('#tt .admin-tt').css('border-color', ui.color.toString());
							}
							if (attr === 'cmtt_tooltipForeground') {
								jQuery('#tt .admin-tt').css('color', ui.color.toString());
							}
							if (attr === 'cmtt_tooltipShadowColor') {
								jQuery('#tt .admin-tt').css('box-shadow', ui.color.toString() + ' 0px 0px 20px');
							}
							if (attr === 'cmtt_tooltipCloseColor') {
								jQuery('#tt-btn-close').css('color', ui.color.toString());
							}
							if (attr === 'cmtt_tooltipTitleColor_text') {
								jQuery('#tt .admin-tt .glossaryItemTitle').css({'color': ui.color.toString()});
							}
							if (attr === 'cmtt_tooltipTitleColor_background') {
								jQuery('#tt .admin-tt .glossaryItemTitle').css({'background-color': ui.color.toString()});
							}
						}
					})
				});
				jQuery(document).on('change', '#tabs-4 input, #tabs-4 textarea, #tabs-4 select , #tabs-4 input[type="checkbox"]', function () {
					var fields = $(this).serializeArray();
					jQuery.each(fields, function (i, field) {
						var fieldName = field.name;
						if ('cmtt_tooltipPadding' === fieldName) {
							jQuery('#tt .admin-tt').css('padding', field.value);
						}

						if ('cmtt_tooltipWidthMin' === fieldName) {
							jQuery('#tt .admin-tt').css({'min-width': field.value + 'px !important'});
						}

						if ('cmtt_tooltipWidthMax' === fieldName) {
							jQuery('#tt .admin-tt').css({'max-width': field.value + 'px'});
						}

						if ('cmtt_tooltipTitleFontSize' === fieldName) {
							jQuery('#tt .admin-tt .glossaryItemTitle').css({'font-size': field.value + 'px'});
						}

						if ('cmtt_tooltipBorderRadius' === fieldName) {
							jQuery('#tt .admin-tt ').css({'border-radius': field.value + 'px'});
						}

						if ('cmtt_tooltipBorderWidth' === fieldName) {
							jQuery('#tt  .admin-tt').css('border-width', field.value);
						}

						if ('cmtt_tooltipBorderStyle' === fieldName) {
							jQuery('#tt .admin-tt').css('border-style', field.value);
						}

						if ('cmtt_tooltipPaddingTitle' === fieldName) {
							jQuery('#tt .admin-tt .glossaryItemTitle').css('padding', field.value);
						}

						if ('cmtt_tooltipTitleColor_background' === fieldName) {
							jQuery('#tt .admin-tt .glossaryItemTitle').css('background-color ', field.value);
						}

						if ('cmtt_tooltipPaddingContent' === fieldName) {
							jQuery('#tt .admin-tt .glossaryItemBody').css('padding', field.value);
						}

						if ('cmtt_tooltipFontStyle' === fieldName) {
							jQuery('#tt .admin-tt ').css('font-family:', field.value);
						}

						if ('cmtt_tooltipCloseSize' === fieldName) {
							jQuery('#tt .admin-tt #tt-btn-close').css({'font-size': field.value + 'px'});
						}
						if ('cmtt_tooltipFontSize' === fieldName) {
							jQuery('#tt .admin-tt div.glossaryItemBody').css({'font-size': field.value + 'px'});
						}

						if ('cmtt_tooltipOpacity' === fieldName) {
							var opacity = field.value;
							jQuery('#tt .admin-tt').fadeTo("slow", opacity / 100);
						}

						if ('cmtt_tooltipCloseSymbol' === fieldName) {
							$("#tt .admin-tt #tt-btn-close").removeClass('dashicons-no').addClass(field.value);
						}

						if ('cmtt_glossaryTooltipContentAfter' === fieldName) {
							$("#tt .admin-tt .glossaryItemBody").after(field.value);
						}

						if ('cmtt_glossaryTooltipContentBefore' === fieldName) {
							$("#tt .admin-tt .glossaryItemBody").before($font);
						}

						if ('cmtt_tooltipFontStyle' === fieldName) {
							var $font = (field.value !== 'default (disables Google Fonts)') ? '"' + field.value + '", sans-serif' : '';
							jQuery('#tt .admin-tt').css({'font-family': $font});
						}
					});
				});
			});
		</script>

		<?php
		// Tooltip styling
		$minw = (int) \CM\CMTT_Settings::get('cmtt_tooltipWidthMin', 200);
		$maxw = (int) \CM\CMTT_Settings::get('cmtt_tooltipWidthMax', 400);
		$endalpha = (int) \CM\CMTT_Settings::get('cmtt_tooltipOpacity');
		$borderStyle = \CM\CMTT_Settings::get('cmtt_tooltipBorderStyle');
		$borderWidth = \CM\CMTT_Settings::get('cmtt_tooltipBorderWidth') . 'px';
		$borderColor = \CM\CMTT_Settings::get('cmtt_tooltipBorderColor');
		$foreground = \CM\CMTT_Settings::get('cmtt_tooltipForeground');
		$boxPadding = \CM\CMTT_Settings::get('cmtt_tooltipPadding');
		$borderRadius = \CM\CMTT_Settings::get('cmtt_tooltipBorderRadius') . 'px';
		$fontSize = \CM\CMTT_Settings::get('cmtt_tooltipFontSize', null);
		$fontName = \CM\CMTT_Settings::get('cmtt_tooltipFontStyle', 'default (disables Google Fonts)');
		$fontFamily = ( $fontName !== 'default (disables Google Fonts)' ) ? 'font-family: "' . $fontName . '", sans-serif !important;' : '';
		$contentBG = \CM\CMTT_Settings::get('cmtt_tooltipBackground', '#000000 ');

		$contentAfter = \CM\CMTT_Settings::get('cmtt_glossaryTooltipContentAfter', ' ');
		$contentBefore = \CM\CMTT_Settings::get('cmtt_glossaryTooltipContentBefore', ' ');
		$closeIconSymbol = \CM\CMTT_Settings::get('cmtt_tooltipCloseSymbol', 'dashicons-no ');

		// Tooltip Title styling
		$titleFontSize = \CM\CMTT_Settings::get('cmtt_tooltipTitleFontSize', null);
		$titleBGColor = \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_background', 'transparent');
		$titlePadding = \CM\CMTT_Settings::get('cmtt_tooltipPaddingTitle', '0');
		$titleColor = \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_text', '#000000 ');

		// Tooltip Body styling
		$bodyPadding = \CM\CMTT_Settings::get('cmtt_tooltipPaddingContent', '0');

		// Close Icon styling
		$iconSize = \CM\CMTT_Settings::get('cmtt_tooltipCloseSize', 14);
		$iconColor = \CM\CMTT_Settings::get('cmtt_tooltipCloseColor', '#222');

		// Tooltip Stem styling
		$stemColor = \CM\CMTT_Settings::get('cmtt_tooltipStemColor', '#ffffff');
		$showStem = \CM\CMTT_Settings::get('cmtt_tooltipShowStem', '0');

		$fontName = \CM\CMTT_Settings::get('cmtt_tooltipFontStyle', 'default (disables Google Fonts)');
		if (is_string($fontName) && $fontName !== 'default (disables Google Fonts)' && $fontName !== 'default') {
			$fontNameFixed = strpos($fontName, 'Condensed') !== FALSE ? $fontName . ':300' : $fontName; //fix for the Open Sans Condensed
			$scriptsConfig['styles']['tooltip-google-font'] = array('path' => '//fonts.googleapis.com/css?family=' . $fontNameFixed);
			CMTT_Glossary_Index::_scriptStyleLoader($scriptsConfig, false);
		}
		?>

		<div id="tt" class="admin-tt-wraper" role="tooltip" aria-label="Tooltip preview">
			<div class="admin-tt" id="ttcont">
				<div id="tttop">
                    <span id="tt-btn-close"
                          class="dashicons
                          <?php
                          if (!empty($closeIconSymbol)) {
	                          echo $closeIconSymbol;
                          } else {
	                          echo "dashicons-no";
                          }
                          ?>"
                          aria-label="Close the tooltip">
                    </span>
				</div>
				<div class="glossaryItemTitle">Tooltip</div>
				<div class="glossaryItemBody">The tooltip or infotip or a hint</div>
			</div>
		</div>

		<script>
			jQuery(document).ready(function ($) {
				var tooltipWrapper = $('.admin-tt');
				var $alpha = "<?php echo $endalpha ?>";
				var $before = "<?php echo $contentAfter ?>";
				var $after = "<?php echo $contentBefore ?>";
				$(tooltipWrapper).fadeTo("slow", $alpha / 100);

				if ($after) {
					$("#tt .admin-tt .glossaryItemBody").after($after);
				}
				if ($before) {
					$("#tt .admin-tt .glossaryItemBody").before($before);
				}
			});
		</script>

		<style>
            #tt .admin-tt {
			<?php if (!empty($maxw)) : ?>
                max-width: <?php echo $maxw; ?>px;
			<?php endif; ?>
			<?php if (!empty($minw)) : ?>
                min-width: <?php echo $minw; ?>px;
			<?php endif; ?>
			<?php if (!empty($borderRadius)) : ?>
                border-radius: <?php echo $borderRadius; ?>;
			<?php endif; ?>
			<?php if (!empty($foreground)) : ?>
                color: <?php echo $foreground; ?>;
			<?php endif; ?>
			<?php if (!empty($borderColor)) : ?>
                border-color: <?php echo $borderColor; ?>;
			<?php endif; ?>
			<?php if (!empty($borderWidth)) : ?>
                border-width: <?php echo $borderWidth; ?>;
			<?php endif; ?>
			<?php if (!empty($borderStyle)) : ?>
                border-style: <?php echo $borderStyle; ?>;
			<?php endif; ?>
			<?php if (!empty($fontFamily)) : ?>
			<?php echo $fontFamily; ?>;
			<?php endif; ?>
			<?php if (!empty($contentBG)) : ?>
                background: <?php echo $contentBG; ?>;
			<?php endif; ?>
			<?php if (!empty($boxPadding)) : ?>
                padding: <?php echo $boxPadding; ?>;
			<?php endif; ?>
            }

            #tt .admin-tt .glossaryItemTitle {
                margin: 10px 0;
			<?php if (!empty($titleColor)) : ?>
                color: <?php echo $titleColor; ?>;
			<?php endif; ?>
			<?php if (!empty($titlePadding)) : ?>
                padding: <?php echo $titlePadding; ?>;
			<?php endif; ?>
			<?php if (!empty($titleBGColor)): ?>
                background-color: <?php echo $titleBGColor; ?>;
			<?php endif; ?>
			<?php if (!empty($titleFontSize)): ?>
                font-size: <?php echo $titleFontSize; ?>px;
			<?php endif; ?>
            }

            #tt #tt-btn-close {
			<?php if (!empty($iconSize)): ?>
                font-size: <?php echo $iconSize; ?>px;
			<?php endif; ?>
			<?php if (!empty($iconColor)): ?>
                color: <?php echo $iconColor; ?>;
			<?php endif; ?>
            }

            #tt #ttcont .glossaryItemBody {
			<?php if (!empty($bodyPadding)) : ?>
                padding: <?php echo $bodyPadding; ?>;
			<?php endif; ?>
			<?php if (!empty($fontSize)) : ?>
                font-size: <?php echo $fontSize; ?>px;
			<?php endif; ?>
            }

			<?php if (!empty($showStem)): ?>
            #tt .admin-tt.vertical_top:after {
                border-bottom: 9px solid <?php echo $stemColor; ?>;
            }
            #tt .admin-tt.vertical_bottom:after {
                border-top: 9px solid <?php echo $stemColor; ?>;
            }
			<?php endif; ?>
		</style>
		<?php
	}

}


if (!function_exists('cminds_is_amp_endpoint')) {

	/**
	 * Whether this is an AMP endpoint.
	 *
	 * @see https://github.com/Automattic/amp-wp/blob/e4472bfa5c304b6c1b968e533819e3fa96579ad4/includes/amp-helper-functions.php#L248
	 * @return boolean
	 */
	function cminds_is_amp_endpoint() {
		$turn_on_amp = (bool) \CM\CMTT_Settings::get('cmtt_glossaryTurnOnAmp', 0);
		$amp1 = function_exists('is_amp_endpoint') && is_amp_endpoint();
		$amp2 = function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint();
		return $turn_on_amp && ($amp1 || $amp2);
	}

}

if (!function_exists('cminds_output_nested_categories')) {

	function cmtt_recursion($categories, $parent_id, $currentCategory, $level = 1) {
		$subcatOutput = '';
		foreach ($categories as $k => $cat) {
			$out_level = $level;
			if ($parent_id == $cat->parent) {
				$indent = str_repeat('&emsp;', $level);
				$selected = is_array($currentCategory) && in_array($cat->term_id, $currentCategory) ? 'selected="selected"' : '';
				$subcatOutput .= '<option ' . $selected . ' value="' . esc_attr($cat->term_id) . '">' . $indent . $cat->name . '</option>';
				$subcatOutput .= cmtt_recursion($categories, $cat->term_id, $currentCategory, ++$level);
				$level = $out_level;
			}
		}
		return $subcatOutput;
	}

	function cminds_output_nested_categories($categories, $cat, $currentCategory) {
		$output = '';
		if (isset($cat->parent) && $cat->parent != 0) {
			$selected = is_array($currentCategory) && in_array($cat->term_id, $currentCategory) ? 'selected="selected"' : '';
			$output .= '<option ' . $selected . ' value="' . $cat->term_id . '">' . $cat->name . '</option>';
			$output .= cmtt_recursion($categories, $cat->term_id, $currentCategory);
		} else {
			$selected = is_array($currentCategory) && in_array($cat->term_id, $currentCategory) ? 'selected="selected"' : '';
			$output .= '<option ' . $selected . ' value="' . $cat->term_id . '">' . $cat->name . '</option>';
		}
		return $output;
	}

}