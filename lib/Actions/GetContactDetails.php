<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use Exception;

class GetContactDetails extends Action {
	public function __invoke() {
		$response = $this->app->getService( 'api' )->getDomainInfo( $this->domain, 'contact' );

		$result = [];

		if ( empty( $this->params['ownerContact'] ) || $this->params['allowOwnerContactUpdate'] === 'on' ) {
			$result['Registrant'] = static::filterContactFromResponse( $response->get( 'contactOwner' ) );
		}

		if ( empty( $this->params['adminContact'] ) || $this->params['allowAdminContactUpdate'] === 'on' ) {
			$result['Admin'] = static::filterContactFromResponse( $response->get( 'contactAdmin' ) );
		}

		if ( empty( $this->params['techContact'] ) || $this->params['allowTechContactUpdate'] === 'on' ) {
			$result['Tech'] = static::filterContactFromResponse( $response->get( 'contactTech' ) );
		}

		if ( count( $result ) == 0 ) {
			throw new Exception( 'Contact modification is disabled. Contact support for more information.' );
		}
		return $result;
	}

	protected static function filterContactFromResponse( $response ) {
		return [
			'First Name'    => $response['name'],
			'Last Name'     => '', // No hay lastname en lo que devuelve ARSYS.
			'Company Name'  => $response['business'],
			'Email Address' => $response['email'],
			'Address'       => $response['address'],
			'City'          => $response['city'],
			'State'         => $response['province'],
			'Zip Code'      => $response['postal_code'],
			'Country'       => $response['country'],
			'Phone Number'  => $response['phone'],
			'VAT Number'    => '' // No hay info en lo que devuelve ARSYS.
		];
	}
}
