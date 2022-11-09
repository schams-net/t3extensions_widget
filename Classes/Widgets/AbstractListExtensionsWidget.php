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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractListExtensionsWidget
{
    protected ServerRequestInterface $request;
    protected int $limit = 8;
    protected int $cacheLifeTime = 14400; // 4 hours
    protected array $items = [];
    protected string $footerLink = 'https://t3extensions.org';

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    protected function getItems(): array
    {
        $cacheHash = md5($this->options['feedUrl']);
        if ($items = $this->cache->get($cacheHash)) {
            // Return data from cache
            return $items;
        }

        // Read the content from the remote instance
        $content = GeneralUtility::getUrl($this->options['feedUrl']);
        if ($this->isJson($content)) {
            $extensions = json_decode($content);
            $itemCount = 0;
            $this->items = [];
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
            $this->cache->set($cacheHash, $this->items, [], $this->cacheLifeTime);
        }
        //throw new RuntimeException('Remote resources could not be retrieved', 1582685124);
        return $this->items;
    }

    /**
     * Basic check to test if a string is valid JSON data
     */
    protected function isJson(string $string): bool
    {
        @json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
