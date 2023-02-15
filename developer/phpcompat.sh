#!/bin/bash

dirs=("wp-content/plugins" "wp-content/themes" "wp-content/mu-plugins" "wp-content/object-cache")

passing=0

ignores=(
  'wp-content/plugins/anthologize/vendor/pear/'
  'wp-content/plugins/anthologize/vendor/tecnickcom/tcpdf/'
  'wp-content/plugins/awesome-flickr-gallery-plugin/afgFlickr/afgFlickr.php'
#  'wp-content/plugins/backtype-connect/parser_php4.php'
  'wp-content/plugins/bbpress/includes/users/template.php' # punt

  # Issues with 'static' keyword in included non-class files
  'wp-content/plugins/bookly-responsive-appointment-booking-tool/backend'

  'wp-content/plugins/bp-reply-by-email/includes/phpseclib'
#  'wp-content/plugins/btcnew/parser_php4.php'
  'wp-content/plugins/buddypress/cli/features'
  'wp-content/plugins/digressit'
  'wp-content/plugins/dk-pdf/vendor/paragonie/random_compat'
  'wp-content/plugins/download-monitor/src/DownloadHandler.php'
  'wp-content/plugins/download-monitor/src/Polyfill/DateTimeImmutable'
  'wp-content/plugins/dw-question-answer/lib/recaptcha-php/recaptchalib.php'
  'wp-content/plugins/edge-suite/includes/edge-suite-general.php'
  'wp-content/plugins/easy-table-of-contents/includes/inc.string-functions.php'
  'wp-content/plugins/easy-table-of-contents/includes/vendor/ultimate-web-scraper/emulate_curl.php'
  'wp-content/plugins/easy-table-of-contents/includes/vendor/ultimate-web-scraper/web_browser.php'
  'wp-content/plugins/event-organiser/event-organiser-debug.php'
#  'wp-content/plugins/extended-categories-widget/2.8'
  'wp-content/plugins/gravityforms'
  'wp-content/plugins/kb-gradebook'
  'wp-content/plugins/nextgen-gallery/vendor/nikic/php-parser/lib/PhpParser/Lexer.php'
#  'wp-content/plugins/newsletters-lite/vendor/phpseclib'
  'wp-content/plugins/osm'
  'wp-content/plugins/out-of-the-box/includes/dropbox-sdk/src/Dropbox/Security/'
  'wp-content/plugins/out-of-the-box/vendors/phpThumb'

  # Only called on old PHP
  'wp-content/plugins/out-of-the-box/vendors/dropbox-sdk/src/Dropbox/Security/McryptRandomStringGenerator.php'
  'wp-content/plugins/papercite/lib/PEAR.php'
  'wp-content/plugins/query-monitor/collectors/environment.php'
  'wp-content/plugins/query-monitor/wp-content/db.php'
  'wp-content/plugins/shardb'
  'wp-content/plugins/social/lib/social/log.php'
  'wp-content/plugins/static-html-output-plugin'
  'wp-content/plugins/tablepress/views/view-about.php'
  'wp-content/plugins/the-events-calendar/src/Tribe/Views/V2/Repository/Event_Period.php'
  'wp-content/plugins/the-events-calendar/src/Tribe/Templates.php'
#  'wp-content/plugins/threewp-broadcast/src/sdk/wordpress/updater/edd.php'

  # Uses some removed mcrypt constants. Not sure how to address.
  'wp-content/plugins/watupro/lib/recaptcha/recaptchalib.php'

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

###
# Parallel processing helpers
# See https://unix.stackexchange.com/questions/103920/parallelize-a-bash-for-loop
#
# Initialize a semaphore with a given number of tokens.
open_sem(){
    mkfifo pipe-$$
    exec 3<>pipe-$$
    rm pipe-$$
    local i=$1
    for((;i>0;i--)); do
        printf %s 000 >&3
    done
}

# Run the given command asynchronously and pop/push tokens.
run_with_lock(){
    local x
    # this read waits until there is something to read
    read -u 3 -n 3 x && ((0==x)) || exit $x
    (
     ( "$@"; )
    # push the return code of the command to the semaphore
    printf '%.3d' $? >&3
    )&
}
# End parallel processing helpers.
###

# Run PHPCS on a given location with the global ignore rules defined above.
run_subdir_phpcs() {
  echo "Testing $1..."
  results=$(./vendor/bin/phpcs -ps --extensions=php,inc --standard=developer/phpcs/CAC --warning-severity=0 --runtime-set testVersion 7.4- $ignore $1)
  if [ $? -eq 1 ]
  then
    echo "$results"
    passing=1
  else
    printf "."
  fi
}

# Open 4 threads.
open_sem 4

if [ "$since" = "" ]
then
  for dir in ${dirs[*]}
  do
    subdirs=$(find $dir -maxdepth 1 -mindepth 1 -type d)

    # If subdirectories are found, scan subdirectories. If none are found,
    # scan the directory itself.
    if [[ ${#subdirs} -gt 0 ]]
    then
      for subdir in ${subdirs[*]}
      do
        # Run PHPCS on the subdirectory in an available thread.
        run_with_lock run_subdir_phpcs $subdir
      done
    else
      # Run PHPCS on the directory in an available thread.
      run_with_lock run_subdir_phpcs $dir
    fi
  done
else
  echo "Examining only files edited in $since days"
  for dir in ${dirs[*]}
  do
    files=$(find $dir -mtime $since -type f -name "*.php")
    for file in ${files[*]}
    do
      echo "Testing $file..."
      results=$(./vendor/bin/phpcs -p --extensions=php,inc --standard=PHPCompatibilityWP --warning-severity=0 --runtime-set testVersion 7.4- $ignore $file)
      if [ $? -eq 1 ]
      then
        echo "$results"
        passing=1
      else
        echo "Passed"
      fi
    done
  done
fi

# Wait for any open jobs to finish before exiting.
wait

exit $passing
