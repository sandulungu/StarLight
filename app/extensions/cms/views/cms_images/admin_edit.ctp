<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsImage', array('type' => 'file'));
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }
    if (isset($this->data['CmsImage']['cms_node_id'])) {
        echo $this->SlForm->hidden('cms_node_id');
    }

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('filename', array('div' => 'input file required', 'label' => __t('Upload image'), 'type' => 'file'));
    echo $this->SlForm->input('href');
    echo $this->SlForm->input('visible');
    echo $this->SlForm->input('weight');

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
