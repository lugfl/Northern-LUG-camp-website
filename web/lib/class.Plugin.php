<?php

abstract class Plugin {

	const OUTPUT_METHOD_BUILDIN = 0;
	const OUTPUT_METHOD_SMARTY = 1;

	/**
	 * Read all required GET/POST Variables, but do nothing with ut
	 */
	abstract public function readInput();

	/**
	 * Process variables from readInput() and exec Changes or read Content
   */
	abstract public function processInput();

	/**
	 * Return the Method for Output-Generated.
   *
   * currently only buildin with getOutput() or Smarty-based
	 */
	abstract public function getOutputMethod();

	protected function checkMaintenance() {
		global $MAINTENANCE_MODE;
		$ret = FALSE;
		if(isset($MAINTENANCE_MODE) && $MAINTENANCE_MODE) {
			$ret = TRUE;
		}
		return $ret;
	}
}

?>
