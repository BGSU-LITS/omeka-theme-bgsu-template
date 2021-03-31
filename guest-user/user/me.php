<?php
echo head(array('title' => get_option('guest_user_dashboard_label')));
echo flash();


foreach ($widgets as $index => $widget) {
    echo GuestUserPlugin::guestUserWidget($widget);
}

echo foot();
