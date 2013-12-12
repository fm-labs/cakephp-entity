<?php
App::uses('EntityData', 'Entity.Lib');

class EntityDataTest extends CakeTestCase {

/**
 * @var TestEntityData
 */
	public $Set;

	public function setUp() {
		parent::setUp();

		$this->Set = new TestEntityData();
	}

	public function testMap() {
		$data = array('id' => 'Lorem ipsum');
		$this->Set->map('Test', $data);

		$entities = $this->Set->getEntities();
		$this->assertTrue(array_key_exists('Test', $entities));
	}

	public function testCheck() {
		$this->assertEqual($this->Set->check('Test'), false);

		$this->Set->map('Test', array());
		$this->assertEqual($this->Set->check('Test'), true);

		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual($this->Set->check('Test'), true);
	}

	public function testGet() {
		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual($this->Set->get('NotExistant'), null);
		$this->assertEqual($this->Set->get('Test'), array('id' => 1));
	}

	public function testMagicGet() {
		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual($this->Set->NotExistant, null);
		$this->assertEqual($this->Set->Test, array('id' => 1));
	}

	public function testMagicIsset() {
		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual(isset($this->Set->NotExistant), false);
		$this->assertEqual(isset($this->Set->Test), true);
	}

	public function testArrayAccessGet() {
		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual($this->Set['NotExistant'], null);
		$this->assertEqual($this->Set['Test'], array('id' => 1));
		$this->assertEqual($this->Set['Test']['id'], 1);
	}

	public function testArrayAccessExists() {
		$this->Set->map('Test', array('id' => 1));
		$this->assertEqual(isset($this->Set['NotExistant']), false);
		$this->assertEqual(isset($this->Set['Test']), true);
		$this->assertEqual(isset($this->Set['Test']['id']), true);
		$this->assertEqual(isset($this->Set['Test']['not_existant']), false);
	}

	public function testArrayAccessSet() {
		$this->Set['Test'] = array('id' => 1);
		$this->assertEqual($this->Set->get('Test'), null);
	}

	public function testArrayAccessUnset() {
		$this->Set->map('Test', array('id' => 1));
		unset($this->Set['Test']);
		$this->assertEqual($this->Set->check('Test'), true);
	}

}

class TestEntityData extends EntityData {

	public function getEntities() {
		return $this->_entities;
	}
}