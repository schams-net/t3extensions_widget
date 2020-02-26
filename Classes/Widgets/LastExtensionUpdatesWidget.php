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
use SchamsNet\T3extensionsWidget\Widgets\AbstractExtensionsWidget;

/**
 * The LastExtensionUpdatesWidget sets the API endpoint and triggers
 * the function to retrieve the data from the remote resource.
 */
class LastExtensionUpdatesWidget extends AbstractExtensionsWidget
{
    protected $apiEndpoint = 'https://api.t3extensions.org/s3/last10updates.json';
    protected $title = AbstractExtensionsWidget::LANGUAGE_FILE . ':lastExtensionUpdatesWidget.title';
    protected $description = AbstractExtensionsWidget::LANGUAGE_FILE . ':lastExtensionUpdatesWidget.description';

    /**
     * Render widget content
     *
     * @access public
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $this->loadData();
        return AbstractExtensionsWidget::renderWidgetContent();
    }
}
