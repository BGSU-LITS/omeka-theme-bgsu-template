<?php
queue_css_string('
#terms {
    margin-left: 20px;
    text-indent: -20px;
    line-height: 1.5;
}
');

echo head(array('title' => 'Contribute an Item'));
echo flash();

if (!($user = current_user()) && !get_option('contribution_open')) {
    $session = new Zend_Session_Namespace;
    $session->redirect = absolute_url();

    echo '<p><a href="' . url('guest-user/user/register') . '">';
    echo get_option('guest_user_register_text') 
        ? get_option('guest_user_register_text')
        : __('Register');
    echo '</a>' . __(' or ');
    echo '<a href="' . url('guest-user/user/login') . '">';
    echo get_option('guest_user_login_text') 
        ? get_option('guest_user_login_text')
        : __('Log In');
    echo '</a>' . __(' to continue.');
} else {
    echo '<form method="post" enctype="multipart/form-data">';

    $options = get_table_options('ContributionType');

    if (sizeof($options) > 2) {
        echo '<label for="contribution-type">';
        echo __("What type of item do you want to contribute?");
        echo '</label><br>';
    
        echo $this->formSelect(
            'contribution_type',
            isset($type) ? $type->id : '',
            array('multiple' => false, 'id' => 'contribution-type'),
            $options
        );

        echo '&nbsp;<input type="submit" name="submit-type" value="Select"';
        echo ' class="button">';
        echo '</div>';
    } else {
        unset($options['']);

        echo '<input type="hidden" name="contribution_type" value="';
        echo key($options) . '">';
    }

    if (isset($type)) {
        include('type-form.php');

        echo '<div class="field">';
        echo $this->formHidden(
            'contribution-public', 
            isset($_POST['contribution-public']) 
                ? $_POST['contribution-public'] 
                : 1
        );

        echo '<div>';

        echo $this->formCheckbox(
            'contribution-anonymous', 
            isset($_POST['contribution-anonymous']) 
                ? $_POST['contribution-anonymous'] 
                : 0, 
            null, 
            array(1, 0)
        );

        echo $this->formLabel(
            'contribution-anonymous',
            __('Keep my identity private when publishing this item.')
        );

        echo '</div>';
        echo '</div>';

        echo '<p id="terms">';

        echo $this->formCheckbox(
            'terms-agree',
            isset( $_POST['terms-agree']) ?  $_POST['terms-agree'] : 0,
            null,
            array('1', '0')
        );
                        
        echo '<label for="terms-agree">';
        echo get_option('contribution_consent_text');
        echo '</label>';
        echo '</p>';

        if (isset($captchaScript)) {
            echo '<div id="captcha">' . $captchaScript . '</div>';
        }
        
        echo $this->formSubmit(
            'form-submit', 
            __('Contribute'),
            array('class' => 'button button-primary')
        );
    }

    echo $csrf;
    echo '</form>';
}

echo foot();
