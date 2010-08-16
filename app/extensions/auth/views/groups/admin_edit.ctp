<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('Group');
    if ($this->id) {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('name');
    echo $this->SlForm->input('description');
    
    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
