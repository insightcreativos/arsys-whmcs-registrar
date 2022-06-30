<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use Exception;

class RegisterDomain extends Action {
	public function __invoke() {
//		$check          = $this->app->getService( 'api' )->checkDomain( $this->domain );

//		$premiumEnabled = (bool) $this->getParam( 'premiumEnabled' );

//		$domains = $check->get( 'purchased' ) === 1;

//		if ( ! $domains ) {
//			throw new Exception( 'Domain already taken' );
//		}

//		if ( ! $premiumEnabled && $domains[0]['premium'] ) {
//			throw new Exception( 'Cannot register premium domains' );
//		}

		$fields = [];

		// Nameservers
		$fields = array_merge( $fields, $this->getNameserversFromParams() );

		// Contact data
		$fields = array_merge( $fields, $this->getContactDataFromParams() );

		// TLD Requirements
		$fields = array_merge( $fields, $this->getTldDataFromParams( $this->params['tld'], $this->params ) );

		// Period
		$fields['period'] = intval( $this->params['regperiod'] );

		// Premium
		$fields['premium'] = false;

		try {
			$this->app->getService( 'api' )->createDomain( $this->domain, $fields );
		} catch ( Exception $e ) {
			// 1100 = Insufficient Balance
			if ( $e->getCode() == 1100 ) {
				throw new Exception( 'Error registering domain. Please, try again later.', $e->getCode(), $e );
			}

			throw $e;
		}
	}
}
