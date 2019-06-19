<?php
if (empty($label)) {
    $label = __('Search Entire Site');
}

echo '<form action="' . url('solr-search') . '" method="get">' . PHP_EOL;
echo '<div class="form-search">' . PHP_EOL;
echo '<input type="text" name="q" id="solr-query" aria-label="';
echo $label . '" placeholder="' . $label . '" value="';
echo (isset($_GET['q']) ? html_escape($_GET['q']) : '') . '">' . PHP_EOL;
echo '<button type="submit">';
echo '<span class="icon icon-search" aria-title="Search"></span>';
echo '</button>' . PHP_EOL;

if (!empty($collection)) {
    echo '<input type="hidden" name="collection" value="';
    echo $collection->id . '">' . PHP_EOL;

    if (empty($_GET['facet'])) {
        $facet = 'collection:"';
        $facet .= metadata($collection, array('Dublin Core', 'Title'));
        $facet .= '"';

        echo '<input type="hidden" name="facet" value="';
        echo html_escape($facet) . '">' . PHP_EOL;
    }
}

if (!empty($_GET['facet'])) {
    echo '<input type="hidden" name="facet" value="';
    echo html_escape($_GET['facet']) . '">' . PHP_EOL;
}

echo '</div>' . PHP_EOL;
echo '</form>' . PHP_EOL;
