#!/bin/bash

dirs=("wp-content/plugins" "wp-content/themes" "wp-content/mu-plugins" "wp-content/object-cache")

passing=0

ignores=(
  'wp-content/plugins/anthologize'
  'wp-content/plugins/awesome-flickr-gallery-plugin/afgFlickr/afgFlickr.php'
#  'wp-content/plugins/backtype-connect/parser_php4.php'
  'wp-content/plugins/bp-reply-by-email/includes/phpseclib'
#  'wp-content/plugins/btcnew/parser_php4.php'
  'wp-content/plugins/buddypress/cli/features'
  'wp-content/plugins/digressit'
  'wp-content/plugins/dk-pdf/vendor/paragonie/random_compat'
  'wp-content/plugins/download-monitor/src/DownloadHandler.php'
  'wp-content/plugins/download-monitor/src/Polyfill/DateTimeImmutable'
  'wp-content/plugins/dw-question-answer/lib/recaptcha-php/recaptchalib.php'
  'wp-content/plugins/edge-suite/includes/edge-suite-general.php'
  'wp-content/plugins/event-organiser/event-organiser-debug.php'
#  'wp-content/plugins/extended-categories-widget/2.8'
  'wp-content/plugins/gravityforms'
  'wp-content/plugins/kb-gradebook'
#  'wp-content/plugins/newsletters-lite/vendor/phpseclib'
  'wp-content/plugins/osm'
  'wp-content/plugins/query-monitor/collectors/environment.php'
  'wp-content/plugins/query-monitor/wp-content/db.php'
  'wp-content/plugins/shardb'
  'wp-content/plugins/social/lib/social/log.php'
  'wp-content/plugins/static-html-output-plugin'
  'wp-content/plugins/tablepress/views/view-about.php'
#  'wp-content/plugins/threewp-broadcast/src/sdk/wordpress/updater/edd.php'
  'wp-content/plugins/wp-document-revisions/tests'
  'wp-content/plugins/wp-post-to-pdf'
  'wp-content/plugins/wp-simile-timeline'
#  'wp-content/plugins/wp-live-preview-links'
#  'wp-content/plugins/wp-security-scan'
  'wp-content/themes/weaver/wvr-includes/wvr-fileio.php'
#  'wp-content/themes/bridge/includes/recaptchalib.php'
#  'wp-content/themes/classipress/includes/lib/recaptchalib.php'
#  'wp-content/themes/nelo-for-tags'
)

separator=','
ignores_joined="$( printf "${separator}%s" "${ignores[@]}" )"
ignores_joined="${ignores_joined:${#separator}}" # remove leading separator

ignore="--ignore=$ignores_joined"

for dir in ${dirs[*]}
do
  subdirs=$(find $dir -maxdepth 1 -mindepth 1 -type d)
  for subdir in ${subdirs[*]}
  do
    echo "Testing $subdir..."
    results=$(./vendor/bin/phpcs -p --extensions=php,inc --standard=PHPCompatibilityWP --warning-severity=0 --runtime-set testVersion 7.2 $ignore $subdir)
    if [ $? -eq 1 ]
    then
      echo "$results"
      passing=1
    else
      echo "Passed"
    fi
  done
done

exit $passing
