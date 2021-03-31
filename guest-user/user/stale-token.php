<?php
echo head(array('title' => 'Complete Your Registration'));
echo flash();

echo '<p>' . __(
    'Your temporary access to the site has expired. Please check your email' .
    ' for the link to confirm your registration.'
) . '</p>';

echo '<p>' . __(
    'You have been logged out, but can continue browsing the site.'
) . '</p>';

echo foot();
