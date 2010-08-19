<?php

class ConfigController extends AppController {

    public function admin_index($activeSection = null) {
        $this->set('sections', $sections = SlConfigure::read2("Config.sections"));

        foreach ($sections as $section => $settings) {
            if (!SlAuth::isAuthorized('config' . Inflector::camelize($section))) {
                unset($sections[$i]);
            }
        }

        if (empty($activeSection) || !isset($sections[$activeSection])) {
            $activeSection = reset(array_keys($sections));
        }

        $locales = SlConfigure::read('I18n.locales');

        $settings = SlConfigure::read2("Config.settings.$activeSection");
        foreach ($settings as &$setting) {
            if (is_string($setting)) {
                $setting = array('name' => $setting);
            }
            if (empty($setting['collection'])) {
                $setting['collection'] = 'global';
            }

            if (empty($setting['translate'])) {
                $setting['value'] = SlConfigure::read($setting['name'], $setting['collection']);

                if (isset($setting['type']) && $setting['type'] == 'json') {
                    $setting['type'] = 'textbox';
                    $setting['value'] = json_encode($setting['value']);
                }
            }
            else {
                foreach ($locales as $locale) {
                    $setting['value'][$locale] = SlConfigure::read($setting['name'], "{$setting['collection']}.{$locale}");

                    if (isset($setting['type']) && $setting['type'] == 'json') {
                        $setting['type'] = 'textbox';
                        $setting['value'][$locale] = json_encode($setting['value'][$locale]);
                    }
                }
            }
        }

        $this->set('settings', $settings);
        $this->set('title', __t(SlConfigure::read2("Config.sections.$activeSection")));

        if ($this->data) {
            foreach ($settings as $name => &$setting) {
                if (is_int($name)) {
                    $name = "setting_$name";
                }

                if ($setting['collection'] == 'user') {
                    $setting['collection'] = 'User' . SlAuth::user('id');
                }

                if (empty($setting['translate'])) {
                    if (isset($this->data[$name])) {
                        $value = $this->data[$name];

                        if (isset($setting['type']) && $setting['type'] == 'json') {
                            $value = json_decode($value, true);
                        }

                        SlConfigure::write($setting['name'], $value, true, $setting['collection']);
                    }
                }
                else {
                    foreach ($locales as $locale) {
                        if (isset($this->data["{$name}_{$locale}"])) {
                            $value = $this->data["{$name}_{$locale}"];
                            
                            if (isset($setting['type']) && $setting['type'] == 'json') {
                                $value = json_decode($value, true);
                            }
    
                            SlConfigure::write($setting['name'], $value, true, "{$setting['collection']}.{$locale}");
                        }
                    }
                }
                
            }
            $this->Session->setFlash(__t('Configuration saved'), array('class' => 'success'));
        }
    }
    
}
