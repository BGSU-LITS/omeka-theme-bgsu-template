<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$config = Symfony\Component\Yaml\Yaml::parse(
    get_theme_option('elements_config')
);

$form = array(
    'action' => url(array(
        'controller' => 'items',
        'action' => 'browse'
    )),
    'method' => 'GET'
);

if (!empty($formActionUri)) {
    $form['action'] = $formActionUri;
}

echo '<form ' . tag_attributes($form) . '>' . PHP_EOL;
echo '<h2>' . __('Search Items') . '</h2>' . PHP_EOL;

echo '<div class="form-item">' . PHP_EOL;
echo $this->formLabel('keyword-search', __('Phrase:')) . PHP_EOL;
echo $this->formText(
    'search',
    isset($_REQUEST['search']) ? $_REQUEST['search'] : '',
    array('id' => 'keyword-search')
);

echo PHP_EOL . '<aside>';
echo __('Use _ to match any single character, % to match multiple.');
echo '</aside>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo '<div class="form-item">' . PHP_EOL;
echo $this->formLabel('tags-search', __('Tags:')) . PHP_EOL;
echo $this->formText(
    'tags',
    isset($_REQUEST['tags']) ? $_REQUEST['tags'] : '',
    array('id' => 'tags-search')
);

echo PHP_EOL . '<aside>';
echo __('Use %s to separate multiple tags.', get_option('tag_delimiter'));
echo '</aside>' . PHP_EOL;
echo '</div>' . PHP_EOL;

$elements = array();
$table = get_db()->getTable('Element');

if (get_theme_option('elements_hidden')) {
    $order = array();
    $other = array();

    foreach (array_keys($config) as $setName) {
        foreach ($config[$setName] as $elementName => $elementConfig) {
            if ($elementConfig['hidden']) {
                continue;
            }

            $element = $table->findByElementSetNameAndElementName(
                $setName,
                $elementName
            );

            $item = array(
                $element->id,
                $elementConfig['label'] ? $elementConfig['label'] : $elementName
            );

            if (!empty($elementConfig['order'])) {
                $order[$elementConfig['order']] = $item;
            } else {
                $other[] = $item;
            }
        }
    }

    ksort($order);

    foreach ($order as $item) {
        $elements[$item[0]] = $item[1];
    }

    foreach ($other as $item) {
        $elements[$item[0]] = $item[1];
    }

    $elements = label_table_options($elements);
} else {
    $elements = get_table_options('Element', null, array(
        'record_types' => array('Item', 'All'),
        'sort' => 'orderBySet'
    ));

    foreach (array_keys($config) as $setName) {
        foreach ($config[$setName] as $elementName => $elementConfig) {
            if (!empty($elementConfig['hidden'])) {
                continue;
            }

            if (empty($elementConfig['label'])) {
                continue;
            }

            $element = $table->findByElementSetNameAndElementName(
                $setName,
                $elementName
            );

            if (empty($element)) {
                continue;
            }

            $elements['Collection'][$element->id] = $elementConfig['label'];
        }
    }
}

if (empty($_REQUEST['advanced'])) {
    $_REQUEST['advanced'] = array(array());
}

$_REQUEST['advanced'][] = array();

echo '<div class="form-item">' . PHP_EOL;
echo '<label>' . __('Fields:') . '</label>' . PHP_EOL;

foreach ($_REQUEST['advanced'] as $count => $row) {
    if ($count > 0) {
        echo '</div>' . PHP_EOL;

        if (sizeof($_REQUEST['advanced']) === $count + 1) {
            echo '<div class="form-item form-item-disabled">' . PHP_EOL;
        } else {
            echo '<div class="form-item">' . PHP_EOL;
        }

        echo '<div class="form-item-before">' . PHP_EOL;
        echo $this->formSelect(
            'advanced[' . $count . '][joiner]',
            isset($row['joiner']) ? $row['joiner'] : '',
            array('title' => __('Logic')),
            array(
                'and' => __('AND'),
                'or' => __('OR')
            )
        );

        echo PHP_EOL . '</div>' . PHP_EOL;
    }

    echo '<div class="form-item-row">' . PHP_EOL;
    echo $this->formSelect(
        'advanced[' . $count . '][element_id]',
        isset($row['element_id']) ? $row['element_id'] : '',
        array('title' => __('Field')),
        $elements
    );

    echo PHP_EOL . $this->formSelect(
        'advanced[' . $count . '][type]',
        isset($row['type']) ? $row['type'] : '',
        array('title' => __('Match')),
        array(
            'contains' => __('contains'),
            'does not contain' => __('does not contain'),
            'is exactly' => __('is exactly'),
            'is empty' => __('is empty'),
            'is not empty' => __('is not empty'),
            'starts with' => __('starts with'),
            'ends with' => __('ends with')
        )
    );

    echo PHP_EOL . $this->formText(
        'advanced[' . $count . '][terms]',
        isset($row['terms']) ? $row['terms'] : '',
        array('title' => __('Phrase'))
    );

    echo PHP_EOL . '</div>' . PHP_EOL;

    if ($count > 0) {
        echo '<div class="form-item-after">';
        echo '<button class="form-item-remove">';
        echo __('Remove') . '</button>';
        echo '</div>' . PHP_EOL;
    }
}

echo '</div>' . PHP_EOL;

echo '<div class="form-item">' . PHP_EOL;
echo '<div><button type="button" class="form-item-add" disabled>';
echo __('Add') . '</button></div>';
echo '</div>' . PHP_EOL;

echo '<div class="form-item">' . PHP_EOL;
echo $this->formLabel('collection-search', __('Collection:')) . PHP_EOL;
echo $this->formSelect(
    'collection',
    isset($_REQUEST['collection']) ? $_REQUEST['collection'] : '',
    array('id' => 'collection-search'),
    get_table_options(
        'Collection',
        null,
        array('include_no_collection' => true)
    )
);

echo PHP_EOL . '</div>' . PHP_EOL;

fire_plugin_hook('public_items_search', array('view' => $this));

echo '<div class="form-item">' . PHP_EOL;
echo $this->formLabel('type-search', __('Type:')) . PHP_EOL;
echo $this->formSelect(
    'type',
    isset($_REQUEST['type']) ? $_REQUEST['type'] : '',
    array('id' => 'type-search'),
    get_table_options('ItemType')
);

echo PHP_EOL . '</div>' . PHP_EOL;

if (is_allowed('Users', 'browse')) {
    echo '<div class="form-item">' . PHP_EOL;
    echo $this->formLabel('user-search', __('User:')) . PHP_EOL;
    echo $this->formSelect(
        'user',
        isset($_REQUEST['user']) ? $_REQUEST['user'] : '',
        array('id' => 'user-search'),
        get_table_options('User')
    );

    echo PHP_EOL . '</div>' . PHP_EOL;
}

if (is_allowed('Items', 'showNotPublic')) {
    echo '<div class="form-item">' . PHP_EOL;
    echo $this->formLabel('public-search', __('Status:')) . PHP_EOL;
    echo $this->formSelect(
        'public',
        isset($_REQUEST['public']) ? $_REQUEST['public'] : '',
        array('id' => 'public-search'),
        label_table_options(array(
            '1' => __('Only Public Items'),
            '0' => __('Only Non-Public Items')
        ))
    );

    echo PHP_EOL . '</div>' . PHP_EOL;
}

if (empty($buttonText)) {
    $buttonText = __('Search');
}

echo '<div class="form-item">' . PHP_EOL;
echo '<div><input type="submit" value="' . $buttonText . '"></div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo '</form>' . PHP_EOL;
