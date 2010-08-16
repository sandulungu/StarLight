<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Edit tag'));
    echo $this->SlForm->create('Tag');
    echo $this->SlForm->hidden('id');

    echo $this->SlForm->input('name');
    echo $this->SlForm->input('tag_type_id', array('empty' => true));

    echo $this->SlForm->end(__t('Save'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('Tag');
