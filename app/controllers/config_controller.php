<?php

class ConfigController extends AppController {

    protected function _getSettings($activeSection) {
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
                elseif (isset($setting['type']) && $setting['type'] == 'array') {
                    $setting['value'] = implode(', ',$setting['value']);
                }
            }
            else {
                foreach ($locales as $locale) {
                    $setting['value'][$locale] = SlConfigure::read($setting['name'], "{$setting['collection']}.{$locale}");

                    if (isset($setting['type']) && $setting['type'] == 'json') {
                        $setting['type'] = 'textbox';
                        $setting['value'][$locale] = json_encode($setting['value'][$locale]);
                    }
                    elseif (isset($setting['type']) && $setting['type'] == 'json') {
                        $setting['value'][$locale] = implode(', ',$setting['value'][$locale]);
                    }
                }
            }
        }

        return $settings;
    }

    public function admin_index($activeSection = null) {
        $this->set('sections', $sections = SlConfigure::read2("Config.sections"));

        foreach ($sections as $section => $settings) {
            if (!SlAuth::isAuthorized('config' . Inflector::camelize($section))) {
                unset($sections[$i]);
            }
        }

        if (isset($this->data['_section'])) {
            $activeSection = $this->data['_section'];
        }

        if (empty($activeSection) || !isset($sections[$activeSection])) {
            $activeSection = reset(array_keys($sections));
        }

        $settings = $this->_getSettings($activeSection);
        $this->set('title', __t(SlConfigure::read2("Config.sections.$activeSection")));

        if ($this->data) {
            $locales = SlConfigure::read('I18n.locales');

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
                        elseif (isset($setting['type']) && $setting['type'] == 'array') {
                            $value = Set::normalize($value, false);
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
                            elseif (isset($setting['type']) && $setting['type'] == 'array') {
                                $value = Set::normalize($value, false);
                            }
    
                            SlConfigure::write($setting['name'], $value, true, "{$setting['collection']}.{$locale}");
                        }
                    }
                }
                
            }

            $settings = $this->_getSettings($activeSection);
            $this->Session->setFlash(__t('Configuration saved'), array('class' => 'success'));
        }

        $this->data['_section'] = $activeSection;
        $this->set('settings', $settings);
    }
    
}
