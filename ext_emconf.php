<?php

/*
 * This file is part of the TYPO3 CMS Extension "TYPO3 Extension Widget (t3extensions.org)"
 * Extension author: Michael Schams - https://schams.net
 *
 * For copyright and license information, please read the LICENSE.txt
 * file distributed with this source code.
 *
 * @author Michael Schams <schams.net>
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Extension Widget (t3extensions.org)',
    'description' => 'Dashboard widget that displays the most recent TYPO3 extensions published/updated at the TER',
    'category' => 'be',
    'author' => 'Michael Schams',
    'author_email' => 'schams.net',
    'author_company' => 'schams.net',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '1.1.2',
    'autoload' => [
        'psr-4' => [
            'SchamsNet\\T3extensionsWidget\\' => 'Classes'
        ]
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'php' => '7.2.0-7.4.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
