<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'] . '/',
        ],
        'config' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'] . '/.laravel-installer-plus/',
        ],
    ],
];
