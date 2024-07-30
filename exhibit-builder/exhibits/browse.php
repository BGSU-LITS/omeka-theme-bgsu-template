<?php
$style = get_theme_option('style');

if (
    !isset($_GET['display']) ||
    !in_array($_GET['display'], ['list', 'gallery'])
) {
    $_GET['display'] = $style === 'default' ? 'gallery' : 'list';
}

$repositories = array(
    'Browne Popular Culture Library' => 'bpcl',
    'Center for Archival Collections' => 'cac',
    'Curriculum Resource Center' => 'crc',
    'Music Library & Bill Schurk Sound Archives' => 'mlbssa',
);

$ancestors = array();
$sidebar_title = $sidebar_search = 'All Exhibits';

$_GET['facet'] = 'resulttype:"Exhibit"';

if (isset($_GET['tag'])) {
    foreach ($repositories as $repository => $slug) {
        if ($_GET['tag'] === $repository) {
            $sidebar_title = $repository;
            $sidebar_search = 'Exhibits';

            $_GET['facet'] .= ' AND tag:"' . $repository . '"';

            $ancestors = array(
                url('repositories') => 'Repositories',
                url('repositories/' . $slug) => $repository
            );

            break;
        }
    }
}

echo head(array('title' => __('Exhibits'), 'ancestors' => $ancestors));
echo '<div class="sidebar">' . PHP_EOL;
echo '<div class="sidebar-left">' . PHP_EOL;
echo '<h2 class="sidebar-title">' . __($sidebar_title) . '</h2>' . PHP_EOL;

echo $this->partial(
    'solr-search/results/sidebar.php',
    array('label' => __('Search ' . $sidebar_search))
);

echo '<br>' . PHP_EOL;

if ($style === 'default') {
    echo '<h3 class="sidebar-title">';

    if (isset($_GET['tag']) && isset($repositories[$_GET['tag']])) {
        echo __('Other Repositories');
    } else {
        echo __('Limit By Repository');
    }

    echo '</h3>' . PHP_EOL;
    echo '<ul>' . PHP_EOL;

    foreach ($repositories as $repository => $slug) {
        if (isset($_GET['tag']) && $_GET['tag'] === $repository) {
            continue;
        }

        echo '<li><a href="';
        echo url('exhibits', array('tag' => $repository)) . '">';
        echo html_escape($repository) . '</a></li>' . PHP_EOL;
    }

    if (isset($_GET['tag']) && isset($repositories[$_GET['tag']])) {
        echo '<li><a href="' . url('exhibits') . '">';
        echo __('View All Exhibits') . '</a></li>' . PHP_EOL;
    }

    echo '</ul>' . PHP_EOL;
}

echo '</div>' . PHP_EOL;
echo '<div>' . PHP_EOL;

echo pagination_links();
echo '<div class="records records-';
echo $_GET['display'] . '">' . PHP_EOL;

foreach (loop('exhibits') as $exhibit) {
    echo $this->partial(
        'exhibits/single.php',
        array('exhibit' => $exhibit)
    );
}

echo '</div>' . PHP_EOL;
echo pagination_links();

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo foot();
