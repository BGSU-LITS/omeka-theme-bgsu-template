<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

$config = Symfony\Component\Yaml\Yaml::parse(
    get_theme_option('elements_config')
);

queue_js_url(BGSU_TEMPLATE . 'facets.js');

$style = get_theme_option('style');

if ($style === 'finding_aids' && !isset($_GET['display'])) {
    $_GET['display'] = 'list';
}

if (!isset($_GET['display']) || $_GET['display'] !== 'list') {
    $_GET['display'] = 'gallery';
}

$ancestors = array();

if (!empty($_GET['collection'])) {
    $collection = get_record_by_id('collection', $_GET['collection']);

    $ancestors = array(
        url('collections') => 'Collections',
        record_url($collection) => metadata($collection, 'display_title')
    );
}

echo head(array(
    'title' => __('Search Results'),
    'ancestors' => $ancestors
));

echo '<div class="sidebar">' . PHP_EOL;

if (!empty($collection)) {
    echo '<div class="sidebar-left" id="collection">' . PHP_EOL;

    echo $this->partial(
        'collections/sidebar.php',
        array('collection' => $collection)
    );
} else {
    echo '<div class="sidebar-left">' . PHP_EOL;
    echo $this->partial('solr-search/results/sidebar.php');
}

$fields = get_db()->getTable('SolrSearchField');
$elements = get_db()->getTable('Element');

$getFacetLabel = function ($name) use ($fields, $elements, $config) {
    $field = $fields->findBySlug(rtrim($name, '_s'));
    $label = $field->label;

    if ($field->element_id) {
        $element = $elements->find($field->element_id);
        $set = $element->getElementSet()->name;

        if (!empty($config[$set][$element->name]['label'])) {
            $label = $config[$set][$element->name]['label'];
        }
    }

    return $label;
};

$getFacetHidden = function ($name) use ($fields, $elements, $config) {
    $field = $fields->findBySlug(rtrim($name, '_s'));

    if ($field->element_id) {
        $element = $elements->find($field->element_id);
        $set = $element->getElementSet()->name;

        if (empty($config[$set][$element->name])) {
            if (get_theme_option('elements_hidden')) {
                return true;
            }
        } elseif (!empty($config[$set][$element->name]['hidden'])) {
            return true;
        }
    }

    return false;
};

$facets = array();

foreach (SolrSearch_Helpers_Facet::parseFacets() as $facet) {
    $html = '<li>' . $facet[1] . '<a class="icon icon-remove" href="';
    $html .= SolrSearch_Helpers_Facet::removeFacet($facet[0], $facet[1]);

    if (!empty($collection) && $facet[0] !== 'collection') {
        $html .= '&amp;collection=' . $collection->id;
    }

    $html .= '" title="' . __('Remove') . ' ';
    $html .= $getFacetLabel($facet[0]) . ': ' . $facet[1];
    $html .= '"></a></li>' . PHP_EOL;

    $facets[$facet[0]]['added'][$facet[1]] = $html;
}

foreach ($results->facet_counts->facet_fields as $name => $items) {
    $facets[$name]['other'] = array();

    if ($getFacetHidden($name)) {
        continue;
    }

    foreach ($items as $value => $total) {
        if (empty($facets[$name]['added'][$value])) {
            $html = '<li><a title="Add ';
            $html .= $getFacetLabel($name);
            $html .= ' ' . $value . '" href="';
            $html .= SolrSearch_Helpers_Facet::addFacet($name, $value);

            if (!empty($collection)) {
                $html .= '&amp;collection=' . $collection->id;
            }

            $html .= '">';
            $html .= $value . '</a> <span class="list-facets-count">';
            $html .= $total . '</span></li>' . PHP_EOL;

            $facets[$name]['other'][$value] = $html;
        }
    }
}

echo '<h2 class="sidebar-title">';
echo __('Refine Your Search') . '</h2>' . PHP_EOL;
echo '<ul>' . PHP_EOL;

foreach (array_keys($facets) as $name) {
    if (!empty($facets[$name]['other'])
     && sizeof($facets[$name]['other']) >= 2) {
        echo '<li class="list-item-toggle">' . PHP_EOL;
        echo '<button id="facets-top-' . $name . '-toggle"';
        echo ' data-toggle="facets-top-' . $name . '"';
        echo ' class="toggle-control">';
    } elseif (!empty($facets[$name]['added'])) {
        echo '<li>';
    } else {
        continue;
    }

    echo $getFacetLabel($name);

    if (!empty($facets[$name]['other'])
     && sizeof($facets[$name]['other']) >= 2) {
        echo '</button>';
    }

    echo PHP_EOL;

    if (!empty($facets[$name]['added'])) {
        echo '<ul class="list-facets">' . PHP_EOL;

        foreach ($facets[$name]['added'] as $html) {
            echo $html;
        }

        echo '</ul>' . PHP_EOL;
    }

    $count = 1;

    if (!empty($facets[$name]['other'])
     && sizeof($facets[$name]['other']) >= 2) {
        echo '<div id="facets-top-' . $name . '">' . PHP_EOL;
        echo '<ul class="list-facets list-facets-top">' . PHP_EOL;

        foreach ($facets[$name]['other'] as $html) {
            if ($count++ > 5) {
                echo '<li><button id="facets-all-' . $name . '-toggle"';
                echo ' data-toggle="facets-all-' . $name . '"';
                echo ' class="toggle-control"';
                echo ' title="' . __('Show More') . ' ';
                echo $getFacetLabel($name) . ' ' . __('Refinements') . '">';
                echo __('More') . '</button></li>' . PHP_EOL;

                break;
            }

            echo $html;
        }

        echo '</ul>' . PHP_EOL;
        echo '<ul id="facets-all-' . $name . '"';
        echo ' class="list-facets list-facets-all">' . PHP_EOL;

        foreach ($facets[$name]['other'] as $html) {
            echo $html;
        }

        echo '</ul>' . PHP_EOL;
        echo '</div>' . PHP_EOL;
    }

    echo '</li>' . PHP_EOL;
}

if (empty($count)) {
    echo '<li>No Refinements Available</li>' . PHP_EOL;
}

echo '</ul>' . PHP_EOL;
echo '</div>' . PHP_EOL;

echo '<div class="records-paginated">' . PHP_EOL;

if (!empty($results->response->docs)) {
    echo pagination_links();
    echo '<div class="records records-';
    echo $_GET['display'] . '">' . PHP_EOL;

    foreach ($results->response->docs as $result) {
        $highlighting = false;

        if (get_option('solr_search_hl')) {
            $highlighting = $results->highlighting->{$result->id};
        }

        echo $this->partial(
            'results/single.php',
            array('result' => $result, 'highlighting' => $highlighting)
        );
    }

    echo '</div>' . PHP_EOL;
    echo pagination_links();
} else {
    echo '<p><strong>No Results</strong><br>';
    echo 'Please refine your search and try again.</p>' . PHP_EOL;
}

echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo '<script>bgsu_facets.setup(bgsu_template.toggle);</script>';
echo foot();
