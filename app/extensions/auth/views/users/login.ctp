<style type="text/css">
</style>

<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('User');
    echo $this->SlForm->input('username');
    echo $this->SlForm->input('password');
    echo $this->SlForm->input('remember', array('type' => 'checkbox'));
    echo $this->SlForm->end(__t('Login'));

?>
<script type="text/javascript">
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('User');
