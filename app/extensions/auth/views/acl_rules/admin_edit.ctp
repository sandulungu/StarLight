<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('AclRule');
    if ($this->id) {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('user_id', array('label' => __t('Select user'), 'empty' => true, 'after' =>
        ' '. __t('or group') .' '.
        $this->SlForm->input('group_id', array('label' => false, 'div' => false, 'empty' => true))
    ));
    echo $this->SlForm->input('who');
    echo $this->SlForm->input('allow', array('checked' => true));
    echo $this->SlForm->input('what');
    echo $this->SlForm->input('collection', array('default' => 'global', 'title' => 'Context (configuration collection name)'));
    
    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));

?>
<script type="text/javascript">
    $('#AclRuleUserId').change(function() {
        var val = $(this).val();
        $('#AclRuleGroupId').attr('disabled', val ? 'disabled' : '');
        $('#AclRuleWho').attr('readonly', val ? 'readonly' : '').val(val ? 'User'+val : '');
    }).change();
    
    $('#AclRuleGroupId').change(function() {
        var val = $(this).val();
        $('#AclRuleUserId').attr('disabled', val ? 'disabled' : '');
        $('#AclRuleWho').attr('readonly', val ? 'readonly' : '').val(val ? 'Group'+val : '');
    }).change();
</script>

<?php
    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('AclRule');
