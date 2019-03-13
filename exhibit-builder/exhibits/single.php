<?php
$output = '';

if ($title = metadata($exhibit, 'title')) {
    if (empty($heading)) {
        $heading = 'h2';
    }

    $output .= '<' . $heading . ' class="record-title">';
    $output .= $title . '</' .  $heading . '>' . PHP_EOL;
}

$description = metadata(
    $exhibit,
    'description',
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

if (!empty($carousel)) {
    $output .= ' data-flickity-lazyload="';
} else {
    $output .= ' src="';
}

if ($file = $exhibit->getFile()) {
    $output .= $file->getWebPath('fullsize');
} else {
    $output .= 'data:image/gif;base64,R0lGODlhAQABAIAAAP';
    $output .= '///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
}

$output .= '">' . PHP_EOL;

if ($output) {
    echo '<div class="record">' . PHP_EOL;
    echo link_to($exhibit, 'show', $output) . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
