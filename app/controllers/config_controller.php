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

        $settings = SlConfigure::read2("Config.settings.$activeSection");
        foreach ($settings as &$setting) {
            if (empty($setting['collection'])) {
                $setting['collection'] = 'global';
            }
        }

        $this->set('settings', $settings);
        $this->set('title', __t(SlConfigure::read2("Config.sections.$activeSection")));

        if ($this->data) {
            foreach ($settings as $name => &$setting) {
                if (is_int($name)) {
                    $name = "setting_$name";
                }

                if (isset($this->data[$name])) {
                    $value = $this->data[$name];
                    if (isset($setting['type']) && $setting['type'] == 'json') {
                        $value = json_decode($value, true);
                    }

                    if ($setting['collection'] == 'user') {
                        $setting['collection'] = 'User' . SlAuth::user('id');
                    }
                    SlConfigure::write($setting['name'], $value, true, $setting['collection']);
                }
            }
            $this->Session->setFlash(__t('Configuration saved'), array('class' => 'success'));
        }
    }
    
}
