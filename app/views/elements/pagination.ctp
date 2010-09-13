<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (isset($this->Paginator)) {
        Pheme::init('JqueryPrettyLoader');
        SlConfigure::write('Asset.js.ready.pagination', "$('.sl-pagination a').click(function(){ $(this).parents('.sl-pagination').parent().load($(this).attr('href')); return false; })");

        if (!isset($options) || $options) {
            if (!isset($options)) {
                $options = array(
                    'url' => am($this->params['named'], $this->params['pass']),
                );
            }
            $this->Paginator->options($options);
        }
        
        echo $this->SlHtml->div(".sl-pagination", implode(' &nbsp; ', array(
            $paginator->prev(__t('« Previous'), null, null, array('class' => 'sl-disabled')),
            $paginator->numbers(),
            $paginator->next(__t('Next »'), null, null, array('class' => 'sl-disabled')),
            $paginator->counter('range'),
        )));
    }
