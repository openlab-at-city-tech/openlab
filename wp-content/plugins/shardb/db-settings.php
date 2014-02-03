<?php

// If you have multiple datacenters you can come up with your own datacenter
// detection logic (php_uname?). This helps ensure the web servers try to
// connect to the nearest database servers first, then distant ones.
define( 'DATACENTER', '' );

function add_slave($read, $host, $lhost = '', $user = DB_USER, $password = DB_PASSWORD) {
	global $slaves;
	$slaves[] = compact('read', 'host', 'lhost', 'user', 'password');
}

/* Add your configuration here */

/* Use this configuration for a hexidecimal based hash 

Ex. you have 256 databases that follow the naming convention acct_wpmuXX
where XX is the hexidecimal hash for the blog DB

// how many characters of hexidecimal hash
$shardb_hash_length = 2;
// what is the prefix of your blog database shards (everything before the hexidecimal hash)
$shardb_prefix = 'acct_wpmu';
// set a string to be used as an internal identifier for the dataset
$shardb_dataset = 'abc';
// do you want to put your primary blog (blog_id 1) in its own 'home' database?
$enable_home_db = true;
// how many, if any, VIP databases do you have?
$num_vipdbs = 5;
// add this to set the write master read priority (default 1)
$shardb_master_read = 99;
// add this if all of your databases are on a local server
$shardb_local_db = true;
// use this function to add a read slave host
add_slave($read_priority, $hostname, $local_hostname, $user, $password); 

// instructions for adding vip blogs at the bottom of this confg filei
*/

/* That's all, stop editing! Happy blogging. */

if ( !defined('SAVEQUERIES') )
	define('SAVEQUERIES', false);

/**
 * A trick used by WordPress.com is .lan hostnames mapped to local IPs. Not required.
 *
 * @param unknown_type $hostname
 * @return unknown
 */
function localize_hostname($hostname) {
	return str_replace('.com', '.lan', $hostname);
}

function localize_hostnames($array) {
	return array_map('localize_hostname', $array);
}

/**
 * This generates the array of servers.
 *
 * @param string $ds Dataset: the name of the dataset. Just use "global" if you don't need horizontal partitioning.
 * @param int $part Partition: the vertical partition number (1, 2, 3, etc.). Use "0" if you don't need vertical partitioning.
 * @param string $dc Datacenter: where the database server is located. Airport codes are convenient. Use whatever.
 * @param int $read Read order: lower number means use this for more reads. Zero means no reads (e.g. for masters).
 * @param bool $write Write flag: is this server writable?
 * @param string $host Internet address: host:port of server on internet. 
 * @param string $lhost Local address: host:port of server for use when in same datacenter. Leave empty if no local address exists.
 * @param string $name Database name.
 * @param string $user Database user.
 * @param string $password Database password.
 */
function add_db_server($ds, $part, $dc, $read, $write, $host, $lhost, $name, $user, $password) {
	global $db_servers, $db_ds_parts;

	if ( empty( $lhost ) )
		$lhost = $host;

	$server = compact('ds', 'part', 'dc', 'read', 'write', 'host', 'lhost', 'name', 'user', 'password');

	$db_servers[$ds][$part][] = $server;
	$db_ds_parts[$name] = "{$ds}_{$part}";
}

// Database servers grouped by dataset. (Totally tabular, dude!)
// R can be 0 (no reads) or a positive integer indicating the order
// in which to attempt communication (all locals, then all remotes)

//dataset, partition, datacenter, R, W,             internet host:port,     internal network host:port,   database,        user,        password

add_db_server( 'global', 0,    '', 1, 1,    DB_HOST, DB_HOST, $shardb_prefix . 'global', DB_USER, DB_PASSWORD );
// Next line populates 'global' dataset from wp-config.php for instant compatibility. Remove it when you put your settings here.
// add_db_server('global', 0,    '', 1, 1,    DB_HOST, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

