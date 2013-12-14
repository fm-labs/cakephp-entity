<?php
App::uses('Entity', 'Entity.Lib');
App::uses('Model', 'Model');

class EntityTest extends CakeTestCase {

	public function testSet() {
		$post = new PostEntity();

		// set key-value-pair
		$post->set('id', 99);
		$post->set('name', 'Test');
		$post->set('author_id', 13);
		$this->assertEqual($post->id, 99);
		$this->assertEqual($post->name, 'Test');
		$this->assertEqual($post->authorId, 13);

		// set multiple
		$post->set(array('id' => 88, 'name' => 'Test2', 'author_id' => 31));
		$this->assertEqual($post->id, 88);
		$this->assertEqual($post->name, 'Test2');
		$this->assertEqual($post->authorId, 31);
	}

	public function testGet() {
		$post = new PostEntity();

		$post->set('id', 99);
		$post->set('name', 'Test');
		$post->set('author_id', 13);
		$this->assertEqual($post->get('id'), 99);
		$this->assertEqual($post->get('name'), 'Test');
		$this->assertEqual($post->get('author_id'), 13);
		//$this->assertEqual($post->get(), array('id'=>99,'name' => 'Test','author_id'=>null,'published'=>null));
	}

	public function testToArray() {
		$post = new PostEntity();
		$post->set('id', 99);
		$post->set('name', 'Test');
		$post->set('author_id', 13);
		$this->assertEqual($post->toArray(),
			array('id' => 99, 'name' => 'Test', 'author_id' => 13, 'published' => null));
	}

	public function testToArrayInclude() {
		$post = new PostEntity();
		$post->set('id', 99);
		$post->set('name', 'Test');
		$this->assertEqual($post->toArray(array('id', 'name')),
			array('id' => 99, 'name' => 'Test'));
	}

	public function testToArrayExclude() {
		$post = new PostEntity();
		$post->set('id', 99);
		$post->set('name', 'Test');
		$this->assertEqual($post->toArray(array(), array('author_id', 'published')),
			array('id' => 99, 'name' => 'Test'));
	}

	public function testToArrayIncludeExclude() {
		$include = array('id', 'author_id');
		$exclude = array('id');

		$post = new PostEntity();
		$post->set('id', 99);
		$post->set('name', 'Test');
		$post->set('author_id', 13);

		$result = $post->toArray($include, $exclude);
		$expected = array('author_id' => 13);
		$this->assertEqual($result, $expected);
	}

	public function testArrayAccessGet() {
		$post = new PostEntity();
		$post->set('id', 99);
		$post->set('name', 'Test');
		$post->set('author_id', 13);

		$this->assertEqual($post['id'], 99);
		$this->assertEqual($post['name'], 'Test');
		$this->assertEqual($post['author_id'], 13);

		$this->assertEqual(
			$post['Post'],
			array('id' => 99, 'name' => 'Test', 'author_id' => 13, 'published' => null)
		);

		$this->assertEqual($post['Post']['id'], 99);
		$this->assertEqual($post['Post']['name'], 'Test');
		$this->assertEqual($post['Post']['author_id'], 13);
	}

	public function testArrayAccessSet() {
		$post = new PostEntity();
		$post['id'] = 1;
		$post['name'] = 'Test';
		$post['author_id'] = 13;
		$this->assertEqual($post->id, 1);
		$this->assertEqual($post->name, 'Test');
		$this->assertEqual($post->authorId, 13);

		//$post['Post']['id'] = 99;
		//$this->assertEqual($post->id, 99);
	}

	public function testArrayAccessUnset() {
		$post = new PostEntity();
		$post['author_id'] = 1;
		unset($post['author_id']);
		$this->assertEqual($post->authorId, null);
	}

	public function testArrayAccessExists() {
		$post = new PostEntity();
		$post['author_id'] = 1;
		$this->assertEqual(isset($post['author_id']), true);
		$this->assertEqual(isset($post['not_set']), false);
	}

	public function testGetModel() {
		$post = new PostEntity();
		$result = $post->getModel();
		$this->assertTrue(is_a($result, 'AppModel'));
	}

	public function testGetModelUsingNoModelEntity() {
		$entity = new NoModelEntity();
		$this->expectException();
		$entity->getModel();
	}

	public function testDependencyInjection() {
		$Post = ClassRegistry::init('PostModel');
		$post = new PostEntity($Post);
		$this->assertTrue(is_a($post->getModel(), 'PostModel'));
	}

	public function testValidate() {
		$Post = ClassRegistry::init('PostModel');
		$Post->validator()->add('name', array('rule' => 'notEmpty'));

		$post = new PostEntity($Post);
		$post->validate();

		$errors = $post->getValidationErrors();
		$this->assertTrue(isset($errors['name']));
	}
}

class PostEntity extends Entity {

	public $id;

	public $name;

	public $authorId;

	public $published;

}

class AuthorEntity extends Entity {

	public $id;

	public $name;

}

class CommentEntity extends Entity {

	public $id;

	public $postId;

	public $text;
}

class NoModelEntity extends Entity {

	protected $_useModel = false;
}

class PostModel extends Model {

	public $alias = 'Post';

	public $useTable = false;

}
