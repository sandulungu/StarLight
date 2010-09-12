<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsTag');
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('cms_tag_category_id', array('label' => __t('Tag category')));
//    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->input('name');
        echo $this->SlForm->input('cms_node_id', array('empty' => true, 'label' => __t('Node containing tag description')));
//    } else {
//        echo $this->SlForm->input('name', array('type' => 'textarea', 'after' => __t('You may add multiple tags, putting each tag in a new line.')));
//    }

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
