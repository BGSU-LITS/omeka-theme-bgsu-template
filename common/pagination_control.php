<?php
$loop = 'Results';

foreach (array('items', 'collections', 'exhibits') as $type) {
    if (has_loop_records($type)) {
        $loop = ucwords($type);
        break;
    }
}

echo '<nav class="nav-page" aria-label="' . __('pagination') . '">' . PHP_EOL;
$queryParams = $_GET;

echo '<div>' . PHP_EOL;
echo '<a class="nav-page-first"';

if (isset($this->previous)) {
    $queryParams['page'] = $this->first;
    echo ' href="' . $this->url(array(), null, $queryParams) . '"';
}

echo '>' . __('First') . '</a>' . PHP_EOL;
echo '<a class="nav-page-previous"';

if (isset($this->previous)) {
    $queryParams['page'] = $this->previous;
    echo ' href="' . $this->url(array(), null, $queryParams) . '"';
}

echo '>' . __('Previous') . '</a>' . PHP_EOL;
echo '<a class="nav-page-next"';

if (isset($this->next)) {
    $queryParams['page'] = $this->next;
    echo ' href="' . $this->url(array(), null, $queryParams) . '"';
}

echo '>' .  __('Next') . '</a>' . PHP_EOL;
echo '<a class="nav-page-last"';

if (isset($this->next)) {
    $queryParams['page'] = $this->last;
    echo ' href="' . $this->url(array(), null, $queryParams) . '"';
}

echo '>' .  __('Last') . '</a>' . PHP_EOL;
echo '</div>' . PHP_EOL;


echo '<div>' . PHP_EOL;
echo '<a aria-current="page">';

if ($this->totalItemCount) {
    echo __($loop) . ' ';
    echo $this->firstItemNumber . ' &ndash; ';
    echo $this->lastItemNumber . ' ' . __('of') . ' ';
    echo $this->totalItemCount;
} else {
    echo $this->totalItemCount . ' ' . __($loop);
}

if ($loop !== 'Results') {
    echo ' ' . __('sorted by') . ' ';

    $queryParams = $_GET;

    if (empty($queryParams[Omeka_Db_Table::SORT_PARAM])) {
        $queryParams[Omeka_Db_Table::SORT_PARAM] = 'added';
    }

    if ($queryParams[Omeka_Db_Table::SORT_PARAM] === 'added') {
        echo __('most recent') . '</a>' . PHP_EOL;

        if ($loop === 'Exhibits') {
            $queryParams[Omeka_Db_Table::SORT_PARAM] = 'title';
        } else {
            $queryParams[Omeka_Db_Table::SORT_PARAM] = 'Dublin Core,Title';
        }

        $queryParams[Omeka_Db_Table::SORT_DIR_PARAM] = 'a';

        echo '<a href="' . $this->url(array(), null, $queryParams) . '">';
        echo __('Sort by title');
    } else {
        echo __('title') . '</a>' . PHP_EOL;

        $queryParams[Omeka_Db_Table::SORT_PARAM] = 'added';
        $queryParams[Omeka_Db_Table::SORT_DIR_PARAM] = 'd';

        echo '<a href="' . $this->url(array(), null, $queryParams) . '">';
        echo __('Sort by most recent');
    }
}

echo '</a>' . PHP_EOL;

$queryParams = $_GET;

if (isset($queryParams['display']) && $queryParams['display'] === 'list') {
    $queryParams['display'] = 'gallery';

    echo '<a href="' . $this->url(array(), null, $queryParams) . '">';
    echo __('View as a gallery') . '</a>' . PHP_EOL;
} else {
    $queryParams['display'] = 'list';

    echo '<a href="' . $this->url(array(), null, $queryParams) . '">';
    echo __('View as a list') . '</a>' . PHP_EOL;
}

echo '</div>' . PHP_EOL;
echo '</nav>' . PHP_EOL;
