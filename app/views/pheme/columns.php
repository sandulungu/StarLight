<?php

/**
 * Columns
 */

Pheme::init('Blocks', 'SiteElements');

?>
{Column("id":"Left","width":"33.3")/}
{Column("id":"Content","width":"33.4")/}
{Column("id":"Right","width":"33.3")/}
<div class="sl-clear">&nbsp;</div>
<?php

class ColumnsParser extends PhemeParser {

    /**
     * Array of blocks, containing: ..., array($parsedHtml, $blockParams), ...
     *
     * @var array
     */
    public $columns = array();

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'showEmpty' => false,
            'unit' => '%',
        );

        $html = parent::parse($html, $blockName);

        // span across empty columns
        $lastCol = null;
        $extraWidth = $extraColspan = 0;
        foreach ($this->columns as &$col) {
            $colHtml = trim($col[0]);
            if (empty($colHtml) && !$blockParams['showEmpty']) {
                if ($lastCol) {
                    $lastCol[1]['width'] += $col[1]['width'];
                    $lastCol[1]['colspan']++;
                } else {
                    $extraWidth += $col[1]['width'];
                    $extraColspan++;
                }
            }
            elseif ($extraWidth) {
                $col[1]['width'] += $extraWidth;
                $col[1]['colspan'] += $extraColspan;
                $extraWidth = 0;
                $extraColspan = 0;
            }
            $lastCol =& $col;
        }

        $first = true;
        foreach ($this->columns as $uid => $col2) {
            $colHtml = trim($col2[0]);
            if (!empty($colHtml) || $blockParams['showEmpty']) {
                $this->vars['width'] = $col2[1]['width'] ? $col2[1]['width'].$blockParams['unit'] : false;
                $this->vars['id'] = $col2[1]['id'];
                $this->vars['colspan'] = $col2[1]['colspan'] > 1 ? $col2[1]['colspan'] : false;
                $this->vars['html'] = $colHtml;
                $this->vars['first'] = $first;
                $this->vars['last'] = $col === $col2;
                $html = r($uid, parent::parse(null, 'ColumnWrapper'), $html);
                $first = false;
            } else {
                $html = r($uid, '', $html);
            }
        }

        return $html;
    } // parse(...)
}

Pheme::registerOutputBuffer('Columns', new ColumnsParser(), true);

// -----------------------------------------------------------------------------

?>
<div style='{if("var":"width")} width: {$width} {/if}' class='sl-column sl-column-{$id} {if("var":"colspan")} sl-colspan-{$colspan} {/if}'>
    <div class='sl-column-inner sl-column-inner-{$id} {if("var":"first")} sl-column-first {/if}{if("var":"last")} sl-column-last {/if}'>
        {$html}
    </div>
</div>
<?php

Pheme::registerOutputBuffer('ColumnWrapper');

// -----------------------------------------------------------------------------

class ColumnParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["Column"] = new PhemeLoopParser();
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'id' => false,
            'width' => 0,
            'colspan' => 1,
        );

        if (empty($blockParams['id']) && preg_match('/^[a-z0-9]+$/i', $html)) {
            $blockParams['id'] = $html;
            $html = null;
        }

        if (empty($html) && $blockParams['id']) {
            $html = $blockParams['id'] == 'Content' ?
                '{SiteContent/}' : "{Blocks(\"id\":\"{$blockParams['id']}\")/}";
        }
        if ($html) {
            $uid = SL::uniqid();
            $this->referrer()->columns[$uid] = array(
                parent::parse($html, $blockName),
                $blockParams
            );
            return $uid;
        }
    } // parse(...)
}

Pheme::register('Column', new ColumnParser(), null, 'Columns');