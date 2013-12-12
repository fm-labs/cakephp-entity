<?php
App::uses('ModelBehavior', 'Model');
App::uses('EntityData', 'Entity.Lib');

class EntityBehavior extends ModelBehavior {

	protected $_runtime = array();

	public function setup(Model $model, $config = array()) {
		if (!isset($this->settings[$model->alias])) {
			// default settings
			$defaultSettings = array(
				'entity' => $model->alias,
				'entityClass' => $model->alias . 'Entity',
				'entityLocation' => 'Model/Entity'
			);
			// model settings
			$modelSettings = array();
			if (isset($model->entity) && is_string($model->entity)) {
				list($plugin, $entityName) = pluginSplit($model->entity, true);
				$modelSettings = array(
					'entity' => $plugin . $entityName,
					'entityClass' => $entityName . 'Entity',
					'entityLocation' => $plugin . 'Model/Entity'
				);
			} elseif (isset($model->entity)) {
				$modelSettings = $model->entity;
			}
			// store settings
			$this->settings[$model->alias] = am(
				$defaultSettings,
				$modelSettings
			);
		}
	}

/**
 * Returns entity settings for model
 *
 * @param Model $model
 * @return mixed
 */
	public function entitySettings(Model $model) {
		return $this->settings[$model->alias];
	}

/**
 * Create a new Entity instance for model
 *
 * @param Model $model
 * @return Entity
 */
	public function entityCreate(Model $model) {
		$settings = $this->settings[$model->alias];

		if (!class_exists($settings['entityClass']) && $settings['entityLocation']) {
			App::uses($settings['entityClass'], $settings['entityLocation']);
		}
		$entity = new $settings['entityClass'];
		return $entity;
	}

	public function beforeFind(Model $model, $query) {
		if (isset($query['list'])) {
			$this->_runtime[$model->alias]['skipEntity'] = true;
		}
	}

/**
 * @param Model $model
 * @param mixed $results
 * @param bool $primary
 * @return mixed
 */
	public function afterFind(Model $model, $results, $primary) {
		$assoc = $model->getAssociated();

		// check runtime settings
		$_runtime =& $this->_runtime[$model->alias];
		if (isset($_runtime['skipEntity']) && $_runtime['skipEntity'] == true) {
			$_runtime['skipEntity'] = false;
			return $results;
		}
		if ($primary) {
			if (!isset($results[0])
				|| !isset($results[0][$model->alias])
				|| !isset($results[0][$model->alias]['id'])) {
				return $results;
			}
			$_results = array();
			foreach ($results as $result) {
				//
				// The EntityData is an object wrapper for the standard model result array
				//
				// Provides ArrayAccess for 'CakePHP-wayish' data access
				// Can be used like this: $result['ModelAlias']['field']
				//
				// Provides OO-wayish data access
				// Can be used like this: $result->ModelAlias->field
				//
				$_set = new EntityData();
				foreach ($result as $alias => $aliasResult) {
					if ($model->alias === $alias) {
						$entity = $this->entityCreate($model);
						$entity->map($result);
						$entity->afterFind($model);
					} elseif (array_key_exists($alias, $assoc)) {
						// Recursive entity conversion only works,
						// when the EntityBehavior is explicitly set in model
						// Model::$actsAs = array('Entity.Entity')
						// DOES NOT WORK PROPERLY WHEN BEHAVIOR IS LOADED/ENABLED
						// ON THE FLY
						// @todo fix me
						$assocModel =& $model->{$alias};
						if ($assocModel->Behaviors->enabled('Entity')) {
							switch($assoc[$alias]) {
								// @todo distinguish relationship-specific diffs
								case "belongsTo":
								case "hasMany":
								case "hasAndBelongsToMany":
								default:
									$entity = $this->entityCreate($assocModel);
									$entity->map(array($alias => $aliasResult));
									$entity->afterFind($assocModel);
									break;
							}
						} else {
							// Associated, but they don't use entities
							// Fallback to result array
							$entity = $aliasResult;
						}
					} else {
						// All other cases
						// Fallback to result array
						$entity = $aliasResult;
					}
					$_set->map($alias, $entity);
				}
				$_results[] = $_set;
			}
			return $_results;
		} else {
			// Non-primary
			// @todo implement me: Non-primary entity result sets
		}
		return $results;
	}
}