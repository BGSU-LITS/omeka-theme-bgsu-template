<?php
echo '<div id="icons"><div>';
echo '<a href="https://icons8.com/icon/5BTv3WK9LLTS/cabinet">Cabinet</a>, ';
echo '<a href="https://icons8.com/icon/pdgB0Zv9XrcB/exhibit">Exhibit</a>, ';
echo '<a href="https://icons8.com/icon/11176/library">Library</a>, ';
echo '<a href="https://icons8.com/icon/DdBNDMCqmCET/media">Media</a>, and ';
echo '<a href="https://icons8.com/icon/di8TwcqWfMcv/shelf">Shelf</a> icons by ';
echo '<a target="_blank" href="https://icons8.com">Icons8</a>';
echo '</div></div>' . PHP_EOL;
echo '</main>' . PHP_EOL;

fire_plugin_hook('public_footer', array('view' => $this));

$outputPages = function ($container, $indent = '    ') use (&$outputPages) {
    echo '[' . PHP_EOL;

    foreach ($container as $page) {
        if ($page->visible) {
            $label = $page->label;
            $title = $page->title;

            if (empty($title)) {
                $parts = array_pad(explode(': ', $label, 2), 2, '');
                list($label, $title) = $parts;
            }

            echo $indent . '    { text: ' . json_encode($label);

            if ($title) {
                echo ', title: ' . json_encode($title);
            }

            if ($page->pages) {
                echo ', menu: ';
                $outputPages($page->pages, '    ' . $indent);
                echo ' },' . PHP_EOL;
                continue;
            }

            echo ', href: ' . json_encode($page->href);
            echo ' },' . PHP_EOL;
        }
    }

    echo $indent . ']';
};

echo '<script>' . PHP_EOL;
?>
bgsu_common.setup({id: 'content'});
bgsu_template.setup({
    body: true,
    main: {
        id: "content"
    },
    unit: {
        thin: "University",
        text: "Libraries",
        href: "https://www.bgsu.edu/library/",
    },
    site: {
        text: <?php echo json_encode(option('site_title')); ?>,
        href: <?php echo json_encode(public_url('/')); ?>,
        heading: <?php echo json_encode(@$home); ?>,
    },
    menu: <?php $outputPages(public_nav_main()->getContainer()); ?>,
    form: {
        action: <?php
            echo json_encode(apply_filters(
                'search_form_default_action',
                public_url('search')
            ));
        ?>,
        method: "get",
        button: <?php echo json_encode(__('Search')); ?>,
        name: "query",
        text: <?php echo json_encode(__('Search Entire Site')); ?>,
        menu: [
            {
                text: <?php echo json_encode(__('Advanced Search')); ?>,
                href: <?php
                    echo json_encode(
                        apply_filters(
                            'items_search_default_url',
                            public_url('items/search')
                        )
                    );
                ?>,
            },
        ],
    },
    help: {
        heading: "Contact Us",
        text: <?php echo json_encode(option('administrator_email')); ?>,
        href: <?php
            echo json_encode(
                'mailto:' .
                option('administrator_email')
            );
        ?>,
    }
});

bgsu_template.toggle("[data-toggle^=toggle-]");
bgsu_tippy.setup("a[title]", {allowHTML: false, arrow: true, placement: "right"});

window.addEventListener('load', function() {
    if (window.bgsu_flickity) {
        window.bgsu_flickity.setup(".records", {lazyLoad: 3, bgLazyLoad: 3});
    }
});
<?php
echo '</script>' . PHP_EOL;
echo '</body>' . PHP_EOL;
echo '</html>' . PHP_EOL;
