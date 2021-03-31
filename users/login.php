<?php
queue_css_string('
#fieldset-login {
    margin-bottom: -20px;
}

#remember-label {
    float: left;
}

#forgot-password {
    margin-top: 20px;
}
');

echo head(array('title' => __('Log In')), $header);
echo flash();
echo $this->form->setAction($this->url('users/login'));

if (empty($required)) {
    echo '<p id="forgot-password">';
    echo link_to('users', 'forgot-password', __('Forgot Password')) . '</p>';
}

echo foot(array(), $footer);
