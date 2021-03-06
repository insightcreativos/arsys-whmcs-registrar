<?php

/**
 * Arsys Domain Importer for WHMCS
 * Synchronization tool for domains in Arsys accounts and WHMCS.
 * @copyright Soluciones Corporativas IP, SL 2015
 * @package ArsysWHMCSImporter
 */

require_once implode( DIRECTORY_SEPARATOR, [ __DIR__, '..', '..', '..', 'init.php' ] );
require_once implode( DIRECTORY_SEPARATOR, [ __DIR__, 'lib', 'autoloader.php' ] );

use WHMCS\Module\Registrar\Arsys\Cli\Arguments;
use WHMCS\Module\Registrar\Arsys\Cli\Output;
use WHMCS\Module\Registrar\Arsys\App;
use WHMCS\Module\Registrar\Arsys\Services\Import_Service;
use WHMCS\Module\Registrar\Arsys\Services\API_Service;

$arguments = new Arguments();

$arguments->addOption( [ 'username', 'u' ], null, 'Arsys API Username (Required)' );
$arguments->addOption( [ 'password', 'p' ], null, 'Arsys API Password (Required)' );
$arguments->addOption( 'uid', null, 'Default Client Id (Required)' );
$arguments->addOption( [ 'output', 'o' ], "php://stdout", 'Filename to output data - Defaults to STDOUT' );

$arguments->addFlag( 'forceUID', 'Use the default Client Id for all domains' );
$arguments->addFlag( 'dry', 'Do not make any changes to the database' );
$arguments->addFlag( [ 'verbose', 'v' ], 'Display extra output' );
$arguments->addFlag( [ 'debug', 'd' ], 'Display cURL debug information' );
$arguments->addFlag( [ 'silent', 's' ], 'No output' );
$arguments->addFlag( 'version', 'Version information' );
$arguments->addFlag( [ 'help', 'h' ], 'This information' );

$arguments->parse();

//¿Enable Silent mode?
if ( $arguments->get( 'silent' ) ) {
	Output::setSilent( true );
}

//Set output file/method
Output::setOutput( $arguments->get( 'output' ) );

//Check required arguments
//If an argument is missing, show help screen.
//Also show help screen with --help (-h) flag.
$missingRequiredArguments = ! $arguments->get( 'username' ) || ! $arguments->get( 'password' ) || ! $arguments->get( 'uid' );

if ( ( $missingRequiredArguments || $arguments->get( 'help' ) && ! $arguments->get( '$version' ) ) ) {
	$arguments->helpScreen();
	Output::line( "" );
	exit();
}

//Display version information
if ( $arguments->get( 'version' ) ) {
	Output::debug( "Version information requested" );
	Import_Service::displayVersion();
	exit();
}

//¿Is the "verbose" flag set?
//If so, enable verbose mode
if ( $arguments->get( 'verbose' ) ) {
	Output::setDebug( true );
}

/*
 * Init DD API SDK
 */
Output::debug( "Initializing Services" );

$app = new App();

//The Arsys API Client
$api = new API_Service( [
	'apiuser'      => $arguments->get( 'username' ),
	'apipasswd'    => $arguments->get( 'password' ),
	'debug'        => ( $arguments->get( 'debug' ) && ! $arguments->get( 'silent' ) ) ? true : false,
	'autoValidate' => true,
	'versionCheck' => true,
	'response'     => [
		'throwExceptions' => false
	]
], $app );
$app->setAPIService( $api );

$import = new Import_Service( [
	'clientId'      => $arguments->get( 'uid' ),            //Default WHMCS client ID
	'dryrun'        => $arguments->get( 'dry' ),                //Dry run - makes no changes to database
	'forceClientId' => $arguments->get( 'forceUID' )    //Always use default WHMCS Client ID for all operations
], $app );
$app->setImportService( $import );

/*
 * Start sinchronization.
 */
Output::debug( "Initializing Sync" );

$app->getService( 'import' )->sync();
