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
');

echo head(array(
    'title' => metadata($exhibit, 'title'),
    'ancestors' => array(url('exhibits') => 'Exhibits')
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

$exhibitDescription = metadata(
    'exhibit',
    'description',
    array('no_escape' => true)
);

if ($exhibitDescription) {
    echo '<h2>' . __('Introduction') . '</h2>';
    echo '<div>' . $exhibitDescription . '</div>';
}

$exhibitCredits = metadata('exhibit', 'credits');

if ($exhibitCredits) {
    echo '<h2>' . __('Credits') . '</h2>';
    echo '<div>' . $exhibitCredits . '</div>';
}

echo '<nav class="nav-page" aria-label="pagination">';
echo '<div>';
echo '</div>';
echo '<div>';

$nextPage = reset($topPages);

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
