<?php

/**
 * Get BP document type by the filename url.
 */
function openlab_get_document_type( $file_name ) {
	return filter_var( $file_name, FILTER_VALIDATE_URL ) ? 'link' : 'upload';
}