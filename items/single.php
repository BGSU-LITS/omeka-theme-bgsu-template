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

if (empty($description)) {
    if (!empty($style) && $style === 'finding_aids') {
        $description = metadata(
            $item,
            array('Item Type Metadata', 'Introduction'),
            array('snippet' => 300, 'no_escape' => true)
        );
    }
}

if ($description) {
    $output .= '<div class="record-description">' . PHP_EOL;
    $output .= text_to_paragraphs($description) . PHP_EOL . '</div>' . PHP_EOL;
}

if ($output) {
    $output = PHP_EOL . '<div class="record-details">' . PHP_EOL . $output;
    $output .= '</div>' . PHP_EOL;
}

if (preg_match('/\ssrc="([^"]+)"/', record_image($item, 'fullsize'), $matches)) {
    $path = $matches[1];
}

if (isset($path) || $_GET['display'] !== 'list') {
    $output .= '<div class="record-image"';

    if (!isset($path)) {
        $path = img('fallback-file.png');
    }

    if (!empty($aspect)) {
        $output .= '><img alt=""';

        if (!empty($carousel)) {
            $output .= ' data-flickity-lazyload="' . $path . '"';
        } else {
            $output .= ' src="' . $path . '"';
        }
    } else {
        if (!empty($carousel)) {
            $output .= ' data-flickity-bg-lazyload="' . $path . '"';
        } else {
            $output .= ' style="background-image:url(' . $path . ')"';
        }
    }

    $output .= '></div>' . PHP_EOL;
}

if ($output) {
    echo '<div class="record">' . PHP_EOL;
    echo link_to($item, 'show', $output) . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
