#!/bin/bash

file="output/tfwp-test-`date +%F_%T`.txt"
touch $file
if [ "`ps | grep -c run-tests.sh`" != "2" ]; then
    echo "ABORTED (already running)" > $file
else
    phpunit tfwp-test.php > $file
fi
