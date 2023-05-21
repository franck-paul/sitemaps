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

class FrontendTemplate
{
    public static function SitemapEntries($attr, $content)
    {
        return
            '<?php if (dcCore::app()->ctx->exists("sitemap_urls")) : ?>' . "\n" .
            '<?php while (dcCore::app()->ctx->sitemap_urls->fetch()) : ?>' . $content . '<?php endwhile; ?>' .
            '<?php endif; ?>' . "\n";
    }

    public static function SitemapEntryIf($attr, $content)
    {
        $if = '';
        if (isset($attr['has_attr'])) {
            switch ($attr['has_attr']) {
                case 'frequency':$if = '!is_null(dcCore::app()->ctx->sitemap_urls->frequency)';

                    break;
                case 'priority':$if = '!is_null(dcCore::app()->ctx->sitemap_urls->priority)';

                    break;
                case 'lastmod':$if = '!is_null(dcCore::app()->ctx->sitemap_urls->lastmod)';

                    break;
            }
        }
        if (!empty($if)) {
            return '<?php if (' . $if . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    public static function SitemapEntryLoc($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->sitemap_urls->loc') . '; ?>';
    }

    public static function SitemapEntryFrequency($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->sitemap_urls->frequency') . '; ?>';
    }

    public static function SitemapEntryPriority($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->sitemap_urls->priority') . '; ?>';
    }

    public static function SitemapEntryLastmod($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->sitemap_urls->lastmod') . '; ?>';
    }
}
