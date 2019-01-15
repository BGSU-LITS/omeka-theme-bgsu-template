<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$config = Symfony\Component\Yaml\Yaml::parse(
    get_theme_option('elements_config')
);

$order = array();
$other = array();

foreach (array_keys($config) as $setName) {
    foreach ($config[$setName] as $elementName => $elementConfig) {
        if (!empty($elementConfig['order'])) {
            $order[$elementConfig['order']] = array($setName, $elementName);
        } else {
            $other[] = array($setName, $elementName);
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

    $id = $elementsForDisplay[$element[0]][$element[1]]['element']->id;
    $rows = sizeof($elementsForDisplay[$element[0]][$element[1]]['texts']);

    echo '<tr><th' . ($rows > 1 ? ' rowspan="' . $rows . '"' : '') . '>';

    $toggle = false;

    if (!empty($config[$element[0]][$element[1]]['toggle'])) {
        $toggle = 'toggle ' . $element[0] . ' ' . $element[1];
        $toggle = strtolower(preg_replace('/\W+/', '-', trim($toggle)));
        echo '<button data-toggle="' . html_escape($toggle) . '">';
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
        } elseif ($config[$element[0]][$element[1]]['linked']) {
            $params = array(
                'advanced' => array(
                    array(
                        'element_id' => $id,
                        'type' => 'is exactly',
                        'terms' => html_entity_decode($text)
                    )
                )
            );

            if ($collection = get_collection_for_item()) {
                $params['collection'] = $collection->id;
            }

            echo '<a href="' . url('items/browse', $params) . '">';
            echo $text . '</a>';
        } else {
            echo $text;
        }

        echo '</td>';

        $first = false;
    }

    echo '</tr>' . PHP_EOL;
}

echo '</table>' . PHP_EOL;
