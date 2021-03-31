<?php 
echo head(array('title' => __('Contribute an Item')));
echo '<h2>' . __("Thank you for contributing!") . '</h2>';
echo '<p>' . __(
	'Your contribution will show up in the archive once an administrator' .
	' approves it. Meanwhile, feel free to %s or %s.', 
	contribution_link_to_contribute(__('make another contribution')), 
	'<a href="' . url('items/browse') . '">' . 
	__('browse the archive') . '</a>'
) . '</p>';

if (get_option('contribution_open') && !current_user()) {
	echo '<p>' . __(
		'If you would like to interact with the site further, you can use' .
		' an account that is ready for you. Visit %s, and request a new' .
		' password for the email you used',
		'<a href="' . url('users/forgot-password') . '">' . 
		__('this page') . '</a>'
	) . '</p>';
}

echo foot();
