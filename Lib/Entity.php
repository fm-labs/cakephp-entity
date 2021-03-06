<?php
App::uses('Inflector', 'Utility');

//@todo make abstract
class Entity implements ArrayAccess {

	//@todo implement me
	protected $_plugin;

	protected $_alias;

	protected $_useModel;

	protected $_Model;

	protected $_validationErrors = array();

/**
 * Constructor
 *
 * @param Model $model
 * @param array $data
 * @throws Exception
 */
	public function __construct($model = null, $data = null) {
		// set alias
		if (!$this->_alias) {
			$this->_alias = (preg_match('/^(.+)Entity$/', get_class($this)))
				? substr(get_class($this), 0, -6)
				: get_class($this);
		}

		// set model
		if ($model && $model instanceof Model) {
			$this->_Model =& $model;
			$this->_useModel = get_class($model);
		} elseif ($model === null && $this->_useModel === null) {
			$this->_useModel = $this->_alias;
		} elseif ($model === false) {
			$this->_useModel = false;
		} elseif ($model) {
			throw new Exception('$model must be an instance of Model');
		}

		// set data
		if ($data) {
			$this->map($data);
		}
	}

	public function __call($key, $params) {
		if (method_exists($this, $key)) {
			return call_user_func(array($this, $key));
		} elseif ($this->isPublic($key)) {
			return $this->get($key);
		}
		//@todo throw Exception
	}

	public function __get($key) {
		if (method_exists($this, $key)) {
			return call_user_func(array($this, $key));
		} elseif ($this->isPublic($key)) {
			return $this->get($key);
		}
		//@todo throw Exception
	}

	public function &getModel() {
		if (!$this->_Model) {
			if ($this->_useModel === false) {
				throw new Exception('No model name set for Entity ' . get_class($this));
			}
			$this->_Model = ClassRegistry::init($this->_useModel);
		}
		return $this->_Model;
	}

/**
 * Map a data array
 *
 * @param array $data
 * @param bool $primary
 * @return Entity
 * @throws Exception
 */
	public function map($data = array(), $primary = true) {
		if ($primary) {
			if (!isset($data[$this->_alias])) {
				throw new Exception('No data set');
			}
			$_data = $data[$this->_alias];
			unset($data[$this->_alias]);
			$this->set($_data);
		} else {
			$this->set($data);
		}

		return $this;
	}

/**
 * Set entity data
 *
 * @param string|array $key
 * @param mixed $val
 * @return Entity
 * @throws Exception
 */
	public function set($key, $val = null) {
		if (is_array($key) && $val === null) {
			foreach ($key as $_k => $_v) {
				$this->set($_k, $_v);
			}
			return $this;
		}

		$key = self::keyCamelized($key);
		if ($this->isPublic($key)) {
			$this->{$key} = $val;
		} else {
			throw new Exception(__("Can not _set_ undefined or protected property '%s::\$%s'",
				get_class($this), $key));
		}
		return $this;
	}

/**
 * @param null $key
 * @return array
 * @throws Exception
 */
	public function get($key = null) {
		if ($key === null) {
			return $this->toArray();
		} else {
			$key = self::keyCamelized($key);
			if ($this->isPublic($key)) {
				return $this->{$key};
			} else {
				throw new Exception(__("Can not _get_ undefined or protected property '%s::\$%s'",
					get_class($this), $key));
			}
		}
	}

/**
 * @param array $include Include field names
 * @param array $exclude Exclude field names
 * @return array
 */
	public function toArray($include = array(), $exclude = array()) {
		$ref = new ReflectionObject($this);
		$props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		$data = array();
		foreach ($props as $prop) {
			$propName = $prop->getName();
			$exportName = self::keyUnderscored($propName);
			// include filter
			if (is_array($include) && !empty($include) && !in_array($exportName, $include)) {
				continue;
			}
			// exclude filter
			if (is_array($exclude) && !empty($exclude) && in_array($exportName, $exclude)) {
				continue;
			}
			// export
			$data[$exportName] = $prop->getValue($this);
		}
		return $data;
	}

/**
 * A wrapper for toArray()
 * Return data in CakePHP-data-style
 * e.g. array(
 * 	'EntityAlias' => array('entityKey' => '', 'etc' => '...')
 * )
 * @return array
 */
	public function toData() {
		return array($this->_alias => $this->toArray());
	}

/**
 * isPublic method
 *
 * @param string $key
 * @return boolean
 * @throws Exception
 */
	public function isPublic($key) {
		try {
			$ref = new ReflectionProperty(get_class($this), $key);
			return $ref->isPublic();
		} catch(ReflectionException $e) {
			// the property is not defined
		} catch(Exception $e) {
			throw $e;
		}
		return false;
	}

	public function validate() {
		$this->_validationErrors = array();
		if ($this->_useModel) {
			$m =& $this->getModel();
			$m->create();
			$m->set(array($m->alias => $this->toArray()));
			if (!$m->validates()) {
				$this->_validationErrors = $m->validationErrors;
				return false;
			}
		}
		return true;
	}

	public function getValidationErrors() {
		return $this->_validationErrors;
	}

	public function afterFind(Model $model) {
		// override in sub-classes
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->set($offset, null);
	}

	public function offsetExists($offset) {
		return $this->isPublic(self::keyCamelized($offset));
	}

	public function offsetGet($offset) {
		if ($offset === $this->_alias) {
			return $this->get(null);
		}
		return $this->get($offset);
	}

	public static function keyCamelized($key) {
		return lcfirst(Inflector::camelize($key));
	}

	public static function keyUnderscored($key) {
		return Inflector::underscore($key);
	}
}