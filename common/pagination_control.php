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

    $sorts = array(
        array(
            'name' => 'most recent',
            'sort' => 'added',
            'dir' => 'd'
        ),
        array(
            'name' => 'title',
            'sort' => $loop === 'Exhibits' ? 'title' : 'Dublin Core,Title',
            'dir' => 'a'
        )
    );

    $queryParams = $_GET;
    $sortDefault = reset($sorts);

    if (empty($queryParams[Omeka_Db_Table::SORT_PARAM])) {
        $queryParams[Omeka_Db_Table::SORT_PARAM] = $sortDefault['sort'];
    }

    if (empty($queryParams[Omeka_Db_Table::SORT_DIR_PARAM])) {
        $queryParams[Omeka_Db_Table::SORT_DIR_PARAM] = $sortDefault['dir'];
    }

    foreach ($sorts as $key => $sort) {
        if ($sort['sort'] !== $queryParams[Omeka_Db_Table::SORT_PARAM]) {
            continue;
        }

        if ($sort['dir'] !== $queryParams[Omeka_Db_Table::SORT_DIR_PARAM]) {
            continue;
        }

        $sortName = $sort['name'];
        unset($sorts[$key]);
        break;
    }

    if (empty($sortName)) {
        list($sortClass, $sortName) = explode(
            ',',
            strtolower($queryParams[Omeka_Db_Table::SORT_PARAM]),
            2
        );

        if (empty($sortName)) {
            $sortName = $sortClass;
        }

        if ($queryParams[Omeka_Db_Table::SORT_DIR_PARAM] === 'a') {
            $sortName .= ' asc.';
        } else {
            $sortName .= ' desc.';
        }
    }

    echo __($sortName) . '</a>' . PHP_EOL;

    foreach ($sorts as $sort) {
        $queryParams[Omeka_Db_Table::SORT_PARAM] = $sort['sort'];
        $queryParams[Omeka_Db_Table::SORT_DIR_PARAM] = $sort['dir'];

        echo '<a href="' . $this->url(array(), null, $queryParams) . '">';
        echo __('Sort by ' . $sort['name']);
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
