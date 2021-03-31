<?php
echo head(array('title' => __('%s Profile', $user->name)));
echo flash();

if (empty($userprofilesprofile)) {
    echo __('No public profile');
} else {
    $type = $userprofilesprofile->getProfileType();

    if (is_allowed($userprofilesprofile, 'edit')) {
        if ($userprofilesprofile->public == 1) {
            echo '<h2>' . __('%s (Public)', $type->label) . '</h2>';
        } else {
            echo '<h2>' . __('%s (Private)', $type->label) . '</h2>';
        }
    } elseif ($type) {
        echo '<h2>' . $type->label . '</h2>';
    }

    foreach ($userprofilesprofile->getElements() as $element) {
        echo '<h3>' .  $element->name . '</h3>';
        echo '<p>';
    
        if (get_class($element) == 'Element') {
            $texts = $userprofilesprofile->getElementTextsByRecord($element);

            foreach ($texts as $text) {
                echo html_escape($text->text) . '<br>';
            }
        } else {
            $valueObject = $userprofilesprofile
                ->getValueRecordForMulti($element);

            if ($valueObject) {
                $values = $valueObject->getValuesForDisplay();

                foreach ($values as $value) {
                    echo html_escape($value) . '<br>';
                }
            }
        }

        echo '</p>';
    }

    fire_plugin_hook(
        'user_profiles_user_page', 
        array('user' => $user, 'view' => $this)
    );
    
    if (
        current_user() && $user->id == current_user()->id || 
        is_allowed('UserProfiles_Profile', 'edit')
    ) {
        echo '<a href="' . url(
            'user-profiles/profiles/edit/id/' . $user->id . 
            '/type/' . $userprofilestype->id
        ). '" class="button">';

        echo __('Edit %s', $userprofilestype->label) .'</a>';
    }
}

echo foot();