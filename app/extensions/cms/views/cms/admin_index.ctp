<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (Configure::read()) {
        echo $this->SlHtml->div('.sl-msg-notice', __t("<b>Security warning</b>: Before deploymont set <b>debug</b> to <b>0</b> in <em>/app/config/core.php</em>."));
    }

    if (!in_array('Group2', SlAuth::user('roles'))) {
        echo $this->SlHtml->div('.sl-msg-message', __t("To access CMS' administrative panel please login as a member of Contributors group."));
        SlConfigure::write('Navigation.sections.active', false, false, 'important');
        echo Sl::requestAction('/admin/auth/users/login');
    }
    else {
        echo $this->SlHtml->p('To edit site content, click in the left menu "Content nodes" link.');
    }

    