<?php
$item = $file->getItem();
$ancestors = array();

if ($collection = get_collection_for_item($item)) {
    $ancestors = array(
        url('collections') => 'Collections',
        record_url($collection) => metadata($collection, 'display_title')
    );
}

$ancestors[record_url($item)] = metadata($item, 'display_title');

echo head(array(
    'title' => metadata('file', 'display_title'),
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
echo file_markup($file, array('imageSize' => 'fullsize'));
echo all_element_texts('file');
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo foot();
