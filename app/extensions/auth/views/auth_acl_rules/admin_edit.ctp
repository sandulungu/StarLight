<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('AuthAclRule');
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('auth_user_id', array('label' => __t('Select user'), 'empty' => true, 'after' =>
        ' '. __t('or group') .' '.
        $this->SlForm->input('auth_group_id', array('label' => false, 'div' => false, 'empty' => true))
    ));
    echo $this->SlForm->input('who');
    echo $this->SlForm->input('allow');
    echo $this->SlForm->input('what');
    echo $this->SlForm->input('collection', array('title' => 'Context (configuration collection name)'));
    
    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));

    SlConfigure::write('Asset.js.jquery', 'head');
?>
<script type="text/javascript">
    $('#AuthAclRuleUserId').change(function() {
        var val = $(this).val();
        $('#AuthAclRuleGroupId').attr('disabled', val ? 'disabled' : '');
        $('#AuthAclRuleWho').attr('readonly', val ? 'readonly' : '').val(val ? 'User'+val : '');
    }).change();
    
    $('#AuthAclRuleGroupId').change(function() {
        var val = $(this).val();
        $('#AuthAclRuleUserId').attr('disabled', val ? 'disabled' : '');
        $('#AuthAclRuleWho').attr('readonly', val ? 'readonly' : '').val(val ? 'Group'+val : '');
    }).change();
</script>
