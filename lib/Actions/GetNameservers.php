<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use Arsys\API\Response\Response;

class GetNameservers extends Action {
	public function __invoke() {
		/** @var Response $response */
		$response = $this->app->getService( 'api' )->getNameServers( $this->domain );

		$result = [];

		foreach ( $response->getResponseData() as $key => $nameserver ) {
			if ( $key <= 5 ) {
				$result[ "ns" . ($key+1) ] = $nameserver["name"];
			}
		}

		return $result;
	}
}
