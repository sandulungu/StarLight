<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Add book'));
    echo $this->SlForm->create('Book', array('type' => 'file'));

    echo $this->SlForm->input('title');
    echo $this->SlForm->input('author');
    echo $this->SlForm->input('description');
    echo $this->SlForm->input('cover_filename', array('type' => 'file'));
    echo $this->SlForm->input('isbn', array(
        'label' => 'ISBN',
        'after' => 'Numărul internațional standardizat al cărților (ISBN) este atribuit de către Biblioteca Națională fiecărei cărți nou publicate și este util în identificarea carții în bazele de date bibliografice internaționale, precum și pentru facilitarea operațiunilor de gestiune a stocurilor de către edituri, distribuitori, biblioteci și librării.',
    ));
    echo $this->SlForm->input('user_id', array('label' => 'Owner'));
    echo $this->SlForm->input('Tag.Tag', array('label' => false, 'multiple' => 'checkbox'));

    echo $this->SlForm->end(__t('Add'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('Book');
