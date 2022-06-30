<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

class DeleteNameserver extends Action {
	public function __invoke() {
		$fields = [
			'name' => $this->params['nameserver']
		];

		$this->app->getService( 'api' )->deleteGlueRecord( $this->domain, $fields );
	}
}
