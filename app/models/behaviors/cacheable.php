<?php

class CacheableBehavior extends ModelBehavior {

    /**
     *
     * @param AppModel $model
     */
    public function afterDelete($model) {
        $this->changed($model);
        return true;
    }

    /**
     *
     * @param AppModel $model
     * @param bool $created
     */
    public function afterSave($model, $created) {
        $this->changed($model);
        return true;
    }

    /**
     *
     * @param AppModel $model
     * @param int $id
     */
    public function readCached($model, $id = null) {
        if (empty($id)) {
            $id = $model->getID();
        }

        $key = $model->Behaviors->enabled('Translate') ?
            Inflector::underscore($model) ."_{$id}_". SlConfigure::read('I18n.locale') :
            Inflector::underscore($model) ."_{$id}";

        $data = Cache::read($key, 'models');
        if (empty($data)) {
            Cache::write($key, $data = $model->read(null, $id), 'models');
        }
        return $data;
    }

    /**
     *
     * @param AppModel $model
     * @param int $id
     */
    public function findCached($model, $what, $options = array()) {
        if (empty($options['fields'])) {
            $options['fields'] = "{$model->alias}.{$model->primaryKey}";
        }
        $results = $model->find($what, $options);

        if ($results && is_array($results)) {
            if (isset($results[0])) {
                $data = array();
                foreach ($results as $item) {
                    $data[] = $this->readCached($item[$model->alias][$model->primaryKey]);
                }
                return $data;
            }
            else {
                return $this->readCached($results[$model->alias][$model->primaryKey]);
            }
        }
    }

    /**
     * Mark a node change, triggering cascade cache clearing
     *
     * @param AppModel $model
     * @param int $id
     */
    public function changed($model, $id = null) {
        if (empty($id)) {
            $id = $model->getID();
        }
        
        if ($model->Behaviors->enabled('Translate')) {
            $locales = SlConfigure::read('I18n.locales');
            foreach ($locales as $locale) {
                $key = Inflector::underscore($model) ."_{$id}_{$locale}";
                Cache::delete($key, 'models');
            }
        }
        else {
            $key = Inflector::underscore($model) ."_{$id}";
            Cache::delete($key, 'models');
        }
    }

}
