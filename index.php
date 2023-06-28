<?php
$style = get_theme_option('style');
$carousel = get_theme_option('homepage_carousel');

if ($carousel) {
    queue_js_url(BGSU_TEMPLATE . 'flickity.js');
}

echo head(array(
    'description' => option('description'),
    'carousel' => $carousel
));

echo flash();

if ($html = get_theme_option('homepage_html')) {
    echo $html;
} else if ($description = option('description')) {
    echo '<p class="text-lead">' . $description . '</p>' . PHP_EOL;
}

if ($style === 'default') {
    $item = get_record('Item', array(
        'hasImage' => true,
        'sort_field' => 'random',
        'tags' => 'Featured'
    ));

    echo '<style>' . PHP_EOL;
    ?>
header form {
    display: none;
}

#featured {
    margin: 0 0 20px 0;
}

#featured-credit {
    text-align: right;
}

#featured-search {
    background-image: url(<?php
    if ($item) {
        $file = $item->getFile();

        if ($file) {
            $mime_types = array(
                'image/gif',
                'image/jpeg',
                'image/png'
            );

            if (in_array($file->mime_type, $mime_types)) {
                echo $file->getWebPath('original');
            } else {
                echo $file->getWebPath('fullsize');
            }
        }
    }
?>);
    background-position: center 20%;
    background-size: cover;
    font-size: 1.25em;
    padding: 48px 10%;
}

#featured-search form {
    background: #fff;
    border-radius: 12px;
    padding: 12px;
}

@supports (backdrop-filter: blur(5px)) or
    (-webkit-backdrop-filter: blur(5px)) {
    #featured-search form {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
}

#featured-search .form-search {
    max-width: none;
}
<?php
    echo '</style>' . PHP_EOL;

    echo '<div id="featured">' . PHP_EOL;
    echo '<div id="featured-search">' . PHP_EOL;

    echo $this->partial(
        'solr-search/results/sidebar.php',
        array(
            'advanced' => true,
            'facet' => true,
            'facet_label' => 'Repository',
            'facet_tags' => array(
                'Browne Popular Culture Library',
                'Center for Archival Collections',
                'Curriculum Resource Center',
                'Music Library & Bill Schurk Sound Archives'
            )
        )
    );

    echo '</div>' . PHP_EOL;

    if ($item) {
        echo '<div id="featured-credit">' . PHP_EOL;
        echo link_to($item, 'show', metadata($item, 'display_title'));

        $collection = get_collection_for_item($item);

        if ($collection) {
            echo ', ' . link_to(
                $collection,
                'show',
                metadata($collection, 'display_title')
            );
        }

        echo '</div>'  . PHP_EOL;
    }

    echo '</div>' . PHP_EOL;
}

$types = array('item', 'collection');

if (plugin_is_active('ExhibitBuilder')) {
    $types[] = 'exhibit';
}

$types_display = array();

foreach ($types as $type) {
    $plural = $this->pluralize($type);

    if (get_theme_option('homepage_' . $plural)) {
        $types_display[$type] = array(
            'desc' => get_theme_option('homepage_' . $plural . '_desc'),
            'link' => get_theme_option('homepage_' . $plural . '_link')
        );
    }
}

$params = array(
    'featured' => true,
    'hasImage' => true,
    'sort_field' => 'added',
    'sort_dir' => 'd'
);

if ($style === 'finding_aids') {
    $params['sort_field'] = 'Dublin Core,title';
    $params['sort_dir'] = 'a';
}

foreach ($types_display as $type => $data) {
    $records = get_records(ucwords($type), $params, 50);

    if (!empty($records)) {
        if ($style === 'finding_aids') {
            echo '<div>';
        } else {
            $plural = $this->pluralize($type);
            $href = $plural;

            if ($href === 'items') {
                $params = array();

                if ($field = get_theme_option('sort_0_field')) {
                    $params[Omeka_Db_Table::SORT_PARAM] = $field;
                }

                if ($dir = get_theme_option('sort_0_dir')) {
                    $params[Omeka_Db_Table::SORT_DIR_PARAM] = $dir;
                }

                if (!empty($params)) {
                    $href .= '?' . http_build_query($params);
                }
            }

            echo '<h2>';
            echo '<a href="' . $href . '">';
            echo __(ucwords($plural)) . '</a>';

            if ($data['link']) {
                echo '<a class="icon icon-info" href="';
                echo public_url($data['link']) . '" title="';
                echo html_escape($data['desc']) . '" aria-label="';
                echo __(ucwords($plural) . ' Information') . '"></a>';
            } elseif ($data['desc']) {
                echo '<span class="icon icon-info" title="';
                echo html_escape($data['desc']) . '"aria-label="';
                echo __(ucwords($plural) . ' Information') . '"></span>';
            }

            echo '</h2>' . PHP_EOL;
            echo '<div class="records records-gallery">' . PHP_EOL;
        }

        foreach ($records as $record) {
            echo $this->partial(
                ($type === 'exhibit' ? 'exhibit-builder/' : '') .
                    $plural . '/single.php',
                array(
                    $type => $record,
                    'carousel' => $carousel,
                    'heading' => $style === 'finding_aids' ? 'h2' : 'h3',
                    'style' => $style
                )
            );
        }

        if ($style !== 'finding_aids') {
            echo '<div class="record"><a href="'. $href . '">';
            echo '<div class="record-details"><h3 class="record-title">';
            echo __('See All ' . ucwords($plural)) . '</h3></div>';
            echo '<div class="record-image"></div></a></div>' . PHP_EOL;
        }

        echo '</div>' . PHP_EOL;
    }
}

if ($style === 'default') {
    $groups = array(
        'topics',
        'formats',
        'collections',
        'exhibits',
        'repositories'
    );

    if ($groups) {
        echo '<div id="groups-container">' . PHP_EOL;
        echo '<h2>' . __('Explore our content through…') . '</h2>' . PHP_EOL;
        echo '<div id="groups">';

        foreach ($groups as $group) {
            echo '<a href="' . $group . '">';
            echo '<img src="' . img('group-' . $group . '.png') . '">';
            echo ucwords($group) . '</a>' . PHP_EOL;
        }

        echo '</div>';
    }
}

fire_plugin_hook('public_home', array('view' => $this));

echo foot(array('home' => true));
