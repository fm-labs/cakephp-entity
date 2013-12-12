<?php
class EntityData implements ArrayAccess {

	protected $_entities = array();

	public function map($alias, $entity) {
		$this->_entities[$alias] = $entity;
	}

	public function check($alias) {
		if (array_key_exists($alias, $this->_entities)) {
			return true;
		}
		return false;
	}

	public function get($alias) {
		if ($this->check($alias)) {
			return $this->_entities[$alias];
		}
		return null;
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function __isset($key) {
		return $this->check($key);
	}

	public function offsetSet($offset, $value) {
		return; // Disabled
	}

	public function offsetUnset($offset) {
		return; // Disabled
	}

	public function offsetExists($offset) {
		return $this->check($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

}