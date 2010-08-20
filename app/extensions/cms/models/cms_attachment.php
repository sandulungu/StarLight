<?php

/**
 *
 * @property-read CmsNode $CmsNode
 */
class CmsAttachment extends AppModel {
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

    public $order = array(
        'CmsAttachment.cms_node_id' => 'ASC',
        'CmsAttachment.title' => 'ASC',
    );

    public $belongsTo = array(
        'Cms.CmsNode',
    );

    public $validate = array(
        'title' => array(
        ),
    );

}
