<!DOCTYPE html>
<html lang="en">
<head>
<title>TF!WP automated tests output</title>
<meta charset="UTF-8">
<style>
table, td, th { border: 1px solid #ccc; border-collapse: collapse; padding: 0.5em; }
td.OK { background-color: limegreen; }
td.FAILURES { background-color: red; }
td.STOPPED { background-color: orange; }
td.ABORTED { background-color: #ccc; }
td:nth-child(2) { text-align: center; }
</style>
</head>
<body>
<h1>TF!WP automated tests output</h1>
<table>
<tr><th>Test</th><th>Result</th><th>Reason</th></td>
<?php

$found_running = false;

foreach (array_reverse(glob("output/tfwp-test-*.txt")) as $file) {
    $file_content = file_get_contents($file);
    if (preg_match('/OK (.\d+ tests, \d+ assertions.)/', $file_content, $msg)) {
        $result = "OK";
    } elseif (preg_match('/FAILURES!\n(.*)/', $file_content, $msg)) {
        $result = "FAILURES";
    } elseif (preg_match('/OK, but (incomplete or skipped tests!)/', $file_content, $msg)) {
        $result = "STOPPED";
    } elseif (preg_match('/ABORTED(.*)/', $file_content, $msg)) {
        $result = "ABORTED";
    } elseif (!$found_running && shell_exec('ps axw | grep run-tests.sh | grep -v grep')) {
        $result = "RUNNING";
        $found_running = true;
    } else {
        $result = "ABORTED";
    }
    if (count($msg) < 2) {
        $msg = array('', '');
    }
    echo '<tr><td><a href="'.$file.'">'.$file.'</a></td><td class="'.$result.'">'.$result.'</td><td>'.$msg[1].'</td></tr>'.PHP_EOL;
}

?>
</table>
</body>
</html>
