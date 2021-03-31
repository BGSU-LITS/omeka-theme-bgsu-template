<?php
echo head(array('title' => __('Edit Your %s', $userprofilestype->label)));
echo flash();

echo '<form method="post">';

foreach($userprofilestype->Elements as $element) {
    echo $this->profileElementForm($element, $userprofilesprofile);
}

echo '<input type="submit" name="submit" class="button" value="';
echo __('Save Changes') . '">';
echo '<input type="hidden" value="0" name="public">';
echo '</form>';

echo foot();