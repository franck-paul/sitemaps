<?php
/**
 * @brief sitemaps, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\sitemaps;

use dcCore;
use Dotclear\Core\Process;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Don't do things in frontend if plugin disabled
        $settings = My::settings();
        if (!(bool) $settings->active) {
            return false;
        }

        dcCore::app()->addBehavior('publicBeforeDocumentV2', FrontendBehaviors::addTemplatePath(...));

        dcCore::app()->tpl->addBlock('SitemapEntries', FrontendTemplate::SitemapEntries(...));
        dcCore::app()->tpl->addBlock('SitemapEntryIf', FrontendTemplate::SitemapEntryIf(...));
        dcCore::app()->tpl->addValue('SitemapEntryLoc', FrontendTemplate::SitemapEntryLoc(...));
        dcCore::app()->tpl->addValue('SitemapEntryFrequency', FrontendTemplate::SitemapEntryFrequency(...));
        dcCore::app()->tpl->addValue('SitemapEntryPriority', FrontendTemplate::SitemapEntryPriority(...));
        dcCore::app()->tpl->addValue('SitemapEntryLastmod', FrontendTemplate::SitemapEntryLastmod(...));

        return true;
    }
}
