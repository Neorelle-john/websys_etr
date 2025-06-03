<?php

return [
    'cache' => [
        'enabled'   => true,
        'duration'  => 600,
    ],

    'csv' => [
        'delimiter'   => ',',
        'enclosure'   => '"',
        'line_ending' => "\r\n",
    ],

    'export' => [
        'autosize' => true,
        'font'     => [
            'family'  => 'Arial',
            'size'    => 12,
            'bold'    => false,
        ],
    ],

    'import' => [
        'heading' => 'slugged',
    ],

    'filters' => [
        'enabled' => true,
    ],

    'format_dates' => true,
];