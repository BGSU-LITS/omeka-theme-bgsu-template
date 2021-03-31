<?php
queue_css_string('
#login {
    margin-top: 20px;
}
');

echo head(array('title' => __('Forgot Password')), $header);
echo flash();

echo '<p>' . __('Enter your email address to retrieve your password.') . '</p>';
echo '<form method="post">';
echo '<div class="field">';
echo '<div class="alpha">';
echo '<label for="email">' . __('Email') . '</label>';
echo '</div>';
echo '<div class="omega">';
echo  $this->formText('email', @$_POST['email']);
echo '</div>';
echo '<input type="submit" class="button" value="' . __('Submit') . '">';
echo '</form>';

echo '<p id="login">' . link_to('users', 'login', __('Back to Log In')) . '</p>';
echo foot(array(), $footer);
