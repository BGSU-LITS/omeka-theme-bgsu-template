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
echo '<div class="records records-gallery records-gallery-aspect">' . PHP_EOL;
echo '<div class="record">' . PHP_EOL;

$markup = file_markup(
    $file,
    array(
        'imageSize' => 'fullsize',
        'imgAttributes' => array('alt' => __('File')),
        'linkAttributes' => get_theme_option('files_window')
            ? array('target' => '_blank')
            : array()
    ),
    array()
);

$markup = preg_replace(
    '/<img [^>]+>/',
    '<div class="record-image">$0</div>',
    $markup
);

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
}

echo $markup;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo all_element_texts('file');
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo foot();
