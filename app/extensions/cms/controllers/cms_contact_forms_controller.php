<?php

/**
 *
 * @property-read CmsContactForm $CmsContactForm
 */
class CmsContactFormsController extends AppController {

    public $components = array(
        'Api.SwiftMailer', // needed for email over SSL support (ex. GMail)
    );

    public function view($node_id) {

        // node common stuff
        $this->set('cmsNode', $node = SlNode::read($node_id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', $node['CmsNode']['title']);

        // contact form specific stuff
        $fields = array();
        $fields2 = empty($node['CmsContactForm']['fields']) ?
            array('From|email', 'Text|textarea') :
            explode("\n", $node['CmsContactForm']['fields']);

        // prepare the array of user fields
        foreach ($fields2 as $i => $field) {
            $parts = explode('|', r("\r", " ", $field));
            $parts = array_map('trim', $parts);

            $label = __t(empty($parts[0]) ? 'Unknown' : $parts[0]);

            $type = empty($parts[1]) ? 'text' : $parts[1];
            if ($type === 'email') {
                $type = 'text';
            }

            // we use only the 'after' setting, assuming that section title and other stuff
            // can be place here and show up correctly with proper css rules
            $after = empty($parts[2]) ? null : "<div class='after'>$parts[2]</div>";

            // 'options' and 'multiple' settings
            $options = empty($parts[3]) ? null : explode(',', $parts[3]);
            $multiple = false;
            if ($options) {
                $options = array_map('trim', $options);
                $options = array_combine($options, $options);
                if ($type === 'checkbox') {
                    $multiple = 'checkbox';
                    $type = null;
                } elseif ($type === 'multiple') {
                    $multiple = true;
                    $type = null;
                }
                if (!$type) {
                    $type = 'select';
                }
            }

            $fields["f$i"] = compact('label', 'type', 'multiple', 'after');
            if ($options) {
                $fields["f$i"]['options'] = $options;
            }
            if ($type === 'radio') {
                $fields["f$i"]['legend'] = $label;
            }
        }

        if (!empty($this->data['CmsContactForm'])) {
            // TODO: Validate feedback fields

            $this->SwiftMailer->_set(SlConfigure::read('Api.swiftMailer'));
            $this->SwiftMailer->to = $node['CmsContactForm']['email'];

            //set variables to template as usual
            $this->set(compact('fields'));

            // send email
            try {
                if ($this->SwiftMailer->send(
                    SL::read('SwiftMailer.contactFormView'),
                    format(SL::read('SwiftMailer.subject'), array('siteTitle' => SL::read('View.siteTitle'))),
                    SL::read('SwiftMailer.method'))
                ) {
                    $this->Session->setFlash(__t('Email sent. Thank you!'), array('class' => 'success'));

                    // after the message has been sent, we no longer need for the form to show
                    $this->viewVars['fields'] = null;
                } else {
                    $this->Session->setFlash(__t(
                        'An unknown error occured.<br /> Please use you email client to send your message to <a href="mailto:{$email}">{$email}</a>',
                        array('email' => $this->SwiftMailer->to)
                    ), array('class' => 'error'));
                }
            }
            catch(Exception $e) {
                $this->Session->setFlash(__t(
                    'An internal error occured.<br /> Please use you email client to send your message to <a href="mailto:{$email}">{$email}</a>',
                    array('email' => $this->SwiftMailer->to)
                ), array('class' => 'error'));
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
        else {
            $this->set(compact('fields'));
        }
    }

    public function admin_index() {
        $this->set('cmsNodes', SlNode::find('all',
            array('conditions' => array(
                'CmsNode.model' => 'CmsContactForm',
                'CmsNode.plugin' => $this->plugin,
            ))
        ));
    }

    public function admin_view($node_id) {
        $this->set('cmsNode', $node = SlNode::getModel()->read(null, $node_id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', __t('Contact form "{$title}"', array(
            'title' => $node['CmsNode']['title']
        )));
    }

    public function admin_edit() {
        $this->_admin_edit(array('node' => true));

        $this->set('cmsTags', SlNode::getTagList());
        $this->set('parents', SlNode::getModel()->find('treelist', array('conditions' => array('CmsNode.id !=' => $this->id))));
    }

    public function admin_add() {
        $this->_admin_add();
    }
    
}
