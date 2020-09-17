<?php
declare(strict_types=1);
namespace SchamsNet\T3extensionsWidget\Widgets;

/*
 * This file is part of the TYPO3 CMS Extension "TYPO3 Extension Widget (t3extensions.org)"
 * Extension author: Michael Schams - https://schams.net
 *
 * For copyright and license information, please read the LICENSE.txt
 * file distributed with this source code.
 *
 * @author Michael Schams <schams.net>
 */

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Other classes extend this class, which provides common properties
 * and functionality.
 */
class AbstractListExtensionsWidget
{
    /**
     * @var WidgetConfigurationInterface
     */
    protected $configuration;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var CacheManager
     */
    protected $cache;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $extensionKey = 't3extensions_widget';

    /**
     * @var string
     */
    protected $templateName = 'ListExtensionsWidget';

    /**
     * @var string
     */
    protected $footerLink = 'https://t3extensions.org';

    /**
     * @var string
     */
    protected $footerLinkText = 't3extensions.org';

    /**
     * @var int
     */
    private $limit = 8;

    /**
     * @var array
     */
    private $items = [];

    /**
     * Cache entry lifetime in seconds
     * 14400 = 4 hours
     *
     * @var int
     */
    private $lifeTime = 14400;

    /**
     * Load data from remote API server (api.t3extensions.org)
     *
     * @access protected
     * @param string
     * @return null|array
     */
    protected function loadData(string $apiEndpoint): ?array
    {
        $cacheHash = md5($apiEndpoint);
        if ($this->items = $this->cache->get($cacheHash)) {
            // Return data from cache
            return $this->items;
        }

        // Reads the content from the given resource using the RequestFactory
        $content = GeneralUtility::getUrl($apiEndpoint);
        if ($this->isJson($content)) {
            $extensions = json_decode($content);
            $itemCount = 0;
            foreach ($extensions as $item) {
                if ($itemCount < $this->limit) {
                    $this->items[] = [
                        'extensionKey' => (string)$item->extension_key,
                        'title' => (string)$item->title,
                        'version' => (string)$item->version,
                        'lastUpdated' => (string)$item->last_updated,
                        'link' => 'https://extensions.typo3.org/extension/' . (string)$item->extension_key,
                    ];
                } else {
                    continue;
                }
                $itemCount++;
            }
            // Put data in the cache
            $this->cache->set($cacheHash, $this->items, [$this->extensionKey], $this->lifeTime);
        }
        //throw new RuntimeException('Remote resources could not be retrieved', 1582685124);
        return $this->items;
    }

    /**
     * Basic check to test if a string is valid JSON
     *
     * @access private
     * @param string
     * @return bool
     */
    private function isJson(string $string): bool
    {
        @json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
