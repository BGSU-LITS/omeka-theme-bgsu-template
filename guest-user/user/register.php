<?php
echo head(array(
    'title' => get_option('guest_user_register_text') 
        ? get_option('guest_user_register_text')
        : __('Register')
));

echo flash();

if ($capabilities = get_option('guest_user_capabilities')) {
    echo '<p>' . $capabilities . '</p>';
}

echo $this->form;
echo foot();
