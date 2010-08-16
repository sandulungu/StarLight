<?php

/**
 *
 * @property-read Node $Node
 */
class Image extends AppModel {
    public $useTable = 'cms_images';

    public $actsAs = array(
        'MeioUpload.MeioUpload' => array(
            'filename' => array(
                'dir' => 'files{DS}cms_images',
        		'maxSize' => 2097152, // 2MB
                'thumbsizes' => array(
                    'icon' => array(
                        'width' => 100,
                        'height' => 100,
                        'zoomCrop' => 'C',
                    ),
                ),
                'length' => array(
                    'minWidth' => 100, // 0 for not validates
                    //'maxWidth' => 0,
                    'minHeight' => 100,
                    //'maxHeight' => 0
                ),
                'validations' => array(
                    'Empty' => array('check' => true, 'on' => 'create'),
                )
            ),
        ),
        'Translate' => array('title'),
    );

    public $order = 'Image.weight';

    public $belongsTo = array(
        'Cms.Node',
    );

    public $validate = array(
//        'href' => array(
//            'rule' => 'url',
//        ),
        'weight' => array(
            'rule' => 'numeric',
            'required' => true,
        ),
        'visible' => array(
            'rule' => 'validBool',
            'required' => true,
        ),
    );

}
