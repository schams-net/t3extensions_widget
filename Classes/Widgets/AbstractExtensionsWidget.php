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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AbstractListWidget;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * The AbstractExtensionsWidget class provides most of the functions
 * required to generate the widgets to list new and updated TYPO3 extensions.
 */
class AbstractExtensionsWidget extends AbstractListWidget
{
    /**
     * Language file
     */
    const LANGUAGE_FILE = 'LLL:EXT:t3extensions_widget/Resources/Private/Language/locallang.xlf';

    /**
     * Extension key
     *
     * @access protected
     * @var string
     */
    protected $extensionKey = 't3extensions_widget';

    protected $apiEndpoint = '';
    protected $height = 6;
    protected $width = 2;
    protected $limit = 8;
    protected $lifeTime = 14400; // 4 hours
    protected $title = '';
    protected $description = '';
    protected $iconIdentifier = 'tx-t3extensions_widget-widget-icon';
    protected $footerLink = 'https://t3extensions.org';
    protected $footerLinkText = self::LANGUAGE_FILE . ':footerLinkText';

    /**
     * @access protected
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cache;

    /**
     * Constructor
     *
     * @access public
     * @param string
     */
    public function __construct(string $identifier)
    {
        AbstractListWidget::__construct($identifier);
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->extensionKey);
    }

    /**
     * Initialize view
     *
     * @access protected
     * @return void
     */
    protected function initializeView(): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $path = ExtensionManagementUtility::extPath($this->extensionKey) . 'Resources/Private/';
        $this->view->getTemplatePaths()->setTemplateRootPaths([$path . 'Templates/']);
        $this->view->getTemplatePaths()->setLayoutRootPaths([$path . 'Layouts/']);
        $this->view->getTemplatePaths()->setPartialRootPaths([$path . 'Partials/']);
        $this->view->setTemplate('Widget/ListWidget');
    }

    /**
     * Render widget content
     *
     * @access public
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $this->view->assign('title', $this->getTitle());
        $this->view->assign('description', $this->getDescription());
        $this->view->assign('items', $this->items);
        $this->view->assign('footerLink', $this->footerLink);
        $this->view->assign('footerLinkText', $this->footerLinkText);
        return $this->view->render();
    }

    /**
     * Load data from remote API server (api.t3extensions.org)
     *
     * @access protected
     * @return void
     */
    protected function loadData(): void
    {
        $cacheHash = md5($this->identifier);
        if ($this->items = $this->cache->get($cacheHash)) {
            return;
        }

        // Reads the content from the given resource using the RequestFactory
        $content = GeneralUtility::getUrl($this->apiEndpoint);
        if ($content === false || !$this->isJson($content)) {
            //throw new RuntimeException('Remote resources could not be retrieved', 1582685124);
            return;
        }
        $lastNewExtensions = json_decode($content);

        $itemCount = 0;
        foreach ($lastNewExtensions as $item) {
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
        $this->cache->set($cacheHash, $this->items, [$this->extensionKey], $this->lifeTime);
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
