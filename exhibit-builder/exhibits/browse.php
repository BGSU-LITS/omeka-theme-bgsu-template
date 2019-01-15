<?php
if (!isset($_GET['display']) || $_GET['display'] !== 'list') {
    $_GET['display'] = 'gallery';
}

echo head(array(
    'title' => __('Exhibits')
));

echo pagination_links();
echo '<div class="records records-paginated records-';
echo $_GET['display'] . '">' . PHP_EOL;

foreach (loop('exhibits') as $exhibit) {
    echo $this->partial(
        'exhibits/single.php',
        array('exhibit' => $exhibit)
    );
}

echo '</div>' . PHP_EOL;
echo pagination_links();
echo foot();
