<?php
echo head(array('title' => __('Activate Your Account')), $header);
echo flash();

echo '<p><strong>' . __('Your Name') . '</strong><br>';
echo html_escape($user->name) . '</p>';

echo '<p><strong>' . __('Your Username') . '</strong><br>';
echo html_escape($user->username) . '</p>';

echo '<form method="post">';
echo '<div class="field">';
echo '<div class="alpha">';
echo $this->formLabel('new_password1', __('Enter a Password'));
echo '</div>';
echo '<div class="omega">';
echo '<input type="password" name="new_password1" id="new_password1"';
echo ' class="textinput">';
echo '</div>';
echo '</div>';

echo '<div class="field">';
echo '<div class="alpha">';
echo $this->formLabel('new_password2', __('Confirm Password'));
echo '</div>';
echo '<div class="omega">';
echo '<input type="password" name="new_password2" id="new_password2"';
echo ' class="textinput">';
echo '</div>';
echo '</div>';

echo '<input type="submit" class="button" name="submit" value="';
echo __('Activate') . '">';
echo '</form>';

echo foot(array(), $footer);
