<?php
echo head(array(
    'title' => metadata($exhibit, 'title'),
    'ancestors' => array(url('exhibits') => 'Exhibits')
));

echo $this->partial(
    'exhibit-builder/exhibits/nav.php',
    array('exhibit' => $exhibit)
);

$exhibitDescription = metadata(
    'exhibit',
    'description',
    array('no_escape' => true)
);

if ($exhibitDescription) {
    echo '<div class="exhibit-description">';
    echo '<h2>' . __('Introduction') . '</h2>';
    echo '<div>' . $exhibitDescription . '</div>';
    echo '</div>';
}

$exhibitCredits = metadata('exhibit', 'credits');

if ($exhibitCredits) {
    echo '<div class="exhibit-credits">';
    echo '<h2>' . __('Credits') . '</h2>';
    echo '<div>' . $exhibitCredits . '</div>';
    echo '</div>';
}

echo '<nav class="nav-page nav-page-large" aria-label="pagination">';
echo '<div>';
echo '</div>';
echo '<div>';

$nextPage = reset($exhibit->getTopPages());

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
