<?php
queue_css_string('
.embedded-content br:first-child {
    display: none;
}

.table.table-elements {
    width: 100%;
}

.table.table-elements + .table.table-elements {
    margin-top: -8px;
}

.table.table-elements th {
    width: 20%;
}

.table.table-elements [data-toggle] {
    margin-left: -13px;
}

.citation {
    font-size: 0.875em;
    line-height: 1.5;
}

.citation, .url {
    word-break: break-word;
}

.rights-statements {
    margin: 4px 0 !important;
}

.flickity-enabled {
    min-height: 80px;
}

.flickity-viewport {
    transition: height 1s ease, opacity 1s ease;
}
');

if (sizeof($item->Files) > 4) {
    queue_js_url(BGSU_TEMPLATE . 'flickity.js');
}

$ancestors = array();

if (!get_theme_option('hide_ancestors')) {
    if (!empty($exhibit)) {
        $ancestors = array(
            url('exhibits') => 'Exhibits',
            exhibit_builder_exhibit_uri($exhibit) => metadata($exhibit, 'title')
        );
    } elseif ($collection = get_collection_for_item()) {
        $ancestors = array(
            url('collections') => 'Collections',
            record_url($collection) => metadata($collection, 'display_title')
        );
    }
}

$image = null;
$file = $item->getFile();

if ($file) {
    $image = $file->getWebPath('fullsize');
}

echo head(array(
    'title' => metadata('item', 'display_title'),
    'description' => metadata('item', array('Dublin Core', 'Description')),
    'image' => $image,
    'ancestors' => $ancestors
));

if (!get_theme_option('hide_ancestors')) {
    if (!empty($exhibit)) {
        echo $this->partial(
            'exhibit-builder/exhibits/nav.php',
            array('exhibit' => $exhibit, 'content' => true)
        );
    } else {
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
    }
}

echo '<div class="records records-gallery records-gallery-aspect">' . PHP_EOL;

$count = 1;

foreach ($item->Files as $file) {
    echo '<div class="record">' . PHP_EOL;

    $title = metadata($file, array('Dublin Core', 'Title'));

    if (!$title) {
        $title = __('File %s', $count);
    }

    $description = metadata(
        $file,
        array('Dublin Core', 'Description'),
        array('snippet' => 300, 'no_escape' => true)
    );

    if ($description) {
        $description = text_to_paragraphs($description);
    }

    $markup = file_markup(
        $file,
        array(
            'imageSize' => 'fullsize',
            'imgAttributes' => array('alt' => $title),
            'linkToMetadata' => get_theme_option('files_metadata'),
            'linkAttributes' => get_theme_option('files_window')
                ? array('target' => '_blank')
                : array(),
            'linkText' =>
                '<div class="record-details">' .
                '<div class="record-title">' . $title . '</div>' .
                '<div class="record-description">' . $description . '</div>' .
                '</div>' .
                '<img src="' . img('fallback-file.png') . '" alt="">'
        ),
        array()
    );

    $markup = preg_replace(
        '/<img [^>]+>/',
        '<div class="record-image">$0</div>',
        $markup
    );

    if ($count > 4) {
        $markup = preg_replace(
            '/ src="([^"]+)" /',
            ' data-flickity-lazyload="$1" aria-hidden="true" ',
            $markup
        );
    }

    $watermark = get_theme_option('watermark_images') !== '0';

    $mime_types = array(
        'image/gif',
        'image/jpeg',
        'image/png'
    );

    if ($watermark && in_array($file->mime_type, $mime_types)) {
        $preg = '{//([^/]+)/((.*/)?files/original/)}';
        $replace = '//$1/w/$2';

        if (preg_match($preg, $markup, $matches)) {
            if ($matches[1] === 'digitalgallery.bgsu.edu') {
                $replace = '//lib.bgsu.edu/w/digitalgallery/$2';
            }
        }

        $markup = preg_replace($preg, $replace, $markup);

        $markup = str_replace(
            '<a ',
            '<a referrerpolicy="unsafe-url" ',
            $markup
        );
    }

    echo $markup;

    echo '</div>' . PHP_EOL;

    $count++;
}

echo '</div>' . PHP_EOL;

try {
    $content = metadata(
        'item',
        array(ElementSet::ITEM_TYPE_NAME, 'Content'),
        array('all' => true)
    );

    if (!empty($content)) {
        echo '<div class="embedded-content">' . PHP_EOL;

        foreach ($content as $html) {
            echo $html;
        }

        echo '</div>' . PHP_EOL;
    }
}
catch (Omeka_Record_Exception $e) {
    // Do nothing.
}

echo '<div class="sidebar">' . PHP_EOL;
echo '<div class="sidebar-right">' . PHP_EOL;
echo '<h3 class="sidebar-title">';

if (plugin_is_active('MlaCitations')) {
    echo __('MLA Citation');
} else {
    echo __('Citation');
}

echo '</h3>' . PHP_EOL;
echo '<div class="citation">';
echo metadata('item', 'citation', array('no_escape' => true));
echo '</div>' . PHP_EOL;

if (get_theme_option('social')) {
    $url = urlencode(absolute_url());

    echo '<hr>' . PHP_EOL;
    echo '<h3 class="sidebar-title">' . __('Share') . '</h3>' . PHP_EOL;
    echo '<div class="social">' . PHP_EOL;
    echo '<ul>';
    echo '<li><a href="https://www.facebook.com/sharer.php?u=';
    echo $url . '">Facebook</a></li>' . PHP_EOL;
    echo '<li><a href="https://twitter.com/intent/tweet?url=';
    echo $url . '">Twitter</a></li>' . PHP_EOL;
    echo '<li><a href="mailto:?subject=';
    echo urlencode(option('site_title') . ': ');
    echo urlencode(metadata('item', 'display_title')) . '&amp;body=';
    echo $url . '">Email</a></li>' . PHP_EOL;
    echo '</ul>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

if (metadata('item', 'has tags')) {
    $tags = tag_string('item');

    if (!empty($collection)) {
        $tags = str_replace(
            '?',
            '?collection=' . $collection->id . '&amp;',
            $tags
        );
    }

    echo '<hr>' . PHP_EOL;
    echo '<h3 class="sidebar-title">' . __('Tags') . '</h3>' . PHP_EOL;
    echo '<div class="tags">' . $tags . '</div>' . PHP_EOL;
}

echo '</div>' . PHP_EOL;
echo '<div>' . PHP_EOL;
echo all_element_texts('item');
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

fire_plugin_hook('public_items_show', array('view' => $this, 'item' => $item));

if (!get_theme_option('hide_ancestors')) {
    if (empty($exhibit)) {
        echo '</div>' . PHP_EOL;
        echo '</div>' . PHP_EOL;
    }
}

echo foot();
