<?php

namespace WHMCS\Module\Registrar\Arsys\Actions;

class ClientAreaCustomButtonArray extends Action {
	public function __invoke() {
		return [
			"WHOIS Privacy" => "whoisPrivacy"
		];
	}
}
