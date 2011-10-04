<?php
/* This is the main function.php override file - it controls most of the stuff happening with the theme */

/* Anything you need to actively do, put here. This is the first code that will get run whenever the
  child theme is activated and used. This example just re-defines a function, but you could do anything.
*/

function weaver_continue_reading_link() {
    /* very simple example - override the read more text... */

    $msg = '[CLICK TO READ MORE]';
    return ' <a class="more-link" href="'. get_permalink() . '">' . $msg . '</a>';
}

?>
