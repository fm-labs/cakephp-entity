<?php
class AllEntityTest extends PHPUnit_Framework_TestSuite {

/**
 * Entity Plugin test suite
 *
 * @return void
 */
	public static function suite() {
		$caseDir = dirname(__FILE__) . DS;

		$suite = new CakeTestSuite('All Entity plugin tests');
		$suite->addTestDirectoryRecursive($caseDir . 'Lib');
		$suite->addTestDirectoryRecursive($caseDir . 'Model');
		return $suite;
	}
}