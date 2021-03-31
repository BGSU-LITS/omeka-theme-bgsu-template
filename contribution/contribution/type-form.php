<?php
if (!$type) {
    return;
}

if (isset($profileType)) {
    echo '<h2>' . $profileType->label . '</h2>';
}

$user = current_user();

if ($user) {
    echo '<p><strong>' . __('You are logged in as:') . '</strong><br>';
    echo metadata($user, 'name') . ' &lt;';
    echo metadata($user, 'email') . '&gt;</p>';
} else if (
    get_option('contribution_open') || 
    get_option('contribution_strict_anonymous')
) {
    echo '<div class="field">';
    echo '<div class="alpha">';

    if (get_option('contribution_strict_anonymous')) {
        echo $this->formLabel('contribution_name', __('Name (Optional):')); 
    } else {
        echo $this->formLabel('contribution_name', __('Name:'));
    }

    echo '</div>';
    echo '<div class="omega">';

    if (isset($_POST['contribution_name'])) {
        $name = $_POST['contribution_name'];
    } else {
        $name = '';
    }

    echo $this->formText('contribution_name', $name);
    echo '</div>';
    echo '</div>';

    echo '<div class="field">';
    echo '<div class="alpha">';

    if (get_option('contribution_strict_anonymous')) {
        echo $this->formLabel(
            'contribution_email',
            __('Email (Optional):')
        ); 
    } else {
        echo $this->formLabel(
            'contribution_email',
            __('Email:')
        );
    }

    echo '</div>';
    echo '<div class="omega">';

    if (isset($_POST['contribution_email'])) {
        $email = $_POST['contribution_email'];
    } else {
        $email = '';
    }

    echo $this->formText('contribution_email', $email);
    echo '</div>';
    echo '</div>';
}

if (isset($profileType)) {
    foreach ($profileType->Elements as $element) {
        echo $this->profileElementForm($element, $profile);
    }
}

echo '<h2>' . __('Submission Information') . '</h2>';

foreach ($type->getTypeElements() as $contributionTypeElement) {
    echo $this->elementForm(
        $contributionTypeElement->Element,
        $item
    );
}

if ($type->isFileRequired() || $type->isFileAllowed()) {
    echo '<div class="field">';
    echo '<div class="alpha">';
    echo $this->formLabel('contributed_file', __('Upload a file:'));

    $size = ini_get('upload_max_filesize');
    
    if ($size) {
        $size = preg_replace('/([KMG])$/', '$1i', $size) . 'B';
        echo '<div>(' . __('Maxmimum Size: %s', $size) . ')</div>';
    }

    echo '</div>';
    echo '<div class="omega">';
    echo $this->formFile('contributed_file', array('class' => 'fileinput'));
    echo '</div>';
    echo '</div>';
}

fire_plugin_hook(
    'contribution_type_form',
    array('type' => $type, 'view' => $this)
);
