<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsNavigationLink');
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }
    if (isset($this->data['CmsNavigationLink']['cms_node_id'])) {
        echo $this->SlForm->hidden('cms_node_id');
    }

    echo $this->SlForm->input('parent_id', array('empty' => true));
    echo $this->SlForm->input('title');
    echo $this->SlForm->input('hint');
    echo $this->SlForm->input('url');
    echo $this->SlForm->input('class');
    echo $this->SlForm->input('target');
    echo $this->SlForm->input('rel');
    echo $this->SlForm->input('name');
    echo $this->SlForm->input('collection');
    echo $this->SlForm->input('visible');

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
