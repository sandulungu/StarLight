<?php

/**
 * Note: When used together with Translate, include MarkdownBehavior BEFORE TranslateBehavior.
 *       Also, for every translateable field that is to be used by Markdown, include "markdown_{$name}" in TranslateBehavior' fields list.
 */
class MarkdownBehavior extends ModelBehavior {


    /**
     * @param AppModel $model
     * @param array $settings Array of DB field names to be localized
     */
	public function setup($model, $settings) {
		$this->settings[$model->alias] = $settings;
	}

    /**
     *
     * @param AppModel $model
     */
    public function beforeSave($model) {
        $localizedFields = $model->Behaviors->enabled('Translate') ?
            $model->Behaviors->Translate->settings[$model->alias] : array();
        $fields = $this->settings[$model->alias];
		$locales = SlConfigure::read('I18n.locales');

		foreach ($fields as $field => $field2) {
            if (is_int($field)) {
                $field = $field2;
                $field2 = "markdown_$field";
            }
            if (in_array($field, $localizedFields)) {
                foreach ($locales as $locale) {
                    if (isset($model->data[$model->alias]["{$field}_{$locale}"])) {
                        $model->data[$model->alias]["{$field2}_{$locale}"] = $this->_markdown($model->data[$model->alias]["{$field}_{$locale}"]);
                    }
                }
            }
            if (isset($model->data[$model->alias][$field])) {
                $model->data[$model->alias][$field2] = $this->_markdown($model->data[$model->alias][$field]);
            }
        }

        return true;
    }

//    protected $_schemaChecked = false;
//
//    protected function _checkSchema($model) {
//        if ($this->_schemaChecked) {
//            return;
//        }
//
//        $fields = $this->settings[$model->alias];
//        $schema = $model->schema();
//        $alterTable = array();
//
//        foreach ($fields as $field) {
//            if (empty($schema["markdown_$field"])) {
//                if (empty($schema[$field])) {
//                    trigger_error("Table for $model->alias Model doesn't have a field named '$field'!", E_USER_ERROR);
//                }
//                $alterTable[$model->table]['add']["markdown_$field"] = $schema[$field];
//            }
//        }
//
//        if ($alterTable) {
//    		$db =& ConnectionManager::getDataSource($model->useDbConfig);
//
//			if (!method_exists($db, 'alterSchema')) {
//				trigger_error("Table configuration for $model->alias Model could not be changed because its DataSource does not support altering schemas.", E_USER_ERROR);
//			}
//
//			$model->cacheSources = false;
//			$model->cacheQueries = false;
//
//			// delete cached model file
//			clearCache("{$model->useDbConfig}_{$model->table}", 'models', '');
//
//			// execute alter table and update schema
//			$model->query($db->alterSchema($alterTable));
//			$model->schema(true);
//
//			// output a notice about updated DB table
//            trigger_error("Table configuration for $model->alias Model has changed.", E_USER_NOTICE);
//        }
//
//        $this->_schemaChecked = true;
//    }

    protected function _markdown($text) {
        if (!function_exists('Markdown')) {
            App::import('Vendor', 'php_markdown_extra/markdown');
        }
        return Markdown($text);
    }

}
