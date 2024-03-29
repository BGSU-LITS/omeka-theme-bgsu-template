<?php
$output = '';

if ($item = get_db()->getTable($result->model)->find($result->modelid)) {
    $title = is_array($result->title) ? $result->title[0] : $result->title;

    if ($title) {
        if (empty($heading)) {
            $heading = 'h2';
        }

        $output .= '<' . $heading . ' class="record-title">';
        $output .= html_escape(strip_formatting($title));
        $output .= '</' .  $heading . '>' . PHP_EOL;
    }

    $description = '';

    switch ($result->resulttype) {
        case 'Item':
            $description .= '<strong>' . __('Item');

            if ($collection = $item->getCollection()) {
                $title = metadata($collection, 'display_title');

                if ($title) {
                    $description .= ' &mdash; ' . $title;
                }
            }

            $description .= '</strong><br>' . metadata(
                $item,
                array('Dublin Core', 'Description'),
                array('snippet' => 250, 'no_escape' => true)
            );

            break;

        case 'Collection':
            $description .= '<strong>' . __('Collection') . '</strong><br>';
            $description .= metadata(
                $item,
                array('Dublin Core', 'Description'),
                array('snippet' => 250, 'no_escape' => true)
            );

            break;

        case 'Exhibit':
            $description .= '<strong>' . __('Exhibit') . '</strong><br>';
            $description .= snippet(
                $item->description,
                0,
                250
            );

            break;

        case 'Exhibit Page':
            $description = '<strong>' . __('Page');

            if ($exhibit = $item->getExhibit()) {
                $description .= ' &mdash; ' . $exhibit->title;
            }

            $description .= '</strong>';

            break;

        case 'Simple Page':
            $description .= '<strong>' . __('Page') . '</strong>';

            break;
    }

    if ($description) {
        $output .= '<div class="record-description">';
        $output .= text_to_paragraphs($description) . '</div>' . PHP_EOL;
    }

    if ($highlighting) {
        $output .= '<blockquote>' . PHP_EOL;

        foreach ($highlighting as $field) {
            foreach ($field as $highlight) {
                $highlight = preg_replace(
                    '/&lt;.*?(&gt;|$)/',
                    ' ',
                    $highlight
                );

                $highlight = preg_replace(
                    '/(^|&lt;).*?&gt;/',
                    ' ',
                    $highlight
                );

                $highlight = html_entity_decode($highlight);
                $highlight = preg_replace('/^\W\s+/', '', $highlight);
                $highlight = preg_replace('/<\/em>\s+<em>/', ' ', $highlight);
                $output .= '<p>' . $highlight . '</p>' . PHP_EOL;
            }
        }

        $output .= '</blockquote>' . PHP_EOL;
    }

    if ($output) {
        $output = PHP_EOL . '<div class="record-details">' . PHP_EOL . $output;
        $output .= '</div>' . PHP_EOL;
    }

    $output .= '<div class="record-image"';

    if ($file = $item->getFile()) {
        $path = $file->getWebPath('fullsize');
    } else {
        $path = img('fallback.png');
        $type = null;

        if (method_exists($item, 'getItemType')) {
            $type = $item->getItemType();
        }

        if ($type instanceof ItemType) {
            if ($type->name === 'Moving Image') {
                $path = img('fallback-video.png');
            }

            if ($type->name === 'Sound') {
                $path = img('fallback-audio.png');
            }

            if ($type->name === 'Still Image') {
                $path = img('fallback-image.png');
            }

            if ($type->name === 'Text') {
                $path = img('fallback-file.png');
            }
        }
    }

    if ($path) {
        if (!empty($carousel)) {
            $output .= ' data-flickity-bg-lazyload="' . $path . '"';
        } else {
            $output .= ' style="background-image:url(' . $path . ')"';
        }
    }

    $output .= '></div>' . PHP_EOL;

    if ($output) {
        echo '<div class="record">' . PHP_EOL;
        echo link_to($item, 'show', $output) . PHP_EOL . '</div>' . PHP_EOL;
    }
}
