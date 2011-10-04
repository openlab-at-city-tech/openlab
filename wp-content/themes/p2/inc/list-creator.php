<?php
/**
* Based on a version of the doLists and related functions from the Markdown PHP Library
* @see http://michelf.com/projects/php-markdown/
*/
class P2_List_Creator {
	/**
	* This function only looks for unordered lists and will convert a list of *'s into the correct <ul><li> pairs
	* The regex contains some useful comments for which parts are stored in the array of matches.
	*
	* @param string String of text to parse for creating a list
	* @return string The converted string with HTML tags
	*/
	function do_list( $text ) {
		$text = str_replace( "\n", "\n\n", $text );

		// Re-usable patterns to match the entire list
		$list_regex =  '(							# $1 = whole list
					(						# $2
						([ ]{0,3})				# $3 = number of spaces
						([*-])					# $4 = first list item marker
					)
					(?s:.+?)
					(						# $5
						\z
						|
						\n{2,}
						(?=\S)
						(?!					# Negative lookahead for another list item marker
							[ ]*
							[*-]
						)
					)
				)';

		// Run our regex through the callback, get the eventual text a few levels down and return it back to P2 here.
		$text = preg_replace_callback( '{^' . $list_regex . '}mx', array( &$this, '_do_list_callback' ), $text );
		$text = str_replace( "\n\n", "\n", $text );
		$text = str_replace( "\n</li>", "</li>", $text );

		return $text;
	}

	/**
	* Pads the list (if found) with the <ul> tags
	* and calls our main processing function to further create the list
	*
	* @param array Regex matches from do_list
	* @return string Processed converted string
	*/
	function _do_list_callback( $matches ) {
		$lines = count (explode( "\n" . $matches[4], $matches[1] ) );

		if ( $lines > 1 )
			return preg_replace( "/(\n([*-]))/", "\n", "<ul>\n" . $this->process_list_items( $matches[1] . "\n\n" ). "</ul>\n\n" );
		else
			return $matches[1];
	}

	/**
	* Breaks up each individual list item so that we can parse them separately later (useful for checking sublists, etc)
	* The regex contains some useful comments for which parts are stored in the array of matches.
	*
	* @param string String of text to parse.
	* @return string the Individual parsed string.
	*/
	function process_list_items( $text ) {
		$text = preg_replace( "/\n{3,}\\z/", "\n", $text );

		return preg_replace_callback( '{
							(\n)?				# leading line = $1
							(^[ ]*)				# leading whitespace = $2
							([*-])				# list marker and space = $3
							((?s:.*?))			# list item text   = $4
							(?:(\n+(?=\n))|\n)		# tailing blank line = $5
							(?= \n*(\z|([*-])))
						}xm',
						array( &$this, '_process_list_items_callback' ),
						$text );
	}

	/**
	* Remove one level of line-leading tabs or spaces
	*
	* @param string String of text to remove the tabs/spaces from
	* @return string The outdented string
	*/
	function outdent( $text ) {
		return preg_replace( '/^(\t|[ ]{1,3})/m', '', $text );
	}

	/**
	* Wraps the list item in an li tag or recursively calls do_list if we have some sub lists.
	*
	* @param array Regex matches from do_list
	* @return string Processed converted string
	*/
	function _process_list_items_callback( $matches ) {
		// Explanation of the values used here
		// see also process_list_items

		// matches = array (
			// 1 => leading line
			// 2 => leading space
			// 3 => marker space
			// 4 => the actual item
			// 5 => trailing blank line
		// )

		$item = str_replace( "\n\n", "\n", $matches[4] );

		if ( preg_match( '/\n{3,}/', $item ) ) {
			// Replace marker with the appropriate whitespace indentation
			$item = $matches[2] . str_repeat( ' ', strlen( $matches[3] ) ) . $item;
			$item = $this->outdent( $item ) . "\n";
		} else {
			// Recursion for sub-lists
			$item = $this->do_list( $this->outdent( $item . "\n" . $matches[3] . "\n" ), 1 );
			$item = preg_replace( '/\n+$/', '', $item );
		}

		if ( $matches[4] )
			return "<li>" . $item . "</li>\n";
	}
}