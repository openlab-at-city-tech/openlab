#!/bin/bash

excludes=(
	'./wp-content/blogs.dir'
	'./wp-content/uploads'
)

separator='--exclude '
excludes_joined="$( printf -- "${separator}%s " "${excludes[@]}" )"

echo $excludes_joined
