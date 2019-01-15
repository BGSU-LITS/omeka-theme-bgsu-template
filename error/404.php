<?php
echo head(array('title' => __('Page Not Found')));

echo '<p class="text-lead">';
echo __('%s is not a valid URL.', html_escape($badUri));
echo '</p>' . PHP_EOL;

echo foot();
