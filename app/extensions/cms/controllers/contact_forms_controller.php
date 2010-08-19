<?php

/**
 *
 * @property-read ContactForm $ContactForm
 */
class ContactFormsController extends AppController {

    public $components = array(
        'Api.SwiftMailer', // needed for email over SSL support (ex. GMail)
    );

    public function view($node_id) {
        $this->set('node', $node = SlNode::read($node_id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', $node['Node']['title']);

        $fields = array();
        $fields2 = empty($node['ContactForm']['fields']) ?
            array('From|email', 'Text|textarea') :
            explode("\n", $node['ContactForm']['fields']);

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

        if (!empty($this->data['ContactForm'])) {
            // TODO: Validate feedback fields

            $this->SwiftMailer->_set(SlConfigure::read('Api.swiftMailer'));
            $this->SwiftMailer->to = $node['ContactForm']['email'];

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
        $this->set('contact_forms', $this->ContactForm->find('all'));
        $this->set('title', __t('Contact forms'));
    }

    public function admin_view($node_id) {
        $this->set('node', $node = SlNode::getModel()->read(null, $node_id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', __t('Contact form "{$title}"', array('title' => $node['Node']['title'])));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->ContactForm;

        $this->set('tags', SlNode::getTagList());

        if ($this->data) {
            if (SlNode::getModel()->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = SlNode::getModel()->read(null, $this->id);
        }

        $this->set('title', __t(!$this->id ? 'Add contact form' : 'Edit contact form'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
