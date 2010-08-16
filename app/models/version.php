<?php

class Version extends AppModel {
    public $useTable = 'core_versions';

    public $actsAs = array(
        'Mirrored' => array(
            'indexField' => 'name',
            'valueField' => 'version',
        ),
    );

}
