<?php
$style = get_theme_option('style');
$css = get_theme_option('collection_style');
$storage = Zend_Registry::get('storage');
$class = 'sidebar-nav';

if ($img = get_theme_option('collection_background')) {
    $uri = $storage->getUri($storage->getPathByType($img, 'theme_uploads'));
    $css .= '.sidebar-left { background-image: url(';
    $css .= html_escape($uri) . ') }';
    $class .= ' sidebar-nav-background';
}

if (!empty(trim($css))) {
    echo '<style>' . $css . '</style>' . PHP_EOL;
}

$href = record_url($collection);

if ($style === 'finding_aids') {
    $href = url(
        'items/browse',
        array(
            'collection' => $collection->id,
            'sort_field' => 'Dublin Core,Title',
            'sort_dir' => 'a'
        )
    );
}

echo '<h2 class="sidebar-title"><a href="' . $href . '">';

if ($img = get_theme_option('collection_logo')) {
    $uri = $storage->getUri($storage->getPathByType($img, 'theme_uploads'));

    echo '<img class="sidebar-image" src="' . html_escape($uri) . '" alt="';
    echo metadata($collection, 'display_title') . '">';
} else {
    echo metadata($collection, 'display_title');
}

echo '</a></h2>' . PHP_EOL;

echo $this->partial(
    'solr-search/results/sidebar.php',
    array(
        'collection' => $collection,
        'label' => __('Search Collection')
    )
);

if ($style === 'finding_aids') {
    $nav[] = array(
        'label' => __('Browse Items'),
        'uri' => $href
    );

} else {
    $nav[] = array(
        'label' => __('Home'),
        'uri' => record_url($collection)
    );

    $nav[] = array(
        'label' => __('Browse Items'),
        'uri' => url('items/browse', array('collection' => $collection->id))
    );
}

$nav[] = array(
    'label' => __('Item Tags'),
    'uri' => url('items/tags', array('collection' => $collection->id))
);

$nav[] = array(
    'label' => __('Advanced Search'),
    'uri' => url('items/search', array('collection' => $collection->id))
);

echo '<nav class="' . $class;
echo '" aria-label="Collection navigation">' . PHP_EOL;
echo nav($nav)->setUlClass('') . PHP_EOL;
echo '</nav>' . PHP_EOL;
