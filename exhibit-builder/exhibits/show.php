<?php
queue_css_string('
#content .layout-file-text .exhibit-items > * {
    margin: 0;
}

#content .layout-file-text .exhibit-items .download-file * {
    display: block;
    max-width: 100%;
    margin: 0 auto 5px;
}
');

$page = get_current_record('exhibit_page');
$current = $page;
$parents = array();

while ($current->parent_id) {
    $current = $current->getParent();
    array_unshift($parents, $current);
}

$ancestors = array(url('exhibits') => 'Exhibits');
$ancestors[exhibit_builder_exhibit_uri($exhibit)] =
    metadata($exhibit, 'title');

foreach ($parents as $parent) {
    $ancestors[exhibit_builder_exhibit_uri($exhibit, $parent)] =
        metadata($parent, 'menu_title');
}

echo head(array(
    'title' => metadata('exhibit_page', 'title'),
    'ancestors' => $ancestors
));

echo $this->partial(
    'exhibit-builder/exhibits/nav.php',
    array('exhibit' => $exhibit, 'content' => true)
);

echo exhibit_builder_render_exhibit_page();

if (get_theme_option('exhibit_nav_subpages')) {
    if ($children = exhibit_builder_child_pages()) {
        echo '<ul>';

        foreach ($children as $child) {
            echo '<li>';
            echo exhibit_builder_link_to_exhibit(
                $exhibit,
                metadata($child, 'title'),
                array(),
                $child
            );

            echo '</li>';
        }

        echo '</ul>';
    }
}

echo '<nav class="nav-page nav-page-large" aria-label="pagination">';
echo '<div>';

$previousPage = $page->previousOrParent();

if ($previousPage) {
    $text = '<div class="nav-page-previous">';
    $text .= metadata($previousPage, 'menu_title');
    $text .= '</div>';

    if (get_theme_option('exhibit_nav_thumbnails')) {
        $attachments = $previousPage->getAllAttachments();

        if ($attachment = reset($attachments)) {
            if ($file = $attachment->getFile()) {
                $text .= '<div class="nav-page-image"';
                $text .= ' style="background-image:url(';
                $text .= $file->getWebPath('fullsize') . ')"></div>' . PHP_EOL;
            }
        }
    }

    echo exhibit_builder_link_to_exhibit(
        $exhibit,
        $text,
        array(),
        $previousPage
    );
} else {
    echo exhibit_builder_link_to_exhibit(
        $exhibit,
        '<div class="nav-page-previous">Introduction</div>'
    );
}

echo '</div>';
echo '<div>';

$nextPage = $page->firstChildOrNext();

if ($nextPage) {
    $text = '<div class="nav-page-next">';
    $text .= metadata($nextPage, 'menu_title');
    $text .= '</div>';

    if (get_theme_option('exhibit_nav_thumbnails')) {
        $attachments = $nextPage->getAllAttachments();

        if ($attachment = reset($attachments)) {
            if ($file = $attachment->getFile()) {
                $text .= '<div class="nav-page-image"';
                $text .= ' style="background-image:url(';
                $text .= $file->getWebPath('fullsize') . ')"></div>' . PHP_EOL;
            }
        }
    }

    echo exhibit_builder_link_to_exhibit(
        $exhibit,
        $text,
        array(),
        $nextPage
    );
}

echo '</div>';
echo '</nav>';

echo foot();
