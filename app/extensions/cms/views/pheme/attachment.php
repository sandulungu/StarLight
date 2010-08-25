<?php

/**
 * Attachements
 *
 * Attachment
 */

?>
<div class="sl-node-attachments">
    {loop("itemTag":"li")}
        <ul>
            <li>
                <a href="{webroot}files/cms_attachments/{$filename}{/webroot}">
                    {if("var":"title")}{$title}{else}{$basename}{/if}
                </a>
            </li>
        </ul>
    {/loop}
</div>
<?php

/**
 * Show a list of attachments for a node
 */
class AttachmentsParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new PhemeLoopParser();
        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {

        if (!empty($blockParams['nodeId'])) {
            $attachment = ClassRegistry::init('CmsAttachment');
            $data = $attachment->find('all', array(
                'conditions' => array(
                    'cms_node_id' => $blockParams['nodeId'],
                    'visible' => true,
                ),
                'recursive' => -1,
            ));
        } else {
            $data = $this->_getVar('CmsAttachment');
        }

        if (empty($data[0]['filename'])) {
            return;
        }
        $this->blocks["loop"]->params[0] =& $data;

        return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('Attachments', new AttachmentsParser(), true);

// -----------------------------------------------------------------------------

?>
<a class="sl-node-attachment" href="{$mediaPath}/{$dirname}/{$basename}">{$title}</a>
<?php

/**
 * Show download link for an attachment
 */
class AttachmentParser extends PhemeParser {

    function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'var' => 'CmsAttachment',
        );

        if (!empty($blockParams['id'])) {
            $attachment = ClassRegistry::init('CmsAttachment');
            list($data) = $attachment->find('first', array(
                'conditions' => array('id' => $blockParams['id']),
                'recursive' => -1,
            ));
        }
        elseif (!empty($blockParams['index'])) {
            $data = $this->_getVar("CmsAttachment.{$blockParams['index']}");
        }
        else {
            $data = $this->_getVar($blockParams["var"]);
        }

        if (empty($data['filename'])) {
            return;
        }
        $this->vars = $data;

        return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('Attachment', new AttachmentParser(), true);
