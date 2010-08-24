<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsNode', array('type' => 'file', 'url' => array(
        'controller' => 'cms_contact_forms',
    )));
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }
    echo $this->SlForm->input('parent_id', array('title' => __t('Parent'), 'empty' => true));

    echo $this->SlForm->input('title');

    echo $this->SlForm->input('CmsContactForm.email', array('label' => __t('Recipient email')));
    echo $this->SlForm->input('CmsContactForm.fields', array(
        'label' => __t('Custom fields'),
        'after' => __t('By default 2 fields are shown to the end-user: From|email and Text|textarea<br />Here you can change this and add custom fields.<br /><br />Use the syntax: <em>&lt;Name in English&gt;[|&lt;field type: all cake supported types + "email"&gt;[|&lt;text to show after the input field (HTML allowed)&gt;[|&lt;options list separated by ","&gt;[|&lt;validation regular expression&gt;[|&lt;validation error message in English&gt;]]]]]</em>'),
    ));

    echo $this->SlForm->input('short_title');
//    echo $this->SlForm->input('teaser');
    echo $this->SlForm->input('body', array('after' => __t('Use <b>{ContactForm/}</b> as a placeholder for the generated contact form and/or <b>{Email/}</b> for a "sendto:" link.')));
    echo $this->SlForm->input('meta_keywords');
    echo $this->SlForm->input('meta_description');
//    echo $this->SlForm->input('skin', array('options' => SlConfigure::read2('Cms.nodeSkins')));
    echo $this->SlForm->input('visible');

//    ClassRegistry::init('Cms.CmsImage');
//    echo $this->SlForm->input('CmsImage.title', array('label' => __t('Thumb image title')));
//    echo $this->SlForm->input('CmsImage.filename', array('label' => __t('Upload thumb image'), 'type' => 'file'));
//
//    echo $this->SlForm->input('CmsTag', array('label' => false, 'multiple' => 'checkbox'));

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
