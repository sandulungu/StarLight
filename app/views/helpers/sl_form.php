<?php

App::import('Helper', 'Form');

class SlFormHelper extends FormHelper {

    /**
     * @var array
     */
    protected $_emptyTags = array('img', 'br', 'hr', 'input', 'button', 'embed', 'param');

    public function input($fieldName, $options = array()) {
        $options += array(
            'label' => __t(Inflector::humanize($fieldName)),
            'translate' => false
        );

		$this->setEntity($fieldName);
        $modelKey = $this->model();
		$fieldKey = $this->field();
        if ($modelKey{0} >= 'A' && $modelKey{0} <= 'Z') {
            $model = ClassRegistry::init($modelKey);
            $schema = $model->schema($fieldKey);

            $options += array(
                'meioUpload' => $model->Behaviors->enabled('MeioUpload') &&
                    issset($model->Behaviors->MeioUpload->__fields[$model->alias][$fieldKey]),
                'after' => '',
                'translate' => $model->Behaviors->enabled('Translate') &&
                    in_array($fieldKey, $model->Behaviors->Translate->settings[$model->alias]),
            );

            // if this is a MeioUpload field and a file has been uploaded, then show it
            if ($options['meioUpload'] && isset($view->data[$modelKey][$fieldKey]) && is_string($view->data[$modelKey][$fieldKey])) {
                $meioUploadOptions = $model->Behaviors->MeioUpload->__fields[$model->alias][$fieldKey];
                $filename = "{$meioUploadOptions['dir']}/{$view->data[$modelKey][$fieldKey]}";

                if (isset($meioUploadOptions['thumbsizes']['icon'])) {
                    $iconFilename = "{$meioUploadOptions['dir']}/thumb/icon/{$view->data[$modelKey][$fieldKey]}";
                    $options['after'] += sprintf(
                        '<a class="sl-uploaded-image" href="%s" rel="colorbox"><img src="%s" /></a>',
                        $this->assetUrl($filename),
                        $this->assetUrl($iconFilename)
                    );
                }
                else {
                    $options['after'] += sprintf(
                        '<a class="sl-uploaded-file" href="%s" target="_blank">%s</a>',
                        $this->assetUrl($filename),
                        __t('View uploaded file')
                    );
                }
            }

            if (in_array($schema['type'], array('datetime', 'date', 'time'))) {
                $options += array(
                    'dateFormat' => SlConfigure::read2('I18n.options.dateFormat'),
                    'timeFormat' => SlConfigure::read2('I18n.options.timeFormat'),
                );
            }
        }

        if (isset($options['checkedByDefault'])) {
            $view = ClassRegistry::getObject('view');
            if (!isset($view->data[$modelKey][$fieldKey])) {
                $options['checked'] = $options['checkedByDefault'];
            }
            unset($options['checkedByDefault']);
        }
        
        $translate = $options['translate'];
        unset($options['translate']);

        if ($translate) {
            $fields = array();
            $catalogs = SlConfigure::read('I18n.catalogs');
            foreach ($catalogs as $catalog) {
                $fields["{$fieldName}_{$catalog['locale']}"] = array('label' => $catalog['language']) + $options;
            }
            return $this->inputs(array('legend' => $options['label']) + $fields);
        } else {
            return parent::input($fieldName, $options);
        }
    }
    
}
