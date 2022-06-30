<?php

namespace WHMCS\Module\Registrar\Arsys\Cli;

use WHMCS\Module\Registrar\Arsys\Output;

class Log {
	/**
	 * Stored messages.
	 * @var array
	 */
	protected $log = array();

	/**
	 * Add a message to the log.
	 *
	 * @param string $msg Message to add
	 */
	public function add( $msg ) {
		$this->log[] = $msg;
	}

	/**
	 * Write the entire log at once.
	 */
	public function write() {
		if ( count( $this->log ) == 0 ) {
			Output::line( "No errors found." );

			return;
		}

		foreach ( $this->log as $msg ) {
			Output::error( $msg );
		}
	}
}
