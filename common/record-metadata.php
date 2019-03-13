<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$config = Symfony\Component\Yaml\Yaml::parse(
    get_theme_option('elements_config')
);

foreach (array_keys($elementsForDisplay) as $setName) {
    foreach ($elementsForDisplay[$setName] as $elementName => $display) {
        if (empty($config[$setName][$elementName]['linked'])) {
            continue;
        }

        foreach ($display['texts'] as $key => $text) {
            $params = array(
                'advanced' => array(
                    array(
                        'element_id' => $display['element']->id,
                        'type' => 'is exactly',
                        'terms' => html_entity_decode($text)
                    )
                )
            );

            if ($collection = get_collection_for_item()) {
                $params['collection'] = $collection->id;
            }

            $elementsForDisplay[$setName][$elementName]['texts'][$key] =
                '<a href="' . url('items/browse', $params) . '">' .
                $text . '</a>';
        }
    }
}

if (get_theme_option('elements_auto') !== '0') {
    $mimeTypes = array_unique(array_map(
        function ($file) {
            return $file->mime_type;
        },
        $record->getFiles()
    ));

    foreach ($mimeTypes as $mimeType) {
        $elementsForDisplay['Dublin Core']['Format']['texts'][] = $mimeType;
    }

    $elementsForDisplay['Dublin Core']['Identifier']['texts'][] =
        record_url($record, 'show', true);

    $itemTypeName = $record->getProperty('item_type_name');

    if (!empty($itemTypeName)) {
        $elementsForDisplay['Dublin Core']['Type']['texts'][] = $itemTypeName;
    }
}

$order = array();
$other = array();

if (!empty($config)) {
    foreach (array_keys($config) as $setName) {
        foreach ($config[$setName] as $elementName => $elementConfig) {
            if (!empty($elementConfig['order'])) {
                $order[$elementConfig['order']] = array($setName, $elementName);
            } else {
                $other[] = array($setName, $elementName);
            }
        }
    }
}

ksort($order);

if (get_theme_option('elements_hidden')) {
    $order = array_merge($order, $other);
} else {
    foreach (array_keys($elementsForDisplay) as $setName) {
        foreach (array_keys($elementsForDisplay[$setName]) as $elementName) {
            if (!in_array(array($setName, $elementName), $order)) {
                $order[] = array($setName, $elementName);
            }
        }
    }
}

echo '<table class="table table-elements">' . PHP_EOL;

foreach ($order as $element) {
    if (empty($elementsForDisplay[$element[0]][$element[1]])) {
        continue;
    }

    if (!empty($config[$element[0]][$element[1]]['hidden'])) {
        continue;
    }

    $rows = sizeof($elementsForDisplay[$element[0]][$element[1]]['texts']);
    echo '<tr><th' . ($rows > 1 ? ' rowspan="' . $rows . '"' : '') . '>';

    $toggle = false;

    if (!empty($config[$element[0]][$element[1]]['toggle'])) {
        $toggle = 'toggle ' . $element[0] . ' ' . $element[1];
        $toggle = strtolower(preg_replace('/\W+/', '-', trim($toggle)));
        echo '<button data-toggle="' . html_escape($toggle) . '"';
        echo ' class="toggle-control">';
    }

    if (!empty($config[$element[0]][$element[1]]['label'])) {
        echo html_escape(__($config[$element[0]][$element[1]]['label']));
    } else {
        echo html_escape(__($element[1]));
    }

    if ($toggle) {
        echo '</button>';
    }

    echo '</th>';

    $first = true;

    foreach ($elementsForDisplay[$element[0]][$element[1]]['texts'] as $text) {
        if (!$first) {
            echo '</tr>' . PHP_EOL . '<tr>';
        }

        echo '<td>';

        if ($toggle) {
            echo '<div id="' . html_escape($toggle) . '">' . $text . '</div>';
        } elseif (filter_var($text, FILTER_VALIDATE_URL)) {
            echo '<a href="' . $text . '" class="url">' . $text . '</a>';
        } else {
            echo $text;
        }

        echo '</td>';

        $first = false;
    }

    echo '</tr>' . PHP_EOL;
}

echo '</table>' . PHP_EOL;
