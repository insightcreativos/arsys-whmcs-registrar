<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use Exception;

class SaveRegistrarLock extends Action {
	protected $errors = [];
	protected $domain = '';

	public function __invoke() {
		$response = $this->app->getService( 'api' )->getDomainInfo( $this->domain, 'status' );
		$state    = $response->get( "state" );
		$locked   = 5 == intval( $state['id'] ); // Está ahora mismo bloqueado

		$this->app->getService( 'api' )
			->updateLockedDomain( $this->domain, ! $locked );

	}
}
