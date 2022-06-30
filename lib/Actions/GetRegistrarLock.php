<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

class GetRegistrarLock extends Action {
	public function __invoke() {
		$response = $this->app->getService( 'api' )->getDomainInfo( $this->domain, 'status' );
		$state    = $response->get( "state" );
		if ( 5 == intval( $state['id'] ) ) {
			return "locked";
		}

		return "unlocked";
	}
}
