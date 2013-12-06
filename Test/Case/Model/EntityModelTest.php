<?php
App::uses('Model', 'Model');
App::uses('Entity', 'Entity.Lib');

class EntityModelTest extends CakeTestCase {

	public $fixtures = array(
		'plugin.entity.entity_model',
		'plugin.entity.entity_parent_model',
	);

	public function setUp() {
		parent::setUp();

		$this->Model = new EntityModel(null, null, 'test');
		$this->Model->Behaviors->attach('Entity.Entity', array(
			'entity' => 'Entity.EntityModel',
			'entityClass' => 'EntityModelEntity',
			'entityLocation' => false
		));
	}

	public function testEntitySettings() {
		$settings = $this->Model->entitySettings();
		$expected = array(
			'entity' => 'EntityModel',
			'entityClass' => 'EntityModelEntity',
			'entityLocation' => 'Model/Entity'
		);
		$this->assertEqual($settings, $expected);
	}

	public function testFindFirst() {
		$result = $this->Model->find('first');
		$this->assertTrue(is_object($result), 'Result is not an object');
		$this->assertTrue(is_a($result, 'EntitySet', 'Result is not an instance of EntityModelEntity'));

		// magic access
		$this->assertTrue(isset($result->EntityModel));
		$this->assertTrue(isset($result->EntityModel->id));
		$this->assertTrue(isset($result->EntityParentModel));
		$this->assertTrue(isset($result->EntityParentModel->id));

		// array access
		$this->assertTrue(isset($result['EntityModel']));
		$this->assertTrue(isset($result['EntityModel']['id']));
		$this->assertTrue(isset($result['EntityParentModel']));
		$this->assertTrue(isset($result['EntityParentModel']['id']));
	}

	public function testFindAll() {
		$results = $this->Model->find('all');
		$this->assertTrue(is_array($results), 'Result is not an object');
		foreach ($results as $r) {
			$this->assertTrue(is_object($r), 'Result is not an object');
			$this->assertTrue(is_a($r, 'EntitySet', 'Result is not an instance of EntityModelEntity'));

			// magic access
			$this->assertTrue(isset($r->EntityModel));
			$this->assertTrue(isset($r->EntityModel->id));
			$this->assertTrue(isset($r->EntityParentModel));
			$this->assertTrue(isset($r->EntityParentModel->id));

			// array access
			$this->assertTrue(isset($r['EntityModel']));
			$this->assertTrue(isset($r['EntityModel']['id']));
			$this->assertTrue(isset($r['EntityParentModel']));
			$this->assertTrue(isset($r['EntityParentModel']['id']));
		}
	}
}

class EntityModel extends Model {

	public $belongsTo = array(
		'EntityParentModel' => array(
			'className' => 'EntityParentModel'
		)
	);
}

class EntityParentModel extends Model {

	public $actsAs = array('Entity');

	public $entity = array(
		'entity' => 'Entity.EntityParentModel',
		'entityClass' => 'EntityParentModelEntity',
		'entityLocation' => false
	);

	public $hasMany = array(
		'EntityModel' => array(
			'className' => 'EntityModel'
		)
	);
}

class EntityModelEntity extends Entity {

	public $id;

	public $entityParentModelId;

	public $name;

	public $authorId;

	public $published;
}

class EntityParentModelEntity extends Entity {

	public $id;

	public $name;
}