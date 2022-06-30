<?php

namespace WHMCS\Module\Registrar\Arsys\Services\Contracts;

interface ImportService_Interface {
	public function sync();

	public static function displayVersion();
}
