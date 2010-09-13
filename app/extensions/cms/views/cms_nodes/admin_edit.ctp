<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsNode', array('type' => 'file'));
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }
    echo $this->SlForm->input('parent_id', array('title' => __t('Parent'), 'empty' => true));

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('short_title');
    echo $this->SlForm->input('teaser');
    echo $this->SlForm->input('body', array('rows' => 14));
    echo $this->SlForm->input('meta_keywords');
    echo $this->SlForm->input('meta_description');
    echo $this->SlForm->input('skin', array('options' => SlConfigure::read2('Cms.nodeSkins')));
    echo $this->SlForm->input('visible');

    if ($this->params['action'] == 'admin_add') {
        ClassRegistry::init('Cms.CmsImage');
        echo $this->SlForm->input('CmsImage.title', array('label' => __t('Thumb image title')));
        echo $this->SlForm->input('CmsImage.filename', array('label' => __t('Upload thumb image'), 'type' => 'file'));
    }

    echo $this->SlForm->input('CmsTag', array('label' => false, 'multiple' => 'checkbox'));

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
