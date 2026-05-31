<?php

return [
    [
        'name' => 'Community',
        'flag' => 'community.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'community.create',
        'parent_flag' => 'community.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'community.edit',
        'parent_flag' => 'community.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'community.destroy',
        'parent_flag' => 'community.index',
    ],
];
