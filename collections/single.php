<?php
if (empty($heading)) {
    $heading = 'h2';
}

if (!empty($style) && $style === 'finding_aids') {
    $params = array(
        'collection' => $collection->id,
        'sort_field' => 'Dublin Core,Title',
        'sort_dir' => 'a',
    );

    if ($title = metadata($collection, 'display_title')) {
        $link = link_to('items', 'browse', $title, array(), $params);

        echo '<div>';
        echo '<' . $heading . '>' . $link . '</' . $heading . '>' . PHP_EOL;

        $subjects = metadata(
            $collection,
            array('Dublin Core', 'Subject'),
            array('all' => true)
        );

        if ($subjects) {
            sort($subjects);

            echo '<div>' . __('Browse Sub-Collections') . '</div>';
            echo '<ul>';

            foreach ($subjects as $subject) {
                $params['tags'] = $subject;

                $link = link_to('items', 'browse', $subject, array(), $params);
                echo '<li>' . $link . '</li>';
            }

            echo '</ul>';
        }

        echo '</div>';
    }
} else {
    $output = '';

    if ($title = metadata($collection, 'display_title')) {
        $output .= '<' . $heading . ' class="record-title">';
        $output .= $title . '</' .  $heading . '>' . PHP_EOL;
    }

    $description = metadata(
        $collection,
        array('Dublin Core', 'Description'),
        array('snippet' => 300, 'no_escape' => true)
    );

    if ($description) {
        $output .= '<div class="record-description">' . PHP_EOL;
        $output .= text_to_paragraphs($description) . PHP_EOL;
        $output .= '</div>' . PHP_EOL;
    }

    if ($output) {
        $output = PHP_EOL . '<div class="record-details">' . PHP_EOL. $output;
        $output .= '</div>' . PHP_EOL;
    }

    $output .= '<div class="record-image"';

    if ($file = $collection->getFile()) {
        $path = $file->getWebPath('fullsize');

        if (!empty($carousel)) {
            $output .= ' data-flickity-bg-lazyload="' . $path . '"';
        } else {
            $output .= ' style="background-image:url(' . $path . ')"';
        }
    }

    $output .= '></div>' . PHP_EOL;

    if ($output) {
        echo '<div class="record">' . PHP_EOL;
        echo link_to($collection, 'show', $output) . PHP_EOL;
        echo '</div>' . PHP_EOL;
    }
}
