<?php
echo head(array('title' => __('Method Not Allowed')));

echo '<p class="text-lead">';

echo __(
    'The method used to access this URL (%s) is not valid.',
    html_escape($this->method)
);

echo '</p>' . PHP_EOL;

echo foot();
