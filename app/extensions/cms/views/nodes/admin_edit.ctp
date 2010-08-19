<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('Node', array('type' => 'file'));
    if ($this->id) {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('short_title');
    echo $this->SlForm->input('teaser');
    echo $this->SlForm->input('body');
    echo $this->SlForm->input('meta_keywords');
    echo $this->SlForm->input('meta_description');
    echo $this->SlForm->input('skin', array('options' => SlConfigure::read2('Cms.nodeSkins')));
    echo $this->SlForm->input('visible', array('checkedByDefault' => true));

    ClassRegistry::init('Cms.Image');
    echo $this->SlForm->input('Image.title');
    echo $this->SlForm->input('Image.filename', array('type' => 'file'));

    echo $this->SlForm->input('Tag', array('multiple' => 'checkbox'));

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
