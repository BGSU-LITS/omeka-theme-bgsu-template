<?php
queue_css_string('
.hTagcloud > .popularity {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: center;
}

.hTagcloud > .popularity > li {
    display: inline-block;
    line-height: 1;
    margin: 0;
    padding: 8px;
    vertical-align: middle;
}

.hTagcloud > .popularity > .popular {
    font-size: 1em;
}

.hTagcloud > .popularity > .v-popular {
    font-size: 1.1em;
}

.hTagcloud > .popularity > .vv-popular {
    font-size: 1.2em;
}

.hTagcloud > .popularity > .vvv-popular {
    font-size: 1.3em;
}

.hTagcloud > .popularity > .vvvv-popular {
    font-size: 1.4em;
}

.hTagcloud > .popularity > .vvvvv-popular {
    font-size: 1.5em;
}

.hTagcloud > .popularity > .vvvvvv-popular {
    font-size: 1.6em;
}

.hTagcloud > .popularity > .vvvvvvv-popular {
    font-size: 1.7em;
}

.hTagcloud > .popularity > .vvvvvvvv-popular {
    font-size: 1.8em;
}
');

$ancestors = array();

if (!empty($_GET['collection'])) {
    $collection = get_record_by_id('collection', $_GET['collection']);

    $ancestors = array(
        url('collections') => 'Collections',
        record_url($collection) => metadata($collection, 'display_title')
    );
}

echo head(array(
    'title' => __('Item Tags'),
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

$tags = tag_cloud($tags, 'items/browse');

if ($collection) {
    $tags = str_replace(
        '?',
        '?collection=' . $collection->id . '&amp;',
        $tags
    );
}

echo $tags . PHP_EOL;

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo foot();
