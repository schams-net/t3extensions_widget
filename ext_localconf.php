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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function () {

    if (TYPO3_MODE === 'BE') {
        // Configure caching framework
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['t3extensions_widget'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['t3extensions_widget'] = [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
                'options' => [
                    'defaultLifetime' => 14400
                ],
            ];
        }
    }
});
