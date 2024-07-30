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
    $sorts = array();

    if ($loop === 'Items') {
        $count = 0;

        while ($name = get_theme_option('sort_' . $count . '_name')) {
            $sorts[] = array(
                'name' => $name,
                'field' => get_theme_option('sort_' . $count . '_field'),
                'dir' => get_theme_option('sort_' . $count . '_dir')
            );

            $count++;
        }
    }

    if (empty($sorts)) {
        $sorts = array(
            array(
                'name' => 'most recent',
                'field' => 'added',
                'dir' => 'd'
            ),
            array(
                'name' => 'title',
                'field' => $loop === 'Exhibits'
                    ? 'title'
                    : 'Dublin Core,Title',
                'dir' => 'a'
            )
        );
    }

    $queryParams = $_GET;

    if (empty($queryParams[Omeka_Db_Table::SORT_PARAM])) {
        $queryParams[Omeka_Db_Table::SORT_PARAM] = 'added';
    }

    if (empty($queryParams[Omeka_Db_Table::SORT_DIR_PARAM])) {
        $queryParams[Omeka_Db_Table::SORT_DIR_PARAM] = 'd';
    }

    foreach ($sorts as $key => $sort) {
        if ($sort['field'] !== $queryParams[Omeka_Db_Table::SORT_PARAM]) {
            continue;
        }

        if ($sort['dir'] !== $queryParams[Omeka_Db_Table::SORT_DIR_PARAM]) {
            continue;
        }

        $sortName = __($sort['name']);
        unset($sorts[$key]);
        break;
    }

    if (empty($sortName)) {
        $sortParam = strtolower($queryParams[Omeka_Db_Table::SORT_PARAM]);

        if ($sortParam && preg_match('/^[a-z, ]+$/', $sortParam)) {
            list($sortClass, $sortName) = explode(
                ',',
                strtolower($queryParams[Omeka_Db_Table::SORT_PARAM]),
                2
            );

            if (empty($sortName)) {
                $sortName = $sortClass;
            }

            if ($queryParams[Omeka_Db_Table::SORT_DIR_PARAM] === 'd') {
                $sortName .= ' desc.';
            } else {
                $sortName .= ' asc.';
            }
        }
    }

    if (!empty($sortName)) {
        echo ' ' . __('sorted by') . ' ' . html_escape($sortName);
    }

    echo '</a>' . PHP_EOL;

    foreach ($sorts as $sort) {
        $queryParams[Omeka_Db_Table::SORT_PARAM] = $sort['field'];
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
