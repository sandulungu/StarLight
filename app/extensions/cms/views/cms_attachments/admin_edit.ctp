<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsAttachment', array('type' => 'file'));
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }
    if (isset($this->data['CmsAttachment']['cms_node_id'])) {
        echo $this->SlForm->hidden('cms_node_id');
    }

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('filename', array('div' => 'input file required', 'label' => __t('Upload file'), 'type' => 'file'));
    echo $this->SlForm->input('visible');

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
