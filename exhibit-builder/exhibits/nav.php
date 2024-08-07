<?php
$css = get_theme_option('exhibit_style');
$img = get_theme_option('exhibit_logo');

$css .= '
.nav-exhibit {
    background: #f2f2f2;
    border-radius: 4px;
    line-height: 1.5;
    margin: 0 0 20px 0;
    padding: 4px 8px;
}

.nav-exhibit > :first-child {
    font-size: 1.25em;
    font-weight: normal;
}

.nav-exhibit .list-inline {
    margin-left: -4px;
}

.nav-exhibit .list-inline li {
    padding: 4px;
    padding-bottom: 0;
}

.nav-exhibit .active {
    font-weight: bold;
}
';

if ($exhibit->use_summary_page && !$img) {
    $css .= '
.nav-exhibit-content .active:first-child {
    font-weight: normal;
}
';
}

if (!empty(trim($css))) {
    echo '<style>' . $css . '</style>' . PHP_EOL;
}

if ($topPages = $exhibit->getTopPages()) {
    if ($exhibit->use_summary_page && !$img) {
        $nav = array(
            array(
                'label' => 'Introduction',
                'uri' => exhibit_builder_exhibit_uri($exhibit)
            )
        );
    }

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

    $class = 'nav-exhibit';

    if (!empty($content)) {
        $class .= ' nav-exhibit-content';
    }

    echo '<nav class="' . $class . '" aria-label="exhibit">';

    if ($img) {
        $storage = Zend_Registry::get('storage');
        $uri = $storage->getUri(
            $storage->getPathByType($img, 'theme_uploads')
        );

        echo '<a href="' . exhibit_builder_exhibit_uri($exhibit) . '">';
        echo '<img class="nav-exhibit-logo" src="' . html_escape($uri);
        echo '" alt="' . metadata($exhibit, 'title') . '"></a>';
    } else {
        echo '<strong>Exhibit Contents</strong>';
    }

    echo nav($nav)->setUlClass('list-inline');
    echo '</nav>';
}
