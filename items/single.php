<?php
$output = '';

if ($title = metadata($item, 'display_title')) {
    if (empty($heading)) {
        $heading = 'h2';
    }

    $output .= '<' . $heading . ' class="record-title">';
    $output .= $title . '</' .  $heading . '>' . PHP_EOL;
}

$description = metadata(
    $item,
    array('Dublin Core', 'Description'),
    array('snippet' => 300, 'no_escape' => true)
);

if (empty($description) && $style === 'finding_aids') {
    $description = metadata(
        $item,
        array('Item Type Metadata', 'Introduction'),
        array('snippet' => 300, 'no_escape' => true)
    );
}

if ($description) {
    $output .= '<div class="record-description">' . PHP_EOL;
    $output .= text_to_paragraphs($description) . PHP_EOL . '</div>' . PHP_EOL;
}

if ($output) {
    $output = PHP_EOL . '<div class="record-details">' . PHP_EOL . $output;
    $output .= '</div>' . PHP_EOL;
}

$file = $item->getFile();

if ($file || $_GET['display'] !== 'list') {
    $output .= '<img alt=""';

    if (!empty($carousel)) {
        $output .= ' data-flickity-lazyload="';
    } else {
        $output .= ' src="';
    }

    if ($file) {
        $output .= $file->getWebPath('fullsize');
    } else {
        $output .= 'data:image/gif;base64,R0lGODlhAQABAIAAAP';
        $output .= '///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
    }

    $output .= '">' . PHP_EOL;
}

if ($output) {
    echo '<div class="record">' . PHP_EOL;
    echo link_to($item, 'show', $output) . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
