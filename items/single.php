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

if ($description) {
    $output .= '<div class="record-description">' . PHP_EOL;
    $output .= text_to_paragraphs($description) . PHP_EOL . '</div>' . PHP_EOL;
}

if ($output) {
    $output = PHP_EOL . '<div class="record-details">' . PHP_EOL . $output;
    $output .= '</div>' . PHP_EOL;
}

$output .= '<img alt=""';

if ($file = $item->getFile()) {
    if (!empty($carousel)) {
        $output .= ' data-flickity-lazyload="';
    } else {
        $output .= ' src="';
    }

    $output .= $file->getWebPath('fullsize') . '"';
}

$output .= '>' . PHP_EOL;

if ($output) {
    echo '<div class="record">' . PHP_EOL;
    echo link_to($item, 'show', $output) . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
