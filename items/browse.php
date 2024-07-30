<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$config = Symfony\Component\Yaml\Yaml::parse(
    get_theme_option('elements_config')
);

queue_js_url(BGSU_TEMPLATE . 'facets.js');

$style = get_theme_option('style');

if (
    !isset($_GET['display']) ||
    !in_array($_GET['display'], ['list', 'gallery'])
) {
    $_GET['display'] = $style === 'default' ? 'gallery' : 'list';
}

$ancestors = array();

if (!empty($_GET['collection'])) {
    $collection = get_record_by_id('collection', $_GET['collection']);

    $ancestors = array(
        url('collections') => 'Collections',
        record_url($collection) => metadata($collection, 'display_title')
    );
}

$elements = get_db()->getTable('Element');

$filters = apply_filters(
    'item_search_filters',
    array(),
    array('request_array' => $_GET)
);

foreach ($filters as $key => $value) {
    $params = $_GET;
    unset($params[$key]);
    $filters[$key] = array(array($value, $params));
}

foreach ($_GET as $key => $value) {
    if ($key === 'advanced') {
        if (is_array($value)) {
            foreach ($value as $index => $field) {
                if (empty($field['element_id'])) {
                    continue;
                }

                if (empty($field['type'])) {
                    $field['type'] = 'contains';
                }

                if ($field['type'] === 'is empty') {
                    $field['terms'] = '';
                } elseif ($field['type'] === 'is not empty') {
                    $field['terms'] = '';
                } elseif (empty($field['terms'])) {
                    continue;
                }

                $element = $elements->find($field['element_id']);
                $name = $element->name;
                $set = $element->getElementSet()->name;

                if (!empty($config[$set][$name]['label'])) {
                    $name = $config[$set][$name]['label'];
                }

                $item = __($name) . ' ' . __($field['type']);

                if (!empty($filters['fields']) && !empty($field['joiner'])) {
                    $item = __($field['joiner']) . ' ' . $item;
                }

                if (!empty($field['terms'])) {
                    $item .= ' “' . $field['terms'] . '”';
                }

                $params = $_GET;
                unset($params[$key][$index]);
                $filters['fields'][] = array($item, $params);
            }
        }
    } elseif ($value !== '') {
        $params = $_GET;
        unset($params[$key]);

        if ($key === 'search') {
            $key = 'phrase';
        } elseif ($key === 'collection') {
            if ($value === '0') {
                $value = __('No Collection');
            } else {
                $value = metadata($collection, 'display_title');
            }
        } elseif ($key === 'type') {
            if ($type = get_db()->getTable('ItemType')->find($value)) {
                $value = $type->name;
            }
        } elseif ($key === 'user') {
            if ($user = get_db()->getTable('User')->find($value)) {
                $value = $user->name;
            }
        } elseif ($key === 'public') {
            $key = 'status';

            if ($value) {
                $value = __('Only Public Items');
            } else {
                $value = __('Only Non-Public Items');
            }
        } elseif ($key === 'featured') {
            $value = $value ? __('Yes') : __('No');
        } elseif ($key !== 'tags' && $key !== 'range') {
            continue;
        }

        $filters[$key][] = array($value, $params);
    }
}

$advanced = false;

if (!empty($filters)) {
    if (sizeof($filters) > 1 || !isset($filters['collection'])) {
        $advanced = true;
    }
}

echo head(array(
    'title' => __('Browse Items'),
    'ancestors' => $ancestors
));

echo '<div class="sidebar">' . PHP_EOL;

if (!empty($collection)) {
    echo '<div class="sidebar-left" id="collection">' . PHP_EOL;

    echo $this->partial(
        'collections/sidebar.php',
        array('collection' => $collection)
    );

    if ($advanced) {
        echo '<hr>' . PHP_EOL;
    }
}

if ($advanced) {
    if (empty($collection)) {
        echo '<div class="sidebar-left">' . PHP_EOL;
    }

    echo '<h2 class="sidebar-title">';
    echo '<a href="' . url('items/search', $_GET) . '">';
    echo 'Advanced Search</a></h2>' . PHP_EOL;
    echo '<ul>' . PHP_EOL;

    foreach ($filters as $key => $value) {
        echo '<li>' . __(ucwords($key)) . PHP_EOL;
        echo '<ul class="list-facets">' . PHP_EOL;

        foreach ($value as $item) {
            echo '<li>' . html_escape($item[0]);
            echo '<a class="icon icon-remove" href="';
            echo url('items/browse', $item[1]) . '" title="';
            echo __('Remove') . ' ' . __(ucwords($key)) . ': ';
            echo html_escape($item[0]) . '"></a>';
            echo '</li>' . PHP_EOL;
        }

        echo '</ul>' . PHP_EOL;
        echo '</li>' . PHP_EOL;
    }

    echo '</ul>' . PHP_EOL;
}

if (!empty($collection) || $advanced) {
    echo '</div>' . PHP_EOL;
}

echo '<div class="records-paginated">' . PHP_EOL;

echo pagination_links();
echo '<div class="records records-';
echo $_GET['display'] . '">' . PHP_EOL;

foreach (loop('items') as $item) {
    echo $this->partial(
        'items/single.php',
        array('item' => $item, 'style' => $style)
    );

    fire_plugin_hook(
        'public_items_browse_each',
        array('view' => $this, 'item' => $item)
    );
}

echo '</div>' . PHP_EOL;
echo pagination_links();

fire_plugin_hook(
    'public_items_browse',
    array('items' => $items, 'view' => $this)
);

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo foot();
