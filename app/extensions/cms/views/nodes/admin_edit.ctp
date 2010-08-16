<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Edit book'));
    echo $this->SlForm->create('Book', array('type' => 'file'));
    echo $this->SlForm->hidden('id');

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('author');
    echo $this->SlForm->input('description');
    echo $this->SlForm->input('cover_filename', array('type' => 'file'));
    echo $this->SlForm->input('isbn', array('label' => 'ISBN'));
    echo $this->SlForm->input('user_id', array('label' => 'Owner'));
    echo $this->SlForm->input('Tag.Tag', array('label' => false, 'multiple' => 'checkbox'));
    //echo $this->SlForm->input('rating');

    echo $this->SlForm->end(__t('Save'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('Book');
