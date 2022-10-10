<?php

namespace WHMCS\Module\Registrar\Arsys\Services;

use WHMCS\Module\Registrar\Arsys\Services\Contracts\APIService_Interface;
use WHMCS\Module\Registrar\Arsys\Helpers\API;
use WHMCS\Module\Registrar\Arsys\Cli\Output;
use Exception;

class API_Service implements APIService_Interface {
	protected $app;
	protected $api;

	/**
	 * Sets API attribute
	 *
	 * @param array $options API Options
	 * @param WHMCS\Module\Registrar\Arsys\App App
	 */
	public function __construct( array $options = [], $app ) {
		$this->api = new API( $options );
		$this->app = $app;
        global $_LANG;
        $lang = $GLOBALS['aInt']->language ?? 'english';
        require_once $app->getDir() . "/lang/overrides/$lang.php";
	}

	/**
	 * Gets App
	 *
	 * @return WHMCS\Module\Registrar\Arsys\App
	 */
	public function getApp() {
		return $this->app;
	}

	/**
	 * Gets API attribute
	 *
	 * @return WHMCS\Module\Registrar\Arsys\Helpers\API
	 */
	public function getApi() {
		return $this->api;
	}

	/**
	 * Retrieves API Connection
	 *
	 * @return \Arsys\API\API
	 */
	public function getApiConnection() {
		return $this->getApi()->getConnection();
	}

	/**
	 * Log action and return response if valid
	 * @see https://developers.whmcs.com/provisioning-modules/module-logging/
	 *
	 * @param \Arsys\API\Response\Response $response API Response
	 * @param array $params Params for logging
	 *
	 * @throws \Exception If response is not success
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function parseResponse( $response, array $params ) {
		// Call internal WHMCS function logModuleCall
		if ( function_exists( 'logModuleCall' ) ) {
			logModuleCall( $this->getApp()->getName(), $response->getAction(), $params, $response->getRawResponse(),
				$response->getArray() );
		}

		if ( ! $response->getSuccess() ) {
			throw new Exception( $response->getErrorCodeMsg(), $response->getErrorCode() );
		}

		return $response;
	}

	/**
	 * Retrieves domain list from api
	 *
	 * @return array Domain list
	 */
	public function getDomainList() {
		$domains = [];

		try {
			do {
				$response = $this->getApiConnection()->domain_list();

				$this->parseResponse( $response, [] );

				$info  = $response->get( "queryInfo" );
				$total = $info['total'];

				$domainList = $response->get( "domains" );

				$domains = array_merge( $domains, $domainList );
			} while ( count( $domains ) < $total );
		} catch ( Exception $e ) {
			Output::line( "" );
			Output::line( "There was an error fetching information: " . $e->getMessage() );
		}

		return $domains;
	}

	/**
	 * Retrieves Domain Info
	 *
	 * @param string $domain Domain
	 * @param string $infoType Info type
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function getDomainInfo( $domain, $infoType ) {
		$response = $this->getApiConnection()->domain_getInfo( $domain, [ 'infoType' => $infoType ] );

		return $this->parseResponse( $response, [ 'domain' => $domain, [ 'infoType' => $infoType ] ] );
	}

	/**
	 * Checks if Domain is available for register
	 *
	 * @param string $domain Domain
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function checkDomain( $domain ) {
		$response = $this->getApiConnection()->domain_check( $domain );

		return $this->parseResponse( $response, [ $domain ] );
	}

	/**
	 * Creates Domain
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function createDomain( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_create( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Transfers Domain
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function transferDomain( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_transfer( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Renews Domain
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function renewDomain( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_renew( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Updates Domain
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function updateDomain( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_update( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Get Nameservers
	 *
	 * @param string $domain Domain
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function getNameservers( $domain ) {
		$response = $this->getApiConnection()->domain_getNameServers( $domain );
		if( empty( $response ) ) {
            // HACER traducir este mensaje, ver que hay en globales.
			throw new Exception( arsys_translate( 'sorrywecouldntservername','No hemos podido leer los server names' ), '100' );
		}
		return $this->parseResponse( $response, [ 'domain' => $domain ] );
	}

	/**
	 * Update locked Domain
	 *
	 * @param string $domain Domain
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function updateLockedDomain( $domain, $locked ) {
		$response = $this->getApiConnection()->domain_updateLockedDomain( $domain, $locked );

		return $this->parseResponse( $response, [ 'domain' => $domain ] );
	}

	/**
	 * Updates Nameservers
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function updateNameservers( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_updateNameServers( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Updates Contact Details
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function updateContactDetails( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_updateContacts( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Gets EPP Code (Auth Code)
	 *
	 * @param string $domain Domain
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function getEppCode( $domain ) {
		$response = $this->getApiConnection()->domain_getAuthCode( $domain );

		return $this->parseResponse( $response, [ $domain ] );
	}

	/**
	 * Creates Glue Record
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function createGlueRecord( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_glueRecordCreate( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Updates Glue Records
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function updateGlueRecord( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_glueRecordUpdate( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Deletes Glue Record
	 *
	 * @param string $domain Domain
	 * @param array $params Parameters
	 *
	 * @return \Arsys\API\Repsonse\Response
	 */
	public function deleteGlueRecord( $domain, array $params ) {
		$response = $this->getApiConnection()->domain_glueRecordDelete( $domain, $params );

		return $this->parseResponse( $response, [ 'domain' => $domain, 'params' => $params ] );
	}

	/**
	 * Get Domain Suggestions
	 *
	 * @see https://dev.arsys.com/api/docs/sdk-php/#tool-domainsuggests
	 *
	 * @param string $query Text to check suggestions
	 * @param string $language Language suggestions
	 * @param array $tlds
	 *
	 * @return \Arsys\API\Response\Response
	 */
	public function getDomainSuggestions( $query = '', $language = '', array $tlds = [] ) {
		$params = [];

		if ( ! empty( $query ) ) {
			$params['query'] = $query;
		}

		if ( ! empty( $language ) ) {
			$params['language'] = $language;
		}

		if ( ! empty( $tlds ) ) {
			$params['tlds'] = implode( ",", $tlds );
		}

		$response = $this->getApiConnection()->domain_domainSuggests( $params );

		return $this->parseResponse( $response, $params );
	}
}
