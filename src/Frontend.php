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

use Dotclear\App;
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

        App::behavior()->addBehavior('publicBeforeDocumentV2', FrontendBehaviors::addTemplatePath(...));

        App::frontend()->template()->addBlock('SitemapEntries', FrontendTemplate::SitemapEntries(...));
        App::frontend()->template()->addBlock('SitemapEntryIf', FrontendTemplate::SitemapEntryIf(...));
        App::frontend()->template()->addValue('SitemapEntryLoc', FrontendTemplate::SitemapEntryLoc(...));
        App::frontend()->template()->addValue('SitemapEntryFrequency', FrontendTemplate::SitemapEntryFrequency(...));
        App::frontend()->template()->addValue('SitemapEntryPriority', FrontendTemplate::SitemapEntryPriority(...));
        App::frontend()->template()->addValue('SitemapEntryLastmod', FrontendTemplate::SitemapEntryLastmod(...));

        return true;
    }
}
