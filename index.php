<?php
$style = get_theme_option('style');
$carousel = get_theme_option('homepage_carousel');

if ($carousel) {
    queue_js_url('https://lib.bgsu.edu/template/1.0.0/flickity.js');
}

echo head(array(
    'description' => option('description'),
    'carousel' => $carousel
));

if ($description = option('description')) {
    echo '<p class="text-lead">' . $description . '</p>' . PHP_EOL;
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

            echo '<h2>';
            echo '<a href="' . $plural  . '">';
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
            echo '<div class="record"><a href="'. $plural . '">';
            echo '<div class="record-details"><h3 class="record-title">';
            echo __('See All ' . ucwords($plural)) . '</h3></div>';
            echo '<img alt=""></a></div>' . PHP_EOL;
        }

        echo '</div>' . PHP_EOL;
    }
}

fire_plugin_hook('public_home', array('view' => $this));

echo foot(array('home' => true));
