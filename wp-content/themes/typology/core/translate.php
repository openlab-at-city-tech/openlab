<?php

/* This is a global array of translation strings on front-end */

global $typology_translate;

$typology_translate = array(
	'latest_stories' => array( 'text' => esc_html__( 'Latest stories', 'typology' ), 'desc' => 'Home page posts section title' ),
	'no_comments' => array( 'text' => esc_html__( 'Add comment', 'typology' ), 'desc' => 'Comment meta data (if zero comments)' ),
	'one_comment' => array( 'text' => esc_html__( '1 comment', 'typology' ), 'desc' => 'Comment meta data (if 1 comment)' ),
	'multiple_comments' => array( 'text' => esc_html__( '% comments', 'typology' ), 'desc' => 'Comment meta data (if more than 1 comments)' ),
	'min_read' => array( 'text' => esc_html__( 'Min read', 'typology' ), 'desc' => 'Used in post meta data (reading time)' ),
	'by' => array( 'text' => esc_html__( 'By', 'typology' ), 'desc' => 'Used in post meta data (before author)' ),
	'in' => array( 'text' => esc_html__( 'In', 'typology' ), 'desc' => 'Used in post meta data (before category list)' ),
	'read_on' => array( 'text' => esc_html__( 'Read on', 'typology' ), 'desc' => 'Read more button label' ),
	'read_later' => array( 'text' => esc_html__( 'Read later', 'typology' ), 'desc' => 'Read later button label' ),
	'search_placeholder' => array('text' => esc_html__('Type here to search...', 'typology'), 'desc' => 'Search placeholder text'),
	'search_button' => array('text' => esc_html__('Search', 'typology'), 'desc' => 'Search button text'),
	'newer_entries' => array('text' => esc_html__('Newer Entries', 'typology'), 'desc' => 'Pagination (prev/next) link text'),
	'older_entries' => array('text' => esc_html__('Older Entries', 'typology'), 'desc' => 'Pagination (prev/next) link text'),
	'previous_posts' => array('text' => esc_html__('Previous', 'typology'), 'desc' => 'Pagination (numeric) link text'),
	'next_posts' => array('text' => esc_html__('Next', 'typology'), 'desc' => 'Pagination (numeric) link text'),
	'load_more' => array('text' => esc_html__('Load More', 'typology'), 'desc' => 'Pagination (load more) link text'),
	'to_top' => array('text' => esc_html__('To Top', 'typology'), 'desc' => 'Text for "Go to top" button '),
	'category' => array('text' => esc_html__('Category', 'typology'), 'desc' => 'Category title prefix'),
	'tag' => array('text' => esc_html__('Tag', 'typology'), 'desc' => 'Tag title prefix'),
	'author' => array('text' => esc_html__('Author', 'typology'), 'desc' => 'Author title prefix'),
	'archive' => array('text' => esc_html__('Archive', 'typology'), 'desc' => 'Archive title prefix'),
	'search_results_for' => array('text' => esc_html__('Search results for', 'typology'), 'desc' => 'Title for search results template'),
	'related' => array('text' => esc_html__('Read more', 'typology'), 'desc' => 'Related posts area title'),
	'about_author' => array('text' => esc_html__('About the author', 'typology'), 'desc' => 'About the author area title'),
	'view_all' => array('text' => esc_html__('View all posts', 'typology'), 'desc' => 'View all posts link text in author box'),
	'comment_submit' => array('text' => esc_html__('Submit Comment', 'typology'), 'desc' => 'Comment form submit button label'),
	'comment_reply' => array('text' => esc_html__('Reply', 'typology'), 'desc' => 'Comment reply label'),
	'comment_text' => array('text' => esc_html__('Comment', 'typology'), 'desc' => 'Comment form text area label'),
	'comment_email' => array('text' => esc_html__('Email', 'typology'), 'desc' => 'Comment form email label'),
	'comment_name' => array('text' => esc_html__('Name', 'typology'), 'desc' => 'Comment form name label'),
	'comment_website' => array('text' => esc_html__('Website', 'typology'), 'desc' => 'Comment form website label'),
	'comment_cancel_reply' => array('text' => esc_html__('Cancel reply', 'typology'), 'desc' => 'Comment cancel reply label'),
	'comment_cookie_gdpr' => array('text' => esc_html__('Save my name, email, and website in this browser for the next time I comment.', 'typology'), 'desc' => 'Comment GDPR cookie label'),
	'404_title' => array('text' => esc_html__('Page not found', 'typology'), 'desc' => '404 page title'),
	'404_text' => array('text' => esc_html__('The page that you are looking for does not exist on this website. You may have accidentally mistype the page address, or followed an expired link. Anyway, we will help you get back on track. Why not try to search for the page you were looking for:', 'typology'), 'desc' => '404 page text'),
	'content_none' => array('text' => esc_html__('Sorry, there are no posts found on this page. Feel free to contact website administrator regarding this issue.', 'typology'), 'desc' => 'Message when there are no posts on archive pages. i.e Empty Category'),
	'content_none_search' => array('text' => esc_html__('No results found. Please try again with a different keyword.', 'typology'), 'desc' => 'Message when there are no search results.') 
);

?>