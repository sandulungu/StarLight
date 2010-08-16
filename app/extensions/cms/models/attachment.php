<?php

/**
 *
 * @property-read Node $Node
 */
class Attachment extends AppModel {
    public $useTable = 'cms_attachments';

    public $actsAs = array(
        'MeioUpload.MeioUpload' => array(
            'filename' => array(
                'dir' => 'files{DS}cms_attachments',
        		'maxSize' => 20971520, // 20MB
                'allowedExt' => array(
                    '.pdf', '.txt', '.html',
                    '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pps', '.ppsx',
                    '.zip', '.gz', '.bz2', '.rar', '.exe',
                    '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.ico',
                ),
                'validations' => array(
                    'Empty' => array('check' => true, 'on' => 'create'),
                    'InvalidMime' => array('check' => false),
                )
            ),
        ),
        'Translate' => array('title'),
    );

    public $order = 'Attachment.weight';

    public $belongsTo = array(
        'Cms.Node',
    );

    public $validate = array(
//        'href' => array(
//            'rule' => 'url',
//        ),
        'title' => array(
        ),
        'visible' => array(
            'rule' => 'validBool',
            'required' => true,
        ),
    );

}
