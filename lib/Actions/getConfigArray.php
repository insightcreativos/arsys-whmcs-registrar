<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

use WHMCS\Database\Capsule;

class getConfigArray extends Action {
	public function __invoke() {
		// Get Custom Fields
		$customFields = $this->app->getService( 'whmcs' )->getCustomFieldsByType( 'client' );

		// Try to get User and Password from Addon Module
		if ( Capsule::schema()->hasTable( 'mod_arsys_settings' ) ) {
			$api_username = Capsule::table( 'mod_arsys_settings' )->where( 'key', 'api_username' )->value( 'value' );
			$api_password = Capsule::table( 'mod_arsys_settings' )->where( 'key', 'api_password' )->value( 'value' );
		} else {
			$api_username = "";
			$api_password = "";
		}

		if ( ! empty( $api_password ) ) {
			$api_password = base64_decode( $api_password );
		}

		return [
			"FriendlyName"            => [
				"Type"  => "System",
				"Value" => "Arsys"
			],
			"Description"             => [
				"Type"  => "System",
				"Value" => "Register domains with Arsys! Signup at <a href='https://www.arsys.es/dominios'>https://www.arsys.es/dominios</a></strong>"
			],
			//API login details
			"apiuser"                 => [
				"FriendlyName" => "GMD API Key",
				"Type"         => "text",
				"Size"         => "25",
				"Description"  => "Enter your GMD API key here",
				"Default"      => $api_username
			],
//			"apipasswd"               => [
//				"FriendlyName" => "API Password",
//				"Type"         => "password",
//				"Size"         => "25",
//				"Description"  => "Enter your API Password here",
//				"Default"      => $api_password
//			],
		];
	}
}
