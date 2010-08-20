<?php

/**
 * Creates a mirrored representation of model data in SL configuration
 *
 * Note: manually call refreshMirror() upon reordering of tree data if needed
 */
class MirroredBehavior extends ModelBehavior {

   protected $_pending = array();

  /**
   * @param AppModel $model
   * @param array $config Defaults:
   *        'collection' => 'global',
   *        'name' => "Mirrored.variableStyleModelName",
   *        'findType' => 'all' or 'threaded',
   *        'findOptions' => array('recursive' => -1),
   */
	public function setup($model, $config = array()) {
        $config = (array)$config;
        $config += array(
            'collection' => 'global',
            'name' => "Mirror." . Inflector::variable($model->alias),
            'findType' => $model->Behaviors->enabled('Tree') ? 'threaded' : 'all',
            'findOptions' => array(),
            'collectionField' => null, // if threaded, multiple collections are only supported on level 0
            'groupField' => null,
            'indexField' => null,
            'valueField' => null,
            'valueFields' => array(),
            'autoRefresh' => true,
        );
        $config['findOptions'] += array(
            'recursive' => -1,
        );
        $config += array(
            'noModelName' => $config['findOptions']['recursive'] == -1,
        );

        $this->_pending[$model->alias] = $config['autoRefresh'] ? 0 : 100000;

        $this->settings[$model->alias] = $config;
	}

    /**
     *
     * @param AppModel $model
     */
    public function refreshMirror($model, $checkPendingOperations = false) {
		if ($checkPendingOperations) {
			if ($this->_pending[$model->alias] > 0) {
				return;
			}
			$this->_pending[$model->alias] = 0;
		}

        $config = $this->settings[$model->alias];

        if ($model->Behaviors->enabled('Translate')) {
            if ($config['findOptions']['recursive'] >= 0 || $model->Behaviors->Translate->settings[$model->alias]) {
                
                $oldLang = SlConfigure::read('I18n.lang');
                $locales = SlConfigure::read('I18n.locales');
                $localesPreg = '/_' . implode('$|_', $locales) . '$/';
                foreach ($locales as $lang => $locale) {
                    Sl::setLocale($lang);
                    $data = $model->find($config['findType'], $config['findOptions']);
                    if ($config['collectionField']) {
                        $this->_cleanup($config['name'], $locale);
                    }
                    $this->_write(
                        $config['name'],
                        $this->_prepareData($model, $config, $data, $localesPreg),
                        $locale
                    );
                }
                Sl::setLocale($oldLang);
                return;
            }
        }

        $data = $model->find($config['findType'], $config['findOptions']);
        if ($config['collectionField']) {
            $this->_cleanup($config['name']);
        }
        $this->_write($config['name'], $this->_prepareData($model, $config, $data));
    }

    protected function _write($name, $data, $locale = null) {
        foreach ($data as $collection => $items) {
            SlConfigure::write($name, $items, true, $locale ? "$collection.$locale" : $collection);
        }
    }

    /**
     * If we work with multiple collections,
     * it is necessary cleanup old data everywhere before saving
     *
     * @param string $locale
     */
    protected function _cleanup($name, $locale = null) {
        $collections = SlConfigure::read(null, 'populated', true);
        foreach ($collections as $collection) {
            SlConfigure::delete($name, true, $locale ? "$collection.$locale" : $collection);
        }
    }

    /**
     *
     * @param array $fields
     * @param string $localesPreg
     */
    protected function _stripLocalizationData(&$fields, $localesPreg) {
        foreach ($fields as $field => &$data) {
            if (is_array($data) && $field{0} >= 'A' && $field{0} <= 'Z') {
                // hasMany
                if (isset($data[0])) {
                    foreach ($data as &$item) {
                        $this->_stripLocalizationData($item, $localesPreg);
                    }
                }
                // hasOne
                else {
                    $this->_stripLocalizationData($data, $localesPreg);
                }
            }
            elseif (preg_match($localesPreg, $field)) {
                unset($fields[$field]);
            }
        }
    }

    /**
     *
     * @param AppModel $model
     */
    public function enableMirror($model) {
        $this->_pending[$model->alias]--;
    }

    /**
     *
     * @param AppModel $model
     */
    public function disableMirror($model) {
        $this->_pending[$model->alias]++;
    }

    /**
     *
     * @param AppModel $model
     * @param array $config
     * @param array $data
     * @return array
     */
    protected function _prepareData($model, $config, &$data, $localesPreg = null) {
        if (empty($data)) {
            return array();
        }

        $results = array();
        foreach ($data as $item) {
            if ($config['collectionField']) {
                $collection = $item[$model->alias][$config['collectionField']];
            } else {
                $collection = null;
            }
            if (empty($collection)) {
                $collection = $config['collection'];
            }
            if (!isset($results[$collection])) {
                $results[$collection] = array();
            }
            $result =& $results[$collection];

            if ($localesPreg) {
                foreach ($item as $modelName => &$fields) {
                    $this->_stripLocalizationData($fields, $localesPreg);
                }
            }

            if ($config['indexField'] && $item[$model->alias][$config['indexField']]) {
                if ($config['groupField']) {
                    $result[$item[$model->alias][$config['groupField']]][$item[$model->alias][$config['indexField']]] =& $item;
                }
                else {
                    $result[$item[$model->alias][$config['indexField']]] =& $item;
                }
            }
            else {
                if ($config['groupField']) {
                    $result[$item[$model->alias][$config['groupField']]][] =& $item;
                }
                else {
                    $result[] =& $item;
                }
            }

            if ($config['valueField']) {
                $item = $item[$model->alias][$config['valueField']];
            }
            else {
                if ($config['valueFields']) {
                    $item[$model->alias] = array_intersect_key(
                        array_combine($config['valueFields'], $config['valueFields']),
                        $item[$model->alias]
                    );
                }

                if ($config['findType'] == 'threaded') {
                    if ($item['children']) {
                        $item['children'] = reset($this->_prepareData($model, $config, $item['children'], $localesPreg));
                    }
                }

                if ($config['noModelName']) {
                    if ($config['findType'] == 'threaded') {
                        $item = am($item[$model->alias], array('children' => $item['children']));
                    } else {
                        $item = $item[$model->alias];
                    }
                }
            }
        }
        return $results;
    }

    /**
     *
     * @param AppModel $model
     */
	public function beforeSave($model) {
        $this->_pending[$model->alias]++;
    }

    /**
     *
     * @param AppModel $model
     */
	public function afterSave($model) {
        $this->_pending[$model->alias]--;
        $this->refreshMirror($model, true);
    }

    /**
     *
     * @param AppModel $model
     */
	public function beforeDelete($model) {
        $this->_pending[$model->alias]++;
    }

    /**
     *
     * @param AppModel $model
     */
	public function afterDelete($model) {
        $this->_pending[$model->alias]--;
        $this->refreshMirror($model, true);
    }
}
