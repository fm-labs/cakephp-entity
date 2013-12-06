<?php
App::uses('EntitySet', 'Entity.Lib');

class EntitySetTest extends CakeTestCase {

/**
 * @var TestEntitySet
 */
	public $Set;

	public function setUp() {
		parent::setUp();

		$this->Set = new TestEntitySet();
	}

	public function testMap() {
		$data = array('id' => 'Lorem ipsum');
		$this->Set->map('Test', $data);

		$entities = $this->Set->getEntities();
		$this->assertTrue(array_key_exists('Test', $entities));
	}

	public function testCheck() {

	}

	public function testGet() {

	}

	public function testMagicGet() {

	}

	public function testMagicIsset() {

	}

	public function testArrayAccessGet() {

	}

	public function testArrayAccessExists() {

	}

	public function testArrayAccessSet() {

	}

	public function testArrayAccessUnset() {

	}
}

class TestEntitySet extends EntitySet {

	public function getEntities() {
		return $this->_entities;
	}
}