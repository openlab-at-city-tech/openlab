# WP Pointer Tour

Create an interactive tour with WP Pointers on any admin screen in WordPress

## Easy setup

```php

require 'wp-pointer-tour/wp-pointer-tour.php';

//flag is saved when user finishes tour
$tour = new WP_Pointer_Tour('user_tour_example');

// simple
$tour->add_pointer(array(
	'content'   => '<h3>title</h3><p>body</p>',
	'ref_id'    => '#node1',
	'position'  => array(
		'edge'      => 'top',
		'align'     => 'left'
	),
	'pointerWidth' => 400,
	'prev'         => false,
	'next'         => 'Start Tour',
	'dismiss'      => 'Dismiss'
));

// use of custom js event to continue
$tour->add_pointer(array(
	'content'   => '<h3>title</h3><p>body</p>',
	'ref_id'    => '#node2',
	'position'  => array(
		'edge'      => 'left',
		'align'     => 'bottom'
	),
	'prev'         => 'Previous',
	'next'         => '.button',
	'nextEvent'    => 'click',
	'dismiss'      => 'End Tour'
));

//hooks into admin_enqueue_scripts automatically
//but can be loaded from a specific screen
if(!$tour->user_has_finished_tour()) {
    add_action('load-edit.php',array($tour,'enqueue_scripts'));
}

echo $tour->get_user_option(); //finish timestamp
```

##License

Copyright 2016 Joachim Jensen

Licensed under the GNU General Public License, version 3: https://opensource.org/licenses/GPL-3.0
