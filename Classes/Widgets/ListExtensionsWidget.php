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

use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend as Cache;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;

class ListExtensionsWidget extends AbstractListExtensionsWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    public function __construct(
        protected readonly WidgetConfigurationInterface $configuration,
        protected readonly Cache $cache,
        protected readonly BackendViewFactory $backendViewFactory,
        protected readonly array $options = []
    ) {
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['schams-net/t3extensions_widget']);
        $view->assignMultiple([
            'options' => $this->options,
            'items' => $this->getItems(),
            'footerLink' => $this->footerLink
        ]);
        return $view->render('Widget/ListExtensionsWidget');
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
