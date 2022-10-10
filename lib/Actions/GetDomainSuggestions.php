<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Domains\DomainLookup\ResultsList;

class GetDomainSuggestions extends Action {
	public function __invoke() {
		$query              = $this->getParam( 'searchTerm' );
		$tlds               = $this->getParam( 'tldsToInclude' );
		$suggestionSettings = $this->getParam( 'suggestionSettings' );

		$language = ( is_array( $suggestionSettings ) && array_key_exists( 'language',
				$suggestionSettings ) ) ? $suggestionSettings['language'] : '';

		/** @var \Arsys\API\Response\Response $response */
		$response = $this->getApp()->getService( 'api' )->getDomainSuggestions( $query, $language, $tlds );
		$return   = new ResultsList();

		foreach ( $response->getResponseData() as $key => $tlds ) {
			$domain       = 'http://' . $tlds['domain'];
			$array        = explode( '.', parse_url( $domain, PHP_URL_HOST ) );
			$sld          = end( $array );
			$searchResult = new SearchResult( $array[0], $array[1] );
			$status       = $tlds['state'] == 'available' ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED;
			$searchResult->setStatus( $status );
			$return->append( $searchResult );
		}

		return $return;
	}
}
