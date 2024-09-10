<?php
/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 *    r-a-y - maintenance of 0.1 branch; slight mod to use a markdown syntax
 *            for the <img> element
 *
 * Based off html2text 0.1.1 to maintain function approach and compatibility
 * with PHP 5.2.  Updated to use newer algorithm from 0.3.1.
 *
 * @link https://github.com/soundasleep/html2text/
 * @link https://github.com/r-a-y/html2text/tree/0.1.x
 ****************************************************************************/

/**
 * Tries to convert the given HTML into a plain text format - best suited for
 * e-mail display, etc.
 *
 * <p>In particular, it tries to maintain the following features:
 * <ul>
 *   <li>Links are maintained, with the 'href' copied over
 *   <li>Information in the &lt;head&gt; is lost
 * </ul>
 *
 * @param html the input HTML
 * @return the HTML converted, as best as possible, to text
 */
function convert_html_to_text($html) {
	// replace &nbsp; with spaces
	$html = str_replace("&nbsp;", " ", $html);
	$html = str_replace("\xc2\xa0", " ", $html);

	$html = fix_newlines($html);

	$html_utf8 = mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' );

	$doc = new DOMDocument();
	if ( ! $doc->loadHTML( '<?xml encoding="UTF-8">' . $html_utf8 ) )
		throw new Html2TextException("Could not load HTML - badly formed?", '');

	$output = iterate_over_node($doc);

	// remove leading and trailing spaces on each line
	$output = preg_replace("/[ \t]*\n[ \t]*/im", "\n", $output);
	$output = preg_replace("/ *\t */im", "\t", $output);

	// remove unnecessary empty lines
	$output = preg_replace("/\n\n\n*/im", "\n\n", $output);

	// remove leading and trailing whitespace
	$output = trim($output);

	return $output;
}

/**
 * Unify newlines; in particular, \r\n becomes \n, and
 * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
 * all become \ns.
 *
 * @param text text with any number of \r, \r\n and \n combinations
 * @return the fixed text
 */
function fix_newlines($text) {
	// replace \r\n to \n
	$text = str_replace("\r\n", "\n", $text);
	// remove \rs
	$text = str_replace("\r", "\n", $text);

	return $text;
}

function next_child_name($node) {
	// get the next child
	$nextNode = $node->nextSibling;
	while ($nextNode != null) {
		if ($nextNode instanceof DOMElement) {
			break;
		}
		$nextNode = $nextNode->nextSibling;
	}
	$nextName = null;
	if ($nextNode instanceof DOMElement && $nextNode != null) {
		$nextName = strtolower($nextNode->nodeName);
	}

	return $nextName;
}

function prev_child_name($node) {
	// get the previous child
	$nextNode = $node->previousSibling;
	while ($nextNode != null) {
		if ($nextNode instanceof DOMElement) {
			break;
		}
		$nextNode = $nextNode->previousSibling;
	}
	$nextName = null;
	if ($nextNode instanceof DOMElement && $nextNode != null) {
		$nextName = strtolower($nextNode->nodeName);
	}

	return $nextName;
}

function iterate_over_node($node) {
	if ($node instanceof DOMText) {
		return preg_replace("/[\\t\\n\\f\\r ]+/im", " ", $node->wholeText);
	}
	if ($node instanceof DOMDocumentType) {
		// ignore
		return "";
	}

	$nextName = next_child_name($node);
	$prevName = prev_child_name($node);

	$name = strtolower($node->nodeName);

	// start whitespace
	switch ($name) {
		case "hr":
			return "------\n";

		case "style":
		case "head":
		case "title":
		case "meta":
		case "script":
			// ignore these tags
			return "";

		case "h1":
		case "h2":
		case "h3":
		case "h4":
		case "h5":
		case "h6":
		case "ol":
		case "ul":
			// add two newlines, second line is added below
			$output = "\n";
			break;

		case "td":
		case "th":
			// add tab char to separate table fields
		   $output = "\t";
		   break;

		case "tr":
		case "p":
		case "div":
			// add one line
			$output = "\n";
			break;

		case "li":
			$output = "- ";
			break;

		case "b":
		case "strong":
			$output = "**";
			break;

		case "i":
		case "em":
			$output = "_";
			break;

		case "del":
			$output = "~~";
			break;

		case "code":
			$output = "`";
			break;

		default:
			// print out contents of unknown tags
			$output = "";
			break;
	}

	// debug
	//$output .= "[$name,$nextName]";

	if (isset($node->childNodes)) {
		for ($i = 0; $i < $node->childNodes->length; $i++) {
			$n = $node->childNodes->item($i);

			$text = iterate_over_node($n);

			$output .= $text;
		}
	}

	// end whitespace
	switch ($name) {
		case "h1":
		case "h2":
		case "h3":
		case "h4":
		case "h5":
		case "h6":
			$output .= "\n";
			break;

		case "p":
		case "br":
			// add one line
			if ($nextName != "div")
				$output .= "\n";
			break;

		case "div":
			// add one line only if the next child isn't a div
			if ($nextName != "div" && $nextName != null)
				$output .= "\n";
			break;

		case "a":
			// links are returned in [text](link) format
			$href = $node->getAttribute("href");

			$output = trim($output);

			// remove double [[ ]] s from linking images
			if (substr($output, 0, 1) == "[" && substr($output, -1) == "]") {
				$output = substr($output, 1, strlen($output) - 2);

				// for linking images, the title of the <a> overrides the title of the <img>
				if ($node->getAttribute("title")) {
					$output = $node->getAttribute("title");
				}
			}

			// if there is no link text, but a title attr
			if (!$output && $node->getAttribute("title")) {
				$output = $node->getAttribute("title");
			}

			if ($href == null) {
				// it doesn't link anywhere
				if ($node->getAttribute("name") != null) {
					$output = "[$output]";
				}
			} else {
				if ($href == $output || $href == "mailto:$output" || $href == "http://$output" || $href == "https://$output") {
					// link to the same address: just use link
					$output;
				} else {
					// replace it
					if ($output) {
						$output = "[$output]($href)";
					} else {
						// empty string
						$output = $href;
					}
				}
			}

			// does the next node require additional whitespace?
			switch ($nextName) {
				case "h1": case "h2": case "h3": case "h4": case "h5": case "h6":
					$output .= "\n";
					break;
			}
			break;

		// mod by r-a-y
		case "img":
			$alt = $node->getAttribute("alt");
			$src = $node->getAttribute("src");

			if ( ! empty( $alt ) ) {
				$output = "![Image - $alt]";
			} else {
				$output = "![Image]";
			}

			$output .= "($src)";

			break;

		case "li":
			$output .= "\n";
			break;

		case "b":
		case "strong":
			$output .= "**";
			break;

		case "i":
		case "em":
			$output .= "_";
			break;

		case "del":
			$output .= "~~";
			break;

		case "code":
			$output .= "`";
			break;

		default:
			// do nothing
	}

	return $output;
}

class Html2TextException extends Exception {
	var $more_info;

	public function __construct($message = "", $more_info = "") {
		parent::__construct($message);
		$this->more_info = $more_info;
	}
}
