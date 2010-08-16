<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Add tags'));
    echo $this->SlForm->create('Tag');

    echo $this->SlForm->input('name', array('after' => 'You may add multiple tags, separated by ","'));
    echo $this->SlForm->input('tag_type_id', array('empty' => true));

    echo $this->SlForm->end(__t('Add'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('Tag');
