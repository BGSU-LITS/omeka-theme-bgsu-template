<?php
$carousel = get_theme_option('featured_carousel');

if ($carousel) {
    queue_js_url(BGSU_TEMPLATE . 'flickity.js');
}

echo head(array(
    'title' => metadata('collection', 'display_title'),
    'ancestors' => array(url('collections') => 'Collections')
));

echo '<div class="sidebar">' . PHP_EOL;
echo '<div class="sidebar-left" id="collection">' . PHP_EOL;

echo $this->partial(
    'collections/sidebar.php',
    array('collection' => $collection)
);

echo '</div>' . PHP_EOL;
echo '<div>' . PHP_EOL;

$description = metadata('collection', array('Dublin Core', 'Description'));

if ($description && strpos($description, '<p>') === false) {
    $description = '<p>' . $description . '</p>';
}
        
if ($description && !get_theme_option('featured_first')) {
    echo $description . PHP_EOL;
}

$featured = get_records(
    'Item',
    array(
        'collection' => $collection->id,
        'featured' => true,
        'hasImage' => true,
        'sort_field' => 'random'
    ),
    12
);

if (!empty($featured)) {
    echo '<div id="featured">' . PHP_EOL;
    echo '<h2>' . __('Featured') . '</h2>' . PHP_EOL;
    echo '<div class="records records-gallery';

    foreach (array('aspect', 'column', 'images', 'single') as $option) {
        if (get_theme_option('featured_' . $option)) {
            echo ' records-gallery-' . $option;
        }
    }

    echo '">' . PHP_EOL;

    foreach ($featured as $item) {
        echo $this->partial(
            'items/single.php',
            array(
                'item' => $item,
                'carousel' => $carousel,
                'heading' => 'h3',
                'aspect' => get_theme_option('featured_aspect')
            )
        );
    }

    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

if ($description && get_theme_option('featured_first')) {
    echo $description . PHP_EOL;
}

fire_plugin_hook(
    'public_collections_show',
    array('view' => $this, 'collection' => $collection)
);

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo foot();
