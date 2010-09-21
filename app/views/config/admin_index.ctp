<?php
    
    $menu = array();
    foreach ($sections as $name => $section) {
        $menu[] = array(
            'title' => __t($section),
            'url' => array('action' => 'index', $name),
        );
    }
    SlConfigure::write('Navigation.sections.active', 'config');
    SlConfigure::write('Navigation.sections.config', $menu);

    echo $this->SlForm->create(null);
    echo $this->SlForm->hidden('_section');

    foreach ($settings as $name => $setting) {
        if (is_int($name)) {
            $name = "setting_$name";
        }

        $setting['label'] = empty($setting['label']) ?
            __t(Inflector::humanize(Inflector::underscore(r('.', '_', $setting['name'])))) :
            __t($setting['label']);

        unset($setting['name']);
        unset($setting['collection']);
        echo $this->SlForm->input($name, $setting);
    }

    echo $this->SlForm->end(__t('Save'));

