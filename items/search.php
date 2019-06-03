<?php
queue_js_url(BGSU_TEMPLATE . 'addremove.js');

$ancestors = array();

if (!empty($_GET['collection'])) {
    $collection = get_record_by_id('collection', $_GET['collection']);

    $ancestors = array(
        url('collections') => 'Collections',
        record_url($collection) => metadata($collection, 'display_title')
    );
}

echo head(array(
    'title' => __('Advanced Search'),
    'ancestors' => $ancestors
));

echo '<div class="sidebar">' . PHP_EOL;

if (!empty($collection)) {
    echo '<div class="sidebar-left" id="collection">' . PHP_EOL;

    echo $this->partial(
        'collections/sidebar.php',
        array('collection' => $collection)
    );

    echo '</div>' . PHP_EOL;
}

echo '<div>' . PHP_EOL;

echo $this->partial('items/search-form.php');

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo '<script>bgsu_addremove.setup();</script>';
echo foot();
