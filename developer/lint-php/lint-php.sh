#!/bin/bash

excludes=(
	'./wp-content/blogs.dir'
	'./wp-content/uploads',
	'./wp-content/plugins/advanced-gutenberg/vendor/pimple/pimple/ext/pimple/tests' # Test dir not loaded in production
	'./wp-content/plugins/wp-post-to-pdf' # Plugin is deprecated and throws lots of false positives
  './wp-content/plugins/anthologize/vendor/pear/pear/' # Not loaded because we have zlib.
  './wp-content/plugins/anthologize/vendor/pear/file_archive/' # Not loaded because we have zlib.
  './wp-content/plugins/anthologize/vendor/pear/mime_type/' # Not loaded because we have zlib.
  './wp-content/plugins/out-of-the-box/vendors/phpThumb'
)

separator='--exclude '
excludes_joined="$( printf -- "${separator}%s " "${excludes[@]}" )"

echo $excludes_joined
