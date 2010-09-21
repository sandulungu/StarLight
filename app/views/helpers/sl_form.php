<?php

App::import('Helper', 'Form');

class SlFormHelper extends FormHelper {

    /**
     * @var array
     */
    protected $_emptyTags = array('img', 'br', 'hr', 'input', 'button', 'embed', 'param');

    public function input($fieldName, $options = array()) {
        $view = ClassRegistry::getObject('view');
		$this->setEntity($fieldName);
        $modelKey = $this->model();
		$fieldKey = $this->field();

        if ($modelKey{0} >= 'A' && $modelKey{0} <= 'Z') {
            $model = ClassRegistry::init($modelKey);
            $schema = $model->schema($fieldKey);

            $options += array(
                'meioUpload' => $model->Behaviors->enabled('MeioUpload') &&
                    isset($model->Behaviors->MeioUpload->__fields[$model->alias][$fieldKey]),
                'before' => '',
                'after' => '',
                'translate' => $model->Behaviors->enabled('Translate') &&
                    in_array($fieldKey, $model->Behaviors->Translate->settings[$model->alias]),
            );

            if ($schema['type'] == 'boolean') {
                $options += array(
                    'checkedByDefault' => (bool)$schema['default'],
                );
            } else {
                $options += array(
                    'default' => $schema['default'],
                );
            }

            // if this is a MeioUpload field and a file has been uploaded, then show it
            if ($options['meioUpload'] && !empty($view->data[$modelKey][$fieldKey]) && is_string($view->data[$modelKey][$fieldKey])) {

                $meioUploadOptions = $model->Behaviors->MeioUpload->__fields[$model->alias][$fieldKey];
                $filename = r(DS, '/', "{$meioUploadOptions['dir']}/{$view->data[$modelKey][$fieldKey]}");

                if (isset($meioUploadOptions['thumbsizes']['icon'])) {
                    $iconFilename = r(DS, '/', "{$meioUploadOptions['dir']}/thumb/icon/{$view->data[$modelKey][$fieldKey]}");
                    $options['before'] .= sprintf(
                        '<a class="sl-uploaded-image" href="%s" rel="colorbox" target="_blank"><img src="%s" /></a>',
                        $this->assetUrl($filename),
                        $this->assetUrl($iconFilename)
                    );
                    Pheme::parse('JqueryColorbox');
                }
                else {
                    $options['after'] .= sprintf(
                        '<a class="sl-uploaded-file" href="%s" target="_blank">%s</a>',
                        $this->assetUrl($filename),
                        __t('View uploaded file')
                    );
                }
            }
            unset($options['meioUpload']);

            if (in_array($schema['type'], array('datetime', 'date', 'time'))) {
                $options += array(
                    'dateFormat' => SlConfigure::read2('I18n.options.dateFormat'),
                    'timeFormat' => SlConfigure::read2('I18n.options.timeFormat'),
                );
            }
        }

        $prefix = SlConfigure::read2('View.options.modelPrefix');
        if (empty($prefix)) {
            $prefix = $this->plugin;
        }

        $options += array(
            'label' => __t(Inflector::humanize(r('.', ' ', preg_replace("/^{$prefix}_|_id$/", '', $fieldName)))),
            'translate' => false
        );

        if (isset($options['checkedByDefault'])) {
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
            $options2 = $options;
            foreach ($catalogs as $catalog) {
                $options2['label'] = $catalog['language'];

                if (isset($options['value']) && is_array($options['value'])) {
                    if (isset($options['value'][$catalog['locale']])) {
                        $options2['value'] = $options['value'][$catalog['locale']];
                    } else {
                        unset($options2['value']);
                    }
                } else {
                    unset($options2['value']);
                }

                $fields["{$fieldName}_{$catalog['locale']}"] = $options2;
            }

            if (count($fields) == 1) {
                return parent::input(key($fields), array('label' => $options['label']) + $options2);
            }
            return $this->inputs(array('legend' => $options['label']) + $fields);
        }
        else {
            return parent::input($fieldName, $options);
        }
    }
    
	function end($options = null) {
        $view = Sl::getInstance()->view;

        $options2 = is_array($options) ? $options : array();
        $options2 += array(
            'validation' => true,
        );

        if ($options2['validation'] && $view->model && isset($view->Validation)) {
            SlConfigure::write('Asset.js.jquery', 'head');
            SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
            $html = $view->Validation->bind($view->model);
        } else {
            $html = '';
        }

		return parent::end($options) . $html;
	}

}
