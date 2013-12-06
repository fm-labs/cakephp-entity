<?php 
class EntityCollection implements ArrayAccess {
	
	protected $_items = array();
	
	public function push(Entity $entity) {
		return array_push($this->_items, $entity);
	}
	
	public function unshift(Entity $entity) {
		return array_unshift($this->_items, $entity);
	}
	
	public function pop() {
		return array_pop($this->_items);
	}
	
	public function shift() {
		return array_shift($this->_items);
	}
	
	public function setItem($idx, Entity $entity) {
		if (is_null($idx)) {
			$this->_items[] = $idx;
		} else {
			$this->_items[$idx] = $idx;
		}
	}
	
	public function getItem($idx) {
		return isset($this->_items[$idx]) ? $this->_items[$idx] : null;
	}
	
	public function getItems() {
		return $this->_items;
	}
	
	public function offsetSet($offset, $value) {
		return $this->setItem($offset, $value);
	}
	
	public function offsetExists($offset) {
		return isset($this->_items[$offset]);
	}
	
	public function offsetUnset($offset) {
		// unset($this->_items[$offset]);
		return false;
	}
	
	public function offsetGet($offset) {
		return $this->getItem($offset);
	}	
	
	public function toArray() {
		$out = array();
		foreach($this->_items as &$item) {
			$out[] = $item->toArray();
		}
		return $out;
	}
}