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
use dcNsProcess;

class Frontend extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::FRONTEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // Don't do things in frontend if plugin disabled
        $settings = dcCore::app()->blog->settings->get(My::id());
        if (!(bool) $settings->active) {
            return false;
        }

        dcCore::app()->addBehavior('publicBeforeDocumentV2', [FrontendBehaviors::class, 'addTemplatePath']);

        dcCore::app()->tpl->addBlock('SitemapEntries', [FrontendTemplate::class, 'SitemapEntries']);
        dcCore::app()->tpl->addBlock('SitemapEntryIf', [FrontendTemplate::class, 'SitemapEntryIf']);
        dcCore::app()->tpl->addValue('SitemapEntryLoc', [FrontendTemplate::class, 'SitemapEntryLoc']);
        dcCore::app()->tpl->addValue('SitemapEntryFrequency', [FrontendTemplate::class, 'SitemapEntryFrequency']);
        dcCore::app()->tpl->addValue('SitemapEntryPriority', [FrontendTemplate::class, 'SitemapEntryPriority']);
        dcCore::app()->tpl->addValue('SitemapEntryLastmod', [FrontendTemplate::class, 'SitemapEntryLastmod']);

        return true;
    }
}
