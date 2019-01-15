<?php
echo head(array('title' => __('Page Forbidden')));

echo flash();

echo '<p class="text-lead">';
echo __('You do not have permission to access this page.');
echo '</p>' . PHP_EOL;

echo foot();
