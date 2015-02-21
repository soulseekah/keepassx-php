<?php
	/**
	 * KeepAss WebDAV Gateway
	 *
	 * Allows you to use KeepassX http://www.keepassx.org/ and
	 * derivations thereof (keepass2android, etc.)
	 * with databases on your remote server over the WebDAV protocol.
	 *
	 * Create your kdbx database files and upload them to DB_DIR.
	 * Set path to https://remote/path/to/vault.php?db=passwords.kdbx
	 * Use with care and don't get hacked.
	 *
	 * Use HTTPS, configure BasicAuth on your web server.
	 */
	
	/** Configuration */
	/** The kdbx vault directory */
	define( 'DB_DIR', dirname( __FILE__ ) );
	
	trigger_error( var_export( $_SERVER, true ) );

	/** What are we working on here? */
	if ( !isset( $_GET['db'] ) ) {
		http_response_code( 404 );
		exit;
	}

	/** Don't allow directory traversal, thanks... */
	$db = DB_DIR . '/' . pathinfo( $_GET['db'], PATHINFO_BASENAME );
	if ( !file_exists( $db ) && pathinfo( $_GET['db'], PATHINFO_EXTENSION ) != 'tmp' ) {
		/** Only allow existing databases to be used, unless it's a tmp being created. */
		http_response_code( 404 );
		exit;
	}

	/** Handle the VERBS */
	switch ( $_SERVER['REQUEST_METHOD'] ):
		case 'GET':
			print( file_get_contents( $db ) );
			http_response_code( 200 );
			exit;
		case 'PUT':
			$dbf = fopen( $db, 'wb' );
			/** Lock and load */
			if ( flock( $dbf, LOCK_EX ) ) {
				fwrite( $dbf, file_get_contents( 'php://input' ) );
				fflush( $dbf );
				flock( $dbf, LOCK_UN );
				fclose( $dbf );
			} else {
				/** Could not acquire lock, conflict! */
				http_response_code( 409 );
				exit;
			}
			http_response_code( 200 );
			exit;
		case 'DELETE':
			unlink( $db );
			http_response_code( 200 );
			exit;
		case 'MOVE':
			if ( !isset( $_SERVER['HTTP_DESTINATION'] ) ) {
				http_response_code( 404 );
				exit;
			}
			parse_str( parse_url( $_SERVER['HTTP_DESTINATION'], PHP_URL_QUERY ), $destination );
			trigger_error( var_export( $destination, true ) );
			if ( !isset( $destination['db'] ) ) {
				http_response_code( 404 );
				exit;
			}
			$destination = DB_DIR . '/' . pathinfo( $destination['db'], PATHINFO_BASENAME );
			rename( $db, $destination );
			http_response_code( 200 );
			exit;
		default:
			http_response_code( 405 );
			exit;
	endswitch;
?>