// build the shard dataset
$have_slaves = is_array($slaves) && (count($slaves) > 0);
if(isset($shardb_hash_length) && $shardb_hash_length > 0 && $shardb_hash_length < 4 ) {
	$fmt = "{$shardb_prefix}%0{$shardb_hash_length}x";
	$shardb_num_db = 1 << ($shardb_hash_length*4);
	if(!$have_slaves || !isset( $shardb_master_read )) {
		$shardb_master_read = 1;
	}
	for($d=0;$d<$shardb_num_db;$d++) {
		$db_name = sprintf($fmt, $d);
		add_db_server($shardb_dataset, $d,  '', 1, $shardb_master_read,  DB_HOST,  DB_HOST, $db_name,  DB_USER,     DB_PASSWORD);
		if($have_slaves) {
			foreach($slaves as $s) {
				add_db_server($shardb_dataset, $d,  '', $s['read'], 0,  $s['host'],  $s['lhost'], $db_name,  $s['user'], $s['password']);
			}
		}
	}
	$numdbs_added = $shardb_num_db;
	
	// Enable home db?
    if ($enable_home_db === true) {
        add_db_server($shardb_dataset, $numdbs_added, '', 1, $shardb_master_read, DB_HOST, DB_HOST, $shardb_prefix .'home', DB_USER, DB_PASSWORD);
		if($have_slaves) {
			foreach($slaves as $s) {
				add_db_server($shardb_dataset, $numdbs_added,  '', $s['read'], 0,  $s['host'],  $s['lhost'], $shardb_prefix .'home',  $s['user'], $s['password']);
			}
		}
    }
    $numdbs_added++;
	// VIP databases
    if ( is_numeric($num_vipdbs) and $num_vipdbs > 0 ) {
        for($d=1;$d<=$num_vipdbs;$d++) {
            add_db_server($shardb_dataset, $numdbs_added, '', 1, $shardb_master_read, DB_HOST, DB_HOST, $shardb_prefix .'vip'. $d, DB_USER, DB_PASSWORD);
			if($have_slaves) {
				foreach($slaves as $s) {
					add_db_server($shardb_dataset, $numdbs_added,  '', $s['read'], 0,  $s['host'],  $s['lhost'], $db_name,  $s['user'], $s['password']);
				}
			}
		   $numdbs_added++;
        }
    }
}
/*
add_db_server(  'misc', 0, 'lax', 1, 1,     'misc.db.example.com:3722',     'misc.db.example.lan:3722',  'wp-misc',  'miscuser',  'miscpassword');
add_db_server('global', 0, 'nyc', 1, 1,'global.mysql.example.com:3509','global.mysql.example.lan:3509','global-db','globaluser','globalpassword');
*/

/**
 * Map a table to a partition.
 *
 * @param string $table
 * @param string $part
 */
function add_db_table( $table, $db ) {
	global $db_tables, $shardb_prefix;

	$db_tables[$table] = $shardb_prefix . $db;
}

// ** NO DUPLICATE TABLE NAMES ALLOWED **
// If running with the home DB enabled & a WP 3.0 single install converted to a network add any main site plugin tables here
// If you want tables to live in a specific database, you can add those here
// add_db_table( 'wp_misc', 'home' );
// add_db_table( 'wp_etc', 'vip1' );
// add_db_table( 'wp_extra', 'vip1' );


/**
 * Map a blog to a custom database. AKA: VIP
 *
 * @param string $blog_id
 * @param string $db
 */
function add_vip_blog( $blog_id, $db ) {
    global $vip_db, $shardb_prefix;
    $vip_db[$blog_id] = $shardb_prefix . $db;
}

// Adding a blog to a VIP database is simple.
// You can put it in whatever VIP DB you want, or even in
// another user db (like for a penalty box for low traffic blogs.

// Simply use this format:
// add_vip_blog($blog_id, 'db_name');

// For example, lets say your buddy John needs some extra db love,
// his blog_id is 24 and you want to put him in the vip3 database.
// You would add a line below like this:
//add_vip_blog(24,'vip3'); // John's blog

// That's all there is to it. Plus a comment at the end reminds you of who is who!


// VIP DB's
if ( $enable_home_db === true )
	add_vip_blog( 1, 'home' );    // home blog
?>
