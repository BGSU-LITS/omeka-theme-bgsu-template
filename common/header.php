<?php
echo '<!doctype html>' . PHP_EOL;
echo '<html lang="' . get_html_lang() . '">' . PHP_EOL;
echo '<head>' . PHP_EOL;
echo '<meta charset="utf-8">' . PHP_EOL;

if (isset($title)) {
    $titleParts[] = $title;
}

if (isset($ancestors)) {
    foreach ($ancestors as $key => $value) {
        $titleParts[] = $value;
    }
}

$titleParts[] = option('site_title');
$titleParts[] = 'BGSU University Libraries';

echo '<title>' . implode(' - ', $titleParts) . '</title>' . PHP_EOL;
echo '<script>' . PHP_EOL;
?>
(function(s) {
    s.display = 'none';

    document.addEventListener('DOMContentLoaded', function() {
        s.display = 'block';
    });
})(document.querySelector('html').style);
<?php
echo '</script>' . PHP_EOL;

if (!isset($description)) {
    $description = option('description');
}

if (isset($description)) {
    echo '<meta name="description" content="';
    echo html_escape($description) . '">' . PHP_EOL;
}

echo '<meta name="viewport" content="';
echo 'width=device-width, initial-scale=1, shrink-to-fit=no">' . PHP_EOL;
echo auto_discovery_link_tags() . PHP_EOL;

echo '<meta property="og:url" content="' . absolute_url() . '">' . PHP_EOL;
echo '<meta property="og:type" content="website">' . PHP_EOL;

if (isset($title)) {
    echo '<meta property="og:title" content="' . $title . '">' . PHP_EOL;
}

if (isset($description)) {
    echo '<meta property="og:description" content="';
    echo html_escape($description) . '">' . PHP_EOL;
}

if (isset($image)) {
    echo '<meta property="og:image" content="' . $image . '">' . PHP_EOL;
}

if (isset($image)) {
    echo '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
} else {
    echo '<meta name="twitter:card" content="summary">' . PHP_EOL;
}

if ($twitter = get_theme_option('twitter')) {
    echo '<meta name="twitter:site" content="@' . $twitter . '">' . PHP_EOL;
}

if (isset($title)) {
    echo '<meta name="twitter:title" content="' . $title . '">' . PHP_EOL;
}

if (isset($description)) {
    echo '<meta name="twitter:description" content="';
    echo html_escape($description) . '">' . PHP_EOL;
}

if (isset($image)) {
    echo '<meta name="twitter:image" content="' . $image . '">' . PHP_EOL;
}

fire_plugin_hook('public_head', array('view' => $this));

get_view()->headScript()
    ->prependFile(BGSU_TEMPLATE . 'common.js')
    ->prependFile(BGSU_TEMPLATE . 'template.js')
    ->prependFile(BGSU_TEMPLATE . 'tippy.js');

queue_css_file('forms');

echo head_css() . PHP_EOL . head_js(false) . PHP_EOL;
echo '</head>' . PHP_EOL;

echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass));
echo '<a href="#content" id="skipnav" hidden aria-hidden="false">';
echo __('Skip to main content') . '</a>' . PHP_EOL;

fire_plugin_hook('public_body', array('view' => $this));
fire_plugin_hook('public_header', array('view' => $this));

echo '<main id="content">' . PHP_EOL;

fire_plugin_hook('public_content_top', array('view' => $this));

if (isset($title)) {
    echo '<h1>' . $title . '</h1>' . PHP_EOL;
    echo '<nav aria-label="' . __('breadcrumb') . '">' . PHP_EOL;
    echo '<ol class="list-breadcrumb">' . PHP_EOL;
    echo '<li><a href="' . public_url('/') . '">';
    echo option('site_title') . '</a></li>';

    if (isset($ancestors)) {
        foreach ($ancestors as $key => $value) {
            echo '<li><a href="' . $key . '">';
            echo $value . '</a></li>';
        }
    }

    echo '<li aria-current="page">' . $title . '</li>' . PHP_EOL;
    echo '</ol>' . PHP_EOL;
    echo '</nav>' . PHP_EOL;
}
