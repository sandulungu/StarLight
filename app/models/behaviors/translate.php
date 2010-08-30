<?php

/**
 * Alternative I18n behavior to replace the standart CakePHP TranslateBehavior,
 * w/o using additional tables, instead altering Model tables
 * 
 * Key advantages: better productivity, DB readability and flexibility and
 * good compatibility
 */
 class TranslateBehavior extends ModelBehavior {

    /**
     *
     * @var bool
     */
    protected $_ready = array();

    /**
     *
     * @var array
     */
    protected static $_settings = array();
	
    /**
     *
     * @param AppModel $model
     * @param array $settings Array of DB field names to be localized
     */
	public function setup($model, $settings) {
        // force this behavior to use shared settings
		$this->settings =& self::$_settings;

		if (empty($settings) || !is_array($settings)) {
			$this->settings[$model->alias] = array();
			return;
		}
		$this->settings[$model->alias] = $settings;
        
        $this->_addValidationRules($model);
	}

	/**
	* Transcribe validation rules to work with translateable fields
     * 
     * @param AppModel $model
	*/
	protected function _addValidationRules($model) {
		$fields = $this->settings[$model->alias];
		if (empty($fields)) {
			return;
        }
		
		$locale = SlConfigure::read('I18n.locale');
        
		foreach ($model->validate as $name => $rules) {
			if (in_array($name, $fields)) {
				$model->validate["{$name}_$locale"] = array(
					'rule' => array('validTranslate', $model->validate[$name]),
					'message' => 'Required at least in one language',
				);
				//unset($model->validate[$name]);
			}
		}
	}
	
    /**
     * Check DB table for localized fields and define them if needed
     *
     * @param AppModel $model
     * @return bool (true if no changes were made)
     */
	protected function _checkSchema($model) {
		if (empty($this->_ready[$model->alias])) {
			$this->_ready[$model->alias] = true;
        } else {
            return;
        }
             
		$fields = $this->settings[$model->alias];
		if (empty($fields)) {
			return true;
        }
		
		$locales = SlConfigure::read('I18n.locales');
		if (empty($locales)) {
			trigger_error('I18n didn\'t initilialize properly.', E_USER_ERROR);
			return false;
		}
		
		$db =& ConnectionManager::getDataSource($model->useDbConfig);
		$schema = $model->schema();
        $alterTable = array();
		
		// add all localized missing fields in this model
		foreach ($fields as $field) {
			foreach ($locales as $locale) {
				$field_lang = $field.'_'.$locale;
				if (empty($schema[$field_lang])) {
					if (empty($schema[$field])) {
						trigger_error("Table for $model->alias Model doesn't have a field named '$field'!", E_USER_ERROR);
                    }
					$alterTable[$model->table]['add'][$field_lang] = $schema[$field];
				}
			}
		}
		foreach ($locales as $locale) {
			$field_lang = '_'.$locale;
			if (empty($schema[$field_lang])) {
				$alterTable[$model->table]['add'][$field_lang] = array( 'type' => 'boolean', 'null' => false );
			}
		}
		
		if ($alterTable) {
			if (!method_exists($db, 'alterSchema')) {
				trigger_error("Table configuration for $model->alias Model could not be changed to reflect your latest language settings because its DataSource does not support altering schemas.", E_USER_ERROR);
			}
			
			$model->cacheSources = false;
			$model->cacheQueries = false;
			
			// delete cached model file
			clearCache("{$model->useDbConfig}_{$model->table}", 'models', '');
			
			// execute alter table and update schema
			$model->query($db->alterSchema($alterTable));
			$model->schema(true);
			
			// output a notice about updated DB table
            trigger_error("Table configuration for $model->alias Model has changed to reflect your latest language settings.", E_USER_NOTICE);
			return false;
		}
		return true;
	}
	
    /**
     * Special validator for localized fields
     *
     * @param AppModel $model
     * @param array $field
     * @param array $rule
     */
    public function validTranslate($model, $field, $params, $rule) {
        $name = array_keys($field);
        $name = preg_replace('/_[a-z]+$/', '', $name[0]);

        $locales = SlConfigure::read('I18n.locales');

        $data =& $model->data;
        if (isset($data[$model->alias])) {
            $data =& $data[$model->alias];
        }
        
        foreach ($locales as $locale) {
            if (!empty($data["{$name}_$locale"])) {
                return true;
            }
        }
        foreach ($locales as $locale) {
            $model->validationErrors["{$name}_$locale"] = $rule['message'];
        }
        return true;
    }

    /**
     * Callback...
     *
     * Allows automagic sorting and field retrival.
     * Add support for $query['localized'] that will filter out items not translated to the current language
     *
     * @param AppModel $model
     */
	public function beforeFind($model, $query) {
    	$this->_checkSchema($model);

		$fields = $this->settings[$model->alias];
		$currLocale = SlConfigure::read('I18n.locale');

        if (!empty($query['localized'])) {
            if (!is_array($query['conditions'])) {
                $query['conditions'] = array($query['conditions']);
            }
            $query['conditions']["_$currLocale"] = true;
            unset($query['localized']);
        }

		// reorder based on current localization
		if (count($query['order']) > 0) {
			$orderArray =& $query['order'];
			foreach ($orderArray as &$order) {
				if (!is_array($order) && $order) {
                    $parts = explode(' ', $order);
					$order = array($parts[0] => empty($parts[1]) ? 'ASC' : $parts[1]);
                }
				if (is_array($order)) {
                    $newOrder = array();
					foreach ($order as $key => $direction) {
                        $field = $key;
						if (is_int($field)) {
							$field = $direction; 
							$direction = 'ASC'; 
						}
						$temp = explode('.', $field);
						if (count($temp) === 1) {
							$modelClass = $model->alias;
							$fieldName = Inflector::slug($temp[0], '');
						} else {
							$modelClass = Inflector::slug($temp[0], '');
							$fieldName = Inflector::slug($temp[1], '');
						}
						if (isset($this->settings[$modelClass]) && in_array($fieldName, $this->settings[$modelClass])) {
							$newOrder[$modelClass.'.'.$fieldName.'_'.$currLocale] = $direction;
						} else {
                            $newOrder[$key] = $direction;
                        }
					}
                    $order = $newOrder;
				}
			}
		}
		
        if (is_string($query['fields'])) {
            $query['fields'] = array($query['fields']);
        }
		$locales = SlConfigure::read('I18n.locales');
        
		if (is_array($query['fields']) && count($query['fields']) > 0) {

    		// Add {$field}_{$lang} to fields list (when needed)
            foreach ($fields as $field) {
                foreach ($query['fields'] as $fieldName) {
                    $temp = explode('.', $fieldName);
                    if (count($temp) === 1) {
                        $modelClass = $model->alias;
                        $fieldName = Inflector::slug($temp[0], '');
                    } else {
                        $modelClass = Inflector::slug($temp[0], '');
                        $fieldName = Inflector::slug($temp[1], '');
                    }
    				if (isset($this->settings[$modelClass]) && in_array($fieldName, $this->settings[$modelClass])) {
                        $query['fields'][] = $modelClass.'.'.$fieldName.'_'.$currLocale;
                    }

//                    foreach (array($field, $model->alias.'.'.$field, $model->escapeField($field)) as $_field) {
//						if ($_field === $fieldName) {
//							foreach ($locales as $locale) {
//								$query['fields'][] = $model->alias.'.'.$field.'_'.$locale;
//                            }
//                            //unset($query['fields'][$fieldName]);
//						}
//					}
				}
			}
		}

        // transcript conditions
        if ($query['conditions']) {
            $recursive = isset($query['recursive']) ? $query['recursive'] : $model->recursive;
            foreach ($fields as $field) {
                foreach (array($field, $model->alias.'.'.$field, $model->escapeField($field)) as $_field) {
                    $fields2["$_field LIKE"] = $model->alias.'.'.$field.'_'.$currLocale.' LIKE';
                    //if ($recursive < 0 || $field != $_field) {
                        $fields2[$_field] = "{$_field}_$currLocale";
                    //}
                }
            }
            $query['conditions'] = $this->__parseConditions($query['conditions'], $fields2);
        }
       
		return $query;
	}

    /**
     * Replace translateable fields with their translated version in condition statements
     *
     * @param mixed $conditions
     * @param array $fields
     * @param array $langs
     * @return mixed
     */
    private function __parseConditions($conditions, $fields) {
        if (is_array($conditions)) {
            foreach ($conditions as $key => $condition) {
                if (is_string($key) && !in_array(low($key), array('and', 'or', 'not'))) {
                    foreach ($fields as $field => $field2) {
                        if ($key === $field) {
                            unset($conditions[$key]);
                            $conditions[$field2] = $condition;
                        }
                    }
                } else {
                    $conditions[$key] = $this->__parseConditions($condition, $fields);
                }
            }
        }
        
        // experimental - may generate unwanted consequences
        /*elseif (is_string($conditions)) {
            foreach ($fields as $field => $field2) {
                if (strpos($conditions, $field) !== false) {
                    $conditions = r($field, $field2, $conditions);
                }
            }
        }*/
        return $conditions;
    }

    /**
     * Sets translated fields to proper values
     *
     * @param array $localizableFields
     * @param array &$data
     * @param string $locale
     */
    private function __afterFind($localizableFields, &$data, $locale) {
        $result = false;

        // hasMany or HABTM data
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as &$item) {
                $result = $this->__afterFind($localizableFields, $item, $locale) || $result;
            }
        } else {
            foreach ($data as $field => &$value) {
                // association
                if ($field{0} >= 'A' && $field{0} <= 'Z') {
                    // speed fix: only parse data for models with Translate behavior and their associations
                    if (isset($this->settings[$field])) {
                        $result = $this->__afterFind($this->settings[$field], $value, $locale) || $result;
                    }
                }
                // translateable field
                elseif($localizableFields) {
                    if (in_array($field, $localizableFields) && empty($value)) {
                        $value = $data["{$field}_{$locale}"];
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Callback...
     *
     * Sets translated fields to proper values
     * 
     * @param AppModel $model
     */
	public function afterFind($model, $results, $primary = false) {
		if (!$primary || empty($results)) {
			return true;
        }
		$currLocale = SlConfigure::read('I18n.locale');

        // set field content according to current language
		foreach ($results as &$row) {
            $result = false;
			foreach ($row as $modelClass => &$data) {
				if (is_array($data) && isset($this->settings[$modelClass])) {
					$localizableFields = $this->settings[$modelClass];
                    $result = $this->__afterFind($localizableFields, $data, $currLocale) || $result;
				}
 			}
            if (!$result) {
                return true; // no changes in the first item => no changes at all
            }
		} // foreach
        
		return $results;
    }

    /**
     * Callback...
     *
     * Set language flags
     *
     * @param AppModel $model
     */
	public function beforeSave($model) {
    	$this->_checkSchema($model);
        
		$fields = $this->settings[$model->alias];
		if (empty($fields)) {
			return true;
        }

		$locales = SlConfigure::read('I18n.locales');
		$currLocale = SlConfigure::read('I18n.locale');
		
		foreach ($fields as $field) {

            // reversed binding - its use is still under question
            if (!isset($model->data[$model->alias][$field.'_'.$currLocale]) && (isset($model->data[$model->alias][$field]))) {
                $model->data[$model->alias][$field.'_'.$currLocale] = $model->data[$model->alias][$field];
                unset($model->data[$model->alias][$field]);
            }

			foreach ($locales as $locale) {
				if (isset($model->data[$model->alias][$field.'_'.$locale]) || (isset($model->data[$model->alias][$field]))) {
					$model->data[$model->alias]['_'.$locale] =
                        !empty($model->data[$model->alias]['_'.$locale]) ||
						!empty($model->data[$model->alias][$field.'_'.$locale]);
				}
			}
		} // foreach
		return true;
	}
	
}
