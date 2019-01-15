<?php
$options = get_table_options('Exhibit');
$default = $options[''];

unset($options['']);
asort($options);

$options = array('' => $default) + $options;

echo '<div class="form-item">' . PHP_EOL;
echo $this->formLabel('exhibit-search', __('Exhibit:')) . PHP_EOL;
echo $this->formSelect(
    'exhibit',
    empty($_REQUEST['exhibit']) ? '' : $_REQUEST['exhibit'],
    array('id' => 'exhibit-search'),
    $options
);

echo PHP_EOL . '</div>' . PHP_EOL;
