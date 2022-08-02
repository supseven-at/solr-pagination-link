<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Solr Pagination Link',
    'description' => 'Provide a viewhelper to create sanitized pagination links',
    'category' => 'fe',
    'author' => 'supseven',
    'author_email' => 'h.strasser@supseven.at',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.1.1-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
