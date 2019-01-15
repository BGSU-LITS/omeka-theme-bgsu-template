<?php
if (!isset($_GET['display']) || $_GET['display'] !== 'list') {
    $_GET['display'] = 'gallery';
}

echo head(array('title' => __('Collections')));

echo pagination_links();
echo '<div class="records records-paginated records-';
echo $_GET['display'] . '">' . PHP_EOL;

foreach (loop('collections') as $collection) {
    echo $this->partial(
        'collections/single.php',
        array('collection' => $collection, 'display' => $_GET['display'])
    );

    fire_plugin_hook(
        'public_collections_browse_each',
        array('view' => $this, 'collection' => $collection)
    );
}

echo '</div>' . PHP_EOL;
echo pagination_links();

fire_plugin_hook(
    'public_collections_browse',
    array('collections' => $collections, 'view' => $this)
);

echo foot();
