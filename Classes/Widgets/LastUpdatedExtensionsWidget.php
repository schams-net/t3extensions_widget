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
 * The LastUpdatedExtensionsWidget lists the latest updated
 * TYPO3 extensions in the TER (data retrieved from t3extensions.org).
 */
class LastUpdatedExtensionsWidget extends AbstractListExtensionsWidget implements WidgetInterface
{
    /**
     * @var string
     */
    private $apiEndpoint = 'https://api.t3extensions.org/s3/last10updates.json';

    /**
     * Constructor
     *
     * @access public
     * @param WidgetConfigurationInterface
     * @param StandaloneView
     * @param array
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->options = $options;
        // Initialize cache
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->extensionKey);
    }

    /**
     * Render widget content by using custom templates
     *
     * @access public
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/' . $this->templateName);
        $this->view->getRenderingContext()->getTemplatePaths()->fillDefaultsByPackageName($this->extensionKey);
        $this->view->assign('configuration', $this->configuration);
        $this->view->assign('items', $this->loadData($this->apiEndpoint));
        $this->view->assign('footerLink', $this->footerLink);
        $this->view->assign('footerLinkText', $this->footerLinkText);
        return $this->view->render();
    }
}
