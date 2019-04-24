<?php
queue_css_string('
#content .nav-exhibit {
    background: #f2f2f2;
    border-radius: 4px;
    line-height: 1.5;
    margin: 0 0 20px 0;
    padding: 4px 8px;
}

#content .nav-exhibit .list-inline {
}

#content .nav-exhibit .list-inline li {
    padding: 4px 8px 0 0;
}

#content .nav-exhibit .active {
    font-weight: bold;
}

#content .nav-exhibit .active:first-child {
    font-weight: normal;
}

#content .nav-page {
    font-size: 1.2em;
    padding: 8px;
}

#content .nav-page img {
    margin-top: 8px;
    object-fit: cover;
    width: 200px;
    height: 150px;
}

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

if ($topPages = $exhibit->getTopPages()) {
    $nav = array(
        array(
            'label' => 'Introduction',
            'uri' => exhibit_builder_exhibit_uri($exhibit)
        )
    );

    foreach ($topPages as $topPage) {
        $nav[] = array(
            'label' => metadata(
                $topPage,
                'menu_title',
                array('no_escape' => true)
            ),
            'uri' => exhibit_builder_exhibit_uri($exhibit, $topPage)
        );
    }

    echo '<nav class="nav-exhibit" aria-label="exhibit">';
    echo '<strong>Exhibit Contents</strong>';
    echo nav($nav)->setUlClass('list-inline');
    echo '</nav>';
}

echo exhibit_builder_render_exhibit_page();

echo '<nav class="nav-page" aria-label="pagination">';
echo '<div>';

$previousPage = $page->previousOrParent();

if ($previousPage) {
    $text = '<div class="nav-page-previous">';
    $text .= metadata($previousPage, 'menu_title');
    $text .= '</div>';

    $attachments = $previousPage->getAllAttachments();

    if ($attachment = reset($attachments)) {
        $text .= file_markup(
            $attachment->getFile(),
            array(
                'linkToFile' => false,
                'imgAttributes' => array('alt' => '')
            ),
            array()
        );
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

    $attachments = $nextPage->getAllAttachments();

    if ($attachment = reset($attachments)) {
        $text .= file_markup(
            $attachment->getFile(),
            array(
                'linkToFile' => false,
                'imgAttributes' => array('alt' => '')
            ),
            array()
        );
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
