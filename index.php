<?php

error_reporting( -1 );
ini_set( 'display_errors', 'on' );

use U232\Config;
use U232\DB;

require_once 'app/config.php';
require_once 'app/init.php';

// this way
$gender = 'Female';
$sarah = 'Sarah';
$id = 1;
$sel = DB::getInstance()->prepare( '
	SELECT
		id, first_name, last_name
	FROM users
	WHERE
		gender = ? AND
		first_name != ? AND
		id > ?
	LIMIT 10
' );
$sel->bind_param( 'ssi', $sarah, $id );

// or this way
$sel = DB::getInstance()->prepare( '
	SELECT
		id, first_name, last_name
	FROM users
	WHERE
		gender = ? AND
		first_name != ? AND
		id > ?
	LIMIT 10
' );
$sel->bind( 'ssi', [ 'Female', 'Sarah', 1 ] );

// or this way
$sel = DB::prepare( '
		SELECT
			id, first_name, last_name
		FROM users
		WHERE
		gender = ? AND
			first_name != ? AND
			id > ?
		LIMIT 10
	' )
	->bind( 'ssi', [ 'Female', 'Sarah', 1 ] );

// normal execution
$sel->execute();
// bind_result( ... &$vars ) or simply create a single refrence array with the results
$sel->result( $row );
// normal execution
while( $sel->fetch() ){
	extract( $row );
	print_r( $id . ' / ' );
	print_r( $first_name . ' / ' );
	print_r( $last_name . "\n" );
	print_r( $row );

}
$sel->free_result();
$sel->close();
