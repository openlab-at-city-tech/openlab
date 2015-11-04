<?php

/**
 * Script index to not allow the display of the directory 
 * from the web server, I send a message 404 page not found
 *
 * @package SZGoogle
 * @subpackage Functions
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

header('HTTP/1.1 404 Not Found');

echo "<!DOCTYPE>\n<html><head>\n<title>404 Not Found</title>\n</head>";
echo "<body>\n<h1>Not Found</h1>\n<p>The requested URL ".$_SERVER['REQUEST_URI']." was not found on this server.</p>\n";
echo "</body></html>\n";

exit();