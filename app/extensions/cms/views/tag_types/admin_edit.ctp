<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Edit tag group'));
    echo $this->SlForm->create('TagType');
    echo $this->SlForm->hidden('id');

    echo $this->SlForm->input('name');

    echo $this->SlForm->end(__t('Save'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('TagType');
