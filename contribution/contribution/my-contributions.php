<?php
echo head(array('title' => __('My Contributions')));
echo flash(); 

if ($contrib_items) {
    echo '<form method="post">';

    foreach (loop('contrib_items') as $contribItem) {
        $item = $contribItem->Item;

        echo '<h2>' . link_to(
            $item, 
            'show', 
            metadata($item, array('Dublin Core', 'Title'))
        ) . '</h2>';

        echo '<p>' . metadata($item, 'added') . '<br>';

        if ($contribItem->public) {
            if (metadata($item, 'public')) {
                echo __('This item is viewable by the public.') . '<br>';
            } else {
                echo __('This item has not been made viewable by the public.') . '<br>';
            }
        }

        echo $this->formCheckbox(
            "contribution_anonymous[{$contribItem->id}]",
            null,
            array('checked' => $contribItem->anonymous)
        );

        echo $this->formLabel(
            "contribution_anonymous[{$contribItem->id}]",
            __('Keep my identity private when publishing this item.')
        );

        echo $this->formHidden(
            "contribution_public[{$contribItem->id}]",
            isset($contribItem->public) 
                ? $contribItem->public
                : 1
        );

        echo '</p>';
    }

    echo '<input type="submit" name="submit" class="button" value="';
    echo __('Save Changes') . '">';
    echo '</form>';
} else {
    echo '<p>' . __('No contributions.') . '</p>';
    echo '<p>' . __(
        'Feel free to %s or %s.',
        '<a href="' . contribution_contribute_url() . '">' . 
        __('contribute') . '</a>',
        '<a href="' . url('items/browse') . '">' . 
        __('browse contributions') . '</a>'
    ) . '</p>';
}

echo foot();
