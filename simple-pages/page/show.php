<?php
$ancestors = array();

if (!$is_home_page) {
    $current = get_current_record('simple_pages_page', false);
    $table = get_db()->getTable('SimplePagesPage');
    $pages = $table->findAncestorPages($current->id);

    foreach (array_reverse($pages) as $page) {
        if ($page->is_published) {
            $ancestors[public_url($page->slug)] = $page->title;
        }
    }
}

echo head(array(
    'title' => metadata('simple_pages_page', 'title'),
    'ancestors' => $ancestors
));

echo $this->shortcodes(
    metadata('simple_pages_page', 'text', array('no_escape' => true))
);

echo PHP_EOL;
echo foot();
